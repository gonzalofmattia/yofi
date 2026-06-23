<?php
if (!defined('YOFI_PROJECT_ROOT')) {
    define('YOFI_PROJECT_ROOT', __DIR__);
}

require_once __DIR__ . '/src/php/url_helpers.php';
require_once __DIR__ . '/src/php/config.php';
require_once __DIR__ . '/config/mercadopago.php';
require_once __DIR__ . '/config/zipnova.php';
require_once __DIR__ . '/config/smtp.php';
require_once __DIR__ . '/config/app.php';

$protocol = yofi_request_protocol();
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = yofi_app_base_path();

define('BASE_PATH', $scriptDir);
define('BASE_URL', $protocol . '://' . $host . ($scriptDir !== '' ? $scriptDir : ''));

define('SITE_DESCRIPTION', 'Ropa infantil con estilo para los más chicos.');
define('SITE_KEYWORDS', 'ropa infantil, niños, bebés, moda kids, Yofi');

if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', ENV === 'dev');
}

function app_path(string $path = ''): string
{
    $clean = ltrim($path, '/');
    if ($clean === '') {
        return BASE_PATH !== '' ? rtrim(BASE_PATH, '/') : '/';
    }

    if (BASE_PATH !== '') {
        return rtrim(BASE_PATH, '/') . '/' . $clean;
    }

    return '/' . $clean;
}

function asset_path(string $path): string
{
    $relativePath = 'assets/' . ltrim($path, '/');
    $url = app_path($relativePath);

    $filePath = __DIR__ . '/' . $relativePath;
    if (file_exists($filePath)) {
        $url .= '?v=' . filemtime($filePath);
    }

    return $url;
}

function page_path(string $page): string
{
    return app_path('index.php?p=' . urlencode($page));
}

function image_path(string $path): string
{
    return app_path('imagenes/' . ltrim($path, '/'));
}

function imgprod_path(string $path): string
{
    return app_path('imgprod/' . ltrim($path, '/'));
}
