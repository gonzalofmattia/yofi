<?php
require_once __DIR__ . '/../src/php/products.php';
require_once __DIR__ . '/../src/php/content.php';

$page_title = SITE_NAME . ' — Nueva colección Otoño Invierno 2025';
$meta_description = 'Yofi: ropa romántica para chicos y chicas. Nueva colección Otoño Invierno 2025. Envío a todo el país, cuotas sin interés.';

$categoriasHome = get_featured_home_categories();
$destacados = get_featured_products(5);
$ageTabs = get_age_filters();
$edadBanners = get_home_edad_banners();
$catalogBase = page_path('catalogo');
$defaultEdadSlug = (string)($ageTabs[0]['slug'] ?? 'mini');

$heroSlides = get_active_slides();
$heroSlideCount = count($heroSlides);
$homeBanners = get_active_banners('home_secundario');
$homeBannerCount = count($homeBanners);
?>

<!-- Hero -->
<?php if ($heroSlideCount > 0): ?>
<section
    class="relative w-full h-[88vh] md:h-[92vh] overflow-hidden bg-cream"
    data-component="hero-slider"
    aria-label="Galería principal"
    <?= $heroSlideCount > 1 ? 'data-autoplay="6000"' : '' ?>
>
    <div class="relative h-full w-full">
        <?php foreach ($heroSlides as $i => $slide): ?>
        <?php
            $slideHref = content_resolve_url(isset($slide['link_url']) ? (string)$slide['link_url'] : null);
            $slideImg = imgprod_path((string)$slide['imagen']);
        ?>
        <div
            class="hero-slider-slide absolute inset-0 transition-opacity duration-700 ease-in-out <?= $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' ?>"
            data-slide-index="<?= (int)$i ?>"
            aria-hidden="<?= $i === 0 ? 'false' : 'true' ?>"
        >
            <?php if ($slideHref !== ''): ?>
            <a href="<?= htmlspecialchars($slideHref, ENT_QUOTES, 'UTF-8') ?>" class="block h-full w-full">
            <?php endif; ?>
                <img
                    src="<?= htmlspecialchars($slideImg, ENT_QUOTES, 'UTF-8') ?>"
                    alt=""
                    class="h-full w-full object-cover"
                    <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>
                >
            <?php if ($slideHref !== ''): ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($heroSlideCount > 1): ?>
    <button
        type="button"
        class="hero-slider-prev absolute left-4 top-1/2 -translate-y-1/2 z-20 flex h-11 w-11 items-center justify-center rounded-full bg-white/80 text-dark shadow hover:bg-white transition"
        aria-label="Slide anterior"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button
        type="button"
        class="hero-slider-next absolute right-4 top-1/2 -translate-y-1/2 z-20 flex h-11 w-11 items-center justify-center rounded-full bg-white/80 text-dark shadow hover:bg-white transition"
        aria-label="Slide siguiente"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
    <div class="hero-slider-dots absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        <?php for ($d = 0; $d < $heroSlideCount; $d++): ?>
        <button
            type="button"
            class="hero-slider-dot h-2.5 w-2.5 rounded-full transition <?= $d === 0 ? 'bg-white scale-110' : 'bg-white/50 hover:bg-white/80' ?>"
            data-slide-to="<?= $d ?>"
            aria-label="Ir al slide <?= $d + 1 ?>"
            aria-current="<?= $d === 0 ? 'true' : 'false' ?>"
        ></button>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>
<?php else: ?>
<section class="relative w-full h-[88vh] md:h-[92vh] overflow-hidden bg-cream">
    <img
        src="<?= htmlspecialchars(imgprod_path('hero-principal.jpg'), ENT_QUOTES, 'UTF-8') ?>"
        alt=""
        class="absolute inset-0 h-full w-full object-cover"
    >
</section>
<?php endif; ?>

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
        <?php $homeFreeShipping = free_shipping_threshold(); ?>
        <div class="flex items-center gap-3 justify-center md:justify-start">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10m10 0h4l3-3V9a1 1 0 00-1-1h-4m-4 0V6a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
            <div class="min-w-0">
                <p class="text-sm font-bold leading-tight">Envío gratis</p>
                <p class="text-xs text-white/60 leading-tight"><?= $homeFreeShipping > 0 ? 'en compras desde ' . htmlspecialchars(format_money_ars($homeFreeShipping), ENT_QUOTES, 'UTF-8') : 'consultá condiciones en el checkout' ?></p>
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

