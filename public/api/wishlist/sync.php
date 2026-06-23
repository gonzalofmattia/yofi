<?php

declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../../src/php/wishlist.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_json(['success' => false, 'message' => 'Método no permitido'], 405);
}

$userId = api_require_login();

$input = json_decode(file_get_contents('php://input') ?: '', true);
if (!is_array($input)) {
    api_json(['success' => false, 'message' => 'Datos inválidos'], 400);
}

$productIds = $input['product_ids'] ?? [];
if (!is_array($productIds)) {
    $productIds = [];
}

$merged = syncWishlist($userId, $productIds);

api_json([
    'success' => true,
    'product_ids' => $merged,
]);
