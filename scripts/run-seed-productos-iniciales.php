<?php

if (PHP_SAPI === 'cli') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_NAME'] = 'localhost';
}

require_once __DIR__ . '/../src/php/config.php';
require_once __DIR__ . '/../src/php/db.php';

$sql = file_get_contents(__DIR__ . '/seed-productos-iniciales.sql');
if ($sql === false) {
    fwrite(STDERR, "No se pudo leer seed-productos-iniciales.sql\n");
    exit(1);
}

try {
    $pdo = db_rw();
    $pdo->exec($sql);

    $categorias = $pdo->query(
        "SELECT nombre, slug FROM tbl_categorias WHERE slug IN ('mini-anima-invierno', 'regalos')"
    )->fetchAll(PDO::FETCH_ASSOC);

    $productos = $pdo->query(
        "SELECT p.id_prod, p.nombre, p.codigo, c.nombre AS categoria
         FROM tbl_productos p
         INNER JOIN tbl_categorias c ON c.id_cate = p.id_cate
         WHERE p.codigo LIKE 'YF-MINI-%' OR p.codigo LIKE 'YF-REG-%'
         ORDER BY p.codigo"
    )->fetchAll(PDO::FETCH_ASSOC);

    $skus = (int)$pdo->query(
        "SELECT COUNT(*) FROM tbl_skus s
         INNER JOIN tbl_productos p ON p.id_prod = s.id_prod
         WHERE p.codigo LIKE 'YF-MINI-%' OR p.codigo LIKE 'YF-REG-%'"
    )->fetchColumn();

    echo "Seed catálogo inicial OK\n";
    echo "Categorías:\n";
    foreach ($categorias as $cat) {
        echo "  - {$cat['nombre']} ({$cat['slug']})\n";
    }
    echo "Productos: " . count($productos) . "\n";
    echo "SKUs: {$skus}\n";
    foreach ($productos as $p) {
        echo "  {$p['codigo']} — {$p['nombre']} [{$p['categoria']}]\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . "\n");
    exit(1);
}
