<?php

function modificado($modif, $imagespath, $item): string
{
    if ((string)$modif === '1') {
        return '<div class="alert alert-success alert-dismissible fade show" role="alert">'
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
            . 'El ' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . ' se modificó con éxito.</div>';
    }

    return '';
}

function borrado($borra, $imagespath, $item): string
{
    if ((string)$borra === '1') {
        return '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
            . 'El ' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . ' se eliminó con éxito.</div>';
    }

    return '';
}

function agregado($agreg, $imagespath, $item): string
{
    if ((string)$agreg === '1') {
        return '<div class="alert alert-success alert-dismissible fade show" role="alert">'
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
            . 'El ' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . ' se agregó con éxito.</div>';
    }

    return '';
}

function buscador(string $destino, string $valorActual = ''): string
{
    if (!function_exists('generateAdminCSRFToken')) {
        $adminSecurityPath = __DIR__ . '/../admin_security.php';
        if (file_exists($adminSecurityPath)) {
            require_once $adminSecurityPath;
        }
    }

    $csrfToken = function_exists('generateAdminCSRFToken') ? generateAdminCSRFToken() : '';
    $valorInput = $valorActual !== '' ? htmlspecialchars($valorActual, ENT_QUOTES, 'UTF-8') : '';

    $salida = '<div class="d-flex align-items-center justify-content-end gap-2 m-3">';
    $salida .= '<form action="' . htmlspecialchars($destino, ENT_QUOTES, 'UTF-8') . '" method="post" class="d-flex align-items-center gap-2">';
    if ($csrfToken !== '') {
        $salida .= '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') . '">';
    }
    $salida .= '<input name="palabra" placeholder="Buscar..." type="text" class="form-control" value="' . $valorInput . '">';
    $salida .= '<button type="submit" class="btn btn-yofi">Buscar</button>';
    $salida .= '</form>';
    $salida .= '<a href="' . htmlspecialchars($destino, ENT_QUOTES, 'UTF-8') . '" class="btn btn-outline-secondary">Ver todos</a>';
    $salida .= '</div>';

    return $salida;
}

function yofi_slug(string $text): string
{
    $text = strtolower(trim($text));
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    $text = trim($text, '-');

    return $text !== '' ? $text : 'item';
}

function admin_csrf_field(): string
{
    if (!function_exists('generateAdminCSRFToken')) {
        return '';
    }

    $token = generateAdminCSRFToken();

    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

function estado_pedido_badge(string $estado): string
{
    $map = [
        'pendiente' => 'secondary',
        'confirmado' => 'success',
        'en_preparacion' => 'primary',
        'preparando_envio' => 'info',
        'enviado' => 'info',
        'entregado' => 'dark',
        'cancelado' => 'danger',
    ];
    $class = $map[$estado] ?? 'secondary';
    $label = ucfirst(str_replace('_', ' ', $estado));

    return '<span class="badge bg-' . $class . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
}

function format_money(float $amount): string
{
    return '$' . number_format($amount, 0, ',', '.');
}

function admin_site_path(string $path = ''): string
{
    $base = defined('SITE_URL')
        ? rtrim((string)(parse_url(SITE_URL, PHP_URL_PATH) ?: ''), '/')
        : (defined('BASE_PATH') ? rtrim(BASE_PATH, '/') : '');

    $clean = ltrim(str_replace('\\', '/', $path), '/');
    if ($clean === '') {
        return $base === '' ? '/' : $base;
    }

    return ($base === '' ? '' : $base) . '/' . $clean;
}

if (!function_exists('app_path')) {
    function app_path(string $path = ''): string
    {
        return admin_site_path($path);
    }
}

if (!function_exists('asset_path')) {
    function asset_path(string $path = ''): string
    {
        return admin_site_path('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('imgprod_path')) {
    function imgprod_path(string $path = ''): string
    {
        return admin_site_path('imgprod/' . ltrim($path, '/'));
    }
}
