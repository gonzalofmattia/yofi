<?php
require_once __DIR__ . '/../src/php/products.php';
require_once __DIR__ . '/../src/php/auth.php';

$current_page = $page_id ?? 'home';
$nav_categorias = get_parent_categories();
$currentCategoriaSlug = isset($_GET['categoria']) ? trim((string)$_GET['categoria']) : '';
?>
<?php if (!empty($preheaderShippingText)): ?>
<div class="w-full bg-cream text-dark text-center text-sm py-2 px-6 md:px-8">
    <?= htmlspecialchars($preheaderShippingText, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<header class="w-full bg-white border-b border-cream sticky top-0 z-40" data-component="header">
    <div class="w-full px-6 md:px-8">
        <div class="flex items-center justify-between h-16 md:h-20">
            <button
                type="button"
                class="md:hidden p-2.5 -ml-2 text-dark hover:text-accent"
                data-action="menu-toggle"
                aria-expanded="false"
                aria-controls="mobile-menu"
                aria-label="Abrir menú"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="hidden md:block w-24"></div>

            <a href="<?= SITE_URL ?>" class="flex-shrink-0" aria-label="Yofi">
                <img src="<?= asset_path('img/logo-yofi.png') ?>"
                     alt="Yofi" height="40" class="h-10 w-auto">
            </a>

            <div class="flex items-center gap-2 md:gap-3">
                <button type="button" class="p-3 text-dark hover:text-accent transition-colors" data-search-open aria-label="Buscar" aria-expanded="false" aria-controls="search-overlay">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>
                    </svg>
                </button>
                <button type="button" class="relative p-3 text-dark hover:text-accent transition-colors" data-wishlist-trigger aria-label="Lista de deseos">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span data-wishlist-count class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-accent text-white text-[10px] font-bold leading-none" style="display: none;">0</span>
                </button>
                <a href="<?php echo isUserLoggedIn() ? page_path('mi-cuenta') : page_path('login'); ?>" class="p-3 text-dark hover:text-accent transition-colors" aria-label="Mi cuenta">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>
                <button type="button" class="relative p-3 text-dark hover:text-accent transition-colors" data-cart-trigger aria-label="Carrito">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span
                        data-cart-count
                        class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-accent text-white text-[10px] font-bold leading-none"
                        style="display: none;"
                    >0</span>
                </button>
            </div>
        </div>
    </div>

    <?php if (!empty($nav_categorias)): ?>
    <nav class="hidden md:block border-t border-cream bg-white w-full" aria-label="Navegación principal">
        <div class="w-full px-6 md:px-8">
            <ul class="flex items-center justify-center gap-6 lg:gap-10 py-3 flex-wrap">
                <?php foreach ($nav_categorias as $categoria): ?>
                <?php
                    $slug = (string)$categoria['slug'];
                    $isActive = $currentCategoriaSlug !== '' && $currentCategoriaSlug === $slug;
                ?>
                <li>
                    <a
                        href="<?php echo htmlspecialchars(category_catalog_url($slug), ENT_QUOTES, 'UTF-8'); ?>"
                        class="text-xs lg:text-sm font-semibold tracking-wide uppercase transition-colors <?php echo $isActive ? 'text-accent' : 'text-dark hover:text-accent'; ?>"
                        <?php echo $isActive ? 'aria-current="page"' : ''; ?>
                    >
                        <?php echo htmlspecialchars((string)$categoria['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
    <?php endif; ?>
</header>

<div id="search-overlay" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-dark/40" data-search-close></div>
    <div class="relative bg-white border-b border-cream shadow-lg">
        <form class="flex items-center gap-3 px-4 h-16 md:h-20"
              method="get"
              action="<?php echo htmlspecialchars(app_path('index.php'), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="p" value="catalogo">
            <svg class="w-5 h-5 text-earth flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>
            </svg>
            <input
                type="search"
                name="q"
                id="search-input"
                placeholder="Buscar productos..."
                autocomplete="off"
                class="flex-1 bg-transparent outline-none text-dark text-base placeholder-earth min-w-0"
                aria-label="Buscar productos"
            >
            <button type="button" class="p-2 text-dark hover:text-accent flex-shrink-0" data-search-close aria-label="Cerrar búsqueda">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </form>
    </div>
</div>

<div
    id="mobile-menu"
    class="fixed inset-0 z-50 hidden"
    aria-hidden="true"
    data-mobile-menu
>
    <div class="absolute inset-0 bg-dark/50" data-action="menu-close" aria-hidden="true"></div>
    <div class="absolute top-0 left-0 h-full w-80 max-w-[85vw] bg-white shadow-xl flex flex-col">
        <div class="flex items-center justify-between px-4 h-16 border-b border-cream">
            <a href="<?= SITE_URL ?>" class="flex-shrink-0" aria-label="Yofi">
                <img src="<?= asset_path('img/logo-yofi.png') ?>"
                     alt="Yofi" height="32" class="h-8 w-auto">
            </a>
            <button type="button" class="p-2 text-dark hover:text-accent" data-action="menu-close" aria-label="Cerrar menú">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto px-4 py-6" aria-label="Menú móvil">
            <?php if (!empty($nav_categorias)): ?>
            <ul class="space-y-1">
                <?php foreach ($nav_categorias as $categoria): ?>
                <?php
                    $slug = (string)$categoria['slug'];
                    $isActive = $currentCategoriaSlug !== '' && $currentCategoriaSlug === $slug;
                ?>
                <li>
                    <a
                        href="<?php echo htmlspecialchars(category_catalog_url($slug), ENT_QUOTES, 'UTF-8'); ?>"
                        class="block py-3 px-2 text-sm font-semibold rounded-lg uppercase tracking-wide <?php echo $isActive ? 'text-accent bg-cream' : 'text-dark hover:bg-cream'; ?>"
                        data-action="menu-close"
                        <?php echo $isActive ? 'aria-current="page"' : ''; ?>
                    >
                        <?php echo htmlspecialchars((string)$categoria['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <div class="mt-6 pt-6 border-t border-cream space-y-1">
                <?php if (isUserLoggedIn()): ?>
                <a href="<?php echo page_path('mi-cuenta'); ?>" class="block py-3 px-2 text-sm font-semibold text-dark hover:bg-cream rounded-lg">Mi cuenta</a>
                <?php else: ?>
                <a href="<?php echo page_path('mi-cuenta'); ?>" class="block py-3 px-2 text-sm font-semibold text-dark hover:bg-cream rounded-lg">Mi cuenta</a>
                <a href="<?php echo page_path('login'); ?>" class="block py-3 px-2 text-sm font-semibold text-dark hover:bg-cream rounded-lg">Ingresar</a>
                <a href="<?php echo page_path('registro'); ?>" class="block py-3 px-2 text-sm font-semibold text-dark hover:bg-cream rounded-lg">Crear cuenta</a>
                <?php endif; ?>
                <button type="button" class="block w-full text-left py-3 px-2 text-sm font-semibold text-dark hover:bg-cream rounded-lg" data-cart-trigger>Carrito</button>
            </div>
        </nav>
    </div>
</div>

<script>
(function () {
    var menu = document.querySelector('[data-mobile-menu]');
    var toggleBtn = document.querySelector('[data-action="menu-toggle"]');
    if (!menu || !toggleBtn) return;

    function openMenu() {
        menu.classList.remove('hidden');
        menu.setAttribute('aria-hidden', 'false');
        toggleBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        menu.classList.add('hidden');
        menu.setAttribute('aria-hidden', 'true');
        toggleBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    toggleBtn.addEventListener('click', function () {
        if (menu.classList.contains('hidden')) {
            openMenu();
        } else {
            closeMenu();
        }
    });

    menu.querySelectorAll('[data-action="menu-close"]').forEach(function (el) {
        el.addEventListener('click', closeMenu);
    });
})();

(function () {
    var overlay = document.getElementById('search-overlay');
    var input = document.getElementById('search-input');
    var openBtn = document.querySelector('[data-search-open]');
    if (!overlay || !input || !openBtn) return;

    function openSearch() {
        overlay.classList.remove('hidden');
        overlay.setAttribute('aria-hidden', 'false');
        openBtn.setAttribute('aria-expanded', 'true');
        setTimeout(function () { input.focus(); }, 50);
    }

    function closeSearch() {
        overlay.classList.add('hidden');
        overlay.setAttribute('aria-hidden', 'true');
        openBtn.setAttribute('aria-expanded', 'false');
        input.value = '';
    }

    openBtn.addEventListener('click', openSearch);

    overlay.querySelectorAll('[data-search-close]').forEach(function (el) {
        el.addEventListener('click', closeSearch);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !overlay.classList.contains('hidden')) {
            closeSearch();
        }
    });
})();
</script>
