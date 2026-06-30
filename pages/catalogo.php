<?php
require_once __DIR__ . '/../src/php/products.php';

$filters = parse_catalog_filters();
$page = max(1, (int)($_GET['pagina'] ?? 1));
$total = count_catalog_products($filters);
$productos = get_catalog_products($filters, $page);
$totalPages = max(1, (int)ceil($total / CATALOG_PAGE_SIZE));

$talles = get_all_talles();
$colores = get_all_colores();
$categorias = get_parent_categories();

$categoriaActual = null;
if ($filters['categoria'] !== '') {
    $categoriaActual = get_category_by_slug($filters['categoria']);
}

$edadActual = $filters['edad'] !== '' ? get_age_filter_by_slug($filters['edad']) : null;

$tituloCatalogo = 'Catálogo';
if ($filters['ofertas']) {
    $tituloCatalogo = 'Ofertas';
} elseif ($categoriaActual && $edadActual) {
    $tituloCatalogo = (string)$categoriaActual['nombre'] . ' · ' . $edadActual['label'];
} elseif ($categoriaActual) {
    $tituloCatalogo = (string)$categoriaActual['nombre'];
} elseif ($edadActual) {
    $tituloCatalogo = $edadActual['label'];
} elseif ($filters['q'] !== '') {
    $tituloCatalogo = 'Resultados: ' . $filters['q'];
}

$page_title = $tituloCatalogo . ' | ' . SITE_NAME;
$meta_description = 'Explorá ' . $tituloCatalogo . ' en ' . SITE_NAME . '. Ropa romántica para chicos.';

$ordenOpciones = [
    '' => 'Relevancia',
    'precio_asc' => 'Menor precio',
    'precio_desc' => 'Mayor precio',
    'novedades' => 'Novedades',
];

$precioSliderMax = 50000;
$precioMinVal = $filters['precio_min'] !== null ? (int)$filters['precio_min'] : 0;
$precioMaxVal = $filters['precio_max'] !== null ? (int)$filters['precio_max'] : $precioSliderMax;

$activeFilters = [];
if ($edadActual) {
    $activeFilters[] = ['type' => 'edad', 'value' => $edadActual['slug'], 'label' => $edadActual['label']];
}
if ($categoriaActual) {
    $activeFilters[] = ['type' => 'categoria', 'value' => (string)$categoriaActual['slug'], 'label' => (string)$categoriaActual['nombre']];
}
foreach ($filters['talle'] as $t) {
    $activeFilters[] = ['type' => 'talle', 'value' => $t, 'label' => $t];
}
foreach ($filters['color'] as $cId) {
    foreach ($colores as $col) {
        if ((int)$col['id_color'] === (int)$cId) {
            $activeFilters[] = ['type' => 'color', 'value' => (string)$cId, 'label' => (string)$col['nombre']];
            break;
        }
    }
}

function catalog_page_url(array $filters, int $pageNum): string
{
    $qs = catalog_query_string($filters, ['pagina' => $pageNum]);
    $base = page_path('catalogo');
    return $qs !== '' ? $base . '&' . $qs . '&pagina=' . $pageNum : $base . '&pagina=' . $pageNum;
}

$clearSidebarFilters = [
    'categoria' => $filters['categoria'],
    'edad' => $filters['edad'],
    'talle' => [],
    'color' => [],
    'precio_min' => null,
    'precio_max' => null,
    'orden' => '',
    'ofertas' => $filters['ofertas'],
    'q' => $filters['q'],
];
$clearSidebarUrl = page_path('catalogo');
$clearSidebarQs = catalog_query_string($clearSidebarFilters);
if ($clearSidebarQs !== '') {
    $clearSidebarUrl .= '&' . $clearSidebarQs;
}

$bannerImg = (!empty($categoriaActual['banner_img']))
    ? imgprod_path((string)$categoriaActual['banner_img'])
    : imgprod_path('catalogo-hero.jpg');
?>

