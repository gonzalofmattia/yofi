<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

const STOCK_RESERVATION_MINUTES = 30;

/**
 * Expresión SQL para stock disponible a la venta (físico menos reservado).
 */
function stock_disponible_sql(string $alias = 's'): string
{
    return '(' . $alias . '.stock - COALESCE(' . $alias . '.stock_reservado, 0))';
}

function stock_reservation_column_exists(PDO $pdo, string $table, string $column): bool
{
    static $cache = [];
    $key = $table . '.' . $column;
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }

    $stmt = $pdo->query('SHOW COLUMNS FROM `' . str_replace('`', '', $table) . '` LIKE ' . $pdo->quote($column));
    $cache[$key] = $stmt !== false && (bool) $stmt->fetch();

    return $cache[$key];
}

/**
 * Libera reservas vencidas (pedidos pendientes sin pago).
 */
function stock_expire_pending_reservations(PDO $pdo): int
{
    if (!stock_reservation_column_exists($pdo, 'tbl_ordenes', 'reserva_expira_at')) {
        return 0;
    }

    $stmt = $pdo->query("
        SELECT id_orden
        FROM tbl_ordenes
        WHERE estado = 'pendiente'
          AND reserva_activa = 1
          AND reserva_expira_at IS NOT NULL
          AND reserva_expira_at < NOW()
          AND deleted_at IS NULL
        ORDER BY reserva_expira_at ASC
        LIMIT 50
    ");
    $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    $count = 0;

    foreach ($rows as $row) {
        $orderId = (int) ($row['id_orden'] ?? 0);
        if ($orderId <= 0) {
            continue;
        }

        $stmtOrder = $pdo->prepare('SELECT * FROM tbl_ordenes WHERE id_orden = ? LIMIT 1');
        $stmtOrder->execute([$orderId]);
        $orderRow = $stmtOrder->fetch(PDO::FETCH_ASSOC);
        if (!$orderRow) {
            continue;
        }

        if (stock_release_order_reservation($pdo, $orderRow, 'Reserva expirada (30 min)', true)) {
            ++$count;
        }
    }

    return $count;
}

/**
 * @return array<int, int>
 */
function stock_aggregate_order_skus(array $orderRow): array
{
    $items = json_decode((string) ($orderRow['items'] ?? '[]'), true);
    if (!is_array($items) || $items === []) {
        return [];
    }

    $bySku = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $idSku = (int) ($item['id_sku'] ?? 0);
        $qty = max(1, (int) ($item['cantidad'] ?? $item['quantity'] ?? 1));
        if ($idSku <= 0) {
            continue;
        }
        $bySku[$idSku] = ($bySku[$idSku] ?? 0) + $qty;
    }

    ksort($bySku, SORT_NUMERIC);

    return $bySku;
}

function stock_order_log_exists(PDO $pdo, int $orderId, string $motivo): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM tbl_stock_log WHERE orden_id = ? AND motivo = ?');
    $stmt->execute([$orderId, $motivo]);

    return (int) $stmt->fetchColumn() > 0;
}

/**
 * Reserva stock al crear pedido pendiente.
 *
 * @param array<int, int> $aggSkus
 */
