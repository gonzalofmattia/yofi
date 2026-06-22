<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__) . '/include/includes.php';
require_once dirname(__DIR__) . '/check_session.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '', true);
if (!is_array($data)) {
    echo json_encode(['success' => false, 'error' => 'JSON inválido']);
    exit;
}

$orderId = (int)($data['order_id'] ?? 0);
if ($orderId <= 0) {
    echo json_encode(['success' => false, 'error' => 'order_id requerido']);
    exit;
}

$url = rtrim(SITE_URL, '/') . '/public/api/zipnova/crear-envio.php';
$payload = json_encode(['order_id' => $orderId]);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-Internal-Key: ' . (defined('INTERNAL_API_KEY') ? INTERNAL_API_KEY : ''),
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
]);
$response = curl_exec($ch);
$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode((string)$response, true);
if ($code >= 200 && $code < 300 && is_array($result) && !empty($result['success'])) {
    echo json_encode($result);
    exit;
}

echo json_encode([
    'success' => false,
    'error' => is_array($result) ? ($result['error'] ?? 'Error Zipnova') : 'Error al contactar Zipnova',
    'http' => $code,
]);
