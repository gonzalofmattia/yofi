<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

const CATALOG_PAGE_SIZE = 20;

function format_price(float $amount): string
{
    return '$' . number_format($amount, 0, ',', '.');
}

function product_effective_price(array $row): float
{
    $base = (float)($row['precio_base'] ?? 0);
    $oferta = isset($row['precio_oferta']) && $row['precio_oferta'] !== null && $row['precio_oferta'] !== ''
        ? (float)$row['precio_oferta']
        : null;

    if ($oferta !== null && $oferta > 0 && $oferta < $base) {
        return $oferta;
    }

    return $base;
}

/**
 * Columnas de listado: una fila por combinación producto + color (catálogo).
 */
function sql_producto_variant_columns(): string
{
    return 'p.id_prod,
        p.nombre,
        p.slug,
        p.precio_base,
        p.precio_oferta,
        p.publicado,
        p.destacado,
        p.oferta,
        p.promo_badge,
        c.id_color,
        c.nombre AS color_nombre,
        c.hex_code,
        (SELECT pi.path FROM tbl_prod_imagenes pi
         WHERE pi.id_prod = p.id_prod AND pi.id_color = c.id_color
         AND pi.es_principal = 1 LIMIT 1) AS imagen_principal,
        (SELECT pi.path FROM tbl_prod_imagenes pi
         WHERE pi.id_prod = p.id_prod AND pi.id_color = c.id_color
         ORDER BY pi.es_principal DESC, pi.orden ASC, pi.id_imagen ASC LIMIT 1) AS imagen_fallback,
        (SELECT SUM(s.stock) FROM tbl_skus s
         WHERE s.id_prod = p.id_prod AND s.id_color = c.id_color
         AND s.activo = 1) AS stock_color,
        (SELECT s4.id_sku FROM tbl_skus s4
         WHERE s4.id_prod = p.id_prod AND s4.id_color = c.id_color AND s4.activo = 1 AND s4.stock > 0
         ORDER BY s4.id_sku ASC LIMIT 1) AS id_sku_default,
        (SELECT t4.nombre FROM tbl_skus s4
         INNER JOIN tbl_talles t4 ON t4.id_talle = s4.id_talle
         WHERE s4.id_prod = p.id_prod AND s4.id_color = c.id_color AND s4.activo = 1 AND s4.stock > 0
         ORDER BY s4.id_sku ASC LIMIT 1) AS sku_talle_nombre,
        (SELECT GROUP_CONCAT(DISTINCT t5.nombre ORDER BY t5.orden SEPARATOR ",")
         FROM tbl_skus s5
         INNER JOIN tbl_talles t5 ON t5.id_talle = s5.id_talle
         WHERE s5.id_prod = p.id_prod AND s5.id_color = c.id_color AND s5.activo = 1 AND s5.stock > 0) AS talles_disponibles';
}

/** Una fila por producto padre (uso interno / futuro). */
function sql_producto_parent_columns(): string
{
    return sql_producto_variant_columns();
}

function product_variant_display_name(string $nombre, string $colorNombre): string
{
    $nombre = trim($nombre);
    $colorNombre = trim($colorNombre);

    if ($colorNombre === '') {
        return $nombre;
    }

    return $nombre . ' — ' . $colorNombre;
}

function map_producto_row(array $row): array
{
    $precioBase = (float)($row['precio_base'] ?? 0);
    $precioOferta = isset($row['precio_oferta']) && $row['precio_oferta'] !== null && $row['precio_oferta'] !== ''
        ? (float)$row['precio_oferta']
        : null;
    $imagen = $row['imagen_principal'] ?? $row['imagen_fallback'] ?? null;
    $idColor = (int)($row['id_color'] ?? 0);
    $slug = (string)($row['slug'] ?? '');
    $nombre = (string)($row['nombre'] ?? '');
    $colorNombre = (string)($row['color_nombre'] ?? '');

    $tallesRaw = trim((string)($row['talles_disponibles'] ?? ''));
    $talles = $tallesRaw !== '' ? array_values(array_filter(array_map('trim', explode(',', $tallesRaw)))) : [];

    return [
        'id_prod' => (int)$row['id_prod'],
        'id_color' => $idColor,
        'nombre' => $nombre,
        'nombre_display' => product_variant_display_name($nombre, $colorNombre),
        'slug' => $slug,
        'precio_base' => $precioBase,
        'precio_oferta' => $precioOferta,
        'color_nombre' => $colorNombre,
        'hex_code' => (string)($row['hex_code'] ?? ''),
        'imagen_principal' => $imagen ? imgprod_path((string)$imagen) : imgprod_path('placeholder.jpg'),
        'stock_color' => (int)($row['stock_color'] ?? 0),
        'talles' => $talles,
        'promo_badge' => !empty($row['promo_badge']) ? (string)$row['promo_badge'] : null,
        'id_sku_default' => isset($row['id_sku_default']) ? (int)$row['id_sku_default'] : null,
        'sku_color_nombre' => $colorNombre,
        'sku_color_hex' => (string)($row['hex_code'] ?? ''),
        'sku_talle_nombre' => (string)($row['sku_talle_nombre'] ?? ''),
        'sku_precio' => product_effective_price($row),
    ];
}

