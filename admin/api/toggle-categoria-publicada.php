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

$id_cate = (int)($data['id_cate'] ?? 0);
if ($id_cate <= 0) {
    echo json_encode(['success' => false, 'error' => 'id_cate requerido']);
    exit;
}

$stmt = mysqli_prepare($con, 'UPDATE tbl_categorias SET publicado = IF(publicado=1,0,1) WHERE id_cate = ?');
mysqli_stmt_bind_param($stmt, 'i', $id_cate);
mysqli_stmt_execute($stmt);

$r = mysqli_query($con, 'SELECT publicado FROM tbl_categorias WHERE id_cate = ' . $id_cate . ' LIMIT 1');
$row = $r ? mysqli_fetch_assoc($r) : null;

echo json_encode(['success' => true, 'publicado' => (int)($row['publicado'] ?? 0)]);
