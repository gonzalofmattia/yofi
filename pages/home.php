<?php
require_once __DIR__ . '/../src/php/products.php';

$page_title = SITE_NAME . ' — Nueva colección Otoño Invierno 2025';
$meta_description = 'Yofi: ropa romántica para chicos y chicas. Nueva colección Otoño Invierno 2025. Envío a todo el país, cuotas sin interés.';

$categoriasPadre = get_parent_categories(3);
$destacados = get_featured_products(5);

$ageTabs = [
    ['label' => 'MINI', 'slug' => 'mini'],
    ['label' => '1 A 4 AÑOS', 'slug' => 'ninas'],
    ['label' => '4 A 12 AÑOS', 'slug' => 'ninos'],
];

$subcategoriasPorTab = [];
foreach ($ageTabs as $tab) {
    $cat = get_category_by_slug($tab['slug']);
    $subs = $cat ? get_subcategories((int)$cat['id_cate'], 4) : [];
    $subcategoriasPorTab[$tab['slug']] = $subs;
}

$fallbackCards = [
    ['label' => 'Abrigos', 'slug' => 'abrigos'],
    ['label' => 'Buzos y Cardigans', 'slug' => 'buzos'],
    ['label' => 'Pantalones', 'slug' => 'pantalones'],
    ['label' => 'Remeras', 'slug' => 'remeras'],
];
?>

<!-- Hero -->
<section class="relative w-full h-[88vh] md:h-[92vh] overflow-hidden bg-cream">
    <img
        src="<?php echo htmlspecialchars(imgprod_path('hero-principal.jpg'), ENT_QUOTES, 'UTF-8'); ?>"
        alt="Nueva colección Otoño Invierno"
        class="absolute inset-0 h-full w-full object-cover"
    >
    <div class="absolute inset-0 bg-gradient-to-r from-white/85 via-white/40 to-transparent"></div>
    <div class="relative z-10 h-full flex items-center px-6 sm:px-16 lg:px-24">
        <div class="max-w-xl">
            <p class="uppercase tracking-[0.18em] text-xs font-semibold text-earth mb-4">Nueva colección</p>
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-[0.95] text-dark">
                Otoño<br>Invierno<br><span class="text-accent">2025</span>
            </h1>
            <p class="mt-6 text-base sm:text-lg text-dark/80 max-w-md">
                Prendas románticas, suaves y resistentes, pensadas para acompañar a tus chicos cada día.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a
                    href="<?php echo page_path('catalogo'); ?>&categoria=novedades"
                    class="inline-flex items-center justify-center h-12 px-7 rounded-full bg-primary text-dark font-bold text-sm tracking-wide hover:brightness-95 transition"
                >
                    Ver colección
                </a>
                <a
                    href="<?php echo page_path('catalogo'); ?>"
                    class="inline-flex items-center justify-center h-12 px-7 rounded-full border-2 border-dark text-dark font-bold text-sm tracking-wide hover:bg-dark hover:text-white transition"
                >
                    Ver todo
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Benefits strip -->
<section class="bg-dark text-white">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-y-6 px-6 sm:px-12 py-8">
        <div class="flex items-center gap-3 justify-center md:justify-start">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <div class="min-w-0">
                <p class="text-sm font-bold leading-tight">Pick up gratis</p>
                <p class="text-xs text-white/60 leading-tight">en locales de CABA y BA</p>
            </div>
        </div>
        <div class="flex items-center gap-3 justify-center md:justify-start">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10m10 0h4l3-3V9a1 1 0 00-1-1h-4m-4 0V6a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
            <div class="min-w-0">
                <p class="text-sm font-bold leading-tight">Envío gratis</p>
                <p class="text-xs text-white/60 leading-tight">en compras desde $80.000</p>
            </div>
        </div>
        <div class="flex items-center gap-3 justify-center md:justify-start">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="min-w-0">
                <p class="text-sm font-bold leading-tight">Envío en 24hs</p>
                <p class="text-xs text-white/60 leading-tight">*hábiles en CABA y BA</p>
            </div>
        </div>
        <div class="flex items-center gap-3 justify-center md:justify-start">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <div class="min-w-0">
                <p class="text-sm font-bold leading-tight">Cuotas sin interés</p>
                <p class="text-xs text-white/60 leading-tight">3 y 6 cuotas</p>
            </div>
        </div>
    </div>
