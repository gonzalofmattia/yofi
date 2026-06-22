<?php
ob_start();
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$pageTitle = 'Configuración';

include __DIR__ . '/header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header card-header-yofi"><strong>Configuración general</strong></div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between"><span>Sitio</span><strong><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>URL</span><strong><?= htmlspecialchars(SITE_URL, ENT_QUOTES, 'UTF-8') ?></strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Base de datos</span><strong><?= htmlspecialchars(DB_DATABASE, ENT_QUOTES, 'UTF-8') ?></strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Entorno</span><strong><?= defined('IS_LOCAL') && IS_LOCAL ? 'Local' : 'Producción' ?></strong></li>
        </ul>
        <p class="text-muted small mt-3 mb-0">Configuración avanzada (Mercado Pago, Zipnova) en <code>config/</code> del proyecto.</p>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