function sql_producto_variant_from(): string
{
    return 'FROM tbl_productos p
            INNER JOIN tbl_skus s2 ON s2.id_prod = p.id_prod AND s2.activo = 1 AND s2.stock > 0
            INNER JOIN tbl_colores c ON c.id_color = s2.id_color';
}

function sql_producto_parent_from(): string
{
    return sql_producto_variant_from();
}

function sql_producto_parent_exists_clause(): string
{
    return 'EXISTS (SELECT 1 FROM tbl_skus s2 WHERE s2.id_prod = p.id_prod AND s2.activo = 1)';
}

function build_variant_filter_clauses(array $filters, array &$params): array
{
    $extra = [];

    if (!empty($filters['talle'])) {
        $placeholders = [];
        foreach ($filters['talle'] as $idx => $nombre) {
            $key = ':talle_' . $idx;
            $placeholders[] = $key;
            $params[$key] = $nombre;
        }
        $extra[] = 'EXISTS (
            SELECT 1 FROM tbl_skus sx
            INNER JOIN tbl_talles tx ON tx.id_talle = sx.id_talle
            WHERE sx.id_prod = p.id_prod AND sx.id_color = c.id_color AND sx.activo = 1 AND sx.stock > 0
            AND tx.nombre IN (' . implode(',', $placeholders) . ')
        )';
    }

    if (!empty($filters['color'])) {
        $placeholders = [];
        foreach ($filters['color'] as $idx => $idColor) {
            $key = ':color_' . $idx;
            $placeholders[] = $key;
            $params[$key] = $idColor;
        }
        $extra[] = 'c.id_color IN (' . implode(',', $placeholders) . ')';
    }

    return $extra;
}

/** @deprecated Use build_variant_filter_clauses() */
function build_product_filter_clauses(array $filters, array &$params): array
{
    return build_variant_filter_clauses($filters, $params);
}

/**
 * Filtros de edad del catálogo (no son categorías de producto).
 *
 * @return list<array{label: string, slug: string}>
 */
function get_age_filters(): array
{
    return [
        ['label' => 'MINI', 'slug' => 'mini'],
        ['label' => '1 A 4 AÑOS', 'slug' => '1-a-4'],
        ['label' => '4 A 12 AÑOS', 'slug' => '4-a-12'],
    ];
}

function get_age_filter_by_slug(string $slug): ?array
{
    $slug = trim($slug);
    foreach (get_age_filters() as $filter) {
        if ($filter['slug'] === $slug) {
            return $filter;
        }
    }

    return null;
}

/**
 * @return list<string>
 */
function get_age_filter_talles(string $slug): array
{
    return match ($slug) {
        'mini' => ['0-3M', '3-6M', '6-12M', '1A', '2A'],
        '1-a-4' => ['1A', '2A', '3A', '4A'],
        '4-a-12' => ['4A', '6A', '8A', '10A', '12A'],
        default => [],
    };
}

function get_age_filter_fallback_image(string $slug): string
{
    return match ($slug) {
        'mini' => 'categoria-mini.jpg',
        '1-a-4' => 'categoria-ninas.jpg',
        '4-a-12' => 'categoria-ninos.jpg',
        default => 'placeholder.jpg',
    };
}

/**
 * Resuelve el filtro de edad en talles concretos para la query del catálogo.
 *
 * @return array{talle: list<string>, no_results: bool}
 */
function resolve_catalog_talle_filters(array $filters): array
{
    $talles = $filters['talle'];
    $noResults = false;

    if ($filters['edad'] !== '') {
        $ageTalles = get_age_filter_talles($filters['edad']);
        if ($ageTalles === []) {
            $noResults = true;
        } elseif ($talles === []) {
            $talles = $ageTalles;
        } else {
            $talles = array_values(array_intersect($talles, $ageTalles));
            if ($talles === []) {
                $noResults = true;
            }
        }
    }

    return ['talle' => $talles, 'no_results' => $noResults];
}

