<?php

/**
 * Configuración SMTP — Yofi (DonWeb).
 * Credenciales reales en config/smtp.local.php (gitignored).
 */

if (file_exists(__DIR__ . '/smtp.local.php')) {
    require_once __DIR__ . '/smtp.local.php';
}

if (!defined('SMTP_HOST')) {
    define('SMTP_HOST', getenv('SMTP_HOST') ?: '');
}
if (!defined('SMTP_PORT')) {
    define('SMTP_PORT', (int)(getenv('SMTP_PORT') ?: 587));
}
if (!defined('SMTP_USER')) {
    define('SMTP_USER', getenv('SMTP_USER') ?: '');
}
if (!defined('SMTP_PASS')) {
    define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
}
if (!defined('SMTP_SECURE')) {
    define('SMTP_SECURE', getenv('SMTP_SECURE') ?: 'tls');
}
if (!defined('MAIL_FROM')) {
    define('MAIL_FROM', getenv('MAIL_FROM') ?: (SMTP_USER !== '' ? SMTP_USER : 'no-reply@yofi.com.ar'));
}
if (!defined('MAIL_FROM_NAME')) {
    define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: 'Yofi');
}
