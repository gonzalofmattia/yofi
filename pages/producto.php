<?php
require_once __DIR__ . '/../src/php/products.php';

$slug = trim((string)($_GET['slug'] ?? ''));
$producto = $slug !== '' ? get_product_by_slug($slug) : null;

if (!$producto) {
    http_response_code(404);
    echo '<div class="max-w-7xl mx-auto px-4 py-32 text-center"><h1 class="text-3xl font-extrabold">Producto no encontrado</h1><a href="' . page_path('home') . '" class="mt-6 inline-block underline">Volver al inicio</a></div>';
    return;
}

$idProd = (int)$producto['id_prod'];
$idColor = isset($_GET['color']) ? (int)$_GET['color'] : 0;

if ($idColor <= 0) {
    $defaultColor = get_default_product_color($idProd);
    if ($defaultColor !== null) {
        header('Location: ' . product_url($slug, $defaultColor));
        exit;
    }
    http_response_code(404);
    echo '<div class="max-w-7xl mx-auto px-4 py-32 text-center"><h1 class="text-3xl font-extrabold">Producto sin variantes disponibles</h1><a href="' . page_path('catalogo') . '" class="mt-6 inline-block underline">Ver catálogo</a></div>';
    return;
}

if (!product_has_color($idProd, $idColor)) {
    $defaultColor = get_default_product_color($idProd);
    if ($defaultColor !== null && $defaultColor !== $idColor) {
        header('Location: ' . product_url($slug, $defaultColor));
        exit;
    }
    http_response_code(404);
    echo '<div class="max-w-7xl mx-auto px-4 py-32 text-center"><h1 class="text-3xl font-extrabold">Color no disponible</h1><a href="' . product_url($slug, $defaultColor ?? 0) . '" class="mt-6 inline-block underline">Ver color disponible</a></div>';
    return;
}

$skus = get_product_skus($idProd, $idColor);
$coloresProducto = get_product_colors($idProd);
$colorVariants = get_product_color_variants($idProd);
$related = get_related_products($idProd, (int)$producto['id_cate'], 4);

$precioBase = (float)$producto['precio_base'];
$precioOferta = isset($producto['precio_oferta']) && $producto['precio_oferta'] !== null && $producto['precio_oferta'] !== ''
    ? (float)$producto['precio_oferta']
    : null;
$tieneOferta = $precioOferta !== null && $precioOferta > 0 && $precioOferta < $precioBase;
$precioFinal = $tieneOferta ? $precioOferta : $precioBase;

$colorActual = null;
$tallesMap = [];
$skuByTalle = [];

foreach ($skus as $sku) {
    if ($colorActual === null) {
        $colorActual = [
            'id_color' => (int)$sku['id_color'],
            'nombre' => (string)$sku['color_nombre'],
            'hex_code' => (string)$sku['color_hex'],
        ];
    }
    $idTalle = (int)$sku['id_talle'];
    $tallesMap[$idTalle] = [
        'id_talle' => $idTalle,
        'nombre' => (string)$sku['talle_nombre'],
        'orden' => (int)$sku['talle_orden'],
    ];
    $skuByTalle[$idTalle] = [
        'id_sku' => (int)$sku['id_sku'],
        'stock' => (int)$sku['stock'],
        'precio_extra' => (float)$sku['precio_extra'],
    ];
}

uasort($tallesMap, static fn($a, $b) => $a['orden'] <=> $b['orden']);

$imagenes = get_product_images($idProd, $idColor);
if (empty($imagenes)) {
    $imagenes = [['path' => imgprod_path('placeholder.jpg'), 'es_principal' => true, 'id_imagen' => 0]];
}

$colorNombre = $colorActual['nombre'] ?? '';
$page_title = (string)$producto['nombre'] . ($colorNombre !== '' ? ' — ' . $colorNombre : '') . ' | ' . SITE_NAME;
$meta_description = !empty($producto['descripcion'])
    ? mb_substr(strip_tags((string)$producto['descripcion']), 0, 160)
    : SITE_DESCRIPTION;