</section>

<!-- Category blocks -->
<section class="grid grid-cols-1 md:grid-cols-3 w-full">
    <?php foreach ($categoriasPadre as $cat): ?>
    <?php
        $catImg = !empty($cat['imagen'])
            ? imgprod_path((string)$cat['imagen'])
            : imgprod_path('categoria-' . $cat['slug'] . '.jpg');
        $catUrl = page_path('catalogo') . '&categoria=' . urlencode((string)$cat['slug']);
    ?>
    <a href="<?php echo htmlspecialchars($catUrl, ENT_QUOTES, 'UTF-8'); ?>" class="relative block aspect-[4/5] md:aspect-[3/4] overflow-hidden group">
        <img
            src="<?php echo htmlspecialchars($catImg, ENT_QUOTES, 'UTF-8'); ?>"
            alt="<?php echo htmlspecialchars((string)$cat['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
            class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.04]"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/0 to-transparent"></div>
        <div class="absolute bottom-6 left-6 text-white">
            <p class="text-2xl sm:text-3xl font-extrabold tracking-tight uppercase"><?php echo htmlspecialchars((string)$cat['nombre'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php if (!empty($cat['descripcion'])): ?>
            <p class="text-sm font-semibold opacity-90">· <?php echo htmlspecialchars((string)$cat['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>
    </a>
    <?php endforeach; ?>
</section>

<!-- Campaign banner -->
<section class="relative w-full aspect-[16/9] md:aspect-[16/5] overflow-hidden bg-cream">
    <img
        src="<?php echo htmlspecialchars(imgprod_path('banner-3x2.jpg'), ENT_QUOTES, 'UTF-8'); ?>"
        alt="3x2 en seleccionados"
        class="absolute inset-0 h-full w-full object-cover"
    >
    <div class="absolute inset-0 bg-gradient-to-r from-black/40 via-black/10 to-black/40"></div>
    <div class="relative z-10 h-full flex items-center justify-between px-6 sm:px-16 lg:px-24">
        <div class="text-white max-w-md">
            <p class="uppercase tracking-[0.18em] text-xs font-semibold text-white/80 mb-2">Solo por tiempo limitado</p>
            <h2 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold leading-none">3 x 2</h2>
            <p class="mt-3 text-lg sm:text-xl font-semibold tracking-wide">EN SELECCIONADOS</p>
        </div>
        <a
            href="<?php echo page_path('catalogo'); ?>&categoria=ofertas"
            class="inline-flex items-center justify-center h-12 px-8 bg-secondary text-white font-bold text-sm tracking-[0.15em] hover:brightness-95 transition shrink-0"
        >
            COMPRAR
        </a>
    </div>
</section>

<!-- Age tabs + subcategory grid -->
<section class="py-16 sm:py-24" data-component="age-tabs">
    <div class="flex justify-center gap-8 sm:gap-16 mb-10 px-4" role="tablist" aria-label="Categorías por edad">
        <?php foreach ($ageTabs as $i => $tab): ?>
        <button
            type="button"
            role="tab"
            id="age-tab-<?php echo $i; ?>"
            aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
            aria-controls="age-panel-<?php echo $i; ?>"
            data-age-tab="<?php echo $i; ?>"
            class="age-tab-btn text-sm sm:text-base font-bold tracking-[0.15em] pb-2 border-b-2 transition-colors <?php echo $i === 0 ? 'border-primary text-dark' : 'border-transparent text-earth hover:text-dark'; ?>"
        >
            <?php echo htmlspecialchars($tab['label'], ENT_QUOTES, 'UTF-8'); ?>
        </button>
        <?php endforeach; ?>
    </div>

    <?php foreach ($ageTabs as $i => $tab): ?>
    <?php
        $cards = $subcategoriasPorTab[$tab['slug']] ?? [];
        if (count($cards) === 0) {
            $cards = $fallbackCards;
            $useFallback = true;
        } else {
            $useFallback = false;
        }
    ?>
    <div
        id="age-panel-<?php echo $i; ?>"
        role="tabpanel"
        aria-labelledby="age-tab-<?php echo $i; ?>"
        class="age-tab-panel grid grid-cols-2 md:grid-cols-4 gap-1 sm:gap-2 px-1 sm:px-2 <?php echo $i === 0 ? '' : 'hidden'; ?>"
        data-age-panel="<?php echo $i; ?>"
    >
        <?php foreach ($cards as $card): ?>
        <?php
            if ($useFallback) {
                $cardLabel = $card['label'];
                $cardUrl = page_path('catalogo') . '&categoria=' . urlencode($tab['slug']) . '&q=' . urlencode($card['slug']);
                $cardImg = imgprod_path('subcat-' . $card['slug'] . '.jpg');
            } else {
                $cardLabel = (string)$card['nombre'];
                $cardUrl = page_path('catalogo') . '&categoria=' . urlencode((string)$card['slug']);
                $cardImg = !empty($card['imagen'])
                    ? imgprod_path((string)$card['imagen'])
                    : imgprod_path('subcat-' . $card['slug'] . '.jpg');
            }
        ?>
        <a href="<?php echo htmlspecialchars($cardUrl, ENT_QUOTES, 'UTF-8'); ?>" class="relative block aspect-square overflow-hidden group">
            <img
                src="<?php echo htmlspecialchars($cardImg, ENT_QUOTES, 'UTF-8'); ?>"
                alt="<?php echo htmlspecialchars($cardLabel, ENT_QUOTES, 'UTF-8'); ?>"
                class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.04]"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-black/45 to-transparent"></div>
            <p class="absolute bottom-4 left-4 text-white text-lg sm:text-xl font-bold"><?php echo htmlspecialchars($cardLabel, ENT_QUOTES, 'UTF-8'); ?></p>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</section>

<!-- Featured products -->
<section class="px-4 sm:px-8 py-12">
    <div class="text-center mb-10">
        <h2 class="text-3xl sm:text-4xl font-extrabold">Elegidas de la semana</h2>
        <p class="mt-2 text-earth">Combiná las prendas más lindas.</p>
    </div>
    <div class="flex md:grid md:grid-cols-5 gap-4 sm:gap-6 overflow-x-auto md:overflow-visible snap-x snap-mandatory -mx-4 px-4 sm:mx-0 sm:px-0">
        <?php if (empty($destacados)): ?>
        <p class="text-sm text-earth col-span-full text-center py-8">Pronto vas a ver productos destacados acá.</p>
        <?php else: ?>
        <?php foreach ($destacados as $producto): ?>
        <div class="snap-start shrink-0 w-[70%] sm:w-[40%] md:w-auto">
            <?php include __DIR__ . '/../partials/product-card.php'; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Newsletter -->
<section class="bg-cream py-20 px-6">
    <div class="max-w-xl mx-auto text-center">
        <h3 class="text-2xl sm:text-3xl font-extrabold text-dark">Suscribite y recibí novedades</h3>
        <p class="mt-3 text-sm text-dark/70">Sé el primero en conocer nuevas colecciones y ofertas exclusivas.</p>
        <form class="mt-6 flex flex-col sm:flex-row gap-2 max-w-md mx-auto" method="post" action="#" onsubmit="return false;">
            <input
                type="email"
                name="email"
                placeholder="tu@email.com"
                class="flex-1 h-12 px-4 rounded-full bg-white border border-cream text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                required
            >
            <button type="submit" class="h-12 px-7 rounded-full bg-dark text-white font-bold text-sm hover:brightness-110 transition">
                Suscribirme
            </button>
        </form>
        <p class="mt-4 text-xs text-dark/60">Sin spam. Solo lo mejor de Yofi.</p>
    </div>
</section>

<script>
(function () {
    var tabs = document.querySelectorAll('[data-age-tab]');
    var panels = document.querySelectorAll('[data-age-panel]');
    if (!tabs.length) return;

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var idx = tab.getAttribute('data-age-tab');
            tabs.forEach(function (t) {
                var active = t.getAttribute('data-age-tab') === idx;
                t.setAttribute('aria-selected', active ? 'true' : 'false');
                t.classList.toggle('border-primary', active);
                t.classList.toggle('text-dark', active);
                t.classList.toggle('border-transparent', !active);
                t.classList.toggle('text-earth', !active);
            });
            panels.forEach(function (p) {
                p.classList.toggle('hidden', p.getAttribute('data-age-panel') !== idx);
            });
        });
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
