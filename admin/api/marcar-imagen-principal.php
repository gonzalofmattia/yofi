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

$id_imagen = (int)($data['id_imagen'] ?? 0);
$id_prod = (int)($data['id_prod'] ?? 0);

if ($id_imagen <= 0 || $id_prod <= 0) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$stmt = mysqli_prepare($con, 'SELECT id_color FROM tbl_prod_imagenes WHERE id_imagen = ? AND id_prod = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'ii', $id_imagen, $id_prod);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$row || empty($row['id_color'])) {
    echo json_encode(['success' => false, 'error' => 'Imagen no encontrada']);
    exit;
}

$id_color = (int)$row['id_color'];

$reset = mysqli_prepare($con, 'UPDATE tbl_prod_imagenes SET es_principal = 0 WHERE id_prod = ? AND id_color = ?');
mysqli_stmt_bind_param($reset, 'ii', $id_prod, $id_color);
mysqli_stmt_execute($reset);

$up = mysqli_prepare($con, 'UPDATE tbl_prod_imagenes SET es_principal = 1 WHERE id_imagen = ? AND id_prod = ?');
mysqli_stmt_bind_param($up, 'ii', $id_imagen, $id_prod);
$ok = mysqli_stmt_execute($up);

echo json_encode(['success' => $ok]);
