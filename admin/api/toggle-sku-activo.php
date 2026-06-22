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
if ($id_sku <= 0) {
    echo json_encode(['success' => false, 'error' => 'id_sku requerido']);
    exit;
}

$stmt = mysqli_prepare($con, 'UPDATE tbl_skus SET activo = IF(activo=1,0,1) WHERE id_sku = ?');
mysqli_stmt_bind_param($stmt, 'i', $id_sku);
mysqli_stmt_execute($stmt);

$r = mysqli_query($con, 'SELECT activo FROM tbl_skus WHERE id_sku = ' . $id_sku . ' LIMIT 1');
$row = $r ? mysqli_fetch_assoc($r) : null;
$activo = (int)($row['activo'] ?? 0);

echo json_encode(['success' => true, 'activo' => $activo]);
