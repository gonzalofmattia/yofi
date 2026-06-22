<?php
ob_start();
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$pageTitle = 'Datos de la empresa';
$successMessage = '';
$errorMessage = '';

$configKeys = [
    'whatsapp' => 'WhatsApp',
    'email_contacto' => 'Email de contacto',
    'telefono' => 'Teléfono',
    'direccion' => 'Dirección',
    'instagram' => 'Instagram',
    'facebook' => 'Facebook',
    'horario_atencion' => 'Horario de atención',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_empresa'])) {
    $failed = [];
    foreach (array_keys($configKeys) as $key) {
        $valor = trim((string)($_POST[$key] ?? ''));
        if (!empresa_config_set($key, $valor)) {
            $failed[] = $key;
        }
    }

    if ($failed === []) {
        $successMessage = 'Datos de la empresa guardados correctamente.';
    } else {
        $errorMessage = 'No se pudieron guardar algunas claves: ' . implode(', ', $failed);
    }
}

$cfg = empresa_config_get_all();

include __DIR__ . '/header.php';
?>

<div class="admin-section-header">
    <div>
        <h1>Datos de la empresa</h1>
        <p class="subtitle">Información de contacto y redes sociales</p>
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
        <form method="post" action="empresa.php">
            <?= admin_csrf_field() ?>
            <input type="hidden" name="guardar_empresa" value="1">

            <?php foreach ($configKeys as $key => $label): ?>
            <div class="mb-3">
                <label class="form-label" for="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></label>
                <?php if ($key === 'direccion' || $key === 'horario_atencion'): ?>
                <textarea name="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" class="form-control" rows="2"><?= htmlspecialchars($cfg[$key] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php else: ?>
                <input type="text" name="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" class="form-control"
                    value="<?= htmlspecialchars($cfg[$key] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-ink">Guardar</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
