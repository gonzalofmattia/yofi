<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../src/php/shipping.php';

function json_response(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Método no permitido'], 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '', true);
if (!is_array($data)) {
    json_response(['success' => false, 'error' => 'JSON inválido'], 400);
}

$cp = trim((string)($data['cp'] ?? ''));
$cpDigits = preg_replace('/\D/', '', $cp) ?? '';
if (strlen($cpDigits) < 4) {
    json_response(['success' => false, 'error' => 'Código postal inválido (mínimo 4 dígitos)'], 400);
}

$items = $data['items'] ?? [];
if (!is_array($items) || $items === []) {
    json_response(['success' => false, 'error' => 'Items requeridos'], 400);
}

try {
    $ciudad = trim((string)($data['ciudad'] ?? $data['city'] ?? ''));
    $provincia = trim((string)($data['provincia'] ?? $data['state'] ?? ''));
    if ($ciudad === '') {
        $ciudad = 'Buenos Aires';
    }
    if ($provincia === '') {
        $provincia = 'Buenos Aires';
    }
    $declaredValue = isset($data['declared_value']) ? (float)$data['declared_value'] : 1000.0;

    $pdo = db_ro();
    $package = zipnova_aggregate_package($pdo, $items);
    $service = new ZipnovaService($pdo);
    $opciones = $service->cotizar(
        $cpDigits,
        (float)$package['weight'],
        (float)$package['height'],
        (float)$package['width'],
        (float)$package['depth'],
        $declaredValue,
        $ciudad,
        $provincia
    );

    json_response([
        'success' => true,
        'opciones' => $opciones,
        'cp' => $cpDigits,
    ]);
} catch (Throwable $e) {
    logZipnova('cotizar.php excepción: ' . $e->getMessage(), 'ERROR');
    json_response(['success' => false, 'error' => 'Error al cotizar envío'], 500);
}
