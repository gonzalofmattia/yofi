<?php

/**
 * Configuración del front público — Yofi.
 * Detecta automáticamente el entorno y usa las credenciales correctas.
 */

if (!defined('YOFI_PROJECT_ROOT')) {
    define('YOFI_PROJECT_ROOT', dirname(__DIR__, 2));
}

require_once __DIR__ . '/url_helpers.php';

if (!function_exists('detectEnvironmentFront')) {
    function detectEnvironmentFront(): string
    {
        $hostname = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
        $serverName = isset($_SERVER['SERVER_NAME']) ? strtolower($_SERVER['SERVER_NAME']) : '';
        $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';

        if ($documentRoot !== '' && stripos($documentRoot, 'laragon') !== false) {
            return 'local';
        }

        $isDemo = (
            strpos($scriptName, '/demo/') !== false ||
            strpos($requestUri, '/demo/') !== false ||
            strpos($documentRoot, '/demo') !== false
        );

        $isLocal = (
            $hostname === 'localhost' ||
            $hostname === '127.0.0.1' ||
            strpos($hostname, '.local') !== false ||
            strpos($hostname, '.test') !== false ||
            strpos($hostname, 'localhost:') !== false ||
            strpos($serverName, 'localhost') !== false ||
            strpos($serverName, '127.0.0.1') !== false ||
            strpos($serverName, '.local') !== false ||
            strpos($serverName, '.test') !== false
        );

        if ($isLocal) {
            return 'local';
        }

        if ($isDemo) {
            return 'demo';
        }

        return 'production';
    }
}

$detectedEnv = detectEnvironmentFront();

$dbLocalFile = YOFI_PROJECT_ROOT . '/config/db.local.php';
if (is_file($dbLocalFile)) {
    require_once $dbLocalFile;
}

if ($detectedEnv === 'local' && !defined('DB_HOST')) {
    $dbHost = 'localhost';
    $dbUser = 'root';
    $dbPass = '';
    $dbName = 'yofi';
} elseif (defined('DB_HOST')) {
    $dbHost = DB_HOST;
    $dbUser = defined('DB_USER') ? DB_USER : (defined('DB_USER_RW') ? DB_USER_RW : '');
    $dbPass = defined('DB_PASSWORD') ? DB_PASSWORD : (defined('DB_PASS') ? DB_PASS : (defined('DB_PASS_RW') ? DB_PASS_RW : ''));
    $dbName = defined('DB_DATABASE') ? DB_DATABASE : (defined('DB_NAME') ? DB_NAME : 'yofi');
} elseif ($detectedEnv === 'demo') {
    $dbHost = 'localhost';
    $dbUser = getenv('DB_USER') ?: 'yofi_demo';
    $dbPass = getenv('DB_PASS') ?: '';
    $dbName = getenv('DB_NAME') ?: 'yofi_demo';
} else {
    $dbHost = 'localhost';
    $dbUser = getenv('DB_USER') ?: 'yofi_prod';
    $dbPass = getenv('DB_PASS') ?: '';
    $dbName = getenv('DB_NAME') ?: 'yofi_prod';
}

$protocol = yofi_request_protocol();
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = yofi_app_base_path();

if (!defined('ENV')) {
    define('ENV', getenv('APP_ENV') ?: ($detectedEnv === 'local' ? 'dev' : 'prod'));
}
if (!defined('ASSETS_BASE')) {
    define('ASSETS_BASE', getenv('ASSETS_BASE') ?: '/assets');
}
if (!defined('DB_DSN')) {
    define('DB_DSN', getenv('DB_DSN') ?: "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4");
}
if (!defined('DB_USER_RO')) {
    define('DB_USER_RO', getenv('DB_USER_RO') ?: $dbUser);
}
if (!defined('DB_PASS_RO')) {
    define('DB_PASS_RO', getenv('DB_PASS_RO') ?: $dbPass);
}
if (!defined('DB_USER_RW')) {
    define('DB_USER_RW', getenv('DB_USER_RW') ?: $dbUser);
}
if (!defined('DB_PASS_RW')) {
    define('DB_PASS_RW', getenv('DB_PASS_RW') ?: $dbPass);
}
if (!defined('CACHE_TTL')) {
    define('CACHE_TTL', (int)(getenv('CACHE_TTL') ?: 120));
}
if (!defined('SITE_URL')) {
    define('SITE_URL', yofi_site_url());
}
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Yofi');
}
if (!defined('CURRENCY')) {
    define('CURRENCY', 'ARS');
}

$isDevEnv = ($detectedEnv === 'local');
if ($isDevEnv) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    $errorLogPath = __DIR__ . '/../../error_log.txt';
    if (file_exists(dirname($errorLogPath)) && is_writable(dirname($errorLogPath))) {
        ini_set('error_log', $errorLogPath);
    }
}