$tabs = [
    'descripcion' => (string)($producto['descripcion'] ?? 'Prenda romántica de algodón premium, confeccionada con suaves acabados y atención al detalle.'),
    'composicion' => (string)($producto['composicion'] ?? 'Consultá la etiqueta del producto para la composición exacta.'),
    'cuidados' => (string)($producto['cuidados'] ?? 'Lavar a máquina con agua fría. No usar lavandina. Planchar a baja temperatura.'),
    'envio' => 'Envío a todo el país en 2 a 7 días hábiles. Envío gratis en compras superiores a $80.000.',
];

$basePath = BASE_PATH !== '' ? rtrim(BASE_PATH, '/') : '';
$stockApiUrl = $basePath . '/public/api/stock/check.php';
$zipnovaApiUrl = $basePath . '/public/api/zipnova/cotizar.php';
$variantsJson = json_encode($colorVariants, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
$imagesByColor = [];
foreach ($coloresProducto as $c) {
    $imgs = get_product_images($idProd, (int)$c['id_color']);
    $imagesByColor[(int)$c['id_color']] = !empty($imgs)
        ? array_column($imgs, 'path')
        : [imgprod_path('placeholder.jpg')];
}
$imagesByColorJson = json_encode($imagesByColor, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>

<div class="px-4 sm:px-8 pt-6 text-xs text-earth">
    <a href="<?php echo page_path('home'); ?>" class="hover:text-dark">Inicio</a>
    <span class="mx-1">›</span>
    <a href="<?php echo page_path('catalogo'); ?>" class="hover:text-dark">Catálogo</a>
    <span class="mx-1">›</span>
    <span class="text-dark font-semibold"><?php echo htmlspecialchars((string)$producto['nombre'], ENT_QUOTES, 'UTF-8'); ?></span>
    <?php if ($colorNombre !== ''): ?>
    <span class="mx-1">›</span>
    <span class="text-dark font-semibold"><?php echo htmlspecialchars($colorNombre, ENT_QUOTES, 'UTF-8'); ?></span>
    <?php endif; ?>
</div>

<div
    class="grid lg:grid-cols-[55fr_45fr] gap-8 lg:gap-12 px-4 sm:px-8 py-8 max-w-[1500px] mx-auto"
    data-component="product-detail"
    data-product-id="<?php echo $idProd; ?>"
    data-color-id="<?php echo $idColor; ?>"
    data-product-nombre="<?php echo htmlspecialchars((string)$producto['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
    data-color-nombre="<?php echo htmlspecialchars($colorNombre, ENT_QUOTES, 'UTF-8'); ?>"
    data-color-hex="<?php echo htmlspecialchars($colorActual['hex_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
    data-precio-base="<?php echo $precioFinal; ?>"
    data-stock-api="<?php echo htmlspecialchars($stockApiUrl, ENT_QUOTES, 'UTF-8'); ?>"
    data-product-slug="<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>"
    data-color-variants="<?php echo htmlspecialchars($variantsJson, ENT_QUOTES, 'UTF-8'); ?>"
    data-images-by-color="<?php echo htmlspecialchars($imagesByColorJson, ENT_QUOTES, 'UTF-8'); ?>"
    data-sku-by-talle="<?php echo htmlspecialchars(json_encode($skuByTalle, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"
>
    <!-- Galería -->
    <div class="grid grid-cols-[80px_1fr] gap-3">
        <div class="hidden lg:flex flex-col gap-2" data-gallery-thumbs>
            <?php foreach ($imagenes as $i => $img): ?>
            <button
                type="button"
                class="aspect-[3/4] overflow-hidden border <?php echo $i === 0 ? 'border-dark' : 'border-cream hover:border-dark'; ?> transition"
                data-gallery-thumb="<?php echo $i; ?>"
                data-image="<?php echo htmlspecialchars((string)$img['path'], ENT_QUOTES, 'UTF-8'); ?>"
                aria-label="Ver imagen <?php echo $i + 1; ?>"
            >
                <img src="<?php echo htmlspecialchars((string)$img['path'], ENT_QUOTES, 'UTF-8'); ?>" alt="" class="w-full h-full object-cover">
            </button>
            <?php endforeach; ?>
        </div>
        <div class="aspect-[3/4] overflow-hidden bg-[#f6f3ef] col-span-2 lg:col-auto">
            <img
                src="<?php echo htmlspecialchars((string)$imagenes[0]['path'], ENT_QUOTES, 'UTF-8'); ?>"
                alt="<?php echo htmlspecialchars((string)$producto['nombre'] . ($colorNombre !== '' ? ' — ' . $colorNombre : ''), ENT_QUOTES, 'UTF-8'); ?>"
                class="w-full h-full object-cover transition-transform duration-700 ease-out hover:scale-[1.04]"
                data-gallery-main
            >
        </div>
    </div>

    <!-- Info -->
    <div class="lg:sticky lg:top-32 self-start space-y-6">
        <div>
            <p class="uppercase tracking-[0.18em] text-xs font-semibold text-earth"><?php echo SITE_NAME; ?></p>
            <h1 class="text-3xl sm:text-4xl font-extrabold mt-2 leading-tight"><?php echo htmlspecialchars((string)$producto['nombre'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <?php if ($colorNombre !== ''): ?>
            <p class="mt-1 text-sm text-earth" data-color-label><?php echo htmlspecialchars($colorNombre, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <div class="mt-3 flex items-baseline gap-3">
                <?php if ($tieneOferta): ?>
                <span class="text-base line-through text-earth"><?php echo format_price($precioBase); ?></span>
                <span class="text-3xl font-extrabold text-accent"><?php echo format_price($precioOferta); ?></span>
                <?php else: ?>
                <span class="text-3xl font-extrabold text-primary"><?php echo format_price($precioFinal); ?></span>
                <?php endif; ?>
            </div>
            <p class="mt-1 text-xs text-earth">3 cuotas sin interés de <?php echo format_price((int)round($precioFinal / 3)); ?></p>
            <?php if (!empty($producto['promo_badge'])): ?>
            <span class="inline-block mt-2 bg-accent text-white text-[10px] font-bold tracking-wider px-2.5 py-1 rounded-full">
                <?php echo htmlspecialchars((string)$producto['promo_badge'], ENT_QUOTES, 'UTF-8'); ?>
            </span>
            <?php endif; ?>
        </div>

        <?php if (count($coloresProducto) > 0): ?>
        <div>
            <p class="text-sm font-semibold mb-3">Color: <span class="text-earth font-normal" data-color-label-inline><?php echo htmlspecialchars($colorNombre, ENT_QUOTES, 'UTF-8'); ?></span></p>
            <div class="flex gap-2 flex-wrap" role="group" aria-label="Seleccionar color" data-color-group>
                <?php foreach ($coloresProducto as $colorOpt): ?>
                <?php
                    $optId = (int)$colorOpt['id_color'];
                    $isActive = $optId === $idColor;
                    $hex = (string)$colorOpt['hex_code'];
                ?>
                <button
                    type="button"
                    class="h-9 w-9 rounded-full border-2 transition shrink-0 <?php echo $isActive ? 'ring-2 ring-primary ring-offset-2 border-dark' : 'border-cream hover:border-dark'; ?>"
                    style="background: <?php echo htmlspecialchars($hex, ENT_QUOTES, 'UTF-8'); ?>;"
                    data-color-id="<?php echo $optId; ?>"
                    data-color-nombre="<?php echo htmlspecialchars((string)$colorOpt['color_nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                    data-color-hex="<?php echo htmlspecialchars($hex, ENT_QUOTES, 'UTF-8'); ?>"
                    title="<?php echo htmlspecialchars((string)$colorOpt['color_nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                    aria-label="<?php echo htmlspecialchars((string)$colorOpt['color_nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                    aria-pressed="<?php echo $isActive ? 'true' : 'false'; ?>"
                ></button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($tallesMap)): ?>
        <div>
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold">Talle:</p>
                <button type="button" class="text-xs underline text-earth hover:text-dark">Guía de talles</button>
            </div>
            <div class="flex flex-wrap gap-2" role="group" aria-label="Seleccionar talle" data-talle-group>
                <?php foreach ($tallesMap as $talle): ?>
                <?php
                    $skuInfo = $skuByTalle[(int)$talle['id_talle']] ?? null;
                    $sinStock = !$skuInfo || (int)$skuInfo['stock'] <= 0;
                ?>
                <button
                    type="button"
                    class="relative h-10 min-w-12 px-3 rounded-full border-2 text-sm font-semibold transition border-cream text-earth hover:border-dark talle-btn <?php echo $sinStock ? 'line-through opacity-50 cursor-not-allowed' : ''; ?>"
                    data-talle-id="<?php echo (int)$talle['id_talle']; ?>"
                    data-talle-nombre="<?php echo htmlspecialchars($talle['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                    <?php echo $sinStock ? 'disabled' : ''; ?>
                >
                    <?php echo htmlspecialchars($talle['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                </button>
                <?php endforeach; ?>
            </div>
            <p class="mt-2 text-xs text-accent hidden" data-stock-message role="status"></p>
        </div>
        <?php endif; ?>

        <div class="space-y-3 pt-2">
            <button
                type="button"
                class="w-full h-12 rounded-full bg-primary text-dark font-extrabold text-sm tracking-wide hover:brightness-95 transition disabled:opacity-50 disabled:cursor-not-allowed"
                data-action="add-to-cart"
                disabled
            >
                Agregar al carrito
            </button>
            <button type="button" class="w-full h-11 rounded-full border-2 border-dark text-dark font-bold text-sm flex items-center justify-center gap-2 hover:bg-dark hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                Agregar a favoritos
            </button>
        </div>

        <div class="bg-cream rounded-lg px-4 py-3 space-y-3" data-shipping-section>
            <div class="flex items-center gap-2 text-sm text-dark/80">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10m10 0h4l3-3V9a1 1 0 00-1-1h-4"/></svg>
                <span>Envío a todo el país</span>
            </div>
            <div class="flex gap-2">
                <input
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="8"
                    placeholder="Código postal"
                    class="flex-1 h-10 px-3 rounded-full bg-white border border-cream text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                    data-shipping-cp
                >
                <button
                    type="button"
                    class="h-10 px-4 rounded-full bg-dark text-white text-xs font-bold hover:brightness-110 transition"
                    data-action="calc-shipping"
                    data-api-url="<?php echo htmlspecialchars($zipnovaApiUrl, ENT_QUOTES, 'UTF-8'); ?>"
                >
                    Calcular
                </button>
            </div>
            <p class="text-xs text-earth hidden" data-shipping-result role="status"></p>
        </div>

        <div data-product-tabs>
            <div class="flex border-b border-cream" role="tablist">
                <?php $tabLabels = ['Descripción', 'Composición', 'Cuidados', 'Envío']; ?>
                <?php $tabKeys = array_keys($tabs); ?>
                <?php foreach ($tabLabels as $i => $label): ?>
                <button
                    type="button"
                    role="tab"
                    class="px-4 py-3 text-sm font-bold border-b-2 -mb-px transition <?php echo $i === 0 ? 'border-primary text-dark' : 'border-transparent text-earth'; ?>"
                    data-product-tab="<?php echo $i; ?>"
                    aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                >
                    <?php echo $label; ?>
                </button>
                <?php endforeach; ?>
            </div>
            <div class="py-4 text-sm text-dark/80 leading-relaxed">
                <?php foreach ($tabKeys as $i => $key): ?>
                <div data-product-tab-panel="<?php echo $i; ?>" class="<?php echo $i === 0 ? '' : 'hidden'; ?>">
                    <?php echo nl2br(htmlspecialchars($tabs[$key], ENT_QUOTES, 'UTF-8')); ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($related)): ?>
<section class="px-4 sm:px-8 py-12">
    <h2 class="text-2xl sm:text-3xl font-extrabold text-center mb-8">También te puede gustar</h2>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <?php foreach ($related as $producto): ?>
        <?php include __DIR__ . '/../partials/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<script>
(function () {
    var root = document.querySelector('[data-component="product-detail"]');
    if (!root) return;

    var colorVariants = JSON.parse(root.getAttribute('data-color-variants') || '[]');
    var imagesByColor = JSON.parse(root.getAttribute('data-images-by-color') || '{}');
    var stockApi = root.getAttribute('data-stock-api') || '';
    var productSlug = root.getAttribute('data-product-slug') || '';
    var productNombre = root.getAttribute('data-product-nombre') || '';
    var precioBase = parseFloat(root.getAttribute('data-precio-base') || '0');
    var productId = root.getAttribute('data-product-id') || '';
    var activeColorId = parseInt(root.getAttribute('data-color-id') || '0', 10);

    var selectedTalleId = null;
    var selectedSku = null;
    var skuByTalle = {};

    var mainImg = root.querySelector('[data-gallery-main]');
    var thumbsWrap = root.querySelector('[data-gallery-thumbs]');
    var addBtn = root.querySelector('[data-action="add-to-cart"]');
    var stockMsg = root.querySelector('[data-stock-message]');
    var talleGroup = root.querySelector('[data-talle-group]');
    var colorLabel = root.querySelector('[data-color-label]');
    var colorLabelInline = root.querySelector('[data-color-label-inline]');

    var colorNombre = root.getAttribute('data-color-nombre') || '';
    var colorHex = root.getAttribute('data-color-hex') || '';

    function findVariant(colorId) {
        return colorVariants.find(function (v) { return parseInt(v.id_color, 10) === colorId; }) || null;
    }

    function rebuildSkuMap(variant) {
        skuByTalle = {};
        if (!variant || !variant.talles) return;
        variant.talles.forEach(function (t) {
            skuByTalle[String(t.id_talle)] = {
                id_sku: t.id_sku,
                stock: t.stock,
                precio_extra: t.precio_extra || 0
            };
        });
    }

    function renderGallery(images) {
        if (!images || !images.length) {
            images = [mainImg ? mainImg.src : ''];
        }
        if (mainImg) {
            mainImg.src = images[0];
            mainImg.alt = productNombre + (colorNombre ? ' — ' + colorNombre : '');
        }
        if (!thumbsWrap) return;
        thumbsWrap.innerHTML = '';
        images.forEach(function (src, i) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'aspect-[3/4] overflow-hidden border ' + (i === 0 ? 'border-dark' : 'border-cream hover:border-dark') + ' transition';
            btn.setAttribute('data-gallery-thumb', String(i));
            btn.setAttribute('data-image', src);
            btn.setAttribute('aria-label', 'Ver imagen ' + (i + 1));
            var img = document.createElement('img');
            img.src = src;
            img.alt = '';
            img.className = 'w-full h-full object-cover';
            btn.appendChild(img);
            btn.addEventListener('click', function () {
                if (mainImg) mainImg.src = src;
                thumbsWrap.querySelectorAll('[data-gallery-thumb]').forEach(function (b) {
                    b.classList.remove('border-dark');
                    b.classList.add('border-cream');
                });
                btn.classList.add('border-dark');
                btn.classList.remove('border-cream');
            });
            thumbsWrap.appendChild(btn);
        });
    }

    function renderTalles(variant) {
        if (!talleGroup) return;
        talleGroup.innerHTML = '';
        selectedTalleId = null;
        selectedSku = null;
        if (addBtn) addBtn.disabled = true;
        if (stockMsg) stockMsg.classList.add('hidden');

        if (!variant || !variant.talles || !variant.talles.length) return;

        variant.talles.forEach(function (t) {
            var sinStock = parseInt(t.stock, 10) <= 0;
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'relative h-10 min-w-12 px-3 rounded-full border-2 text-sm font-semibold transition border-cream text-earth hover:border-dark talle-btn' +
                (sinStock ? ' line-through opacity-50 cursor-not-allowed' : '');
            btn.setAttribute('data-talle-id', String(t.id_talle));
            btn.setAttribute('data-talle-nombre', t.nombre);
            btn.textContent = t.nombre;
            if (sinStock) btn.disabled = true;
            btn.addEventListener('click', function () {
                if (btn.disabled) return;
                selectTalle(btn.getAttribute('data-talle-id'));
            });
            talleGroup.appendChild(btn);
        });
    }

    function updateColorUi(colorId, variant) {
        activeColorId = colorId;
        colorNombre = variant ? variant.color_nombre : '';
        colorHex = variant ? variant.hex_code : '';
        root.setAttribute('data-color-id', String(colorId));
        root.setAttribute('data-color-nombre', colorNombre);
        root.setAttribute('data-color-hex', colorHex);

        if (colorLabel) colorLabel.textContent = colorNombre;
        if (colorLabelInline) colorLabelInline.textContent = colorNombre;

        root.querySelectorAll('[data-color-id]').forEach(function (btn) {
            var isActive = parseInt(btn.getAttribute('data-color-id'), 10) === colorId;
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            btn.classList.toggle('ring-2', isActive);
            btn.classList.toggle('ring-primary', isActive);
            btn.classList.toggle('ring-offset-2', isActive);
            btn.classList.toggle('border-dark', isActive);
            btn.classList.toggle('border-cream', !isActive);
        });

        rebuildSkuMap(variant);
        renderGallery(imagesByColor[String(colorId)] || imagesByColor[colorId]);
        renderTalles(variant);

        if (window.history && window.history.pushState && productSlug) {
            var url = 'index.php?p=producto&slug=' + encodeURIComponent(productSlug) + '&color=' + colorId;
            window.history.pushState({ colorId: colorId }, '', url);
        }
    }

    function selectTalle(talleId) {
        var sku = skuByTalle[String(talleId)] || skuByTalle[talleId];
        if (!sku || parseInt(sku.stock, 10) <= 0) return;

        selectedTalleId = String(talleId);
        selectedSku = sku;

        root.querySelectorAll('[data-talle-id]').forEach(function (btn) {
            var active = btn.getAttribute('data-talle-id') === selectedTalleId;
            btn.classList.toggle('border-dark', active);
            btn.classList.toggle('bg-dark', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('border-cream', !active);
        });

        if (addBtn) addBtn.disabled = false;

        if (stockApi && sku.id_sku) {
            fetch(stockApi + '?id_sku=' + encodeURIComponent(sku.id_sku))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!stockMsg) return;
                    var stock = parseInt(data.stock, 10);
                    if (stock > 0 && stock <= 3) {
                        stockMsg.textContent = '¡Últimas ' + stock + ' unidades!';
                        stockMsg.classList.remove('hidden');
                    } else if (stock <= 0) {
                        stockMsg.textContent = 'Sin stock para este talle';
                        stockMsg.classList.remove('hidden');
                        if (addBtn) addBtn.disabled = true;
                    } else {
                        stockMsg.classList.add('hidden');
                    }
                })
                .catch(function () {});
        }
    }

    root.querySelectorAll('[data-color-group] [data-color-id]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var colorId = parseInt(btn.getAttribute('data-color-id'), 10);
            if (!colorId || colorId === activeColorId) return;
            var variant = findVariant(colorId);
            if (!variant) return;
            updateColorUi(colorId, variant);
        });
    });

    root.querySelectorAll('[data-gallery-thumb]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var src = btn.getAttribute('data-image');
            if (mainImg && src) mainImg.src = src;
            root.querySelectorAll('[data-gallery-thumb]').forEach(function (b) {
                b.classList.remove('border-dark');
                b.classList.add('border-cream');
            });
            btn.classList.add('border-dark');
            btn.classList.remove('border-cream');
        });
    });

    root.querySelectorAll('[data-talle-id]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (btn.disabled) return;
            selectTalle(btn.getAttribute('data-talle-id'));
        });
    });

    rebuildSkuMap(findVariant(activeColorId));

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            if (!selectedSku || !window.YofiCart) return;
            var talleBtn = root.querySelector('[data-talle-id].bg-dark');
            var result = window.YofiCart.addToCart({
                id_sku: selectedSku.id_sku,
                id_prod: productId,
                nombre: productNombre,
                precio: precioBase + (parseFloat(selectedSku.precio_extra) || 0),
                imagen: mainImg ? mainImg.src : '',
                color_nombre: colorNombre,
                color_hex: colorHex,
                talle_nombre: talleBtn ? talleBtn.getAttribute('data-talle-nombre') : '',
                cantidad: 1
            });
            if (result.success && window.YofiCart.openDrawer) {
                window.YofiCart.openDrawer();
            }
        });
    }

    root.querySelectorAll('[data-product-tab]').forEach(function (tab) {
        tab.addEventListener('click', function () {
            var idx = tab.getAttribute('data-product-tab');
            root.querySelectorAll('[data-product-tab]').forEach(function (t) {
                var active = t.getAttribute('data-product-tab') === idx;
                t.setAttribute('aria-selected', active ? 'true' : 'false');
                t.classList.toggle('border-primary', active);
                t.classList.toggle('text-dark', active);
                t.classList.toggle('border-transparent', !active);
                t.classList.toggle('text-earth', !active);
            });
            root.querySelectorAll('[data-product-tab-panel]').forEach(function (p) {
                p.classList.toggle('hidden', p.getAttribute('data-product-tab-panel') !== idx);
            });
        });
    });

    var calcBtn = root.querySelector('[data-action="calc-shipping"]');
    var cpInput = root.querySelector('[data-shipping-cp]');
    var shipResult = root.querySelector('[data-shipping-result]');
    if (calcBtn && cpInput) {
        calcBtn.addEventListener('click', function () {
            var cp = (cpInput.value || '').trim();
            var apiUrl = calcBtn.getAttribute('data-api-url') || '';
            if (!cp || !apiUrl) return;
            shipResult.classList.remove('hidden');
            shipResult.textContent = 'Calculando envío...';
            fetch(apiUrl + '?cp=' + encodeURIComponent(cp))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.error) {
                        shipResult.textContent = data.error;
                        return;
                    }
                    var costo = data.costo != null ? data.costo : data.price;
                    var eta = data.eta || data.plazo || '';
                    shipResult.textContent = costo != null
                        ? 'Envío: $' + Number(costo).toLocaleString('es-AR') + (eta ? ' · ' + eta : '')
                        : 'Consultá opciones de envío disponibles.';
                })
                .catch(function () {
                    shipResult.textContent = 'No pudimos calcular el envío. Intentá de nuevo.';
                });
        });
    }

    window.addEventListener('popstate', function (e) {
        var colorId = e.state && e.state.colorId ? parseInt(e.state.colorId, 10) : activeColorId;
        var variant = findVariant(colorId);
        if (variant) updateColorUi(colorId, variant);
    });
})();
</script>

<script>
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
