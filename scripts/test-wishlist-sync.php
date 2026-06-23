<?php

declare(strict_types=1);

/**
 * Prueba de fusión wishlist (invitado → cuenta) sin duplicados.
 * Ejecutar: php scripts/test-wishlist-sync.php
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/php/db.php';
require_once __DIR__ . '/../src/php/wishlist.php';
require_once __DIR__ . '/../src/php/users.php';

function test_assert(bool $cond, string $msg): void
{
    echo ($cond ? '[OK] ' : '[FAIL] ') . $msg . PHP_EOL;
    if (!$cond) {
        exit(1);
    }
}

$pdo = db_rw();

// Usuario de prueba
$email = 'wishlist-test-' . time() . '@yofi.local';
$hash = password_hash('testpass123', PASSWORD_DEFAULT);
$pdo->prepare('INSERT INTO tbl_usuarios (email, password_hash, nombre, apellido, is_guest, activo, email_verificado) VALUES (?, ?, ?, ?, 0, 1, 1)')
    ->execute([$email, $hash, 'Test', 'Wishlist']);
$userId = (int) $pdo->lastInsertId();
test_assert($userId > 0, 'Usuario de prueba creado id=' . $userId);

// Productos publicados
$prodIds = $pdo->query('SELECT id_prod FROM tbl_productos WHERE publicado = 1 AND borrado = 0 LIMIT 3')->fetchAll(PDO::FETCH_COLUMN);
if (count($prodIds) < 2) {
    echo "[SKIP] Se necesitan al menos 2 productos publicados en la BD.\n";
    $pdo->prepare('DELETE FROM tbl_usuarios WHERE id_usuario = ?')->execute([$userId]);
    exit(0);
}

$p1 = (int) $prodIds[0];
$p2 = (int) $prodIds[1];
$p3 = isset($prodIds[2]) ? (int) $prodIds[2] : $p2;

// Caso 1: sync sin repetidos — local [p1,p2] + server [p2,p3] => {p1,p2,p3}
$pdo->prepare('INSERT INTO tbl_wishlist (usuario_id, producto_id) VALUES (?, ?)')->execute([$userId, $p2]);
$pdo->prepare('INSERT INTO tbl_wishlist (usuario_id, producto_id) VALUES (?, ?)')->execute([$userId, $p3]);

$merged = syncWishlist($userId, [$p1, $p2]);
test_assert(in_array($p1, $merged, true), 'Caso sin repetidos: incluye p1 del localStorage');
test_assert(in_array($p2, $merged, true), 'Caso sin repetidos: incluye p2');
test_assert(in_array($p3, $merged, true), 'Caso sin repetidos: conserva p3 del servidor');
test_assert(count($merged) === count(array_unique($merged)), 'Caso sin repetidos: sin duplicados internos');
$expectedCount = count(array_unique([$p1, $p2, $p3]));
test_assert(count($merged) === $expectedCount, 'Caso sin repetidos: cardinalidad ' . $expectedCount);

// Caso 2: sync con repetidos — local [p1,p1,p2,p2]
$merged2 = syncWishlist($userId, [$p1, $p1, $p2, $p2]);
test_assert(count($merged2) === count(array_unique($merged2)), 'Caso con repetidos: resultado sin duplicados');
test_assert(count($merged2) === $expectedCount, 'Caso con repetidos: misma cardinalidad que caso 1');

// Limpieza
$pdo->prepare('DELETE FROM tbl_wishlist WHERE usuario_id = ?')->execute([$userId]);
$pdo->prepare('DELETE FROM tbl_usuarios WHERE id_usuario = ?')->execute([$userId]);

echo "Todas las pruebas de wishlist sync pasaron.\n";
