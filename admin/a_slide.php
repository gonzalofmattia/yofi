<?php
ob_start();
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$id_slide = (int)($_GET['id'] ?? 0);
$pageTitle = $id_slide > 0 ? 'Editar slide' : 'Nuevo slide';
$errorMessage = '';
$row = [
    'imagen' => '',
    'imagen_mobile' => '',
    'link_url' => '',
    'orden' => 0,
    'activo' => 1,
];

if ($id_slide > 0) {
    $stmt = mysqli_prepare($con, 'SELECT * FROM tbl_slider WHERE id_slide = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id_slide);
    mysqli_stmt_execute($stmt);
    $found = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($found) {
        $row = $found;
    }
}

if (isset($_POST['envio'])) {
    $link_url = trim((string)($_POST['link_url'] ?? ''));
    $orden = (int)($_POST['orden'] ?? 0);
    $activo = isset($_POST['activo']) ? 1 : 0;
    $imagen = trim((string)($row['imagen'] ?? ''));
    $imagen_mobile = null;
    if ($id_slide > 0) {
        $existingMobile = trim((string)($row['imagen_mobile'] ?? ''));
        $imagen_mobile = $existingMobile !== '' ? $existingMobile : null;
    }

    if (!empty($_FILES['imagen']['tmp_name'])) {
        $upload = yofi_upload_imgprod_file($_FILES['imagen'], 'slide');
        if (!$upload['success']) {
            $errorMessage = $upload['error'] ?? 'Error al subir la imagen';
        } else {
            $imagen = $upload['filename'];
        }
    }

    if ($errorMessage === '' && !empty($_FILES['imagen_mobile']['tmp_name'])) {
        $uploadMobile = yofi_upload_imgprod_file($_FILES['imagen_mobile'], 'slide-mobile');
        if (!$uploadMobile['success']) {
            $errorMessage = $uploadMobile['error'] ?? 'Error al subir la imagen mobile';
        } else {
            $imagen_mobile = $uploadMobile['filename'];
        }
    } elseif ($errorMessage === '' && isset($_POST['quitar_imagen_mobile'])) {
        $imagen_mobile = null;
    }

    if ($errorMessage === '' && $imagen === '') {
        $errorMessage = 'La imagen es obligatoria.';
    }

    if ($errorMessage === '') {
        if ($id_slide > 0) {
            $stmt = mysqli_prepare($con, 'UPDATE tbl_slider SET imagen=?, imagen_mobile=?, link_url=?, orden=?, activo=? WHERE id_slide=?');
            $linkParam = $link_url !== '' ? $link_url : null;
            mysqli_stmt_bind_param($stmt, 'sssiii', $imagen, $imagen_mobile, $linkParam, $orden, $activo, $id_slide);
            mysqli_stmt_execute($stmt);
            header('Location: slider.php?modificado=1');
        } else {
            $stmt = mysqli_prepare($con, 'INSERT INTO tbl_slider (imagen, imagen_mobile, link_url, orden, activo) VALUES (?, ?, ?, ?, ?)');
            $linkParam = $link_url !== '' ? $link_url : null;
            mysqli_stmt_bind_param($stmt, 'sssii', $imagen, $imagen_mobile, $linkParam, $orden, $activo);
            mysqli_stmt_execute($stmt);
            header('Location: slider.php?agregado=1');
        }
        exit();
    }

    $row['link_url'] = $link_url;
    $row['orden'] = $orden;
    $row['activo'] = $activo;
    if ($imagen !== '') {
        $row['imagen'] = $imagen;
    }
    $row['imagen_mobile'] = $imagen_mobile ?? '';
}

include __DIR__ . '/header.php';
?>

<div class="admin-section-header">
    <div>
        <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="subtitle">Solo imagen y link opcional — sin título ni texto superpuesto</p>
    </div>
</div>

<?php if ($errorMessage !== ''): ?>
<div class="alert alert-danger"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-body">
        <form method="post" enctype="multipart/form-data">
            <?= admin_csrf_field() ?>
            <input type="hidden" name="envio" value="1">

            <?php if (!empty($row['imagen'])): ?>
            <div class="mb-3">
                <label class="form-label">Imagen actual</label>
                <div>
                    <img
                        src="<?= htmlspecialchars(imgprod_path((string)$row['imagen']), ENT_QUOTES, 'UTF-8') ?>"
                        alt=""
                        class="rounded border"
                        style="max-width:320px;max-height:180px;object-fit:cover"
                    >
                </div>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label" for="imagen"><?= $id_slide > 0 ? 'Reemplazar imagen' : 'Imagen' ?> (desktop)</label>
                <input type="file" name="imagen" id="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp" <?= $id_slide > 0 ? '' : 'required' ?>>
                <div class="form-text">JPG, PNG o WebP. Recomendado: horizontal, alta resolución.</div>
            </div>

            <?php if (!empty($row['imagen_mobile'])): ?>
            <div class="mb-3">
                <label class="form-label">Imagen mobile actual</label>
                <div>
                    <img
                        src="<?= htmlspecialchars(imgprod_path((string)$row['imagen_mobile']), ENT_QUOTES, 'UTF-8') ?>"
                        alt=""
                        class="rounded border"
                        style="max-width:160px;max-height:200px;object-fit:cover"
                    >
                </div>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label" for="imagen_mobile">Imagen mobile (opcional)</label>
                <input type="file" name="imagen_mobile" id="imagen_mobile" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <div class="form-text">JPG, PNG o WebP. Recomendado: 4:5 o similar, vertical, para que se vea bien en pantallas chicas.</div>
            </div>

            <?php if (!empty($row['imagen_mobile'])): ?>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="quitar_imagen_mobile" id="quitar_imagen_mobile" value="1">
                <label class="form-check-label" for="quitar_imagen_mobile">Quitar imagen mobile</label>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label" for="link_url">Link de destino (opcional)</label>
                <input type="text" name="link_url" id="link_url" class="form-control"
                    placeholder="index.php?p=catalogo&categoria=ofertas"
                    value="<?= htmlspecialchars((string)($row['link_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-text">URL interna relativa o externa (https://…). Dejá vacío si el slide no es clickeable.</div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="orden">Orden</label>
                <input type="number" name="orden" id="orden" class="form-control" min="0" step="1"
                    value="<?= (int)($row['orden'] ?? 0) ?>">
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="activo" id="activo"
                    <?= (int)($row['activo'] ?? 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="activo">Activo</label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-ink">Guardar</button>
                <a href="slider.php" class="btn btn-outline-ink">Volver</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
