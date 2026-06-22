<?php
declare(strict_types=1);

/**
 * Webhook / IPN de Mercado Pago (Checkout Pro).
 *
 * Mercado Pago puede notificar con:
 *  - POST + JSON (webhooks)
 *  - GET ?topic=payment&id=... (IPN clásico)
 *  - GET ?topic=merchant_order&id=... (varias órdenes de preferencia; resolvemos pagos vía API)
 *
 * La lógica de negocio vive en src/php/mp_mercadopago_sync.php (también usada en pago-exitoso).
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../config/mercadopago.php';
require_once __DIR__ . '/../src/php/mp_mercadopago_sync.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = DB_DSN ?? 'mysql:host=localhost;dbname=lacasa;charset=utf8mb4';
    $user = DB_USER_RO ?? 'root';
    $pass = DB_PASS_RO ?? '';

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function log_mp(string $message, string $type = 'INFO'): void
{
    $logFile = __DIR__ . '/../logs/mercadopago-webhook.log';
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $timestamp = date('Y-m-d H:i:s');
    @file_put_contents($logFile, "[$timestamp] [$type] $message" . PHP_EOL, FILE_APPEND | LOCK_EX);
}

http_response_code(200);
header('Content-Type: text/plain; charset=utf-8');

$paymentIds = [];
$rawBody = file_get_contents('php://input');
if ($rawBody === false) {
    $rawBody = '';
}

if (trim($rawBody) !== '') {
    $payload = json_decode($rawBody, true);
    if (is_array($payload)) {
        log_mp('Recibido payload: ' . substr($rawBody, 0, 2000));
        $data = $payload['data'] ?? [];
        $pid = $data['id'] ?? $payload['id'] ?? null;
        if ($pid !== null && (string)$pid !== '') {
            $paymentIds[] = (string)$pid;
        }

        // Formato alternativo: {"resource":"https://api.mercadolibre.com/merchant_orders/123","topic":"merchant_order"}
        $topicBody = (string)($payload['topic'] ?? '');
        $resource = $payload['resource'] ?? null;
        if ($topicBody === 'merchant_order' && is_string($resource) && $resource !== '') {
            if (preg_match('#merchant[_/]orders?/(\d+)#i', $resource, $m)) {
                log_mp('Webhook JSON merchant_order mo_id=' . $m[1] . ' desde resource');
                $paymentIds = array_merge($paymentIds, mp_mercadopago_payment_ids_from_merchant_order($m[1]));
            }
        }
    } else {
        log_mp('Webhook body JSON inválido: ' . substr($rawBody, 0, 500), 'ERROR');
    }
}

$topic = isset($_GET['topic']) ? (string)$_GET['topic'] : '';
$topicId = isset($_GET['id']) ? (string)$_GET['id'] : '';

if ($topic === 'payment' && $topicId !== '') {
    log_mp('IPN GET topic=payment id=' . $topicId);
    $paymentIds[] = $topicId;
}

if ($topic === 'merchant_order' && $topicId !== '') {
    log_mp('IPN GET topic=merchant_order id=' . $topicId);
    $paymentIds = array_merge($paymentIds, mp_mercadopago_payment_ids_from_merchant_order($topicId));
}

$paymentIds = array_values(array_unique(array_filter($paymentIds, static function ($v) {
    return $v !== null && (string)$v !== '';
})));

if ($paymentIds === []) {
    log_mp('Sin payment_id (ni JSON ni GET útil). GET=' . json_encode($_GET, JSON_UNESCAPED_UNICODE) . ' body_len=' . (string)strlen($rawBody), 'WARN');
    echo 'OK';
    exit;
}

try {
    $pdo = db();
    foreach ($paymentIds as $pid) {
        mp_mercadopago_sync_payment($pdo, (string)$pid);
    }
} catch (Throwable $e) {
    log_mp('Excepción en sync: ' . $e->getMessage(), 'ERROR');
}

echo 'OK';
exit;
