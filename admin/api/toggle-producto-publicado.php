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
if ($id_prod <= 0) {
    echo json_encode(['success' => false, 'error' => 'id_prod requerido']);
    exit;
}

$stmt = mysqli_prepare($con, 'UPDATE tbl_productos SET publicado = IF(publicado=1,0,1) WHERE id_prod = ?');
mysqli_stmt_bind_param($stmt, 'i', $id_prod);
mysqli_stmt_execute($stmt);

$r = mysqli_query($con, 'SELECT publicado FROM tbl_productos WHERE id_prod = ' . $id_prod . ' LIMIT 1');
$row = $r ? mysqli_fetch_assoc($r) : null;

echo json_encode(['success' => true, 'publicado' => (int)($row['publicado'] ?? 0)]);
