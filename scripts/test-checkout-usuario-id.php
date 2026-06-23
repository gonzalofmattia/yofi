<?php

declare(strict_types=1);

/**
 * Verifica INSERT de tbl_ordenes con/sin usuario_id.
 * Ejecutar: php scripts/test-checkout-usuario-id.php
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/php/db.php';

function ok(bool $c, string $m): void
{
    echo ($c ? '[OK] ' : '[FAIL] ') . $m . PHP_EOL;
    if (!$c) {
        exit(1);
    }
}

$pdo = db_rw();

$emailGuest = 'checkout-guest-' . uniqid('', true) . '@yofi.local';
$numGuest = 'TEST-GUEST-' . uniqid();
$pdo->prepare('
    INSERT INTO tbl_ordenes (numero_orden, estado, nombre, apellido, email, usuario_id, telefono,
        direccion, ciudad, provincia, codigo_postal, metodo_pago, subtotal, envio, total, items)
    VALUES (?, ?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
')->execute([
    $numGuest,
    'pendiente',
    'Invitado',
    'Test',
    $emailGuest,
    '1100000000',
    'Calle 1',
    'CABA',
    'CABA',
    '1406',
    'transferencia',
    1000,
    500,
    1500,
    '[]',
]);
$guestId = (int) $pdo->lastInsertId();
$row = $pdo->query('SELECT usuario_id FROM tbl_ordenes WHERE id_orden = ' . $guestId)->fetch(PDO::FETCH_ASSOC);
ok($row && $row['usuario_id'] === null, 'Pedido invitado: usuario_id IS NULL');

$emailUser = 'checkout-user-' . uniqid('', true) . '@yofi.local';
$hash = password_hash('testpass123', PASSWORD_DEFAULT);
$pdo->prepare('INSERT INTO tbl_usuarios (email, password_hash, nombre, apellido, is_guest, activo, email_verificado) VALUES (?, ?, ?, ?, 0, 1, 1)')
    ->execute([$emailUser, $hash, 'Logueado', 'Test']);
$userId = (int) $pdo->lastInsertId();

$numUser = 'TEST-USER-' . uniqid();
$pdo->prepare('
    INSERT INTO tbl_ordenes (numero_orden, estado, nombre, apellido, email, usuario_id, telefono,
        direccion, ciudad, provincia, codigo_postal, metodo_pago, subtotal, envio, total, items)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
')->execute([
    $numUser,
    'pendiente',
    'Logueado',
    'Test',
    $emailUser,
    $userId,
    '1100000000',
    'Calle 2',
    'CABA',
    'CABA',
    '1406',
    'transferencia',
    2000,
    0,
    2000,
    '[]',
]);
$loggedOrderId = (int) $pdo->lastInsertId();
$row2 = $pdo->query('SELECT usuario_id FROM tbl_ordenes WHERE id_orden = ' . $loggedOrderId)->fetch(PDO::FETCH_ASSOC);
ok($row2 && (int) $row2['usuario_id'] === $userId, 'Pedido logueado: usuario_id = ' . $userId);

$pdo->prepare('DELETE FROM tbl_ordenes WHERE id_orden IN (?, ?)')->execute([$guestId, $loggedOrderId]);
$pdo->prepare('DELETE FROM tbl_usuarios WHERE id_usuario = ?')->execute([$userId]);

echo "Prueba checkout usuario_id completada.\n";
