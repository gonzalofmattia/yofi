<?php
ob_start();
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$pageTitle = 'Configuración';

$mpConfigurado = defined('MP_ACCESS_TOKEN') && MP_ACCESS_TOKEN !== '';
$zipnovaConfigurado = defined('ZIPNOVA_KEY') && ZIPNOVA_KEY !== '';

include __DIR__ . '/header.php';
?>

<div class="admin-section-header">
    <div>
        <h1>Configuración</h1>
        <p class="subtitle">Estado del sitio e integraciones</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="admin-card h-100">
            <div class="admin-card-body d-flex gap-3 align-items-start">
                <?php if ($mpConfigurado): ?>
                    <i class="bi bi-check-circle-fill text-success fs-4" aria-hidden="true"></i>
                    <div>
                        <h2 class="h6 mb-1">Mercado Pago</h2>
                        <p class="mb-0 text-success fw-semibold">Configurado</p>
                    </div>
                <?php else: ?>
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-4" aria-hidden="true"></i>
                    <div>
                        <h2 class="h6 mb-1">Mercado Pago</h2>
                        <p class="mb-1 fw-semibold text-warning">No configurado</p>
                        <p class="small text-muted mb-0">Para editar, modificá <code>config/mercadopago.local.php</code></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="admin-card h-100">
            <div class="admin-card-body d-flex gap-3 align-items-start">
                <?php if ($zipnovaConfigurado): ?>
                    <i class="bi bi-check-circle-fill text-success fs-4" aria-hidden="true"></i>
                    <div>
                        <h2 class="h6 mb-1">Zipnova</h2>
                        <p class="mb-0 text-success fw-semibold">Configurado</p>
                    </div>
                <?php else: ?>
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-4" aria-hidden="true"></i>
                    <div>
                        <h2 class="h6 mb-1">Zipnova</h2>
                        <p class="mb-1 fw-semibold text-warning">No configurado</p>
                        <p class="small text-muted mb-0">Para editar, modificá <code>config/zipnova.local.php</code></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        <h2 class="h6 text-uppercase text-muted mb-3">General</h2>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between px-0"><span>Sitio</span><strong><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></strong></li>
            <li class="list-group-item d-flex justify-content-between px-0"><span>URL</span><strong><?= htmlspecialchars(SITE_URL, ENT_QUOTES, 'UTF-8') ?></strong></li>
            <li class="list-group-item d-flex justify-content-between px-0"><span>Base de datos</span><strong><?= htmlspecialchars(DB_DATABASE, ENT_QUOTES, 'UTF-8') ?></strong></li>
            <li class="list-group-item d-flex justify-content-between px-0"><span>Entorno</span><strong><?= defined('IS_LOCAL') && IS_LOCAL ? 'Local' : 'Producción' ?></strong></li>
        </ul>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
