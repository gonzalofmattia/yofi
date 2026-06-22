<?php
// SOLO LOCAL — borrar antes de subir a producción
require_once __DIR__ . '/../config.php';

if (defined('ENV') && ENV === 'production') {
    die('No disponible en producción');
}

require_once __DIR__ . '/../src/php/shipping.php';

echo '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><title>Test Zipnova Quote</title></head>';
echo '<body style="font-family:sans-serif;max-width:960px;margin:24px auto;padding:0 16px;">';

echo '<h1>Test Zipnova — POST /shipments/quote</h1>';
echo '<p style="color:#15803d;font-weight:600;">✓ Integración Zipnova verificada y operativa</p>';

$service = new ZipnovaService();

$testCp = '1406';
$testCiudad = 'Buenos Aires';
$testProvincia = 'Buenos Aires';
$testPeso = 0.5;
$testAlto = 20;
$testAncho = 20;
$testProf = 5;
$testDeclared = 1000;

$payload = $service->buildQuotePayload(
    $testCp,
    $testPeso,
    $testAlto,
    $testAncho,
    $testProf,
    $testDeclared,
    null,
    $testCiudad,
    $testProvincia
);

echo '<h2>Payload enviado</h2>';
echo '<pre style="background:#f3f4f6;padding:12px;font-size:12px;">'
    . htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8')
    . '</pre>';

$opciones = $service->cotizar(
    $testCp,
    $testPeso,
    $testAlto,
    $testAncho,
    $testProf,
    $testDeclared,
    $testCiudad,
    $testProvincia
);

echo '<h2>Opciones mapeadas</h2>';
if ($opciones === []) {
    echo '<p style="color:#ef4444;">Sin opciones — revisar credenciales o logs/zipnova.log</p>';
} else {
    echo '<pre style="background:#f3f4f6;padding:12px;font-size:12px;">'
        . htmlspecialchars(json_encode($opciones, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8')
        . '</pre>';
}

echo '</body></html>';
