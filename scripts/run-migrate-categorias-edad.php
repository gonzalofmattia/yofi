<?php
require_once dirname(__DIR__) . '/admin/include/includes.php';

$sql = file_get_contents(__DIR__ . '/migrate-categorias-edad.sql');
if ($sql === false) {
    fwrite(STDERR, "No se pudo leer migrate-categorias-edad.sql\n");
    exit(1);
}

mysqli_multi_query($con, $sql);
do {
    if ($result = mysqli_store_result($con)) {
        mysqli_free_result($result);
    }
} while (mysqli_next_result($con));

if (mysqli_errno($con)) {
    fwrite(STDERR, 'Error SQL: ' . mysqli_error($con) . PHP_EOL);
    exit(1);
}

$r = mysqli_query($con, 'SELECT id_cate, nombre, slug, imagen FROM tbl_categorias ORDER BY orden, id_cate');
echo "Categorías:\n";
while ($row = mysqli_fetch_assoc($r)) {
    echo "- {$row['id_cate']} | {$row['nombre']} | {$row['slug']} | {$row['imagen']}\n";
}

$r = mysqli_query($con, 'SELECT p.id_prod, p.nombre, c.slug AS categoria FROM tbl_productos p INNER JOIN tbl_categorias c ON c.id_cate = p.id_cate WHERE p.id_prod BETWEEN 1 AND 5 ORDER BY p.id_prod');
echo "\nProductos demo:\n";
while ($row = mysqli_fetch_assoc($r)) {
    echo "- {$row['id_prod']} | {$row['nombre']} | {$row['categoria']}\n";
}

echo "\nOK\n";
