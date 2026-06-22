<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$pageTitle = 'Clientes';
$result = mysqli_query($con, 'SELECT id_usuario, email, nombre, apellido, telefono, activo, fecha_registro FROM tbl_usuarios ORDER BY fecha_registro DESC LIMIT 100');

include __DIR__ . '/../header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header card-header-yofi"><strong>Clientes registrados</strong></div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Email</th><th>Nombre</th><th>Teléfono</th><th>Activo</th><th>Registro</th></tr></thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)$row['telefono'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int)$row['activo'] ? 'Sí' : 'No' ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($row['fecha_registro'])), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../pie.php'; ?>
