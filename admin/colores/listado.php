<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$pageTitle = 'Colores';
$result = mysqli_query($con, 'SELECT id_color, nombre, hex_code, activo FROM tbl_colores ORDER BY nombre');

include __DIR__ . '/../header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header card-header-yofi d-flex justify-content-between">
        <strong>Colores</strong>
        <a href="a_color.php" class="btn btn-light btn-sm">Nuevo</a>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Color</th><th>Hex</th><th>Activo</th><th></th></tr></thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><span class="d-inline-block rounded-circle me-2" style="width:16px;height:16px;background:<?= htmlspecialchars($row['hex_code'], ENT_QUOTES, 'UTF-8') ?>"></span><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['hex_code'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int)$row['activo'] ? 'Sí' : 'No' ?></td>
                    <td class="text-end"><a href="a_color.php?id=<?= (int)$row['id_color'] ?>" class="btn btn-sm btn-outline-primary">Editar</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../pie.php'; ?>
