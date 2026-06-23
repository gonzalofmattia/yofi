<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * @return list<array<string, mixed>>
 */
function get_active_slides(): array
{
    $stmt = db_ro()->query(
        'SELECT id_slide, imagen, link_url, orden
         FROM tbl_slider
         WHERE activo = 1
         ORDER BY orden ASC, id_slide ASC'
    );

    return $stmt ? $stmt->fetchAll() : [];
}

/**
 * @return list<array<string, mixed>>
 */
function get_active_banners(string $posicion): array
{
    $stmt = db_ro()->prepare(
        'SELECT id_banner, eyebrow, titulo, subtitulo, texto_boton, imagen, link_url, orden
         FROM tbl_banners
         WHERE activo = 1 AND posicion = ?
         ORDER BY orden ASC, id_banner ASC'
    );
    $stmt->execute([$posicion]);

    return $stmt->fetchAll();
}

function content_resolve_url(?string $url): string
{
    if ($url === null || trim($url) === '') {
        return '';
    }

    $url = trim($url);
    if (preg_match('#^https?://#i', $url)) {
        return $url;
    }

    return app_path(ltrim($url, '/'));
}

/**
 * @return list<array<string, mixed>>
 */
function get_home_edad_banners(): array
{
    $stmt = db_ro()->query(
        'SELECT id_edad_banner, slug, titulo, subtitulo, imagen, link_url, orden
         FROM tbl_home_edad_banners
         WHERE activo = 1
         ORDER BY orden ASC, id_edad_banner ASC'
    );

    return $stmt ? $stmt->fetchAll() : [];
}

/**
 * @return array<string, string>
 */
function empresa_config_get_all(): array
{
    static $cache = null;
    if (is_array($cache)) {
        return $cache;
    }

    $cache = [];
    $stmt = db_ro()->query('SELECT clave, valor FROM tbl_config_empresa');
    if ($stmt) {
        while ($row = $stmt->fetch()) {
            $cache[(string)$row['clave']] = (string)($row['valor'] ?? '');
        }
    }

    return $cache;
}

function empresa_config_get(string $clave, string $default = ''): string
{
    $all = empresa_config_get_all();

    return $all[$clave] ?? $default;
}

function shipping_config_get(string $clave, string $default = ''): string
{
    static $cache = null;
    if (!is_array($cache)) {
        $cache = [];
        $stmt = db_ro()->query('SELECT clave, valor FROM tbl_shipping_config');
        if ($stmt) {
            while ($row = $stmt->fetch()) {
                $cache[(string)$row['clave']] = (string)($row['valor'] ?? '');
            }
        }
    }

    return $cache[$clave] ?? $default;
}

function free_shipping_threshold(): int
{
    return max(0, (int)shipping_config_get('free_shipping_threshold', '0'));
}

function format_money_ars(int|float $amount): string
{
    return '$' . number_format((float)$amount, 0, ',', '.');
}

function free_shipping_preheader_text(): string
{
    $threshold = free_shipping_threshold();
    if ($threshold <= 0) {
        return '';
    }

    return 'Envío gratis en compras superiores a ' . format_money_ars($threshold);
}

function whatsapp_href(?string $number): string
{
    $digits = preg_replace('/\D+/', '', (string)$number) ?? '';
    if ($digits === '') {
        return '';
    }

    return 'https://wa.me/' . $digits;
}

function normalize_social_url(?string $url, string $type): string
{
    $url = trim((string)$url);
    if ($url === '') {
        return '';
    }

    if (preg_match('#^https?://#i', $url)) {
        return $url;
    }

    $handle = ltrim($url, '@');
    if ($type === 'instagram') {
        return 'https://www.instagram.com/' . rawurlencode($handle) . '/';
    }
    if ($type === 'facebook') {
        return 'https://www.facebook.com/' . rawurlencode($handle);
    }

    return $url;
}
