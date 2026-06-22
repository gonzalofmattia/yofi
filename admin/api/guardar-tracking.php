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

$id_orden = (int)($data['id_orden'] ?? 0);
$tracking = trim((string)($data['tracking_number'] ?? ''));

if ($id_orden <= 0) {
    echo json_encode(['success' => false, 'error' => 'id_orden requerido']);
    exit;
}

$stmt = mysqli_prepare($con, 'UPDATE tbl_ordenes SET tracking_number = ?, fecha_actualizacion = NOW() WHERE id_orden = ?');
mysqli_stmt_bind_param($stmt, 'si', $tracking, $id_orden);
$ok = mysqli_stmt_execute($stmt);

echo json_encode(['success' => $ok, 'tracking_number' => $tracking]);