<!-- Age entry blocks -->
<?php if (!empty($edadBanners)): ?>
<section class="grid grid-cols-1 md:grid-cols-3 w-full">
    <?php foreach ($edadBanners as $banner): ?>
    <?php
        $bannerHref = content_resolve_url(isset($banner['link_url']) ? (string)$banner['link_url'] : null);
        if ($bannerHref === '') {
            $bannerHref = $catalogBase . '&edad=' . urlencode((string)$banner['slug']);
        }
        $bannerImg = !empty($banner['imagen'])
            ? imgprod_path((string)$banner['imagen'])
            : imgprod_path(get_age_filter_fallback_image((string)$banner['slug']));
        $bannerTitulo = trim((string)($banner['titulo'] ?? ''));
        $bannerSubtitulo = trim((string)($banner['subtitulo'] ?? ''));
    ?>
    <a href="<?php echo htmlspecialchars($bannerHref, ENT_QUOTES, 'UTF-8'); ?>" class="relative block aspect-[4/5] md:aspect-[3/4] overflow-hidden group">
        <img
            src="<?php echo htmlspecialchars($bannerImg, ENT_QUOTES, 'UTF-8'); ?>"
            alt="<?php echo htmlspecialchars($bannerTitulo, ENT_QUOTES, 'UTF-8'); ?>"
            class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.04]"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/0 to-transparent"></div>
        <div class="absolute bottom-6 left-6 text-white">
            <p class="text-2xl sm:text-3xl font-extrabold tracking-tight uppercase"><?php echo htmlspecialchars($bannerTitulo, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php if ($bannerSubtitulo !== ''): ?>
            <p class="text-sm font-semibold opacity-90 mt-1"><?php echo htmlspecialchars($bannerSubtitulo, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>
    </a>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<!-- Campaign banner -->
<?php if ($homeBannerCount > 0): ?>
<section
    class="relative w-full aspect-[16/9] md:aspect-[16/5] overflow-hidden bg-cream"
    data-component="campaign-banner-slider"
    aria-label="Promociones"
    <?= $homeBannerCount > 1 ? 'data-autoplay="8000"' : '' ?>
>
    <div class="relative h-full w-full">
        <?php foreach ($homeBanners as $i => $banner): ?>
        <?php
            $bannerImg = imgprod_path((string)$banner['imagen']);
            $bannerHref = content_resolve_url(isset($banner['link_url']) ? (string)$banner['link_url'] : null);
            $bannerEyebrow = trim((string)($banner['eyebrow'] ?? ''));
            $bannerTitulo = trim((string)($banner['titulo'] ?? ''));
            $bannerSubtitulo = trim((string)($banner['subtitulo'] ?? ''));
            $bannerBoton = trim((string)($banner['texto_boton'] ?? ''));
            $bannerAlt = $bannerTitulo !== '' ? $bannerTitulo : ($bannerEyebrow !== '' ? $bannerEyebrow : 'Promoción');
        ?>
        <div
            class="campaign-banner-slide absolute inset-0 transition-opacity duration-700 ease-in-out <?= $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' ?>"
            data-slide-index="<?= (int)$i ?>"
            aria-hidden="<?= $i === 0 ? 'false' : 'true' ?>"
        >
            <img
                src="<?= htmlspecialchars($bannerImg, ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($bannerAlt, ENT_QUOTES, 'UTF-8') ?>"
                class="absolute inset-0 h-full w-full object-cover"
                <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>
            >
            <div class="absolute inset-0 bg-gradient-to-r from-black/40 via-black/10 to-black/40"></div>
            <div class="relative z-10 h-full flex items-center justify-between px-6 sm:px-16 lg:px-24">
                <div class="text-white max-w-md">
                    <?php if ($bannerEyebrow !== ''): ?>
                    <p class="uppercase tracking-[0.18em] text-xs font-semibold text-white/80 mb-2"><?= htmlspecialchars($bannerEyebrow, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                    <?php if ($bannerTitulo !== ''): ?>
                    <h2 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold leading-none"><?= htmlspecialchars($bannerTitulo, ENT_QUOTES, 'UTF-8') ?></h2>
                    <?php endif; ?>
                    <?php if ($bannerSubtitulo !== ''): ?>
                    <p class="mt-3 text-lg sm:text-xl font-semibold tracking-wide"><?= htmlspecialchars($bannerSubtitulo, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($bannerBoton !== '' && $bannerHref !== ''): ?>
                <a
                    href="<?= htmlspecialchars($bannerHref, ENT_QUOTES, 'UTF-8') ?>"
                    class="inline-flex items-center justify-center h-12 px-8 bg-secondary text-white font-bold text-sm tracking-[0.15em] hover:brightness-95 transition shrink-0"
                >
                    <?= htmlspecialchars($bannerBoton, ENT_QUOTES, 'UTF-8') ?>
                </a>
                <?php elseif ($bannerBoton !== ''): ?>
                <span class="inline-flex items-center justify-center h-12 px-8 bg-secondary text-white font-bold text-sm tracking-[0.15em] shrink-0">
                    <?= htmlspecialchars($bannerBoton, ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($homeBannerCount > 1): ?>
    <button
        type="button"
        class="campaign-banner-prev absolute left-4 top-1/2 -translate-y-1/2 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-white/80 text-dark shadow hover:bg-white transition"
        aria-label="Banner anterior"
    >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button
        type="button"
        class="campaign-banner-next absolute right-4 top-1/2 -translate-y-1/2 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-white/80 text-dark shadow hover:bg-white transition"
        aria-label="Banner siguiente"
    >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
    <div class="campaign-banner-dots absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        <?php for ($d = 0; $d < $homeBannerCount; $d++): ?>
        <button
            type="button"
            class="campaign-banner-dot h-2 w-2 rounded-full transition <?= $d === 0 ? 'bg-white scale-110' : 'bg-white/50 hover:bg-white/80' ?>"
            data-slide-to="<?= $d ?>"
            aria-label="Ir al banner <?= $d + 1 ?>"
            aria-current="<?= $d === 0 ? 'true' : 'false' ?>"
        ></button>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- Age tabs + featured category slider -->
<?php if (!empty($categoriasHome)): ?>
<?php $categoriasSlider = array_merge($categoriasHome, $categoriasHome); ?>
<section
    class="py-16 sm:py-24"
    data-component="category-home-slider"
    data-catalog-base="<?php echo htmlspecialchars($catalogBase, ENT_QUOTES, 'UTF-8'); ?>"
    data-default-edad="<?php echo htmlspecialchars($defaultEdadSlug, ENT_QUOTES, 'UTF-8'); ?>"
>
    <div class="flex justify-center gap-8 sm:gap-16 mb-10 px-4" role="tablist" aria-label="Filtrar por edad">
        <?php foreach ($ageTabs as $i => $tab): ?>
        <button
            type="button"
            role="tab"
            id="age-tab-<?php echo $i; ?>"
            aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
            data-age-tab="<?php echo htmlspecialchars((string)$tab['slug'], ENT_QUOTES, 'UTF-8'); ?>"
            class="age-tab-btn text-sm sm:text-base font-bold tracking-[0.15em] pb-2 border-b-2 transition-colors <?php echo $i === 0 ? 'border-primary text-dark' : 'border-transparent text-earth hover:text-dark'; ?>"
        >
            <?php echo htmlspecialchars($tab['label'], ENT_QUOTES, 'UTF-8'); ?>
        </button>
        <?php endforeach; ?>
    </div>

    <div class="overflow-hidden w-full">
        <div class="category-home-track flex gap-2 sm:gap-3 w-max px-1 sm:px-2">
            <?php foreach ($categoriasSlider as $cat): ?>
            <?php
                $cardLabel = (string)$cat['nombre'];
                $cardSlug = (string)$cat['slug'];
                $cardUrl = $catalogBase
                    . '&edad=' . urlencode($defaultEdadSlug)
                    . '&categoria=' . urlencode($cardSlug);
                $cardImg = !empty($cat['imagen'])
                    ? imgprod_path((string)$cat['imagen'])
                    : imgprod_path('subcat-' . $cardSlug . '.jpg');
            ?>
            <a
                href="<?php echo htmlspecialchars($cardUrl, ENT_QUOTES, 'UTF-8'); ?>"
                data-category-link
                data-categoria-slug="<?php echo htmlspecialchars($cardSlug, ENT_QUOTES, 'UTF-8'); ?>"
                class="relative block shrink-0 w-[45vw] sm:w-[32vw] md:w-[24vw] max-w-[320px] aspect-square overflow-hidden group"
            >
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
    </div>
</section>
<style>
@keyframes yofi-category-marquee {
    from { transform: translateX(0); }
    to { transform: translateX(-50%); }
}
.category-home-track {
    animation: yofi-category-marquee 40s linear infinite;
}
.category-home-track:hover {
    animation-play-state: paused;
}
</style>
<?php endif; ?>

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
    var root = document.querySelector('[data-component="hero-slider"]');
    if (!root) return;

    var slides = root.querySelectorAll('.hero-slider-slide');
    var dots = root.querySelectorAll('.hero-slider-dot');
    var prevBtn = root.querySelector('.hero-slider-prev');
    var nextBtn = root.querySelector('.hero-slider-next');
    if (slides.length <= 1) return;

    var current = 0;
    var autoplayMs = parseInt(root.getAttribute('data-autoplay') || '0', 10);
    var timer = null;

    function goTo(index) {
        current = (index + slides.length) % slides.length;
        slides.forEach(function (slide, i) {
            var active = i === current;
            slide.classList.toggle('opacity-100', active);
            slide.classList.toggle('z-10', active);
            slide.classList.toggle('opacity-0', !active);
            slide.classList.toggle('z-0', !active);
            slide.classList.toggle('pointer-events-none', !active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
        dots.forEach(function (dot, i) {
            var active = i === current;
            dot.classList.toggle('bg-white', active);
            dot.classList.toggle('scale-110', active);
            dot.classList.toggle('bg-white/50', !active);
            dot.setAttribute('aria-current', active ? 'true' : 'false');
        });
    }

    function scheduleAutoplay() {
        if (timer) clearInterval(timer);
        if (autoplayMs > 0) {
            timer = setInterval(function () { goTo(current + 1); }, autoplayMs);
        }
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            goTo(current - 1);
            scheduleAutoplay();
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            goTo(current + 1);
            scheduleAutoplay();
        });
    }
    dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
            var idx = parseInt(dot.getAttribute('data-slide-to') || '0', 10);
            goTo(idx);
            scheduleAutoplay();
        });
    });

    scheduleAutoplay();
})();
</script>