function stock_reserve_for_order(PDO $pdo, int $orderId, array $aggSkus): void
{
    if ($aggSkus === []) {
        throw new InvalidArgumentException('Sin SKUs para reservar');
    }

    $hasReservedCol = stock_reservation_column_exists($pdo, 'tbl_skus', 'stock_reservado');
    $dispSql = $hasReservedCol ? stock_disponible_sql('s') : 's.stock';

    $stmtLock = $pdo->prepare("
        SELECT s.id_sku, s.stock, s.stock_reservado, s.id_prod, p.nombre, {$dispSql} AS disponible
        FROM tbl_skus s
        INNER JOIN tbl_productos p ON p.id_prod = s.id_prod
        WHERE s.id_sku = ?
        FOR UPDATE
    ");
    $stmtReserve = $hasReservedCol
        ? $pdo->prepare('UPDATE tbl_skus SET stock_reservado = stock_reservado + ? WHERE id_sku = ?')
        : $pdo->prepare('UPDATE tbl_skus SET stock = stock - ? WHERE id_sku = ? AND stock >= ?');
    $stmtLog = $pdo->prepare('
        INSERT INTO tbl_stock_log
            (producto_id, cantidad_anterior, cantidad_nueva, diferencia, motivo, orden_id, nota)
        VALUES
            (?, ?, ?, ?, ?, ?, ?)
    ');

    foreach ($aggSkus as $idSku => $qty) {
        $idSku = (int) $idSku;
        $qty = (int) $qty;

        $stmtLock->execute([$idSku]);
        $row = $stmtLock->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new RuntimeException('SKU ' . $idSku . ' no encontrado');
        }

        $disponible = (int) ($row['disponible'] ?? $row['stock'] ?? 0);
        if ($qty > $disponible) {
            throw new RuntimeException('Stock insuficiente SKU ' . $idSku);
        }

        if ($hasReservedCol) {
            $prevRes = (int) ($row['stock_reservado'] ?? 0);
            $stmtReserve->execute([$qty, $idSku]);
            $stmtLog->execute([
                (int) $row['id_prod'],
                $prevRes,
                $prevRes + $qty,
                $qty,
                'reserva',
                $orderId,
                'Reserva checkout SKU ' . $idSku,
            ]);
        } else {
            $prev = (int) $row['stock'];
            $stmtReserve->execute([$qty, $idSku, $qty]);
            if ($stmtReserve->rowCount() === 0) {
                throw new RuntimeException('No se pudo reservar SKU ' . $idSku);
            }
            $stmtLog->execute([
                (int) $row['id_prod'],
                $prev,
                $prev - $qty,
                -$qty,
                'reserva',
                $orderId,
                'Reserva checkout SKU ' . $idSku,
            ]);
        }
    }

    if (stock_reservation_column_exists($pdo, 'tbl_ordenes', 'reserva_expira_at')) {
        $pdo->prepare('
            UPDATE tbl_ordenes
            SET reserva_expira_at = DATE_ADD(NOW(), INTERVAL ? MINUTE),
                reserva_activa = 1
            WHERE id_orden = ?
        ')->execute([STOCK_RESERVATION_MINUTES, $orderId]);
    }
}

/**
 * Confirma venta: descuenta stock físico y libera la reserva.
 *
 * @param array<string, mixed> $orderRow
 */
function stock_confirm_order_reservation(PDO $pdo, array $orderRow): void
{
    $orderId = (int) ($orderRow['id_orden'] ?? 0);
    if ($orderId <= 0) {
        return;
    }

    if (stock_order_log_exists($pdo, $orderId, 'venta')) {
        return;
    }

    $aggSkus = stock_aggregate_order_skus($orderRow);
    if ($aggSkus === []) {
        return;
    }

    $hasReservedCol = stock_reservation_column_exists($pdo, 'tbl_skus', 'stock_reservado');
    $reservaActiva = (int) ($orderRow['reserva_activa'] ?? 0) === 1;

    $stmtUpdate = $hasReservedCol && $reservaActiva
        ? $pdo->prepare('
            UPDATE tbl_skus
            SET stock = stock - ?, stock_reservado = GREATEST(0, stock_reservado - ?)
            WHERE id_sku = ? AND stock_reservado >= ?
        ')
        : $pdo->prepare('
            UPDATE tbl_skus
            SET stock = stock - ?
            WHERE id_sku = ? AND stock >= ?
        ');

    $stmtLog = $pdo->prepare('
        INSERT INTO tbl_stock_log
            (producto_id, cantidad_anterior, cantidad_nueva, diferencia, motivo, orden_id, usuario_admin, nota)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?)
    ');

    foreach ($aggSkus as $idSku => $qty) {
        $idSku = (int) $idSku;
        $qty = (int) $qty;

        $stmtSel = $pdo->prepare('SELECT stock, stock_reservado, id_prod FROM tbl_skus WHERE id_sku = ? LIMIT 1');
        $stmtSel->execute([$idSku]);
        $row = $stmtSel->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            error_log("stock_confirm: SKU {$idSku} no encontrado order={$orderId}");
            continue;
        }

        $prevStock = (int) $row['stock'];

        if ($hasReservedCol && $reservaActiva) {
            $stmtUpdate->execute([$qty, $qty, $idSku, $qty]);
        } else {
            $stmtUpdate->execute([$qty, $idSku, $qty]);
        }

        if ($stmtUpdate->rowCount() === 0) {
            error_log("stock_confirm: falló descuento SKU {$idSku} order={$orderId}");
            continue;
        }

        $stmtLog->execute([
            (int) $row['id_prod'],
            $prevStock,
            $prevStock - $qty,
            -$qty,
            'venta',
            $orderId,
            'MercadoPago',
            'Venta confirmada SKU ' . $idSku,
        ]);
    }

    if (stock_reservation_column_exists($pdo, 'tbl_ordenes', 'reserva_activa')) {
        $pdo->prepare('UPDATE tbl_ordenes SET reserva_activa = 0 WHERE id_orden = ?')->execute([$orderId]);
    }
}

/**
 * Libera reserva (pago rechazado/cancelado o expiración).
 *
 * @param array<string, mixed> $orderRow
 */
function stock_release_order_reservation(PDO $pdo, array $orderRow, string $nota, bool $markCancelled = false): bool
{
    $orderId = (int) ($orderRow['id_orden'] ?? 0);
    if ($orderId <= 0) {
        return false;
    }

    if ((int) ($orderRow['reserva_activa'] ?? 0) !== 1) {
        return false;
    }

    if (stock_order_log_exists($pdo, $orderId, 'liberacion_reserva')) {
        return false;
    }

    $aggSkus = stock_aggregate_order_skus($orderRow);
    if ($aggSkus === []) {
        return false;
    }

    $hasReservedCol = stock_reservation_column_exists($pdo, 'tbl_skus', 'stock_reservado');

    $pdo->beginTransaction();
    try {
        foreach ($aggSkus as $idSku => $qty) {
            $idSku = (int) $idSku;
            $qty = (int) $qty;

            $stmtSel = $pdo->prepare('SELECT stock, stock_reservado, id_prod FROM tbl_skus WHERE id_sku = ? FOR UPDATE');
            $stmtSel->execute([$idSku]);
            $row = $stmtSel->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                continue;
            }

            if ($hasReservedCol) {
                $prevRes = (int) ($row['stock_reservado'] ?? 0);
                $pdo->prepare('
                    UPDATE tbl_skus
                    SET stock_reservado = GREATEST(0, stock_reservado - ?)
                    WHERE id_sku = ?
                ')->execute([$qty, $idSku]);

                $pdo->prepare('
                    INSERT INTO tbl_stock_log
                        (producto_id, cantidad_anterior, cantidad_nueva, diferencia, motivo, orden_id, nota)
                    VALUES
                        (?, ?, ?, ?, ?, ?, ?)
                ')->execute([
                    (int) $row['id_prod'],
                    $prevRes,
                    max(0, $prevRes - $qty),
                    -$qty,
                    'liberacion_reserva',
                    $orderId,
                    $nota . ' SKU ' . $idSku,
                ]);
            } else {
                $prev = (int) $row['stock'];
                $pdo->prepare('UPDATE tbl_skus SET stock = stock + ? WHERE id_sku = ?')->execute([$qty, $idSku]);
                $pdo->prepare('
                    INSERT INTO tbl_stock_log
                        (producto_id, cantidad_anterior, cantidad_nueva, diferencia, motivo, orden_id, nota)
                    VALUES
                        (?, ?, ?, ?, ?, ?, ?)
                ')->execute([
                    (int) $row['id_prod'],
                    $prev,
                    $prev + $qty,
                    $qty,
                    'liberacion_reserva',
                    $orderId,
                    $nota . ' SKU ' . $idSku,
                ]);
            }
        }

        $pdo->prepare('UPDATE tbl_ordenes SET reserva_activa = 0 WHERE id_orden = ?')->execute([$orderId]);

        if ($markCancelled) {
            $estado = strtolower(trim((string) ($orderRow['estado'] ?? '')));
            if ($estado === 'pendiente') {
                $pdo->prepare("UPDATE tbl_ordenes SET estado = 'cancelado', fecha_actualizacion = NOW() WHERE id_orden = ?")
                    ->execute([$orderId]);
            }
        }

        $pdo->commit();

        return true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('stock_release_order_reservation: ' . $e->getMessage());

        return false;
    }
}
