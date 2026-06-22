<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/include/includes.php';
require_once dirname(__DIR__) . '/check_session.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '', true);
if (!is_array($data)) {
    echo json_encode(['success' => false, 'error' => 'JSON inválido']);
    exit;
}

$id_prod = (int)($data['id_prod'] ?? 0);
$id_color = (int)($data['id_color'] ?? 0);
$id_talle = (int)($data['id_talle'] ?? 0);
$stock = max(0, (int)($data['stock'] ?? 0));

if ($id_prod <= 0 || $id_color <= 0 || $id_talle <= 0) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$codigo = 'YOFI-' . $id_prod . '-' . $id_color . '-' . $id_talle;
$stmt = mysqli_prepare($con, '
    INSERT INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock, activo)
    VALUES (?, ?, ?, ?, ?, 1)
    ON DUPLICATE KEY UPDATE stock = VALUES(stock)
');
mysqli_stmt_bind_param($stmt, 'iiisi', $id_prod, $id_color, $id_talle, $codigo, $stock);
$ok = mysqli_stmt_execute($stmt);

echo json_encode(['success' => $ok, 'codigo_sku' => $codigo]);
