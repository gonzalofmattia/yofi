<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function getWishlistProductIds(int $userId): array
{
    $pdo = db_ro();
    $stmt = $pdo->prepare('
        SELECT producto_id FROM tbl_wishlist
        WHERE usuario_id = ?
        ORDER BY fecha_agregado DESC
    ');
    $stmt->execute([$userId]);
    $ids = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ids[] = (int) $row['producto_id'];
    }

    return $ids;
}

/**
 * @param int[] $productIds
 * @return int[] merged unique ids
 */
function syncWishlist(int $userId, array $productIds): array
{
    $productIds = array_values(array_unique(array_filter(array_map('intval', $productIds), static fn($id) => $id > 0)));
    if ($productIds === []) {
        return getWishlistProductIds($userId);
    }

    try {
        $pdo = db_rw();
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $validStmt = $pdo->prepare("
            SELECT id_prod FROM tbl_productos
            WHERE id_prod IN ($placeholders) AND publicado = 1 AND borrado = 0
        ");
        $validStmt->execute($productIds);
        $validIds = array_map('intval', $validStmt->fetchAll(PDO::FETCH_COLUMN));

        foreach ($validIds as $pid) {
            $pdo->prepare('
                INSERT IGNORE INTO tbl_wishlist (usuario_id, producto_id)
                VALUES (?, ?)
            ')->execute([$userId, $pid]);
        }

        return getWishlistProductIds($userId);
    } catch (Throwable $e) {
        error_log('syncWishlist: ' . $e->getMessage());

        return getWishlistProductIds($userId);
    }
}

function toggleWishlistItem(int $userId, int $productId): array
{
    if ($productId <= 0) {
        return ['success' => false, 'message' => 'Producto inválido', 'in_wishlist' => false];
    }

    try {
        $pdo = db_rw();
        $chk = $pdo->prepare('SELECT id_prod FROM tbl_productos WHERE id_prod = ? AND publicado = 1 AND borrado = 0');
        $chk->execute([$productId]);
        if (!$chk->fetch()) {
            return ['success' => false, 'message' => 'Producto no disponible', 'in_wishlist' => false];
        }

        $exists = $pdo->prepare('SELECT id_wishlist FROM tbl_wishlist WHERE usuario_id = ? AND producto_id = ?');
        $exists->execute([$userId, $productId]);
        $row = $exists->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $pdo->prepare('DELETE FROM tbl_wishlist WHERE id_wishlist = ? AND usuario_id = ?')
                ->execute([(int) $row['id_wishlist'], $userId]);

            return ['success' => true, 'in_wishlist' => false, 'message' => 'Quitado de favoritos'];
        }

        $pdo->prepare('INSERT INTO tbl_wishlist (usuario_id, producto_id) VALUES (?, ?)')
            ->execute([$userId, $productId]);

        return ['success' => true, 'in_wishlist' => true, 'message' => 'Agregado a favoritos'];
    } catch (Throwable $e) {
        error_log('toggleWishlistItem: ' . $e->getMessage());

        return ['success' => false, 'message' => 'Error al actualizar favoritos', 'in_wishlist' => false];
    }
}

function removeWishlistItem(int $userId, int $productId): bool
{
    try {
        $pdo = db_rw();
        $stmt = $pdo->prepare('DELETE FROM tbl_wishlist WHERE usuario_id = ? AND producto_id = ?');

        return $stmt->execute([$userId, $productId]);
    } catch (Throwable $e) {
        error_log('removeWishlistItem: ' . $e->getMessage());

        return false;
    }
}
