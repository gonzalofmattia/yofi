<?php

declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../../src/php/wishlist.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_json(['success' => false, 'message' => 'Método no permitido'], 405);
}

$userId = api_require_login();

$input = json_decode(file_get_contents('php://input') ?: '', true);
$productId = (int) ($input['product_id'] ?? 0);

$result = toggleWishlistItem($userId, $productId);
$code = $result['success'] ? 200 : 400;
api_json($result, $code);
