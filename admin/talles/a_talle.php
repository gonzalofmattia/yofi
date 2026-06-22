<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$id_talle = (int)($_GET['id'] ?? 0);
$pageTitle = $id_talle > 0 ? 'Editar talle' : 'Nuevo talle';
$row = ['nombre' => '', 'orden' => 0, 'activo' => 1];

if ($id_talle > 0) {
    $stmt = mysqli_prepare($con, 'SELECT * FROM tbl_talles WHERE id_talle = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id_talle);
    mysqli_stmt_execute($stmt);
    $found = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($found) {
        $row = $found;
    }
}

if (isset($_POST['envio'])) {
    $nombre = trim((string)($_POST['nombre'] ?? ''));
    $orden = (int)($_POST['orden'] ?? 0);
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($id_talle > 0) {
        $stmt = mysqli_prepare($con, 'UPDATE tbl_talles SET nombre=?, orden=?, activo=? WHERE id_talle=?');
        mysqli_stmt_bind_param($stmt, 'siii', $nombre, $orden, $activo, $id_talle);
        mysqli_stmt_execute($stmt);
    } else {
        $stmt = mysqli_prepare($con, 'INSERT INTO tbl_talles (nombre, orden, activo) VALUES (?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sii', $nombre, $orden, $activo);
        mysqli_stmt_execute($stmt);
    }
    header('Location: listado.php');
    exit();
}

include __DIR__ . '/../header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form method="post">
            <?= admin_csrf_field() ?>
            <input type="hidden" name="envio" value="1">
            <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="mb-3"><label class="form-label">Orden</label><input type="number" name="orden" class="form-control" value="<?= (int)$row['orden'] ?>"></div>
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="activo" <?= (int)$row['activo'] ? 'checked' : '' ?>><label>Activo</label></div>
            <button type="submit" class="btn btn-yofi">Guardar</button>
            <a href="listado.php" class="btn btn-outline-secondary">Volver</a>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../pie.php'; ?>
