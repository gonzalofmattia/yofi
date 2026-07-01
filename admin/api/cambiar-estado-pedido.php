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

$stmt = mysqli_prepare($con, 'SELECT * FROM tbl_ordenes WHERE id_orden = ? LIMIT 1');
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

if ($estadoNuevo !== $estadoAnterior) {
    try {
        require_once dirname(__DIR__, 2) . '/src/php/order_emails.php';

        $clienteEmail = (string)($row['email'] ?? '');
        if ($clienteEmail !== '') {
            $orderData = [
                'numero_orden' => (string)($row['numero_orden'] ?? ('ORD-' . $id_orden)),
                'nombre' => (string)($row['nombre'] ?? ''),
                'apellido' => (string)($row['apellido'] ?? ''),
                'total' => (float)($row['total'] ?? 0),
            ];

            $titulos = [
                'confirmado' => 'Tu pedido ha sido confirmado',
                'en_preparacion' => 'Tu pedido está siendo preparado',
                'preparando_envio' => 'Tu pedido está siendo preparado',
                'enviado' => '¡Tu pedido ha sido enviado!',
                'entregado' => 'Tu pedido ha sido entregado',
                'cancelado' => 'Tu pedido ha sido cancelado',
            ];
            $emailSubject = ($titulos[$estadoNuevo] ?? 'Actualización de tu pedido') . ' - Pedido #' . $orderData['numero_orden'] . ' - Yofi';
            $emailBody = generateEstadoChangeEmail($orderData, $estadoNuevo, $estadoAnterior);

            sendEmail($clienteEmail, $emailSubject, $emailBody, true);
        }
    } catch (Throwable $e) {
        error_log('Error al enviar email de cambio de estado (admin): ' . $e->getMessage());
    }
}

echo json_encode(['success' => true, 'estado' => $estadoNuevo]);
