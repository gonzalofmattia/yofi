<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../config/zipnova.php';

function zipnova_webhook_log(string $message, string $type = 'INFO'): void
{
    $logFile = __DIR__ . '/../logs/zipnova-webhook.log';
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $timestamp = date('Y-m-d H:i:s');
    @file_put_contents($logFile, "[$timestamp] [$type] $message" . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function zipnova_verify_webhook(): bool
{
    $expected = defined('ZIPNOVA_WEBHOOK_TOKEN') ? (string)ZIPNOVA_WEBHOOK_TOKEN : '';
    if ($expected === '') {
        return true;
    }

    $headerToken = $_SERVER['HTTP_X_ZIPNOVA_TOKEN'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (is_string($headerToken) && str_starts_with($headerToken, 'Bearer ')) {
        $headerToken = substr($headerToken, 7);
    }

    $queryToken = isset($_GET['token']) ? (string)$_GET['token'] : '';

    if ($headerToken !== '' && hash_equals($expected, $headerToken)) {
        return true;
    }
    if ($queryToken !== '' && hash_equals($expected, $queryToken)) {
        return true;
    }

    return false;
}

function zipnova_map_status(string $zipnovaStatus): ?string
{
    $map = [
        'in_transit' => 'enviado',
        'delivered' => 'entregado',
    ];

    return $map[strtolower(trim($zipnovaStatus))] ?? null;
}

/**
 * Estados de Zipnova que reconocemos pero que NO disparan una transición de
 * estado propia en tbl_ordenes (a diferencia de zipnova_map_status()).
 *
 * - 'ready_to_ship': el pedido ya está 'confirmado' en ese punto — no existe un
 *   estado intermedio propio de "preparando envío" en el modelo de 5 estados.
 * - 'failed' (envío fallido): no se cancela ni se revierte automáticamente el
 *   pedido; queda tal cual está y se registra en historial/log para que el
 *   admin decida a mano (reintentar envío o cancelar).
 *
 * Igual guardamos el tracking_number si vino, y dejamos rastro en el historial.
 */
function zipnova_known_status_without_transition(string $zipnovaStatus): bool
{
    return in_array(strtolower(trim($zipnovaStatus)), ['ready_to_ship', 'failed'], true);
}

http_response_code(200);
header('Content-Type: text/plain; charset=utf-8');

if (!zipnova_verify_webhook()) {
    zipnova_webhook_log('Webhook rechazado: token inválido', 'WARN');
    echo 'OK';
    exit;
}

$rawBody = file_get_contents('php://input');
if ($rawBody === false) {
    $rawBody = '';
}

zipnova_webhook_log('Payload recibido: ' . substr($rawBody, 0, 2000));

$payload = json_decode($rawBody, true);
if (!is_array($payload)) {
    zipnova_webhook_log('JSON inválido', 'ERROR');
    echo 'OK';
    exit;
}

$zipnovaStatus = (string)($payload['status'] ?? $payload['shipment_status'] ?? '');
$estadoNuevo = zipnova_map_status($zipnovaStatus);
$sinTransicionConocida = zipnova_known_status_without_transition($zipnovaStatus);
$tracking = (string)($payload['tracking_number'] ?? $payload['tracking'] ?? '');
$shipmentId = (string)($payload['shipment_id'] ?? $payload['id'] ?? '');
$reference = (string)($payload['reference'] ?? $payload['external_reference'] ?? '');

if ($estadoNuevo === null && !$sinTransicionConocida) {
    zipnova_webhook_log('Estado Zipnova no mapeado: ' . $zipnovaStatus, 'WARN');
    echo 'OK';
    exit;
}

try {
    $pdo = db_rw();

    $orden = null;
    if ($shipmentId !== '') {
        $stmt = $pdo->prepare('SELECT * FROM tbl_ordenes WHERE zipnova_shipment_id = ? LIMIT 1');
        $stmt->execute([$shipmentId]);
        $orden = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if (!$orden && $reference !== '') {
        $stmt = $pdo->prepare('SELECT * FROM tbl_ordenes WHERE numero_orden = ? LIMIT 1');
        $stmt->execute([$reference]);
        $orden = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$orden) {
        zipnova_webhook_log('Orden no encontrada shipment=' . $shipmentId . ' ref=' . $reference, 'WARN');
        echo 'OK';
        exit;
    }

    $orderId = (int)$orden['id_orden'];
    $estadoAnterior = (string)($orden['estado'] ?? 'pendiente');

    // $estadoNuevo === null acá solo puede pasar para 'ready_to_ship'/'failed'
    // ($sinTransicionConocida === true): no tocamos 'estado', pero sí guardamos
    // el tracking_number si vino y dejamos rastro en el historial.
    $setParts = ['fecha_actualizacion = NOW()'];
    $params = [];
    if ($estadoNuevo !== null) {
        $setParts[] = 'estado = ?';
        $params[] = $estadoNuevo;
    }

    if ($tracking !== '') {
        $setParts[] = 'tracking_number = ?';
        $params[] = $tracking;
    }

    $params[] = $orderId;
    $pdo->prepare('UPDATE tbl_ordenes SET ' . implode(', ', $setParts) . ' WHERE id_orden = ?')
        ->execute($params);

    $pdo->prepare('
        INSERT INTO tbl_ordenes_historial
            (id_orden, estado_anterior, estado_nuevo, usuario_admin, notas, tracking_number)
        VALUES
            (?, ?, ?, ?, ?, ?)
    ')->execute([
        $orderId,
        $estadoAnterior,
        $estadoNuevo ?? $estadoAnterior,
        'Zipnova Webhook',
        'Actualización automática: ' . $zipnovaStatus,
        $tracking !== '' ? $tracking : null,
    ]);

    // Zipnova es quien realmente marca 'enviado'/'entregado' en producción (no un admin
    // a mano), así que el mail de cambio de estado se dispara también desde acá — mismo
    // patrón que admin/api/cambiar-estado-pedido.php y src/php/mp_sync.php.
    if ($estadoNuevo !== null && $estadoNuevo !== $estadoAnterior) {
        try {
            require_once __DIR__ . '/../src/php/order_emails.php';

            $clienteEmail = (string)($orden['email'] ?? '');
            if ($clienteEmail !== '') {
                $itemsDecoded = json_decode((string)($orden['items'] ?? '[]'), true);
                $orderData = [
                    'numero_orden' => (string)($orden['numero_orden'] ?? ('ORD-' . $orderId)),
                    'nombre' => (string)($orden['nombre'] ?? ''),
                    'apellido' => (string)($orden['apellido'] ?? ''),
                    'total' => (float)($orden['total'] ?? 0),
                    'id_orden' => $orderId,
                    'subtotal' => (float)($orden['subtotal'] ?? 0),
                    'envio' => (float)($orden['envio'] ?? 0),
                    'items' => is_array($itemsDecoded) ? $itemsDecoded : [],
                    'direccion' => (string)($orden['direccion'] ?? ''),
                    'ciudad' => (string)($orden['ciudad'] ?? ''),
                    'provincia' => (string)($orden['provincia'] ?? ''),
                    'codigo_postal' => (string)($orden['codigo_postal'] ?? ''),
                    'tracking_number' => $tracking !== '' ? $tracking : (string)($orden['tracking_number'] ?? ''),
                    'shipping_carrier' => (string)($orden['shipping_carrier'] ?? ''),
                    'shipping_eta' => (string)($orden['shipping_eta'] ?? ''),
                ];

                $titulos = [
                    'enviado' => '¡Tu pedido ha sido enviado!',
                    'entregado' => 'Tu pedido ha sido entregado',
                ];
                $emailSubject = ($titulos[$estadoNuevo] ?? 'Actualización de tu pedido') . ' - Pedido #' . $orderData['numero_orden'] . ' - Yofi';
                $emailBody = generateEstadoChangeEmail($orderData, $estadoNuevo, $estadoAnterior);

                sendEmail($clienteEmail, $emailSubject, $emailBody, true);
            }
        } catch (Throwable $e) {
            zipnova_webhook_log('Error al enviar email: ' . $e->getMessage(), 'ERROR');
        }
    }

    zipnova_webhook_log("OK order_id={$orderId} {$estadoAnterior}->{$estadoNuevo} tracking={$tracking}");
} catch (Throwable $e) {
    zipnova_webhook_log('Excepción: ' . $e->getMessage(), 'ERROR');
}

echo 'OK';
exit;