<!-- Hero strip -->
<section class="relative w-full h-[260px] sm:h-[300px] overflow-hidden">
    <img
        src="<?php echo htmlspecialchars($bannerImg, ENT_QUOTES, 'UTF-8'); ?>"
        alt="<?php echo htmlspecialchars($tituloCatalogo, ENT_QUOTES, 'UTF-8'); ?>"
        class="absolute inset-0 w-full h-full object-cover"
    >
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="relative z-10 h-full flex items-center justify-center">
        <h1 class="text-white text-4xl sm:text-6xl font-extrabold tracking-tight"><?php echo htmlspecialchars($tituloCatalogo, ENT_QUOTES, 'UTF-8'); ?></h1>
    </div>
</section>

<div class="px-4 sm:px-8 pt-6 text-xs text-earth">
    <a href="<?php echo page_path('home'); ?>" class="hover:text-dark">Inicio</a>
    <span class="mx-1">›</span>
    <span class="text-dark font-semibold"><?php echo htmlspecialchars($tituloCatalogo, ENT_QUOTES, 'UTF-8'); ?></span>
</div>

<?php if (!empty($activeFilters)): ?>
<div class="px-4 sm:px-8 mt-4 flex flex-wrap gap-2">
    <?php foreach ($activeFilters as $af): ?>
    <?php
        $removeFilters = $filters;
        if ($af['type'] === 'talle') {
            $removeFilters['talle'] = array_values(array_filter($removeFilters['talle'], static fn($t) => $t !== $af['value']));
        } elseif ($af['type'] === 'color') {
            $removeFilters['color'] = array_values(array_filter($removeFilters['color'], static fn($c) => (string)$c !== $af['value']));
        } elseif ($af['type'] === 'edad') {
            $removeFilters['edad'] = '';
        } elseif ($af['type'] === 'categoria') {
            $removeFilters['categoria'] = '';
        }
        $removeUrl = page_path('catalogo') . '&' . catalog_query_string($removeFilters);
    ?>
    <a
        href="<?php echo htmlspecialchars($removeUrl, ENT_QUOTES, 'UTF-8'); ?>"
        class="inline-flex items-center gap-1 bg-cream text-dark text-xs font-semibold px-3 py-1.5 rounded-full hover:bg-primary/40 transition"
    >
        <?php echo htmlspecialchars($af['label'], ENT_QUOTES, 'UTF-8'); ?>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="grid lg:grid-cols-[240px_1fr] gap-8 px-4 sm:px-8 mt-6 pb-16">
    <!-- Sidebar filtros desktop -->
    <aside class="hidden lg:block">
        <form method="get" action="<?php echo page_path('catalogo'); ?>" id="catalog-filters">
            <input type="hidden" name="p" value="catalogo">
            <?php if ($filters['ofertas']): ?>
            <input type="hidden" name="ofertas" value="1">
            <?php endif; ?>
            <?php if ($filters['q'] !== ''): ?>
            <input type="hidden" name="q" value="<?php echo htmlspecialchars($filters['q'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php endif; ?>
            <?php if ($filters['edad'] !== ''): ?>
            <input type="hidden" name="edad" value="<?php echo htmlspecialchars($filters['edad'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php endif; ?>
            <?php if ($filters['categoria'] !== ''): ?>
            <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($filters['categoria'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php endif; ?>

            <div class="flex items-center justify-between mb-6">
                <h3 class="font-extrabold text-lg">Filtros</h3>
                <a href="<?php echo htmlspecialchars($clearSidebarUrl, ENT_QUOTES, 'UTF-8'); ?>" class="text-xs underline text-earth">Limpiar todo</a>
            </div>

            <div class="border-b border-cream">
                <button type="button" class="w-full flex items-center justify-between py-4 text-left" data-filter-toggle="talle" data-default="open">
                    <span class="font-bold text-sm uppercase">Talle</span>
                    <svg data-chevron class="w-4 h-4 text-earth transition-transform duration-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="filter-content overflow-hidden" style="transition: max-height 0.3s ease;">
                    <div class="pb-4">
                        <div class="grid grid-cols-4 gap-1.5">
                            <?php foreach ($talles as $talle): ?>
                            <?php $tNombre = (string)$talle['nombre']; $checked = in_array($tNombre, $filters['talle'], true); ?>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="talle[]" value="<?php echo htmlspecialchars($tNombre, ENT_QUOTES, 'UTF-8'); ?>" class="sr-only peer" <?php echo $checked ? 'checked' : ''; ?>>
                                <span class="flex items-center justify-center h-9 text-xs font-semibold border rounded peer-checked:border-dark peer-checked:bg-dark peer-checked:text-white border-cream hover:border-dark transition">
                                    <?php echo htmlspecialchars($tNombre, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-b border-cream">
                <button type="button" class="w-full flex items-center justify-between py-4 text-left" data-filter-toggle="color" data-default="open">
                    <span class="font-bold text-sm uppercase">Color</span>
                    <svg data-chevron class="w-4 h-4 text-earth transition-transform duration-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="filter-content overflow-hidden" style="transition: max-height 0.3s ease;">
                    <div class="pb-4">
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($colores as $color): ?>
                            <?php $checked = in_array((int)$color['id_color'], $filters['color'], true); ?>
                            <label class="cursor-pointer" title="<?php echo htmlspecialchars((string)$color['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="checkbox" name="color[]" value="<?php echo (int)$color['id_color']; ?>" class="sr-only peer" <?php echo $checked ? 'checked' : ''; ?>>
                                <span
                                    class="block h-7 w-7 rounded-full border-2 transition peer-checked:border-dark peer-checked:scale-110 border-cream"
                                    style="background-color: <?php echo htmlspecialchars((string)$color['hex_code'], ENT_QUOTES, 'UTF-8'); ?>"
                                ></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-b border-cream">
                <button type="button" class="w-full flex items-center justify-between py-4 text-left" data-filter-toggle="precio" data-default="open">
                    <span class="font-bold text-sm uppercase">Precio</span>
                    <svg data-chevron class="w-4 h-4 text-earth transition-transform duration-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="filter-content overflow-hidden" style="transition: max-height 0.3s ease;">
                    <div class="pb-4">
                        <p class="text-sm text-dark mb-4" id="price-range-display" data-price-display>
                            $<?php echo number_format($precioMinVal, 0, ',', '.'); ?> — $<?php echo number_format($precioMaxVal, 0, ',', '.'); ?>
                        </p>
                        <div class="price-slider-wrap" data-price-slider data-slider-max="<?php echo $precioSliderMax; ?>">
                            <div class="price-slider-track"></div>
                            <div class="price-slider-fill" data-price-fill></div>
                            <input
                                type="range"
                                min="0"
                                max="<?php echo $precioSliderMax; ?>"
                                step="500"
                                value="<?php echo $precioMinVal; ?>"
                                class="price-range-input"
                                data-price-min-range
                                aria-label="Precio mínimo"
                            >
                            <input
                                type="range"
                                min="0"
                                max="<?php echo $precioSliderMax; ?>"
                                step="500"
                                value="<?php echo $precioMaxVal; ?>"
                                class="price-range-input"
                                data-price-max-range
                                aria-label="Precio máximo"
                            >
                            <input type="hidden" name="precio_min" value="<?php echo $filters['precio_min'] !== null ? (int)$filters['precio_min'] : ''; ?>" data-price-min-input>
                            <input type="hidden" name="precio_max" value="<?php echo $filters['precio_max'] !== null ? (int)$filters['precio_max'] : ''; ?>" data-price-max-input>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-b border-cream">
                <button type="button" class="w-full flex items-center justify-between py-4 text-left" data-filter-toggle="categoria" data-default="closed">
                    <span class="font-bold text-sm uppercase">Categoría</span>
                    <svg data-chevron class="w-4 h-4 text-earth transition-transform duration-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="filter-content overflow-hidden" style="transition: max-height 0.3s ease;">
                    <div class="pb-4">
                        <ul class="space-y-2">
                            <li>
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input
                                        type="radio"
                                        name="categoria"
                                        value=""
                                        class="rounded border-cream text-dark focus:ring-primary"
                                        <?php echo $filters['categoria'] === '' ? 'checked' : ''; ?>
                                    >
                                    <span>Todas</span>
                                </label>
                            </li>
                            <?php foreach ($categorias as $cat): ?>
                            <?php $catSlug = (string)$cat['slug']; ?>
                            <li>
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input
                                        type="radio"
                                        name="categoria"
                                        value="<?php echo htmlspecialchars($catSlug, ENT_QUOTES, 'UTF-8'); ?>"
                                        class="rounded border-cream text-dark focus:ring-primary"
                                        <?php echo $filters['categoria'] === $catSlug ? 'checked' : ''; ?>
                                    >
                                    <span><?php echo htmlspecialchars((string)$cat['nombre'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </label>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <button type="submit" class="mt-4 w-full h-10 rounded-full bg-dark text-white text-sm font-bold hover:brightness-110 transition">
                Aplicar filtros
            </button>
        </form>
    </aside>

    <!-- Grid productos -->
    <div>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <p class="text-sm text-earth"><?php echo (int)$total; ?> producto<?php echo $total === 1 ? '' : 's'; ?></p>
            <form method="get" action="<?php echo page_path('catalogo'); ?>" class="flex items-center gap-2 text-sm">
                <input type="hidden" name="p" value="catalogo">
                <?php if ($filters['ofertas']): ?>
                <input type="hidden" name="ofertas" value="1">
                <?php endif; ?>
                <?php if ($filters['q'] !== ''): ?>
                <input type="hidden" name="q" value="<?php echo htmlspecialchars($filters['q'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                <?php if ($filters['edad'] !== ''): ?>
                <input type="hidden" name="edad" value="<?php echo htmlspecialchars($filters['edad'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                <?php if ($filters['categoria'] !== ''): ?>
                <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($filters['categoria'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                <?php foreach ($filters['talle'] as $t): ?>
                <input type="hidden" name="talle[]" value="<?php echo htmlspecialchars($t, ENT_QUOTES, 'UTF-8'); ?>">
                <?php endforeach; ?>
                <?php foreach ($filters['color'] as $c): ?>
                <input type="hidden" name="color[]" value="<?php echo (int)$c; ?>">
                <?php endforeach; ?>
                <label class="flex items-center gap-2">
                    Ordenar por:
                    <select
                        name="orden"
                        onchange="this.form.submit()"
                        class="border border-cream rounded px-2 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                        <?php foreach ($ordenOpciones as $val => $label): ?>
                        <option value="<?php echo htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $filters['orden'] === $val ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </form>
        </div>

        <!-- Filtros móvil -->
        <details class="lg:hidden mb-6 border border-cream rounded-lg">
            <summary class="px-4 py-3 text-sm font-bold cursor-pointer">Filtros</summary>
            <div class="px-4 pb-4">
                <?php /* Reutiliza el mismo form en móvil copiando estructura simplificada */ ?>
                <form method="get" action="<?php echo page_path('catalogo'); ?>">
                    <input type="hidden" name="p" value="catalogo">
                    <?php if ($filters['ofertas']): ?>
                    <input type="hidden" name="ofertas" value="1">
                    <?php endif; ?>
                    <?php if ($filters['q'] !== ''): ?>
                    <input type="hidden" name="q" value="<?php echo htmlspecialchars($filters['q'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endif; ?>
                    <?php if ($filters['edad'] !== ''): ?>
                    <input type="hidden" name="edad" value="<?php echo htmlspecialchars($filters['edad'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endif; ?>
                    <?php if ($filters['categoria'] !== ''): ?>
                    <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($filters['categoria'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endif; ?>
                    <p class="text-xs font-bold uppercase mt-2 mb-2">Talle</p>
                    <div class="grid grid-cols-4 gap-1.5 mb-4">
                        <?php foreach ($talles as $talle): ?>
                        <?php $tNombre = (string)$talle['nombre']; ?>
                        <label class="flex items-center gap-1 text-xs">
                            <input type="checkbox" name="talle[]" value="<?php echo htmlspecialchars($tNombre, ENT_QUOTES, 'UTF-8'); ?>" <?php echo in_array($tNombre, $filters['talle'], true) ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars($tNombre, ENT_QUOTES, 'UTF-8'); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs font-bold uppercase mb-2">Color</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <?php foreach ($colores as $color): ?>
                        <label class="flex items-center gap-1 text-xs">
                            <input type="checkbox" name="color[]" value="<?php echo (int)$color['id_color']; ?>" <?php echo in_array((int)$color['id_color'], $filters['color'], true) ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars((string)$color['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs font-bold uppercase mb-2">Precio</p>
                    <p class="text-sm text-dark mb-3" data-price-display-mobile>
                        $<?php echo number_format($precioMinVal, 0, ',', '.'); ?> — $<?php echo number_format($precioMaxVal, 0, ',', '.'); ?>
                    </p>
                    <div class="price-slider-wrap mb-4" data-price-slider data-slider-max="<?php echo $precioSliderMax; ?>">
                        <div class="price-slider-track"></div>
                        <div class="price-slider-fill" data-price-fill></div>
                        <input
                            type="range"
                            min="0"
                            max="<?php echo $precioSliderMax; ?>"
                            step="500"
                            value="<?php echo $precioMinVal; ?>"
                            class="price-range-input"
                            data-price-min-range
                            aria-label="Precio mínimo"
                        >
                        <input
                            type="range"
                            min="0"
                            max="<?php echo $precioSliderMax; ?>"
                            step="500"
                            value="<?php echo $precioMaxVal; ?>"
                            class="price-range-input"
                            data-price-max-range
                            aria-label="Precio máximo"
                        >
                        <input type="hidden" name="precio_min" value="<?php echo $filters['precio_min'] !== null ? (int)$filters['precio_min'] : ''; ?>" data-price-min-input>
                        <input type="hidden" name="precio_max" value="<?php echo $filters['precio_max'] !== null ? (int)$filters['precio_max'] : ''; ?>" data-price-max-input>
                    </div>
                    <button type="submit" class="w-full h-10 rounded-full bg-dark text-white text-sm font-bold">Aplicar</button>
                </form>
            </div>
        </details>

        <?php if (empty($productos)): ?>
        <p class="text-center text-earth py-16">No encontramos productos con esos filtros.</p>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-6">
            <?php foreach ($productos as $producto): ?>
            <?php include __DIR__ . '/../partials/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
        <nav class="flex items-center justify-center gap-2 mt-12 text-sm" aria-label="Paginación">
            <?php if ($page > 1): ?>
            <a href="<?php echo htmlspecialchars(catalog_page_url($filters, $page - 1), ENT_QUOTES, 'UTF-8'); ?>" class="h-9 w-9 grid place-items-center border border-cream rounded hover:border-dark transition" aria-label="Página anterior">‹</a>
            <?php endif; ?>

            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($n = $start; $n <= $end; $n++):
            ?>
            <a
                href="<?php echo htmlspecialchars(catalog_page_url($filters, $n), ENT_QUOTES, 'UTF-8'); ?>"
                class="h-9 w-9 grid place-items-center rounded font-bold <?php echo $n === $page ? 'bg-dark text-white' : 'border border-cream hover:border-dark'; ?> transition"
                <?php echo $n === $page ? 'aria-current="page"' : ''; ?>
            ><?php echo $n; ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
            <a href="<?php echo htmlspecialchars(catalog_page_url($filters, $page + 1), ENT_QUOTES, 'UTF-8'); ?>" class="h-9 w-9 grid place-items-center border border-cream rounded hover:border-dark transition" aria-label="Página siguiente">›</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>
    </div>
</div>

<style>
.price-slider-wrap {
    position: relative;
    height: 28px;
    margin-top: 4px;
}
.price-slider-track {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 4px;
    transform: translateY(-50%);
    background: #e5e5e5;
    border-radius: 9999px;
}
.price-slider-fill {
    position: absolute;
    top: 50%;
    height: 4px;
    transform: translateY(-50%);
    background: #FAAF7D;
    border-radius: 9999px;
    left: 0%;
    width: 100%;
}
.price-range-input {
    position: absolute;
    width: 100%;
    top: 50%;
    transform: translateY(-50%);
    margin: 0;
    pointer-events: none;
    -webkit-appearance: none;
    appearance: none;
    background: transparent;
    height: 28px;
}
.price-range-input::-webkit-slider-runnable-track {
    -webkit-appearance: none;
    background: transparent;
    height: 4px;
}
.price-range-input::-moz-range-track {
    background: transparent;
    height: 4px;
    border: none;
}
.price-range-input::-webkit-slider-thumb {
    -webkit-appearance: none;
    pointer-events: all;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #FAAF7D;
    cursor: pointer;
    margin-top: -7px;
}
.price-range-input::-moz-range-thumb {
    pointer-events: all;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #FAAF7D;
    cursor: pointer;
}
.price-range-input[data-price-max-range] {
    z-index: 2;
}
.price-range-input[data-price-min-range] {
    z-index: 3;
}
</style>

<script>
(function () {
    function formatPrice(n) {
        return '$' + n.toLocaleString('es-AR');
    }

    function initPriceSlider(slider) {
        var sliderMax = parseInt(slider.getAttribute('data-slider-max'), 10) || 50000;
        var minRange = slider.querySelector('[data-price-min-range]');
        var maxRange = slider.querySelector('[data-price-max-range]');
        var minInput = slider.querySelector('[data-price-min-input]');
        var maxInput = slider.querySelector('[data-price-max-input]');
        var fill = slider.querySelector('[data-price-fill]');
        var form = slider.closest('form');
        if (!minRange || !maxRange || !minInput || !maxInput || !fill) return;

        var display = form.querySelector('[data-price-display]') || form.querySelector('[data-price-display-mobile]');

        function updateUI() {
            var minVal = parseInt(minRange.value, 10);
            var maxVal = parseInt(maxRange.value, 10);
            var step = parseInt(minRange.step, 10) || 500;

            if (minVal > maxVal - step) {
                if (minRange === document.activeElement) {
                    minVal = maxVal - step;
                    minRange.value = minVal;
                } else {
                    maxVal = minVal + step;
                    maxRange.value = maxVal;
                }
            }

            var pctMin = (minVal / sliderMax) * 100;
            var pctMax = (maxVal / sliderMax) * 100;
            fill.style.left = pctMin + '%';
            fill.style.width = (pctMax - pctMin) + '%';

            if (display) {
                display.textContent = formatPrice(minVal) + ' — ' + formatPrice(maxVal);
            }

            minInput.value = minVal > 0 ? String(minVal) : '';
            maxInput.value = maxVal < sliderMax ? String(maxVal) : '';
        }

        function onRelease() {
            updateUI();
            if (form) {
                form.submit();
            }
        }

        minRange.addEventListener('input', updateUI);
        maxRange.addEventListener('input', updateUI);
        minRange.addEventListener('change', onRelease);
        maxRange.addEventListener('change', onRelease);

        updateUI();
    }

    document.querySelectorAll('[data-price-slider]').forEach(initPriceSlider);
})();

(function () {
    document.querySelectorAll('[data-filter-toggle]').forEach(function (toggle) {
        var key = 'filter_' + toggle.dataset.filterToggle;
        var content = toggle.nextElementSibling;
        var chevron = toggle.querySelector('[data-chevron]');
        if (!content || !chevron) return;

        var defaultState = toggle.getAttribute('data-default') || 'open';
        var stored = localStorage.getItem(key);
        var isOpen = stored !== null ? stored !== 'closed' : defaultState !== 'closed';

        content.style.transition = 'max-height 0.3s ease';

        if (!isOpen) {
            content.style.maxHeight = '0';
            content.style.overflow = 'hidden';
            chevron.style.transform = 'rotate(0deg)';
        } else {
            content.style.maxHeight = content.scrollHeight + 'px';
            chevron.style.transform = 'rotate(180deg)';
        }

        toggle.addEventListener('click', function () {
            var open = content.style.maxHeight !== '0' && content.style.maxHeight !== '0px' && content.style.maxHeight !== '';
            if (open) {
                content.style.maxHeight = '0';
                content.style.overflow = 'hidden';
                chevron.style.transform = 'rotate(0deg)';
                localStorage.setItem(key, 'closed');
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.overflow = 'hidden';
                chevron.style.transform = 'rotate(180deg)';
                localStorage.setItem(key, 'open');
            }
        });
    });
})();

document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-action="quick-add"]');
    if (!btn || !window.YofiCart) return;
    e.preventDefault();
    var result = window.YofiCart.addToCart({
        id_sku: btn.getAttribute('data-id-sku'),
        id_prod: btn.getAttribute('data-id-prod'),
        nombre: btn.getAttribute('data-nombre'),
        precio: btn.getAttribute('data-precio'),
        imagen: btn.getAttribute('data-imagen'),
        color_nombre: btn.getAttribute('data-color-nombre'),
        color_hex: btn.getAttribute('data-color-hex'),
        talle_nombre: btn.getAttribute('data-talle-nombre'),
        cantidad: 1
    });
    if (result.success && window.YofiCart.openDrawer) {
        window.YofiCart.openDrawer();
    }
});
</script>