function get_parent_categories(int $limit = 0): array
{
    $pdo = db_ro();
    $sql = 'SELECT * FROM tbl_categorias
            WHERE id_cate_padre IS NULL AND publicado = 1
            ORDER BY orden ASC';
    if ($limit > 0) {
        $sql .= ' LIMIT ' . (int)$limit;
    }
    $stmt = $pdo->query($sql);

    return $stmt->fetchAll();
}

function get_featured_home_categories(): array
{
    $pdo = db_ro();
    $stmt = $pdo->query(
        'SELECT * FROM tbl_categorias
         WHERE id_cate_padre IS NULL AND publicado = 1 AND destacado_home = 1
         ORDER BY orden ASC, nombre ASC'
    );

    return $stmt ? $stmt->fetchAll() : [];
}

function get_category_by_slug(string $slug): ?array
{
    $slug = trim($slug);
    if ($slug === '') {
        return null;
    }

    $pdo = db_ro();
    $stmt = $pdo->prepare('SELECT * FROM tbl_categorias WHERE slug = :slug AND publicado = 1 LIMIT 1');
    $stmt->execute([':slug' => $slug]);

    $row = $stmt->fetch();

    return $row ?: null;
}

function get_subcategories(int $parentId, int $limit = 4): array
{
    $pdo = db_ro();
    $stmt = $pdo->prepare(
        'SELECT * FROM tbl_categorias
         WHERE id_cate_padre = :parent AND publicado = 1
         ORDER BY orden ASC
         LIMIT :lim'
    );
    $stmt->bindValue(':parent', $parentId, PDO::PARAM_INT);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_featured_products(int $limit = 5): array
{
    $pdo = db_ro();
    $sql = 'SELECT ' . sql_producto_variant_columns() . '
            ' . sql_producto_variant_from() . '
            WHERE p.destacado = 1 AND p.publicado = 1 AND p.borrado = 0
            GROUP BY p.id_prod, c.id_color
            ORDER BY p.fecha_actualizacion DESC, p.id_prod DESC, c.nombre ASC
            LIMIT :lim';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return array_map('map_producto_row', $stmt->fetchAll());
}

function get_all_talles(): array
{
    $pdo = db_ro();
    $stmt = $pdo->query('SELECT * FROM tbl_talles WHERE activo = 1 ORDER BY orden ASC, nombre ASC');

    return $stmt->fetchAll();
}

function get_all_colores(): array
{
    $pdo = db_ro();
    $stmt = $pdo->query('SELECT * FROM tbl_colores WHERE activo = 1 ORDER BY nombre ASC');

    return $stmt->fetchAll();
}

function parse_catalog_filters(): array
{
    $filters = [
        'categoria' => isset($_GET['categoria']) ? trim((string)$_GET['categoria']) : '',
        'edad' => isset($_GET['edad']) ? trim((string)$_GET['edad']) : '',
        'talle' => [],
        'color' => [],
        'precio_min' => null,
        'precio_max' => null,
        'orden' => isset($_GET['orden']) ? trim((string)$_GET['orden']) : '',
        'ofertas' => !empty($_GET['ofertas']),
        'q' => isset($_GET['q']) ? trim((string)$_GET['q']) : '',
    ];

    if ($filters['edad'] !== '' && get_age_filter_by_slug($filters['edad']) === null) {
        $filters['edad'] = '';
    }

    if (isset($_GET['talle'])) {
        $talles = is_array($_GET['talle']) ? $_GET['talle'] : [$_GET['talle']];
        foreach ($talles as $t) {
            $t = trim((string)$t);
            if ($t !== '') {
                $filters['talle'][] = $t;
            }
        }
    }

    if (isset($_GET['color'])) {
        $colores = is_array($_GET['color']) ? $_GET['color'] : [$_GET['color']];
        foreach ($colores as $c) {
            if (is_numeric($c)) {
                $filters['color'][] = (int)$c;
            }
        }
    }

    if (isset($_GET['precio_min']) && $_GET['precio_min'] !== '') {
        $filters['precio_min'] = (float)$_GET['precio_min'];
    }

    if (isset($_GET['precio_max']) && $_GET['precio_max'] !== '') {
        $filters['precio_max'] = (float)$_GET['precio_max'];
    }

    return $filters;
}

function build_catalog_query(array $filters, bool $countOnly, int $page = 1, int $pageSize = CATALOG_PAGE_SIZE): array
{
    $pdo = db_ro();
    $conditions = ['p.publicado = 1', 'p.borrado = 0'];
    $params = [];

    if ($filters['categoria'] !== '') {
        $cat = get_category_by_slug($filters['categoria']);
        if ($cat) {
            $conditions[] = 'p.id_cate = :id_cate';
            $params[':id_cate'] = (int)$cat['id_cate'];
        }
    }

    if ($filters['ofertas']) {
        $conditions[] = '(p.oferta = 1 OR (p.precio_oferta IS NOT NULL AND p.precio_oferta > 0 AND p.precio_oferta < p.precio_base))';
    }

    if ($filters['q'] !== '') {
        $conditions[] = '(p.nombre LIKE :q OR p.codigo LIKE :q OR p.descripcion LIKE :q)';
        $params[':q'] = '%' . $filters['q'] . '%';
    }

    if ($filters['precio_min'] !== null) {
        $conditions[] = 'COALESCE(NULLIF(p.precio_oferta, 0), p.precio_base) >= :precio_min';
        $params[':precio_min'] = $filters['precio_min'];
    }

    if ($filters['precio_max'] !== null) {
        $conditions[] = 'COALESCE(NULLIF(p.precio_oferta, 0), p.precio_base) <= :precio_max';
        $params[':precio_max'] = $filters['precio_max'];
    }

    $talleFilters = resolve_catalog_talle_filters($filters);
    if ($talleFilters['no_results']) {
        $conditions[] = '1 = 0';
    }

    $variantFilters = build_variant_filter_clauses(
        array_merge($filters, ['talle' => $talleFilters['talle']]),
        $params
    );
    $conditions = array_merge($conditions, $variantFilters);

    $where = 'WHERE ' . implode(' AND ', $conditions);
    $from = sql_producto_variant_from();

    if ($countOnly) {
        $sql = 'SELECT COUNT(*) FROM (
            SELECT p.id_prod, c.id_color
            ' . $from . '
            ' . $where . '
            GROUP BY p.id_prod, c.id_color
        ) AS variantes';

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $type);
        }
        $stmt->execute();

        return ['count' => (int)$stmt->fetchColumn(), 'results' => []];
    }

    $order = match ($filters['orden']) {
        'precio_asc' => 'COALESCE(NULLIF(p.precio_oferta, 0), p.precio_base) ASC, p.nombre ASC, c.nombre ASC',
        'precio_desc' => 'COALESCE(NULLIF(p.precio_oferta, 0), p.precio_base) DESC, p.nombre ASC, c.nombre ASC',
        'nombre_asc' => 'p.nombre ASC, c.nombre ASC',
        'nombre_desc' => 'p.nombre DESC, c.nombre ASC',
        'novedades' => 'p.fecha_creacion DESC, p.id_prod DESC, c.nombre ASC',
        default => 'p.destacado DESC, p.nombre ASC, c.nombre ASC',
    };

    $sql = 'SELECT ' . sql_producto_variant_columns() . '
            ' . $from . '
            ' . $where . '
            GROUP BY p.id_prod, c.id_color
            ORDER BY ' . $order . '
            LIMIT :limit OFFSET :offset';

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue($key, $value, $type);
    }
    $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
    $stmt->bindValue(':offset', max(0, ($page - 1) * $pageSize), PDO::PARAM_INT);
    $stmt->execute();

    return [
        'count' => 0,
        'results' => array_map('map_producto_row', $stmt->fetchAll()),
    ];
}

