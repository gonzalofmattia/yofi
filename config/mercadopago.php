<?php
/**
 * Configuración de MercadoPago — Yofi
 *
 * Sandbox (pruebas):
 *  - En el panel de Mercado Pago usá "Credenciales de prueba" (Access Token / Public Key).
 *  - La API sigue siendo https://api.mercadopago.com — el modo lo define el token, no la URL.
 *
 * Dónde poner credenciales (en este orden):
 *  1) Variables de entorno del servidor: MP_ACCESS_TOKEN, MP_PUBLIC_KEY, MP_ENVIRONMENT, etc.
 *  2) Archivo local NO versionado: mercadopago.local.php
 */

if (is_file(__DIR__ . '/mercadopago.local.php')) {
    require_once __DIR__ . '/mercadopago.local.php';
}

if (!defined('MP_ENVIRONMENT')) {
    $e = getenv('MP_ENVIRONMENT');
    $env = ($e !== false && trim((string)$e) !== '') ? strtolower(trim((string)$e)) : 'production';
    if ($env === 'test') {
        $env = 'sandbox';
    }
    if (!in_array($env, ['sandbox', 'production'], true)) {
        $env = 'production';
    }
    define('MP_ENVIRONMENT', $env);
}

if (!defined('MP_ACCESS_TOKEN')) {
    define('MP_ACCESS_TOKEN', '');
}
if (!defined('MP_PUBLIC_KEY')) {
    define('MP_PUBLIC_KEY', '');
}
if (!defined('MP_CLIENT_ID')) {
    define('MP_CLIENT_ID', '');
}
if (!defined('MP_CLIENT_SECRET')) {
    define('MP_CLIENT_SECRET', '');
}

$mpSiteUrl = defined('SITE_URL') ? SITE_URL : '';

define('MP_WEBHOOK_SUCCESS', $mpSiteUrl . '/webhooks/mp-success.php');
define('MP_WEBHOOK_FAILURE', $mpSiteUrl . '/webhooks/mp-failure.php');
define('MP_WEBHOOK_PENDING', $mpSiteUrl . '/webhooks/mp-pending.php');

define('MP_RETURN_SUCCESS', $mpSiteUrl . '/?p=pago-exitoso');
define('MP_RETURN_FAILURE', $mpSiteUrl . '/?p=pago-fallido');
define('MP_RETURN_PENDING', $mpSiteUrl . '/?p=pago-pendiente');

define('MP_SHIPPING_ENABLED', false);
define('MP_SHIPPING_DIMENSIONS', [
    'width' => 20,
    'height' => 20,
    'length' => 20,
    'weight' => 500,
]);

define('MP_NOTIFICATION_URL', $mpSiteUrl . '/webhooks/mp-notification.php');

define('MP_PREFERENCE_CONFIG', [
    'auto_return' => 'approved',
    'back_urls' => [
        'success' => MP_RETURN_SUCCESS,
        'failure' => MP_RETURN_FAILURE,
        'pending' => MP_RETURN_PENDING,
    ],
    'notification_url' => MP_NOTIFICATION_URL,
    'statement_descriptor' => 'YOFI',
    'expires' => false,
]);

define('MP_SHIPPING_CONFIG', [
    'mode' => 'not_specified',
    'dimensions' => MP_SHIPPING_DIMENSIONS,
    'local_pickup' => false,
    'free_shipping' => false,
]);

define('MP_PAYMENT_STATUS', [
    'pending' => 'Pendiente',
    'approved' => 'Aprobado',
    'authorized' => 'Autorizado',
    'in_process' => 'En proceso',
    'in_mediation' => 'En mediación',
    'rejected' => 'Rechazado',
    'cancelled' => 'Cancelado',
    'refunded' => 'Reembolsado',
    'charged_back' => 'Contracargo',
]);

define('MP_LOG_ENABLED', true);
define('MP_LOG_FILE', __DIR__ . '/../logs/mercadopago.log');

function mp_credential(string $constantName): string
{
    if (!preg_match('/^MP_[A-Z0-9_]+$/', $constantName)) {
        return '';
    }
    $fromEnv = getenv($constantName);
    if ($fromEnv !== false && trim((string)$fromEnv) !== '') {
        return trim((string)$fromEnv);
    }
    if (defined($constantName)) {
        return (string)constant($constantName);
    }
    return '';
}

function mp_api_base_url(): string
{
    return 'https://api.mercadopago.com';
}

function mp_credentials(): array
{
    return [
        'access_token' => mp_credential('MP_ACCESS_TOKEN'),
        'public_key' => mp_credential('MP_PUBLIC_KEY'),
        'client_id' => mp_credential('MP_CLIENT_ID'),
        'client_secret' => mp_credential('MP_CLIENT_SECRET'),
    ];
}

function mp_is_sandbox(): bool
{
    return defined('MP_ENVIRONMENT') && MP_ENVIRONMENT === 'sandbox';
}

function getMercadoPagoCredentials(): array
{
    return mp_credentials();
}

function validateMercadoPagoConfig(): array
{
    $errors = [];

    if (mp_credential('MP_ACCESS_TOKEN') === '') {
        $errors[] = 'Access Token de MercadoPago no configurado';
    }

    if (mp_credential('MP_PUBLIC_KEY') === '') {
        $errors[] = 'Public Key de MercadoPago no configurado';
    }

    return $errors;
}

function logMercadoPago($message, $type = 'INFO'): void
{
    if (!MP_LOG_ENABLED) {
        return;
    }

    $logDir = dirname(MP_LOG_FILE);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $mode = (defined('MP_ENVIRONMENT') ? '[' . MP_ENVIRONMENT . '] ' : '');
    $logMessage = "[$timestamp] [$type] {$mode}$message" . PHP_EOL;

    file_put_contents(MP_LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
}
