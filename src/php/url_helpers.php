<?php

declare(strict_types=1);

/**
 * URL base de la app y protocolo (incluye ngrok / reverse proxy).
 */

function yofi_request_protocol(): string
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return 'https';
    }

    $forwarded = strtolower(trim((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')));
    if ($forwarded === 'https' || $forwarded === 'http') {
        return $forwarded;
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] !== 'off') {
        return 'https';
    }

    return 'http';
}

function yofi_app_base_path(): string
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    if (defined('YOFI_PROJECT_ROOT') && !empty($_SERVER['DOCUMENT_ROOT'])) {
        $docRoot = realpath(str_replace('\\', '/', (string) $_SERVER['DOCUMENT_ROOT']));
        $projRoot = realpath(str_replace('\\', '/', (string) YOFI_PROJECT_ROOT));
        if ($docRoot !== false && $projRoot !== false && str_starts_with($projRoot, $docRoot)) {
            $rel = substr($projRoot, strlen($docRoot));
            $rel = str_replace('\\', '/', (string) $rel);
            $rel = rtrim($rel, '/');
            $cached = ($rel === '' || $rel === '/') ? '' : $rel;

            return $cached;
        }
    }

    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    if (preg_match('#^(.+?)/(?:public/|checkout/|webhooks/|scripts/|admin/)#', $script, $m)) {
        $cached = rtrim($m[1], '/');

        return $cached;
    }

    $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');
    if ($dir === '/' || $dir === '.') {
        $cached = '';

        return $cached;
    }

    $cached = $dir;

    return $cached;
}

function yofi_site_url(): string
{
    $protocol = yofi_request_protocol();
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = yofi_app_base_path();

    return $protocol . '://' . $host . ($basePath !== '' ? $basePath : '');
}
