<?php
ob_start();
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$id_banner = (int)($_GET['id'] ?? 0);
$pageTitle = $id_banner > 0 ? 'Editar banner' : 'Nuevo banner';
$errorMessage = '';
$posicionHome = 'home_secundario';
$row = [
    'eyebrow' => '',
    'titulo' => '',
    'subtitulo' => '',
    'texto_boton' => '',
    'imagen' => '',
    'link_url' => '',
    'posicion' => $posicionHome,
    'orden' => 0,
    'activo' => 1,
];

if ($id_banner > 0) {
    $stmt = mysqli_prepare($con, 'SELECT * FROM tbl_banners WHERE id_banner = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id_banner);
    mysqli_stmt_execute($stmt);
    $found = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($found) {
        $row = $found;
    }
}

if (isset($_POST['envio'])) {
    $eyebrow = trim((string)($_POST['eyebrow'] ?? ''));
    $titulo = trim((string)($_POST['titulo'] ?? ''));
    $subtitulo = trim((string)($_POST['subtitulo'] ?? ''));
    $texto_boton = trim((string)($_POST['texto_boton'] ?? ''));
    $link_url = trim((string)($_POST['link_url'] ?? ''));
    $posicion = trim((string)($_POST['posicion'] ?? $posicionHome));
    $orden = (int)($_POST['orden'] ?? 0);
    $activo = isset($_POST['activo']) ? 1 : 0;
    $imagen = trim((string)($row['imagen'] ?? ''));

    if (!empty($_FILES['imagen']['tmp_name'])) {
        $upload = yofi_upload_imgprod_file($_FILES['imagen'], 'banner');
        if (!$upload['success']) {
            $errorMessage = $upload['error'] ?? 'Error al subir la imagen';
        } else {
            $imagen = $upload['filename'];
        }
    }

    if ($errorMessage === '' && $imagen === '') {
        $errorMessage = 'La imagen es obligatoria.';
    }

    if ($errorMessage === '') {
        $eyebrowParam = $eyebrow !== '' ? $eyebrow : null;
        $tituloParam = $titulo !== '' ? $titulo : null;
        $subtituloParam = $subtitulo !== '' ? $subtitulo : null;
        $textoBotonParam = $texto_boton !== '' ? $texto_boton : null;
        $linkParam = $link_url !== '' ? $link_url : null;
        $posicionParam = $posicion !== '' ? $posicion : $posicionHome;

        if ($id_banner > 0) {
            $stmt = mysqli_prepare(
                $con,
                'UPDATE tbl_banners SET eyebrow=?, titulo=?, subtitulo=?, texto_boton=?, imagen=?, link_url=?, posicion=?, orden=?, activo=? WHERE id_banner=?'
            );
            mysqli_stmt_bind_param(
                $stmt,
                'sssssssiii',
                $eyebrowParam,
                $tituloParam,
                $subtituloParam,
                $textoBotonParam,
                $imagen,
                $linkParam,
                $posicionParam,
                $orden,
                $activo,
                $id_banner
            );
            mysqli_stmt_execute($stmt);
            header('Location: banners.php?modificado=1');
        } else {
            $stmt = mysqli_prepare(
                $con,
                'INSERT INTO tbl_banners (eyebrow, titulo, subtitulo, texto_boton, imagen, link_url, posicion, orden, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            mysqli_stmt_bind_param(
                $stmt,
                'sssssssii',
                $eyebrowParam,
                $tituloParam,
                $subtituloParam,
                $textoBotonParam,
                $imagen,
                $linkParam,
                $posicionParam,
                $orden,
                $activo
            );
            mysqli_stmt_execute($stmt);
            header('Location: banners.php?agregado=1');
        }
        exit();
    }

    $row = array_merge($row, [
        'eyebrow' => $eyebrow,
        'titulo' => $titulo,
        'subtitulo' => $subtitulo,
        'texto_boton' => $texto_boton,
        'link_url' => $link_url,
        'posicion' => $posicion,
        'orden' => $orden,
        'activo' => $activo,
    ]);
    if ($imagen !== '') {
        $row['imagen'] = $imagen;
    }
}

include __DIR__ . '/header.php';
?>

<div class="admin-section-header">
    <div>
        <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="subtitle">Banner promocional con imagen y textos superpuestos</p>
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
            <input type="hidden" name="posicion" value="<?= htmlspecialchars($posicionHome, ENT_QUOTES, 'UTF-8') ?>">

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
                <label class="form-label" for="imagen"><?= $id_banner > 0 ? 'Reemplazar imagen' : 'Imagen' ?></label>
                <input type="file" name="imagen" id="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp" <?= $id_banner > 0 ? '' : 'required' ?>>
            </div>

            <div class="mb-3">
                <label class="form-label" for="eyebrow">Eyebrow (texto chico superior)</label>
                <input type="text" name="eyebrow" id="eyebrow" class="form-control"
                    placeholder="Solo por tiempo limitado"
                    value="<?= htmlspecialchars((string)($row['eyebrow'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="titulo">Título principal</label>
                <input type="text" name="titulo" id="titulo" class="form-control"
                    placeholder="3 x 2"
                    value="<?= htmlspecialchars((string)($row['titulo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="subtitulo">Subtítulo</label>
                <input type="text" name="subtitulo" id="subtitulo" class="form-control"
                    placeholder="EN SELECCIONADOS"
                    value="<?= htmlspecialchars((string)($row['subtitulo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="texto_boton">Texto del botón</label>
                <input type="text" name="texto_boton" id="texto_boton" class="form-control"
                    placeholder="COMPRAR"
                    value="<?= htmlspecialchars((string)($row['texto_boton'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="link_url">Link de destino</label>
                <input type="text" name="link_url" id="link_url" class="form-control"
                    placeholder="index.php?p=catalogo&categoria=ofertas"
                    value="<?= htmlspecialchars((string)($row['link_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="orden">Orden</label>
                <input type="number" name="orden" id="orden" class="form-control" min="0" step="1"
                    value="<?= (int)($row['orden'] ?? 0) ?>">
                <div class="form-text">Si hay varios banners activos en la misma posición, se muestran en este orden.</div>
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="activo" id="activo"
                    <?= (int)($row['activo'] ?? 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="activo">Activo</label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-ink">Guardar</button>
                <a href="banners.php" class="btn btn-outline-ink">Volver</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
