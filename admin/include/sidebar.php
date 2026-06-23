<?php
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
$currentPath = $_SERVER['SCRIPT_NAME'] ?? '';

function nav_active(string $script, string $current): string
{
    return $script === $current ? ' active' : '';
}

function nav_active_dir(string $dir, string $currentPath): string
{
    return str_contains($currentPath, '/' . $dir . '/') ? ' active' : '';
}
?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-brand">
        <a href="<?= htmlspecialchars(app_path('admin/dashboard.php'), ENT_QUOTES, 'UTF-8') ?>">
            <img src="<?= asset_path('img/logo-yofi.png') ?>" alt="Yofi" height="28">
            <span>Yofi Admin</span>
        </a>
    </div>
    <nav class="nav flex-column">
        <div class="nav-section">General</div>
        <a class="nav-link<?= nav_active('dashboard.php', $currentScript) ?>" href="<?= app_path('admin/dashboard.php') ?>">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>

        <div class="nav-section">Catálogo</div>
        <a class="nav-link<?= nav_active_dir('productos', $currentPath) ?>" href="<?= app_path('admin/productos/listado.php') ?>">
            <i class="bi bi-bag"></i> Productos
        </a>
        <a class="nav-link<?= nav_active_dir('categorias', $currentPath) ?>" href="<?= app_path('admin/categorias/listado.php') ?>">
            <i class="bi bi-folder"></i> Categorías
        </a>
        <a class="nav-link<?= nav_active_dir('colores', $currentPath) ?>" href="<?= app_path('admin/colores/listado.php') ?>">
            <i class="bi bi-palette"></i> Colores
        </a>
        <a class="nav-link<?= nav_active_dir('talles', $currentPath) ?>" href="<?= app_path('admin/talles/listado.php') ?>">
            <i class="bi bi-rulers"></i> Talles
        </a>

        <div class="nav-section">Ventas</div>
        <a class="nav-link<?= nav_active_dir('pedidos', $currentPath) ?>" href="<?= app_path('admin/pedidos/listado.php') ?>">
            <i class="bi bi-cart3"></i> Pedidos
        </a>
        <a class="nav-link<?= nav_active_dir('clientes', $currentPath) ?>" href="<?= app_path('admin/clientes/listado.php') ?>">
            <i class="bi bi-people"></i> Clientes
        </a>

        <div class="nav-section">Contenido</div>
        <a class="nav-link<?= nav_active('slider.php', $currentScript) ?><?= nav_active('a_slide.php', $currentScript) ?>" href="<?= app_path('admin/slider.php') ?>">
            <i class="bi bi-images"></i> Slider del home
        </a>
        <a class="nav-link<?= nav_active('banners.php', $currentScript) ?><?= nav_active('a_banner.php', $currentScript) ?>" href="<?= app_path('admin/banners.php') ?>">
            <i class="bi bi-badge-ad"></i> Banners
        </a>
        <a class="nav-link<?= nav_active('banners-edad.php', $currentScript) ?><?= nav_active('a_banner_edad.php', $currentScript) ?>" href="<?= app_path('admin/banners-edad.php') ?>">
            <i class="bi bi-person-badge"></i> Banners de edad
        </a>
        <a class="nav-link<?= nav_active('empresa.php', $currentScript) ?>" href="<?= app_path('admin/empresa.php') ?>">
            <i class="bi bi-building"></i> Datos de empresa
        </a>

        <div class="nav-section">Sistema</div>
        <a class="nav-link<?= nav_active('configuracion_envios.php', $currentScript) ?>" href="<?= app_path('admin/configuracion_envios.php') ?>">
            <i class="bi bi-truck"></i> Envíos
        </a>
        <a class="nav-link<?= nav_active('metodos_pago.php', $currentScript) ?><?= nav_active('a_metodo_pago.php', $currentScript) ?>" href="<?= app_path('admin/metodos_pago.php') ?>">
            <i class="bi bi-credit-card"></i> Métodos de pago
        </a>
        <a class="nav-link<?= nav_active('configuracion.php', $currentScript) ?>" href="<?= app_path('admin/configuracion.php') ?>">
            <i class="bi bi-gear"></i> Configuración
        </a>
        <a class="nav-link text-danger" href="<?= app_path('admin/logout.php') ?>">
            <i class="bi bi-box-arrow-left"></i> Cerrar sesión
        </a>
    </nav>
</aside>
