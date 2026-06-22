<?php
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
$adminUser = $_SESSION['idUsuarioAdminSUser'] ?? 'Admin';
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
    <div class="p-3 border-bottom">
        <a href="<?= htmlspecialchars(app_path('admin/dashboard.php'), ENT_QUOTES, 'UTF-8') ?>" class="d-flex align-items-center gap-2 text-decoration-none">
            <img src="<?= asset_path('img/logo-yofi.png') ?>" alt="Yofi" height="28">
            <span class="fw-bold text-dark">Yofi Admin</span>
        </a>
        <small class="text-muted d-block mt-2"><?= htmlspecialchars((string)$adminUser, ENT_QUOTES, 'UTF-8') ?></small>
    </div>
    <nav class="nav flex-column py-2">
        <div class="nav-section">General</div>
        <a class="nav-link<?= nav_active('dashboard.php', $currentScript) ?>" href="<?= app_path('admin/dashboard.php') ?>">Dashboard</a>

        <div class="nav-section">Catálogo</div>
        <a class="nav-link<?= nav_active_dir('productos', $_SERVER['SCRIPT_NAME'] ?? '') ?>" href="<?= app_path('admin/productos/listado.php') ?>">Productos</a>
        <a class="nav-link<?= nav_active_dir('categorias', $_SERVER['SCRIPT_NAME'] ?? '') ?>" href="<?= app_path('admin/categorias/listado.php') ?>">Categorías</a>
        <a class="nav-link<?= nav_active_dir('colores', $_SERVER['SCRIPT_NAME'] ?? '') ?>" href="<?= app_path('admin/colores/listado.php') ?>">Colores</a>
        <a class="nav-link<?= nav_active_dir('talles', $_SERVER['SCRIPT_NAME'] ?? '') ?>" href="<?= app_path('admin/talles/listado.php') ?>">Talles</a>

        <div class="nav-section">Ventas</div>
        <a class="nav-link<?= nav_active_dir('pedidos', $_SERVER['SCRIPT_NAME'] ?? '') ?>" href="<?= app_path('admin/pedidos/listado.php') ?>">Pedidos</a>
        <a class="nav-link" href="<?= app_path('admin/clientes/listado.php') ?>">Clientes</a>

        <div class="nav-section">Sistema</div>
        <a class="nav-link" href="<?= app_path('admin/configuracion.php') ?>">Configuración</a>
        <a class="nav-link text-danger" href="<?= app_path('admin/logout.php') ?>">Cerrar sesión</a>
    </nav>
</aside>
