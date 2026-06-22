<?php
require_once __DIR__ . '/config.php';

$raw_page = isset($_GET['p']) ? strtolower((string)$_GET['p']) : 'home';
$page = preg_replace('/[^a-z0-9\-]/', '', $raw_page);
$page = $page ?: 'home';
$page = str_replace('--', '-', $page);

$allowed_pages = [
    'home',
    'catalogo',
    'producto',
    'checkout',
    'confirmacion',
    'login',
    'registro',
    'mi-cuenta',
];

if (!in_array($page, $allowed_pages, true)) {
    http_response_code(404);
    $page_file = __DIR__ . '/pages/404.php';
    $page_id = '404';
} else {
    $page_file = __DIR__ . '/pages/' . $page . '.php';
    if (!file_exists($page_file)) {
        http_response_code(404);
        $page_file = __DIR__ . '/pages/404.php';
        $page_id = '404';
    } else {
        $page_id = $page;
    }
}

include __DIR__ . '/layout.php';
