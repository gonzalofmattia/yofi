<?php

if (PHP_SAPI === 'cli') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_NAME'] = 'localhost';
}

require_once __DIR__ . '/../src/php/config.php';
require_once __DIR__ . '/../src/php/db.php';

$sql = file_get_contents(__DIR__ . '/seed-demo.sql');
if ($sql === false) {
    fwrite(STDERR, "No se pudo leer seed-demo.sql\n");
    exit(1);
}

try {
    $pdo = db_rw();
    $pdo->exec($sql);

    $productos = $pdo->query('SELECT id_prod, nombre, destacado FROM tbl_productos ORDER BY id_prod')->fetchAll(PDO::FETCH_ASSOC);
    $skus = (int)$pdo->query('SELECT COUNT(*) FROM tbl_skus')->fetchColumn();
    $imagenes = (int)$pdo->query('SELECT COUNT(*) FROM tbl_prod_imagenes')->fetchColumn();

    echo "Seed OK\n";
    echo "Productos: " . count($productos) . "\n";
    echo "SKUs: {$skus}\n";
    echo "Imágenes: {$imagenes}\n";
    foreach ($productos as $p) {
        echo "  #{$p['id_prod']} {$p['nombre']} (destacado={$p['destacado']})\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . "\n");
    exit(1);
}
