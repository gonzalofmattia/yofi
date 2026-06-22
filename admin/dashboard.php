<?php
ob_start();
require_once __DIR__ . '/include/session_init.php';
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';
ob_end_flush();

$pageTitle = 'Dashboard';

$estadosVenta = admin_estados_venta_confirmada();
$pedidos_hoy = 0;
$ventas_mes = 0.0;
$stock_bajo = 0;
$topProductos = [];
$ultimos_pedidos = false;

$placeholdersVenta = implode(',', array_fill(0, count($estadosVenta), '?'));
$typesVenta = str_repeat('s', count($estadosVenta));

$r = mysqli_query($con, "SELECT COUNT(*) AS total FROM tbl_ordenes WHERE DATE(fecha_creacion) = CURDATE() AND deleted_at IS NULL");
if ($r) {
    $pedidos_hoy = (int)(mysqli_fetch_assoc($r)['total'] ?? 0);
}

$stmtVentas = mysqli_prepare($con, "
    SELECT COALESCE(SUM(total), 0) AS total FROM tbl_ordenes
    WHERE estado IN ($placeholdersVenta)
      AND MONTH(fecha_creacion) = MONTH(NOW())
      AND YEAR(fecha_creacion) = YEAR(NOW())
      AND deleted_at IS NULL
");
if ($stmtVentas) {
    mysqli_stmt_bind_param($stmtVentas, $typesVenta, ...$estadosVenta);
    mysqli_stmt_execute($stmtVentas);
    $rVentas = mysqli_stmt_get_result($stmtVentas);
    if ($rVentas) {
        $ventas_mes = (float)(mysqli_fetch_assoc($rVentas)['total'] ?? 0);
    }
}

$r = mysqli_query($con, "SELECT COUNT(*) AS total FROM tbl_skus WHERE stock <= 3 AND activo = 1");
if ($r) {
    $stock_bajo = (int)(mysqli_fetch_assoc($r)['total'] ?? 0);
}

$stmtTop = mysqli_prepare($con, "
    SELECT items FROM tbl_ordenes
    WHERE estado IN ($placeholdersVenta)
      AND deleted_at IS NULL
      AND MONTH(fecha_creacion) = MONTH(NOW())
      AND YEAR(fecha_creacion) = YEAR(NOW())
");
if ($stmtTop) {
    mysqli_stmt_bind_param($stmtTop, $typesVenta, ...$estadosVenta);
    mysqli_stmt_execute($stmtTop);
    $resOrdenes = mysqli_stmt_get_result($stmtTop);
} else {
    $resOrdenes = false;
}
if ($resOrdenes) {
    while ($row = mysqli_fetch_assoc($resOrdenes)) {
        $items = json_decode((string)($row['items'] ?? '[]'), true);
        if (!is_array($items)) {
            continue;
        }
        foreach ($items as $item) {
            $idProd = (int)($item['id_prod'] ?? 0);
            if ($idProd <= 0) {
                continue;
            }
            if (!isset($topProductos[$idProd])) {
                $topProductos[$idProd] = [
                    'nombre' => $item['nombre'] ?? 'Producto #' . $idProd,
                    'cantidad' => 0,
                    'ingresos' => 0.0,
                ];
            }
            $cant = (int)($item['cantidad'] ?? 0);
            $precio = (float)($item['precio_unitario'] ?? 0);
            $topProductos[$idProd]['cantidad'] += $cant;
            $topProductos[$idProd]['ingresos'] += $cant * $precio;
        }
    }
    uasort($topProductos, static fn($a, $b) => $b['cantidad'] - $a['cantidad']);
    $topProductos = array_slice($topProductos, 0, 5, true);
}

$ultimos_pedidos = mysqli_query($con, "
    SELECT id_orden, numero_orden, CONCAT(nombre, ' ', apellido) AS cliente, total, estado, fecha_creacion
    FROM tbl_ordenes
    WHERE deleted_at IS NULL
    ORDER BY fecha_creacion DESC
    LIMIT 5
");

include __DIR__ . '/header.php';
?>

<div class="admin-section-header">
    <div>
        <h1>Dashboard</h1>
        <p class="subtitle">Hoy es <?= htmlspecialchars(admin_fecha_es(), ENT_QUOTES, 'UTF-8') ?></p>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div>
            <div class="kpi-card-label">Pedidos hoy</div>
            <div class="kpi-card-value"><?= $pedidos_hoy ?></div>
        </div>
        <div class="kpi-card-icon"><i class="bi bi-cart-check"></i></div>
    </div>
    <div class="kpi-card">
        <div>
            <div class="kpi-card-label">Ventas confirmadas (mes)</div>
            <div class="kpi-card-value"><?= format_money($ventas_mes) ?></div>
        </div>
        <div class="kpi-card-icon"><i class="bi bi-currency-dollar"></i></div>
    </div>
    <div class="kpi-card">
        <div>
            <div class="kpi-card-label">SKUs con stock ≤ 3</div>
            <div class="kpi-card-value text-danger"><?= $stock_bajo ?></div>
        </div>
        <div class="kpi-card-icon"><i class="bi bi-exclamation-triangle"></i></div>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="admin-card-header">
        <span>Top productos vendidos</span>
        <span class="text-muted small">Mes en curso</span>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="admin-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Producto</th>
                        <th class="text-end">Unidades</th>
                        <th class="text-end">Ingresos</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($topProductos)): ?>
                    <?php $rank = 1; foreach ($topProductos as $prod): ?>
                        <tr>
                            <td class="text-muted"><?= $rank++ ?></td>
                            <td><?= htmlspecialchars((string)$prod['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-end"><?= (int)$prod['cantidad'] ?></td>
                            <td class="text-end"><?= format_money((float)$prod['ingresos']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Sin ventas en el período</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <span>Últimos pedidos</span>
        <a href="<?= app_path('admin/pedidos/listado.php') ?>" class="btn btn-sm btn-outline-ink">Ver todos</a>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="admin-table mb-0">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($ultimos_pedidos && mysqli_num_rows($ultimos_pedidos) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($ultimos_pedidos)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['numero_orden'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['cliente'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= format_money((float)$row['total']) ?></td>
                            <td><?= estado_pedido_badge((string)$row['estado']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><a href="<?= app_path('admin/pedidos/detalle.php?id=' . (int)$row['id_orden']) ?>" class="btn btn-sm btn-outline-ink">Ver</a></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Sin pedidos</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
