<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$pageTitle = 'Talles';
$result = mysqli_query($con, 'SELECT id_talle, nombre, orden, activo FROM tbl_talles ORDER BY orden, nombre');

include __DIR__ . '/../header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header card-header-yofi d-flex justify-content-between">
        <strong>Talles</strong>
        <a href="a_talle.php" class="btn btn-light btn-sm">Nuevo</a>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Nombre</th><th>Orden</th><th>Activo</th><th></th></tr></thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int)$row['orden'] ?></td>
                    <td><?= (int)$row['activo'] ? 'Sí' : 'No' ?></td>
                    <td class="text-end"><a href="a_talle.php?id=<?= (int)$row['id_talle'] ?>" class="btn btn-sm btn-outline-primary">Editar</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../pie.php'; ?>
