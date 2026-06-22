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

$id_sku = (int)($data['id_sku'] ?? 0);
$stock = (int)($data['stock'] ?? 0);

if ($id_sku <= 0 || $stock < 0) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

$stmt = mysqli_prepare($con, 'UPDATE tbl_skus SET stock = ? WHERE id_sku = ?');
mysqli_stmt_bind_param($stmt, 'ii', $stock, $id_sku);
$ok = mysqli_stmt_execute($stmt);

echo json_encode(['success' => $ok, 'stock' => $stock]);