function get_catalog_products(array $filters, int $page = 1): array
{
    return build_catalog_query($filters, false, $page, CATALOG_PAGE_SIZE)['results'];
}

function count_catalog_products(array $filters): int
{
    return build_catalog_query($filters, true)['count'];
}

function get_product_by_slug(string $slug): ?array
{
    $slug = trim($slug);
    if ($slug === '') {
        return null;
    }

    $pdo = db_ro();
    $stmt = $pdo->prepare(
        'SELECT p.*
         FROM tbl_productos p
         WHERE p.slug = :slug AND p.publicado = 1 AND p.borrado = 0
         LIMIT 1'
    );
    $stmt->execute([':slug' => $slug]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function get_default_product_color(int $idProd): ?int
{
    $pdo = db_ro();
    $stmt = $pdo->prepare(
        'SELECT s.id_color
         FROM tbl_skus s
         INNER JOIN tbl_colores c ON c.id_color = s.id_color
         WHERE s.id_prod = :id AND s.activo = 1
         ORDER BY c.nombre ASC
         LIMIT 1'
    );
    $stmt->execute([':id' => $idProd]);
    $id = $stmt->fetchColumn();

    return $id !== false ? (int)$id : null;
}

function product_has_color(int $idProd, int $idColor): bool
{
    $pdo = db_ro();
    $stmt = $pdo->prepare(
        'SELECT 1 FROM tbl_skus WHERE id_prod = :id AND id_color = :color AND activo = 1 LIMIT 1'
    );
    $stmt->execute([':id' => $idProd, ':color' => $idColor]);

    return (bool)$stmt->fetchColumn();
}

function get_product_skus(int $idProd, ?int $idColor = null): array
{
    $pdo = db_ro();
    $sql = 'SELECT s.*, c.nombre AS color_nombre, c.hex_code AS color_hex,
                t.nombre AS talle_nombre, t.orden AS talle_orden
         FROM tbl_skus s
         INNER JOIN tbl_colores c ON c.id_color = s.id_color
         INNER JOIN tbl_talles t ON t.id_talle = s.id_talle
         WHERE s.id_prod = :id AND s.activo = 1';
    $params = [':id' => $idProd];

    if ($idColor !== null) {
        $sql .= ' AND s.id_color = :color';
        $params[':color'] = $idColor;
    }

    $sql .= ' ORDER BY c.nombre ASC, t.orden ASC, t.nombre ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function get_product_colors(int $idProd): array
{
    $pdo = db_ro();
    $stmt = $pdo->prepare(
        'SELECT c.id_color, c.nombre AS color_nombre, c.hex_code,
            COALESCE(
                (SELECT pi.path FROM tbl_prod_imagenes pi
                 WHERE pi.id_prod = :id AND pi.id_color = c.id_color AND pi.es_principal = 1 LIMIT 1),
                (SELECT pi.path FROM tbl_prod_imagenes pi
                 WHERE pi.id_prod = :id2 AND pi.id_color = c.id_color
                 ORDER BY pi.orden ASC, pi.id_imagen ASC LIMIT 1)
            ) AS imagen
         FROM tbl_skus s
         INNER JOIN tbl_colores c ON c.id_color = s.id_color
         WHERE s.id_prod = :id3 AND s.activo = 1
         GROUP BY c.id_color, c.nombre, c.hex_code
         ORDER BY c.nombre ASC'
    );
    $stmt->execute([
        ':id' => $idProd,
        ':id2' => $idProd,
        ':id3' => $idProd,
    ]);

    return array_map(static function (array $row): array {
        $imagen = $row['imagen'] ?? null;

        return [
            'id_color' => (int)$row['id_color'],
            'color_nombre' => (string)$row['color_nombre'],
            'hex_code' => (string)$row['hex_code'],
            'imagen' => $imagen ? imgprod_path((string)$imagen) : imgprod_path('placeholder.jpg'),
        ];
    }, $stmt->fetchAll());
}

function get_product_color_variants(int $idProd): array
{
    $skus = get_product_skus($idProd);
    $variants = [];

    foreach ($skus as $sku) {
        $idColor = (int)$sku['id_color'];
        if (!isset($variants[$idColor])) {
            $variants[$idColor] = [
                'id_color' => $idColor,
                'color_nombre' => (string)$sku['color_nombre'],
                'hex_code' => (string)$sku['color_hex'],
                'talles' => [],
            ];
        }
        $idTalle = (int)$sku['id_talle'];
        $variants[$idColor]['talles'][$idTalle] = [
            'id_talle' => $idTalle,
            'nombre' => (string)$sku['talle_nombre'],
            'orden' => (int)$sku['talle_orden'],
            'id_sku' => (int)$sku['id_sku'],
            'stock' => (int)$sku['stock'],
            'precio_extra' => (float)$sku['precio_extra'],
        ];
    }

    foreach ($variants as &$variant) {
        uasort($variant['talles'], static fn($a, $b) => $a['orden'] <=> $b['orden']);
        $variant['talles'] = array_values($variant['talles']);
    }
    unset($variant);

    return array_values($variants);
}

function get_product_other_colors(int $idProd, int $excludeColorId): array
{
    $pdo = db_ro();
    $stmt = $pdo->prepare(
        'SELECT c.id_color, c.nombre AS color_nombre, c.hex_code,
            COALESCE(
                (SELECT pi.path FROM tbl_prod_imagenes pi
                 WHERE pi.id_prod = :id AND pi.id_color = c.id_color AND pi.es_principal = 1 LIMIT 1),
                (SELECT pi.path FROM tbl_prod_imagenes pi
                 WHERE pi.id_prod = :id2 AND pi.id_color = c.id_color
                 ORDER BY pi.orden ASC, pi.id_imagen ASC LIMIT 1)
            ) AS imagen
         FROM tbl_skus s
         INNER JOIN tbl_colores c ON c.id_color = s.id_color
         WHERE s.id_prod = :id3 AND s.activo = 1 AND c.id_color != :exclude
         GROUP BY c.id_color, c.nombre, c.hex_code
         ORDER BY c.nombre ASC'
    );
    $stmt->execute([
        ':id' => $idProd,
        ':id2' => $idProd,
        ':id3' => $idProd,
        ':exclude' => $excludeColorId,
    ]);

    return array_map(static function (array $row): array {
        $imagen = $row['imagen'] ?? null;

        return [
            'id_color' => (int)$row['id_color'],
            'color_nombre' => (string)$row['color_nombre'],
            'hex_code' => (string)$row['hex_code'],
            'imagen' => $imagen ? imgprod_path((string)$imagen) : imgprod_path('placeholder.jpg'),
        ];
    }, $stmt->fetchAll());
}

function get_product_images(int $idProd, ?int $idColor = null): array
{
    $pdo = db_ro();
    $sql = 'SELECT * FROM tbl_prod_imagenes WHERE id_prod = :id';
    $params = [':id' => $idProd];

    if ($idColor !== null) {
        $sql .= ' AND id_color = :color';
        $params[':color'] = $idColor;
    }

    $sql .= ' ORDER BY es_principal DESC, orden ASC, id_imagen ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    return array_map(static function (array $row): array {
        return [
            'id_imagen' => (int)$row['id_imagen'],
            'path' => imgprod_path((string)$row['path']),
            'id_color' => isset($row['id_color']) ? (int)$row['id_color'] : null,
            'es_principal' => (int)$row['es_principal'] === 1,
        ];
    }, $rows);
}

function get_related_products(int $idProd, int $idCate, int $limit = 4): array
{
    $pdo = db_ro();
    $stmt = $pdo->prepare(
        'SELECT ' . sql_producto_variant_columns() . '
         ' . sql_producto_variant_from() . '
         WHERE p.id_prod != :id AND p.id_cate = :cate
           AND p.publicado = 1 AND p.borrado = 0
         GROUP BY p.id_prod, c.id_color
         ORDER BY RAND()
         LIMIT :lim'
    );
    $stmt->bindValue(':id', $idProd, PDO::PARAM_INT);
    $stmt->bindValue(':cate', $idCate, PDO::PARAM_INT);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return array_map('map_producto_row', $stmt->fetchAll());
}

function catalog_query_string(array $filters, array $overrides = []): string
{
    $merged = array_merge($filters, $overrides);
    $parts = [];

    if ($merged['categoria'] !== '') {
        $parts[] = 'categoria=' . urlencode($merged['categoria']);
    }
    if ($merged['edad'] !== '') {
        $parts[] = 'edad=' . urlencode($merged['edad']);
    }
    if (!empty($merged['ofertas'])) {
        $parts[] = 'ofertas=1';
    }
    if ($merged['q'] !== '') {
        $parts[] = 'q=' . urlencode($merged['q']);
    }
    if ($merged['orden'] !== '') {
        $parts[] = 'orden=' . urlencode($merged['orden']);
    }
    if ($merged['precio_min'] !== null) {
        $parts[] = 'precio_min=' . urlencode((string)$merged['precio_min']);
    }
    if ($merged['precio_max'] !== null) {
        $parts[] = 'precio_max=' . urlencode((string)$merged['precio_max']);
    }
    foreach ($merged['talle'] as $t) {
        $parts[] = 'talle[]=' . urlencode($t);
    }
    foreach ($merged['color'] as $c) {
        $parts[] = 'color[]=' . urlencode((string)$c);
    }

    return implode('&', $parts);
}

function category_catalog_url(string $slug): string
{
    return page_path('catalogo') . '&categoria=' . urlencode(trim($slug));
}

function product_url(string $slug, ?int $idColor = null): string
{
    $url = page_path('producto') . '&slug=' . urlencode($slug);
    if ($idColor !== null && $idColor > 0) {
        $url .= '&color=' . $idColor;
    }

    return $url;
}