<script>
(function () {
    var root = document.querySelector('[data-component="campaign-banner-slider"]');
    if (!root) return;

    var slides = root.querySelectorAll('.campaign-banner-slide');
    var dots = root.querySelectorAll('.campaign-banner-dot');
    var prevBtn = root.querySelector('.campaign-banner-prev');
    var nextBtn = root.querySelector('.campaign-banner-next');
    if (slides.length <= 1) return;

    var current = 0;
    var autoplayMs = parseInt(root.getAttribute('data-autoplay') || '0', 10);
    var timer = null;

    function goTo(index) {
        current = (index + slides.length) % slides.length;
        slides.forEach(function (slide, i) {
            var active = i === current;
            slide.classList.toggle('opacity-100', active);
            slide.classList.toggle('z-10', active);
            slide.classList.toggle('opacity-0', !active);
            slide.classList.toggle('z-0', !active);
            slide.classList.toggle('pointer-events-none', !active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
        dots.forEach(function (dot, i) {
            var active = i === current;
            dot.classList.toggle('bg-white', active);
            dot.classList.toggle('scale-110', active);
            dot.classList.toggle('bg-white/50', !active);
            dot.setAttribute('aria-current', active ? 'true' : 'false');
        });
    }

    function scheduleAutoplay() {
        if (timer) clearInterval(timer);
        if (autoplayMs > 0) {
            timer = setInterval(function () { goTo(current + 1); }, autoplayMs);
        }
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            goTo(current - 1);
            scheduleAutoplay();
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            goTo(current + 1);
            scheduleAutoplay();
        });
    }
    dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
            var idx = parseInt(dot.getAttribute('data-slide-to') || '0', 10);
            goTo(idx);
            scheduleAutoplay();
        });
    });

    scheduleAutoplay();
})();
</script>

<script>
(function () {
    var root = document.querySelector('[data-component="category-home-slider"]');
    if (!root) return;

    var tabs = root.querySelectorAll('[data-age-tab]');
    var catalogBase = root.getAttribute('data-catalog-base') || '';
    var currentEdad = root.getAttribute('data-default-edad') || 'mini';

    function updateCategoryLinks() {
        root.querySelectorAll('[data-category-link]').forEach(function (link) {
            var slug = link.getAttribute('data-categoria-slug') || '';
            if (!slug) return;
            link.href = catalogBase + '&edad=' + encodeURIComponent(currentEdad) + '&categoria=' + encodeURIComponent(slug);
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            currentEdad = tab.getAttribute('data-age-tab') || currentEdad;
            tabs.forEach(function (t) {
                var active = t === tab;
                t.setAttribute('aria-selected', active ? 'true' : 'false');
                t.classList.toggle('border-primary', active);
                t.classList.toggle('text-dark', active);
                t.classList.toggle('border-transparent', !active);
                t.classList.toggle('text-earth', !active);
            });
            updateCategoryLinks();
        });
    });

    updateCategoryLinks();
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
