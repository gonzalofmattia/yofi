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

$action = trim((string)($data['action'] ?? ''));
$ids = $data['ids'] ?? [];

if (!is_array($ids) || empty($ids)) {
    echo json_encode(['success' => false, 'error' => 'Sin productos seleccionados']);
    exit;
}

$idList = array_values(array_filter(array_map('intval', $ids), static fn($id) => $id > 0));
if (empty($idList)) {
    echo json_encode(['success' => false, 'error' => 'IDs inválidos']);
    exit;
}

$placeholders = implode(',', array_fill(0, count($idList), '?'));
$types = str_repeat('i', count($idList));
$affected = 0;

if ($action === 'publicar') {
    $stmt = mysqli_prepare($con, "UPDATE tbl_productos SET publicado = 1, fecha_actualizacion = NOW() WHERE id_prod IN ($placeholders) AND borrado = 0");
    mysqli_stmt_bind_param($stmt, $types, ...$idList);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
} elseif ($action === 'despublicar') {
    $stmt = mysqli_prepare($con, "UPDATE tbl_productos SET publicado = 0, fecha_actualizacion = NOW() WHERE id_prod IN ($placeholders) AND borrado = 0");
    mysqli_stmt_bind_param($stmt, $types, ...$idList);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
} elseif ($action === 'eliminar') {
    $stmt = mysqli_prepare($con, "UPDATE tbl_productos SET borrado = 1, publicado = 0, fecha_actualizacion = NOW() WHERE id_prod IN ($placeholders)");
    mysqli_stmt_bind_param($stmt, $types, ...$idList);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
} else {
    echo json_encode(['success' => false, 'error' => 'Acción no válida']);
    exit;
}

echo json_encode(['success' => true, 'affected' => $affected]);
