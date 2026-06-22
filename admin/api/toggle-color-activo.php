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

$id_color = (int)($data['id_color'] ?? 0);
if ($id_color <= 0) {
    echo json_encode(['success' => false, 'error' => 'id_color requerido']);
    exit;
}

$stmt = mysqli_prepare($con, 'UPDATE tbl_colores SET activo = IF(activo=1,0,1) WHERE id_color = ?');
mysqli_stmt_bind_param($stmt, 'i', $id_color);
mysqli_stmt_execute($stmt);

$r = mysqli_query($con, 'SELECT activo FROM tbl_colores WHERE id_color = ' . $id_color . ' LIMIT 1');
$row = $r ? mysqli_fetch_assoc($r) : null;

echo json_encode(['success' => true, 'activo' => (int)($row['activo'] ?? 0)]);
