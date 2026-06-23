<?php

declare(strict_types=1);

/**
 * Verifica configuración de Mercado Pago (sin mostrar tokens completos).
 * Ejecutar desde navegador vía ngrok: .../scripts/mp-config-check.php
 * O CLI (SITE_URL será localhost — preferí abrirlo por ngrok).
 */

if (PHP_SAPI === 'cli') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
    $_SERVER['HTTP_HOST'] = 'localhost';
}

require_once __DIR__ . '/../config.php';

header('Content-Type: text/plain; charset=utf-8');

$token = defined('MP_ACCESS_TOKEN') ? (string) MP_ACCESS_TOKEN : '';
$pub = defined('MP_PUBLIC_KEY') ? (string) MP_PUBLIC_KEY : '';
$env = defined('MP_ENVIRONMENT') ? (string) MP_ENVIRONMENT : '(no definido)';

echo "=== Yofi — chequeo Mercado Pago ===\n\n";
echo 'MP_ENVIRONMENT: ' . $env . "\n";
echo 'Access Token: ' . ($token !== '' ? substr($token, 0, 12) . '… (' . strlen($token) . ' chars)' : 'VACÍO') . "\n";
echo 'Public Key: ' . ($pub !== '' ? substr($pub, 0, 12) . '… (' . strlen($pub) . ' chars)' : 'VACÍO') . "\n";
echo 'Token parece TEST: ' . (stripos($token, 'TEST-') === 0 ? 'sí' : 'no / revisar') . "\n\n";

echo 'SITE_URL (esta petición): ' . (defined('SITE_URL') ? SITE_URL : '(no definido)') . "\n";
echo 'Notification URL esperada: ' . (defined('MP_NOTIFICATION_URL') ? MP_NOTIFICATION_URL : '(no definido)') . "\n";
echo 'Return success: ' . (defined('MP_RETURN_SUCCESS') ? MP_RETURN_SUCCESS : '') . "\n\n";

$errors = function_exists('validateMercadoPagoConfig') ? validateMercadoPagoConfig() : [];
if ($errors === []) {
    echo "Configuración mínima: OK\n";
} else {
    echo "Faltantes:\n- " . implode("\n- ", $errors) . "\n";
}

echo "\nLogs webhook: logs/mercadopago-webhook.log\n";
echo "Logs MP: logs/mercadopago.log\n\n";

if (PHP_SAPI === 'cli') {
    echo "Nota: en CLI SITE_URL suele ser localhost. Abrí este script por la URL de ngrok\n";
    echo "para ver la notification_url que MP usará en el checkout.\n";
}
