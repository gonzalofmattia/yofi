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
$estadoNuevo = trim((string)($data['estado'] ?? ''));

if ($id_orden <= 0 || $estadoNuevo === '') {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

$stmt = mysqli_prepare($con, 'SELECT estado FROM tbl_ordenes WHERE id_orden = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id_orden);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$row) {
    echo json_encode(['success' => false, 'error' => 'Orden no encontrada']);
    exit;
}

$estadoAnterior = (string)$row['estado'];
$adminUser = (string)($_SESSION['idUsuarioAdminSUser'] ?? 'admin');

$stmtUp = mysqli_prepare($con, 'UPDATE tbl_ordenes SET estado = ?, fecha_actualizacion = NOW() WHERE id_orden = ?');
mysqli_stmt_bind_param($stmtUp, 'si', $estadoNuevo, $id_orden);
mysqli_stmt_execute($stmtUp);

$stmtHist = mysqli_prepare($con, '
    INSERT INTO tbl_ordenes_historial (id_orden, estado_anterior, estado_nuevo, usuario_admin, notas)
    VALUES (?, ?, ?, ?, ?)
');
$notas = 'Cambio manual desde admin';
mysqli_stmt_bind_param($stmtHist, 'issss', $id_orden, $estadoAnterior, $estadoNuevo, $adminUser, $notas);
mysqli_stmt_execute($stmtHist);

echo json_encode(['success' => true, 'estado' => $estadoNuevo]);
