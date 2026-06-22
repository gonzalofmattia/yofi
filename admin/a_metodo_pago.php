<?php
ob_start();
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$id_metodo = (int)($_GET['id'] ?? 0);
$pageTitle = $id_metodo > 0 ? 'Editar método de pago' : 'Nuevo método de pago';
$errorMessage = '';
$row = [
    'codigo' => '',
    'nombre' => '',
    'descripcion' => '',
    'activo' => 1,
    'orden' => 0,
];

if ($id_metodo > 0) {
    $stmt = mysqli_prepare($con, 'SELECT * FROM tbl_metodos_pago WHERE id_metodo = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id_metodo);
    mysqli_stmt_execute($stmt);
    $found = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($found) {
        $row = $found;
    }
}

if (isset($_POST['envio'])) {
    $codigo = trim((string)($_POST['codigo'] ?? ''));
    $nombre = trim((string)($_POST['nombre'] ?? ''));
    $descripcion = trim((string)($_POST['descripcion'] ?? ''));
    $activo = isset($_POST['activo']) ? 1 : 0;
    $orden = (int)($_POST['orden'] ?? 0);

    if ($codigo === '' || $nombre === '') {
        $errorMessage = 'Código y nombre son obligatorios.';
    } else {
        if ($id_metodo > 0) {
            $stmt = mysqli_prepare($con, 'UPDATE tbl_metodos_pago SET codigo=?, nombre=?, descripcion=?, activo=?, orden=? WHERE id_metodo=?');
            mysqli_stmt_bind_param($stmt, 'sssiii', $codigo, $nombre, $descripcion, $activo, $orden, $id_metodo);
            mysqli_stmt_execute($stmt);
        } else {
            $stmt = mysqli_prepare($con, 'INSERT INTO tbl_metodos_pago (codigo, nombre, descripcion, activo, orden) VALUES (?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sssii', $codigo, $nombre, $descripcion, $activo, $orden);
            mysqli_stmt_execute($stmt);
        }
        header('Location: metodos_pago.php');
        exit();
    }
}

include __DIR__ . '/header.php';
?>

<div class="admin-section-header">
    <div>
        <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="subtitle">Catálogo visible en el checkout (no guarda credenciales de API)</p>
    </div>
</div>

<?php if (!empty($errorMessage)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-body">
        <form method="post">
            <?= admin_csrf_field() ?>
            <input type="hidden" name="envio" value="1">
            <div class="mb-3">
                <label class="form-label" for="codigo">Código</label>
                <input type="text" name="codigo" id="codigo" class="form-control" required
                    pattern="[a-z0-9_]+"
                    value="<?= htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') ?>"
                    <?= $id_metodo > 0 ? 'readonly' : '' ?>>
                <div class="form-text">Minúsculas, sin espacios. Ej: mercadopago, transferencia</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required
                    value="<?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label" for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="2"><?= htmlspecialchars($row['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label" for="orden">Orden</label>
                <input type="number" name="orden" id="orden" class="form-control" min="0" step="1"
                    value="<?= (int)$row['orden'] ?>">
            </div>
            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="activo" id="activo"
                    <?= (int)$row['activo'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="activo">Activo en checkout</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-ink">Guardar</button>
                <a href="metodos_pago.php" class="btn btn-outline-ink">Volver</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
