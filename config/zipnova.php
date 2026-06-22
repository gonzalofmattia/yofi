<?php
/**
 * Configuración de Zipnova — Yofi
 *
 * Dónde poner credenciales (en este orden):
 *  1) Variables de entorno: ZIPNOVA_ACCOUNT_ID, ZIPNOVA_KEY, ZIPNOVA_SECRET, ZIPNOVA_CP_ORIGEN, etc.
 *  2) Archivo local NO versionado: zipnova.local.php
 */

if (is_file(__DIR__ . '/zipnova.local.php')) {
    require_once __DIR__ . '/zipnova.local.php';
}

$isLocalEnv = (defined('ENV') && ENV === 'dev');

if (!defined('ZIPNOVA_ACCOUNT_ID')) {
    define('ZIPNOVA_ACCOUNT_ID', $isLocalEnv
        ? (getenv('ZIPNOVA_ACCOUNT_ID') ?: '')
        : (getenv('ZIPNOVA_ACCOUNT_ID') ?: ''));
}

if (!defined('ZIPNOVA_KEY')) {
    $keyFromEnv = getenv('ZIPNOVA_KEY');
    if ($keyFromEnv === false || trim((string)$keyFromEnv) === '') {
        $keyFromEnv = getenv('ZIPNOVA_API_KEY');
    }
    define('ZIPNOVA_KEY', ($keyFromEnv !== false && trim((string)$keyFromEnv) !== '') ? trim((string)$keyFromEnv) : '');
}

if (!defined('ZIPNOVA_SECRET')) {
    define('ZIPNOVA_SECRET', getenv('ZIPNOVA_SECRET') ?: '');
}

// Alias legacy (no usar para Basic auth)
if (!defined('ZIPNOVA_API_KEY')) {
    define('ZIPNOVA_API_KEY', defined('ZIPNOVA_KEY') ? ZIPNOVA_KEY : '');
}

if (!defined('ZIPNOVA_CP_ORIGEN')) {
    define('ZIPNOVA_CP_ORIGEN', $isLocalEnv ? '1706' : '1706');
}

if (!defined('ZIPNOVA_WEBHOOK_TOKEN')) {
    define('ZIPNOVA_WEBHOOK_TOKEN', getenv('ZIPNOVA_WEBHOOK_TOKEN') ?: '');
}

define('ZIPNOVA_BASE_URL', 'https://api.zipnova.com.ar/v2');
define('ZIPNOVA_API_BASE_URL', ZIPNOVA_BASE_URL);
define('ZIPNOVA_QUOTE_ENDPOINT', '/shipments/quote');
if (!defined('ZIPNOVA_ORIGIN_ID')) {
    define('ZIPNOVA_ORIGIN_ID', 378086);
}
if (!defined('ZIPNOVA_CLASSIFICATION_ID')) {
    define('ZIPNOVA_CLASSIFICATION_ID', 1);
}
define('ZIPNOVA_LOG_ENABLED', true);
define('ZIPNOVA_LOG_FILE', __DIR__ . '/../logs/zipnova.log');

// Activar en config/zipnova.local.php: define('ZIPNOVA_DEBUG', true);
if (!defined('ZIPNOVA_DEBUG')) {
    define('ZIPNOVA_DEBUG', false);
}

function zipnova_credential(string $constantName): string
{
    if (!preg_match('/^ZIPNOVA_[A-Z0-9_]+$/', $constantName)) {
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

function logZipnova(string $message, string $type = 'INFO'): void
{
    if (!ZIPNOVA_LOG_ENABLED) {
        return;
    }

    $logDir = dirname(ZIPNOVA_LOG_FILE);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$type] $message" . PHP_EOL;
    file_put_contents(ZIPNOVA_LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
}
