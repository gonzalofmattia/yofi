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

$stmt = mysqli_prepare($con, 'SELECT path, id_color, es_principal FROM tbl_prod_imagenes WHERE id_imagen = ? AND id_prod = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'ii', $id_imagen, $id_prod);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$row) {
    echo json_encode(['success' => false, 'error' => 'Imagen no encontrada']);
    exit;
}

$path = (string)$row['path'];
$id_color = (int)$row['id_color'];
$wasPrincipal = (int)$row['es_principal'] === 1;

$del = mysqli_prepare($con, 'DELETE FROM tbl_prod_imagenes WHERE id_imagen = ? AND id_prod = ?');
mysqli_stmt_bind_param($del, 'ii', $id_imagen, $id_prod);
mysqli_stmt_execute($del);

$filePath = dirname(__DIR__, 2) . '/imgprod/' . basename($path);
if (is_file($filePath)) {
    @unlink($filePath);
}

if ($wasPrincipal && $id_color > 0) {
    $next = mysqli_prepare($con, 'SELECT id_imagen FROM tbl_prod_imagenes WHERE id_prod = ? AND id_color = ? ORDER BY orden ASC, id_imagen ASC LIMIT 1');
    mysqli_stmt_bind_param($next, 'ii', $id_prod, $id_color);
    mysqli_stmt_execute($next);
    $nextRow = mysqli_fetch_assoc(mysqli_stmt_get_result($next));
    if ($nextRow) {
        $nextId = (int)$nextRow['id_imagen'];
        $up = mysqli_prepare($con, 'UPDATE tbl_prod_imagenes SET es_principal = 1 WHERE id_imagen = ?');
        mysqli_stmt_bind_param($up, 'i', $nextId);
        mysqli_stmt_execute($up);
    }
}

echo json_encode(['success' => true]);
