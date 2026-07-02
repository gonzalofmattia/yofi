<?php

/**
 * Helpers para volver a mandar al login (URL absoluta, no relativa al script
 * actual) y preservar a dónde quería ir el admin para volver ahí después de
 * loguearse.
 */

function admin_base_path(): string
{
    $siteUrl = defined('SITE_URL') ? SITE_URL : '';
    $path = parse_url($siteUrl, PHP_URL_PATH) ?: '';

    return rtrim($path, '/') . '/admin/';
}

function admin_safe_redirect_target(?string $raw): string
{
    $prefix = admin_base_path();
    $raw = (string) $raw;

    if ($raw === '' || $raw[0] !== '/' || str_starts_with($raw, '//') || str_contains($raw, '://')) {
        return $prefix . 'dashboard.php';
    }
    if (!str_starts_with($raw, $prefix)) {
        return $prefix . 'dashboard.php';
    }

    return $raw;
}

function adminLoginRedirect(string $errorCode = 'unauthorized'): void
{
    $prefix = admin_base_path();
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $loginUrl = $prefix . 'index.php?error=' . urlencode($errorCode);

    if ($requestUri !== '' && strpos($requestUri, $prefix . 'index.php') !== 0) {
        $loginUrl .= '&redirect=' . urlencode($requestUri);
    }

    header('Location: ' . $loginUrl);
    exit();
}
