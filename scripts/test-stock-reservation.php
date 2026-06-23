<?php

declare(strict_types=1);

/**
 * Prueba flujo de reserva de stock (sin Mercado Pago real).
 * Ejecutar: php scripts/test-stock-reservation.php
 */

if (PHP_SAPI === 'cli') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
    $_SERVER['HTTP_HOST'] = 'localhost';
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/php/db.php';
require_once __DIR__ . '/../src/php/stock_reservation.php';

$pdo = db_rw();

$skuRow = $pdo->query('SELECT id_sku, stock, stock_reservado FROM tbl_skus WHERE activo = 1 AND stock > 0 LIMIT 1')->fetch(PDO::FETCH_ASSOC);
if (!$skuRow) {
    echo "No hay SKUs con stock para probar.\n";
    exit(1);
}

$idSku = (int) $skuRow['id_sku'];
$qty = 1;

$numero = 'TEST-RES-' . uniqid('');
$pdo->beginTransaction();
try {
    $pdo->prepare("
        INSERT INTO tbl_ordenes (numero_orden, estado, nombre, apellido, email, telefono,
            direccion, ciudad, provincia, codigo_postal, metodo_pago, subtotal, envio, total, items)
        VALUES (?, 'pendiente', 'Test', 'Reserva', 'test-res@yofi.local', '111', 'Calle 1', 'CABA', 'CABA', '1000', 'mercadopago', 100, 0, 100, ?)
    ")->execute([
        $numero,
        json_encode([['id_sku' => $idSku, 'cantidad' => $qty, 'precio_unitario' => 100, 'nombre' => 'Test']], JSON_UNESCAPED_UNICODE),
    ]);
    $orderId = (int) $pdo->lastInsertId();
    stock_reserve_for_order($pdo, $orderId, [$idSku => $qty]);
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    echo 'FAIL reserva: ' . $e->getMessage() . "\n";
    exit(1);
}

$rowAfterReserve = $pdo->query('SELECT stock, stock_reservado FROM tbl_skus WHERE id_sku = ' . $idSku)->fetch(PDO::FETCH_ASSOC);
echo "Reserva OK order={$orderId} stock_reservado=" . ($rowAfterReserve['stock_reservado'] ?? '?') . "\n";

$orderRow = $pdo->query('SELECT * FROM tbl_ordenes WHERE id_orden = ' . $orderId)->fetch(PDO::FETCH_ASSOC);
stock_confirm_order_reservation($pdo, $orderRow ?: []);

$rowAfterConfirm = $pdo->query('SELECT stock, stock_reservado FROM tbl_skus WHERE id_sku = ' . $idSku)->fetch(PDO::FETCH_ASSOC);
echo "Confirmación OK stock=" . ($rowAfterConfirm['stock'] ?? '?') . " reservado=" . ($rowAfterConfirm['stock_reservado'] ?? '?') . "\n";

// Segundo pedido para probar liberación
$numero2 = 'TEST-REL-' . uniqid('');
$pdo->prepare("
    INSERT INTO tbl_ordenes (numero_orden, estado, nombre, apellido, email, telefono,
        direccion, ciudad, provincia, codigo_postal, metodo_pago, subtotal, envio, total, items, reserva_activa, reserva_expira_at)
    VALUES (?, 'pendiente', 'Test', 'Libera', 'test-rel@yofi.local', '111', 'Calle 1', 'CABA', 'CABA', '1000', 'mercadopago', 100, 0, 100, ?, 0, NULL)
")->execute([
    $numero2,
    json_encode([['id_sku' => $idSku, 'cantidad' => $qty, 'precio_unitario' => 100, 'nombre' => 'Test']], JSON_UNESCAPED_UNICODE),
]);
$orderId2 = (int) $pdo->lastInsertId();
$pdo->beginTransaction();
stock_reserve_for_order($pdo, $orderId2, [$idSku => $qty]);
$pdo->commit();

$orderRow2 = $pdo->query('SELECT * FROM tbl_ordenes WHERE id_orden = ' . $orderId2)->fetch(PDO::FETCH_ASSOC);
stock_release_order_reservation($pdo, $orderRow2 ?: [], 'Test liberación');

$pdo->prepare('DELETE FROM tbl_stock_log WHERE orden_id IN (?, ?)')->execute([$orderId, $orderId2]);
$pdo->prepare('DELETE FROM tbl_ordenes WHERE id_orden IN (?, ?)')->execute([$orderId, $orderId2]);

echo "Liberación OK\n";
echo "Prueba stock reserva completada.\n";
