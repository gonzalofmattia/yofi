<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$id_color = (int)($_GET['id'] ?? 0);
$pageTitle = $id_color > 0 ? 'Editar color' : 'Nuevo color';
$row = ['nombre' => '', 'hex_code' => '#000000', 'activo' => 1];

if ($id_color > 0) {
    $stmt = mysqli_prepare($con, 'SELECT * FROM tbl_colores WHERE id_color = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id_color);
    mysqli_stmt_execute($stmt);
    $found = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($found) {
        $row = $found;
    }
}

if (isset($_POST['envio'])) {
    $nombre = trim((string)($_POST['nombre'] ?? ''));
    $hex = trim((string)($_POST['hex_code'] ?? '#000000'));
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($id_color > 0) {
        $stmt = mysqli_prepare($con, 'UPDATE tbl_colores SET nombre=?, hex_code=?, activo=? WHERE id_color=?');
        mysqli_stmt_bind_param($stmt, 'ssii', $nombre, $hex, $activo, $id_color);
        mysqli_stmt_execute($stmt);
    } else {
        $stmt = mysqli_prepare($con, 'INSERT INTO tbl_colores (nombre, hex_code, activo) VALUES (?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'ssi', $nombre, $hex, $activo);
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
            <div class="mb-3"><label class="form-label">Hex</label><input type="color" name="hex_code" class="form-control form-control-color" value="<?= htmlspecialchars($row['hex_code'], ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="activo" <?= (int)$row['activo'] ? 'checked' : '' ?>><label>Activo</label></div>
            <button type="submit" class="btn btn-yofi">Guardar</button>
            <a href="listado.php" class="btn btn-outline-secondary">Volver</a>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../pie.php'; ?>
