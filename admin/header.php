<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') . ' — ' : '' ?>Yofi Admin</title>
    <?php include dirname(__DIR__) . '/partials/favicon-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= app_path('admin/assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
<?php
$adminUser = (string)($_SESSION['idUsuarioAdminSUser'] ?? 'Admin');
$adminInitials = admin_user_initials($adminUser);
$adminSearchAction = $adminSearchAction ?? app_path('admin/productos/listado.php');
$adminSearchQuery = $adminSearchQuery ?? '';
$adminSearchPlaceholder = $adminSearchPlaceholder ?? 'Buscar productos…';
include __DIR__ . '/include/sidebar.php';
?>
<header class="admin-topbar">
    <button type="button" class="admin-sidebar-toggle" id="adminSidebarToggle" aria-label="Abrir menú">
        <i class="bi bi-list"></i>
    </button>
    <a href="<?= htmlspecialchars(app_path('admin/dashboard.php'), ENT_QUOTES, 'UTF-8') ?>" class="admin-topbar-brand">
        <img src="<?= asset_path('img/logo-yofi.png') ?>" alt="Yofi" height="24">
        <span>Yofi Admin</span>
    </a>
    <form method="get" action="<?= htmlspecialchars($adminSearchAction, ENT_QUOTES, 'UTF-8') ?>" class="admin-topbar-search" role="search">
        <i class="bi bi-search"></i>
        <input type="search" name="q" id="adminGlobalSearch" placeholder="<?= htmlspecialchars($adminSearchPlaceholder, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($adminSearchQuery, ENT_QUOTES, 'UTF-8') ?>" aria-label="Buscar">
        <?php if (!empty($adminSearchHidden)): ?>
            <?= $adminSearchHidden ?>
        <?php endif; ?>
    </form>
    <div class="admin-topbar-actions">
        <a href="<?= htmlspecialchars(app_path('index.php'), ENT_QUOTES, 'UTF-8') ?>" class="admin-topbar-link" target="_blank" rel="noopener">
            <i class="bi bi-box-arrow-up-right"></i> Ver tienda
        </a>
        <span class="admin-avatar" title="<?= htmlspecialchars($adminUser, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($adminInitials, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
</header>
<main class="admin-main">
    <div class="admin-content">
