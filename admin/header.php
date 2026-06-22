<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') . ' — ' : '' ?>Yofi Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="<?= app_path('admin/assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
<?php include __DIR__ . '/include/sidebar.php'; ?>
<main class="admin-main">
    <div class="container-fluid py-4 px-4">
        <?php if (!empty($pageTitle)): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h4 mb-0"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
                <button class="btn btn-outline-secondary d-lg-none" type="button" onclick="document.getElementById('adminSidebar').classList.toggle('show')">Menú</button>
            </div>
        <?php endif; ?>
