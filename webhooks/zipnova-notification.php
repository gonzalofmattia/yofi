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
        'ready_to_ship' => 'preparando_envio',
        'in_transit' => 'enviado',
        'delivered' => 'entregado',
        'failed' => 'problema_envio',
    ];

    return $map[strtolower(trim($zipnovaStatus))] ?? null;
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
$tracking = (string)($payload['tracking_number'] ?? $payload['tracking'] ?? '');
$shipmentId = (string)($payload['shipment_id'] ?? $payload['id'] ?? '');
$reference = (string)($payload['reference'] ?? $payload['external_reference'] ?? '');

if ($estadoNuevo === null) {
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

    $setParts = ['estado = ?', 'fecha_actualizacion = NOW()'];
    $params = [$estadoNuevo];

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
        $estadoNuevo,
        'Zipnova Webhook',
        'Actualización automática: ' . $zipnovaStatus,
        $tracking !== '' ? $tracking : null,
    ]);

    zipnova_webhook_log("OK order_id={$orderId} {$estadoAnterior}->{$estadoNuevo} tracking={$tracking}");
} catch (Throwable $e) {
    zipnova_webhook_log('Excepción: ' . $e->getMessage(), 'ERROR');
}

echo 'OK';
exit;
