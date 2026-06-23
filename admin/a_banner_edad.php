<?php
ob_start();
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$id_edad_banner = (int)($_GET['id'] ?? 0);
$pageTitle = 'Editar banner de edad';
$errorMessage = '';
$row = null;

if ($id_edad_banner > 0) {
    $stmt = mysqli_prepare($con, 'SELECT * FROM tbl_home_edad_banners WHERE id_edad_banner = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id_edad_banner);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if (!$row) {
    header('Location: banners-edad.php');
    exit();
}

if (isset($_POST['envio'])) {
    $titulo = trim((string)($_POST['titulo'] ?? ''));
    $subtitulo = trim((string)($_POST['subtitulo'] ?? ''));
    $link_url = trim((string)($_POST['link_url'] ?? ''));
    $imagen = trim((string)($row['imagen'] ?? ''));

    if (!empty($_FILES['imagen']['tmp_name'])) {
        $upload = yofi_upload_imgprod_file($_FILES['imagen'], 'edad-banner');
        if (!$upload['success']) {
            $errorMessage = $upload['error'] ?? 'Error al subir la imagen';
        } else {
            $imagen = $upload['filename'];
        }
    }

    if ($errorMessage === '' && $titulo === '') {
        $errorMessage = 'El título es obligatorio.';
    }

    if ($errorMessage === '') {
        $subtituloParam = $subtitulo !== '' ? $subtitulo : null;
        $linkParam = $link_url !== '' ? $link_url : null;
        $imagenParam = $imagen !== '' ? $imagen : null;

        $stmt = mysqli_prepare(
            $con,
            'UPDATE tbl_home_edad_banners SET titulo=?, subtitulo=?, imagen=?, link_url=? WHERE id_edad_banner=?'
        );
        mysqli_stmt_bind_param($stmt, 'ssssi', $titulo, $subtituloParam, $imagenParam, $linkParam, $id_edad_banner);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: banners-edad.php?modificado=1');
            exit();
        }
        $errorMessage = 'Error al guardar.';
    }

    $row['titulo'] = $titulo;
    $row['subtitulo'] = $subtitulo;
    $row['link_url'] = $link_url;
    if ($imagen !== '') {
        $row['imagen'] = $imagen;
    }
}

include __DIR__ . '/header.php';
?>

<div class="admin-section-header">
    <div>
        <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="subtitle">Banner del home para el filtro <?= htmlspecialchars((string)$row['slug'], ENT_QUOTES, 'UTF-8') ?></p>
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
                        style="max-width:320px;max-height:240px;object-fit:cover"
                    >
                </div>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label" for="imagen">Imagen</label>
                <input type="file" name="imagen" id="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <div class="form-text">JPG, PNG o WebP. Recomendado: vertical, relación 3:4 o 4:5.</div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="titulo">Título</label>
                <input type="text" name="titulo" id="titulo" class="form-control" required
                    value="<?= htmlspecialchars((string)($row['titulo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="subtitulo">Subtítulo (opcional)</label>
                <input type="text" name="subtitulo" id="subtitulo" class="form-control"
                    placeholder="Texto secundario debajo del título"
                    value="<?= htmlspecialchars((string)($row['subtitulo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="mb-4">
                <label class="form-label" for="link_url">Link de destino</label>
                <input type="text" name="link_url" id="link_url" class="form-control"
                    placeholder="index.php?p=catalogo&edad=mini"
                    value="<?= htmlspecialchars((string)($row['link_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-text">URL interna relativa o externa (https://…).</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-ink">Guardar</button>
                <a href="banners-edad.php" class="btn btn-outline-ink">Volver</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
