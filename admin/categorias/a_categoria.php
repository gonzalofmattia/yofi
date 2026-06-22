<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$id_cate = (int)($_GET['id'] ?? 0);
$pageTitle = $id_cate > 0 ? 'Editar categoría' : 'Nueva categoría';
$error = '';
$row = ['nombre' => '', 'descripcion' => '', 'publicado' => 1];

if ($id_cate > 0) {
    $stmt = mysqli_prepare($con, 'SELECT * FROM tbl_categorias WHERE id_cate = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id_cate);
    mysqli_stmt_execute($stmt);
    $found = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($found) {
        $row = $found;
    }
}

if (isset($_POST['envio'])) {
    $nombre = trim((string)($_POST['nombre'] ?? ''));
    $descripcion = trim((string)($_POST['descripcion'] ?? ''));
    $publicado = isset($_POST['publicado']) ? 1 : 0;
    $slug = yofi_slug($nombre);

    if ($nombre === '') {
        $error = 'El nombre es obligatorio.';
    } elseif ($id_cate > 0) {
        $stmt = mysqli_prepare($con, 'UPDATE tbl_categorias SET nombre=?, slug=?, descripcion=?, publicado=? WHERE id_cate=?');
        mysqli_stmt_bind_param($stmt, 'sssii', $nombre, $slug, $descripcion, $publicado, $id_cate);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: listado.php?modificado=1');
            exit();
        }
    } else {
        $stmt = mysqli_prepare($con, 'INSERT INTO tbl_categorias (nombre, slug, descripcion, publicado) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sssi', $nombre, $slug, $descripcion, $publicado);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: listado.php?agregado=1');
            exit();
        }
    }
    $error = 'Error al guardar.';
}

include __DIR__ . '/../header.php';
?>

<div class="admin-section-header">
    <div>
        <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="subtitle"><?= $id_cate > 0 ? 'Modificá los datos de la categoría' : 'Completá los datos para crear una categoría' ?></p>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        <form method="post">
            <?= admin_csrf_field() ?>
            <input type="hidden" name="envio" value="1">
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($row['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="publicado" id="publicado" <?= (int)($row['publicado'] ?? 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="publicado">Publicada</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-ink">Guardar</button>
                <a href="listado.php" class="btn btn-outline-ink">Volver</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../pie.php'; ?>
