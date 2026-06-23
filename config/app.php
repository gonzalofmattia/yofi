<?php

/**
 * Configuración de aplicación (claves internas, etc.).
 * Valores sensibles en config/app.local.php (no versionado).
 */

if (is_file(__DIR__ . '/app.local.php')) {
    require_once __DIR__ . '/app.local.php';
}

if (!defined('INTERNAL_API_KEY')) {
    define('INTERNAL_API_KEY', getenv('INTERNAL_API_KEY') ?: '');
}
