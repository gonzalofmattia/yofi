<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$pageTitle = 'Categorías';
$result = mysqli_query($con, 'SELECT id_cate, nombre, slug, publicado FROM tbl_categorias ORDER BY nombre');

include __DIR__ . '/../header.php';
echo agregado($_GET['agregado'] ?? '', '', 'categoría');
echo modificado($_GET['modificado'] ?? '', '', 'categoría');
echo borrado($_GET['borrado'] ?? '', '', 'categoría');
?>

<div class="card shadow-sm border-0">
    <div class="card-header card-header-yofi d-flex justify-content-between">
        <strong>Categorías</strong>
        <a href="a_categoria.php" class="btn btn-light btn-sm">Nueva</a>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Nombre</th><th>Slug</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['slug'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int)$row['publicado'] ? 'Activa' : 'Inactiva' ?></td>
                    <td class="text-end">
                        <a href="a_categoria.php?id=<?= (int)$row['id_cate'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form method="post" action="b_categoria.php" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                            <?= admin_csrf_field() ?>
                            <input type="hidden" name="id_cate" value="<?= (int)$row['id_cate'] ?>">
                            <button class="btn btn-sm btn-outline-danger">Borrar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../pie.php'; ?>
