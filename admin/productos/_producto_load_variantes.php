<?php
/** @var mysqli $con */
/** @var int $id_prod */

$coloresTodos = [];
$resColores = mysqli_query($con, 'SELECT id_color, nombre, hex_code FROM tbl_colores WHERE activo = 1 ORDER BY nombre');
while ($c = mysqli_fetch_assoc($resColores)) {
    $coloresTodos[(int)$c['id_color']] = $c;
}

$tallesTodos = [];
$resTalles = mysqli_query($con, 'SELECT id_talle, nombre FROM tbl_talles WHERE activo = 1 ORDER BY orden, nombre');
while ($t = mysqli_fetch_assoc($resTalles)) {
    $tallesTodos[] = $t;
}

$coloresProducto = [];

$resSkus = mysqli_query($con, "
    SELECT s.id_sku, s.id_color, s.id_talle, s.stock, s.activo, c.nombre AS color_nombre, c.hex_code, t.nombre AS talle_nombre
    FROM tbl_skus s
    INNER JOIN tbl_colores c ON c.id_color = s.id_color
    INNER JOIN tbl_talles t ON t.id_talle = s.id_talle
    WHERE s.id_prod = $id_prod
    ORDER BY c.nombre, t.orden, t.nombre
");
while ($sku = mysqli_fetch_assoc($resSkus)) {
    $idColor = (int)$sku['id_color'];
    if (!isset($coloresProducto[$idColor])) {
        $coloresProducto[$idColor] = [
            'id_color' => $idColor,
            'nombre' => $sku['color_nombre'],
            'hex_code' => $sku['hex_code'],
            'skus' => [],
            'imagenes' => [],
        ];
    }
    $coloresProducto[$idColor]['skus'][(int)$sku['id_talle']] = [
        'id_sku' => (int)$sku['id_sku'],
        'stock' => (int)$sku['stock'],
        'activo' => (int)$sku['activo'],
        'talle_nombre' => $sku['talle_nombre'],
    ];
}

$resImg = mysqli_query($con, "
    SELECT i.id_imagen, i.path, i.id_color, i.es_principal, c.nombre AS color_nombre, c.hex_code
    FROM tbl_prod_imagenes i
    LEFT JOIN tbl_colores c ON c.id_color = i.id_color
    WHERE i.id_prod = $id_prod AND i.id_color IS NOT NULL
    ORDER BY i.id_color, i.es_principal DESC, i.orden ASC
");
while ($img = mysqli_fetch_assoc($resImg)) {
    $idColor = (int)$img['id_color'];
    if (!isset($coloresProducto[$idColor])) {
        $coloresProducto[$idColor] = [
            'id_color' => $idColor,
            'nombre' => $img['color_nombre'] ?? 'Color #' . $idColor,
            'hex_code' => $img['hex_code'] ?? '#ccc',
            'skus' => [],
            'imagenes' => [],
        ];
    }
    $coloresProducto[$idColor]['imagenes'][] = $img;
}

$coloresUsados = array_keys($coloresProducto);
$coloresDisponibles = array_filter($coloresTodos, static fn($c) => !in_array((int)$c['id_color'], $coloresUsados, true));
