<?php

declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../../src/php/products.php';
require_once __DIR__ . '/../../../src/php/wishlist.php';

$raw = $_GET['ids'] ?? '';
$ids = array_values(array_unique(array_filter(array_map('intval', explode(',', (string) $raw)), static fn($id) => $id > 0)));

$userId = getLoggedInUserId();
if ($userId !== null && $ids !== []) {
    $ids = syncWishlist($userId, $ids);
} elseif ($userId !== null) {
    $ids = getWishlistProductIds($userId);
}

$products = get_products_for_wishlist($ids);
$list = [];
foreach ($ids as $id) {
    if (isset($products[$id])) {
        $list[] = $products[$id];
    }
}

api_json([
    'success' => true,
    'product_ids' => $ids,
    'products' => $list,
]);
