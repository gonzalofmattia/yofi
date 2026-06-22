<?php
declare(strict_types=1);

/**
 * Crea una preferencia de pago en MercadoPago (Checkout Pro - redirect).
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../config/mercadopago.php';
require_once __DIR__ . '/../../../src/php/db.php';

function json_response(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function get_base_url(): string
{
    if (defined('SITE_URL') && SITE_URL !== '') {
        return rtrim((string)SITE_URL, '/');
    }

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
    $basePath = '';

    if (strpos($scriptName, '/public/api/') !== false) {
        $basePath = preg_replace('#/public/api/.*$#', '', $scriptName);
    } elseif (strpos($scriptName, '/checkout/') !== false) {
        $basePath = preg_replace('#/checkout/.*$#', '', $scriptName);
    } else {
        $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        if (strpos($basePath, '/public/api/') !== false) {
            $basePath = preg_replace('#/public/api/.*$#', '', $basePath);
        }
    }

    if ($basePath === '/' || $basePath === '.') {
        $basePath = '';
    }

    return $protocol . '://' . $host . ($basePath ? $basePath : '');
}

function mp_request(string $method, string $url, array $headers = [], ?array $jsonBody = null): array
{
    $ch = curl_init($url);
    if ($ch === false) {
        throw new RuntimeException('No se pudo inicializar cURL');
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $sendHeaders = $headers;
    if ($jsonBody !== null) {
        $body = json_encode($jsonBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $sendHeaders[] = 'Content-Type: application/json';
    }

    if (!empty($sendHeaders)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);
    }

    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false) {
        throw new RuntimeException('Error cURL: ' . $err);
    }

    $decoded = json_decode($raw, true);
    return [
        'status_code' => $code,
        'raw' => $raw,
        'json' => is_array($decoded) ? $decoded : null,
    ];
}

function build_mp_item_title(array $item): string
{
    $nombre = trim((string)($item['nombre'] ?? $item['name'] ?? 'Producto'));
    $color = trim((string)($item['color_nombre'] ?? ''));
    $talle = trim((string)($item['talle_nombre'] ?? ''));
    $variant = trim($color . ' ' . $talle);

    if ($variant === '') {
        return $nombre;
    }

    return $nombre . ' — ' . $variant;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Método no permitido'], 405);
}

$inputRaw = file_get_contents('php://input');
$data = json_decode($inputRaw, true);
if (!is_array($data)) {
    json_response(['success' => false, 'message' => 'Datos inválidos'], 400);
}

$orderId = isset($data['order_id']) ? (int)$data['order_id'] : 0;
$numeroOrden = isset($data['numero_orden']) ? (string)$data['numero_orden'] : '';
$orderData = isset($data['orderData']) && is_array($data['orderData']) ? $data['orderData'] : null;

if ($orderId <= 0 || !$orderData) {
    json_response(['success' => false, 'message' => 'Parámetros incompletos'], 400);
}

$pdo = db_ro();
$stmt = $pdo->prepare('SELECT id_orden, metodo_pago FROM tbl_ordenes WHERE id_orden = ? LIMIT 1');
$stmt->execute([$orderId]);
$orderRow = $stmt->fetch();
if (!$orderRow) {
    json_response(['success' => false, 'message' => 'La orden no existe'], 404);
}
if (($orderRow['metodo_pago'] ?? '') !== 'mercadopago') {
    json_response(['success' => false, 'message' => 'La orden no corresponde a MercadoPago'], 400);
}

$creds = mp_credentials();
if (empty($creds['access_token'])) {
    json_response(['success' => false, 'message' => 'Credenciales de MercadoPago incompletas (MP_ACCESS_TOKEN faltante)'], 500);
}

$baseUrl = get_base_url();
$notificationUrl = $baseUrl . '/webhooks/mp-notification.php';

$backUrls = [
    'success' => $baseUrl . '/?p=pago-exitoso&order=' . urlencode((string)$orderId),
    'failure' => $baseUrl . '/?p=pago-fallido&order=' . urlencode((string)$orderId),
    'pending' => $baseUrl . '/?p=pago-pendiente&order=' . urlencode((string)$orderId),
];

$customer = $orderData['customer'] ?? [];
$shipping = $orderData['shipping'] ?? [];
$items = $orderData['items'] ?? [];
$shippingCost = (float)($orderData['shipping_cost'] ?? 0);

$firstName = (string)($customer['firstName'] ?? '');
$lastName = (string)($customer['lastName'] ?? '');
$email = (string)($customer['email'] ?? '');
$phone = (string)($customer['phone'] ?? '');

$payer = [
    'email' => $email,
    'name' => trim(($firstName !== '' ? $firstName : '') . ' ' . ($lastName !== '' ? $lastName : '')),
];

$digitsPhone = preg_replace('/\D+/', '', $phone);
if (!empty($digitsPhone)) {
    $payer['phone'] = ['number' => $digitsPhone];
}

$mpItems = [];
if (is_array($items)) {
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $mpItems[] = [
            'id' => (string)($item['id_sku'] ?? $item['id'] ?? ''),
            'title' => build_mp_item_title($item),
            'quantity' => (int)($item['cantidad'] ?? $item['quantity'] ?? 1),
            'unit_price' => (float)($item['precio_unitario'] ?? $item['precio'] ?? $item['price'] ?? 0),
            'currency_id' => 'ARS',
        ];
    }
}

if ($shippingCost > 0) {
    $mpItems[] = [
        'id' => 'shipping',
        'title' => 'Envío',
        'quantity' => 1,
        'unit_price' => $shippingCost,
        'currency_id' => 'ARS',
    ];
}

if (empty($mpItems)) {
    json_response(['success' => false, 'message' => 'No se pudieron construir items del carrito'], 400);
}

$externalReference = (string)$orderId;
$statementDescriptor = 'YOFI';

$preferencePayload = [
    'back_urls' => $backUrls,
    'notification_url' => $notificationUrl,
    'external_reference' => $externalReference,
    'statement_descriptor' => $statementDescriptor,
    'items' => $mpItems,
    'payer' => $payer,
];

if (strncasecmp($baseUrl, 'https://', 8) === 0) {
    $preferencePayload['auto_return'] = 'approved';
}

if (function_exists('mp_is_sandbox') && mp_is_sandbox()) {
    $preferencePayload['payment_methods'] = [
        'installments' => 1,
    ];
}

$apiBase = mp_api_base_url();
$url = $apiBase . '/checkout/preferences';
$requestHeaders = [
    'Authorization: Bearer ' . $creds['access_token'],
];

try {
    $mpResp = mp_request('POST', $url, $requestHeaders, $preferencePayload);
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => 'Error al llamar a MercadoPago: ' . $e->getMessage()], 502);
}

if (($mpResp['status_code'] ?? 0) < 200 || ($mpResp['status_code'] ?? 0) >= 300) {
    $mpJson = $mpResp['json'] ?? null;
    $mpMessage = $mpJson['message'] ?? $mpResp['raw'] ?? 'Error desconocido de MercadoPago';
    json_response(['success' => false, 'message' => 'MercadoPago rechazó la preferencia: ' . (string)$mpMessage], 502);
}

$mpJson = $mpResp['json'] ?? [];
$preferenceId = $mpJson['id'] ?? null;
$initPoint = $mpJson['init_point'] ?? null;

if (empty($preferenceId) || empty($initPoint)) {
    json_response(['success' => false, 'message' => 'Respuesta inválida de MercadoPago (id/init_point faltantes)'], 502);
}

$shippingInfo = [
    'order_id' => $orderId,
    'numero_orden' => $numeroOrden,
    'customer' => [
        'email' => $email,
        'firstName' => $firstName,
        'lastName' => $lastName,
    ],
    'shipping' => $shipping,
    'mp_currency' => 'ARS',
];

$pdoRw = db_rw();
$stmtIns = $pdoRw->prepare('
    INSERT INTO tbl_mp_preferences (preference_id, items, shipping_info, status, created_at)
    VALUES (?, ?, ?, ?, NOW())
');
$stmtIns->execute([
    (string)$preferenceId,
    json_encode($mpItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    json_encode($shippingInfo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'pending',
]);

header('X-MP-Environment: ' . (defined('MP_ENVIRONMENT') ? (string)MP_ENVIRONMENT : 'unknown'));

json_response([
    'success' => true,
    'init_point' => $initPoint,
    'preference_id' => (string)$preferenceId,
    'order_id' => $orderId,
    'mp_environment' => defined('MP_ENVIRONMENT') ? (string)MP_ENVIRONMENT : 'unknown',
], 200);
