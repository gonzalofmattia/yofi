<?php
ob_start();
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$pageTitle = 'Configuración de envíos';
$successMessage = '';
$errorMessage = '';

$configKeys = [
    'zipnova_enabled',
    'zipnova_label',
    'zipnova_eta_default',
    'free_shipping_threshold',
    'pickup_enabled',
    'pickup_label',
    'pickup_address',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_envios'])) {
    $values = [
        'zipnova_enabled' => isset($_POST['zipnova_enabled']) ? '1' : '0',
        'zipnova_label' => trim((string)($_POST['zipnova_label'] ?? '')),
        'zipnova_eta_default' => trim((string)($_POST['zipnova_eta_default'] ?? '')),
        'free_shipping_threshold' => (string)max(0, (int)($_POST['free_shipping_threshold'] ?? 0)),
        'pickup_enabled' => isset($_POST['pickup_enabled']) ? '1' : '0',
        'pickup_label' => trim((string)($_POST['pickup_label'] ?? '')),
        'pickup_address' => trim((string)($_POST['pickup_address'] ?? '')),
    ];

    $failed = [];
    foreach ($configKeys as $key) {
        if (!shipping_config_set($key, $values[$key])) {
            $failed[] = $key;
        }
    }

    if ($failed === []) {
        $successMessage = 'Configuración de envíos guardada correctamente.';
    } else {
        $errorMessage = 'No se pudieron guardar algunas claves: ' . implode(', ', $failed);
    }
}

$cfg = [];
foreach ($configKeys as $key) {
    $cfg[$key] = shipping_config_get($key);
}

include __DIR__ . '/header.php';
?>

<div class="admin-section-header">
    <div>
        <h1>Envíos</h1>
        <p class="subtitle">Opciones operativas del checkout (sin credenciales de API)</p>
    </div>
</div>

<?php if ($successMessage !== ''): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>

<?php if ($errorMessage !== ''): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-body">
        <form method="post" action="configuracion_envios.php">
            <?= admin_csrf_field() ?>
            <input type="hidden" name="guardar_envios" value="1">

            <h2 class="h6 text-uppercase text-muted mb-3">Zipnova</h2>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="zipnova_enabled" id="zipnova_enabled" value="1"
                    <?= $cfg['zipnova_enabled'] === '1' ? 'checked' : '' ?>>
                <label class="form-check-label" for="zipnova_enabled">Zipnova activo</label>
            </div>
            <div class="mb-3">
                <label class="form-label" for="zipnova_label">Label de envío</label>
                <input type="text" class="form-control" name="zipnova_label" id="zipnova_label"
                    value="<?= htmlspecialchars($cfg['zipnova_label'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="mb-4">
                <label class="form-label" for="zipnova_eta_default">ETA por defecto</label>
                <input type="text" class="form-control" name="zipnova_eta_default" id="zipnova_eta_default"
                    value="<?= htmlspecialchars($cfg['zipnova_eta_default'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <h2 class="h6 text-uppercase text-muted mb-3">Envío gratis</h2>
            <div class="mb-4">
                <label class="form-label" for="free_shipping_threshold">Monto mínimo para envío gratis</label>
                <input type="number" class="form-control" name="free_shipping_threshold" id="free_shipping_threshold"
                    min="0" step="1"
                    value="<?= htmlspecialchars($cfg['free_shipping_threshold'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-text">0 = desactivado</div>
            </div>

            <h2 class="h6 text-uppercase text-muted mb-3">Retiro en local</h2>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="pickup_enabled" id="pickup_enabled" value="1"
                    <?= $cfg['pickup_enabled'] === '1' ? 'checked' : '' ?>>
                <label class="form-check-label" for="pickup_enabled">Retiro en local activo</label>
            </div>
            <div id="pickup-fields" class="<?= $cfg['pickup_enabled'] === '1' ? '' : 'd-none' ?>">
                <div class="mb-3">
                    <label class="form-label" for="pickup_label">Label de retiro</label>
                    <input type="text" class="form-control" name="pickup_label" id="pickup_label"
                        value="<?= htmlspecialchars($cfg['pickup_label'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label" for="pickup_address">Dirección de retiro</label>
                    <input type="text" class="form-control" name="pickup_address" id="pickup_address"
                        value="<?= htmlspecialchars($cfg['pickup_address'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-ink">Guardar configuración</button>
        </form>
    </div>
</div>

<script>
(function () {
    var pickupSwitch = document.getElementById('pickup_enabled');
    var pickupFields = document.getElementById('pickup-fields');
    if (!pickupSwitch || !pickupFields) return;

    pickupSwitch.addEventListener('change', function () {
        pickupFields.classList.toggle('d-none', !pickupSwitch.checked);
    });
})();
</script>

<?php include __DIR__ . '/pie.php'; ?>
