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

$id_edad_banner = (int)($data['id_edad_banner'] ?? 0);
if ($id_edad_banner <= 0) {
    echo json_encode(['success' => false, 'error' => 'id_edad_banner requerido']);
    exit;
}

$stmt = mysqli_prepare($con, 'UPDATE tbl_home_edad_banners SET activo = IF(activo=1,0,1) WHERE id_edad_banner = ?');
mysqli_stmt_bind_param($stmt, 'i', $id_edad_banner);
mysqli_stmt_execute($stmt);

$r = mysqli_query($con, 'SELECT activo FROM tbl_home_edad_banners WHERE id_edad_banner = ' . $id_edad_banner . ' LIMIT 1');
$row = $r ? mysqli_fetch_assoc($r) : null;

echo json_encode(['success' => true, 'activo' => (int)($row['activo'] ?? 0)]);
