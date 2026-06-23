<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$pageTitle = 'Categorías';
$q = trim((string)($_GET['q'] ?? ''));
$adminSearchAction = 'listado.php';
$adminSearchQuery = $q;
$adminSearchPlaceholder = 'Buscar categorías…';

$sql = 'SELECT id_cate, nombre, slug, imagen, publicado, destacado_home FROM tbl_categorias';
$params = [];
$types = '';
if ($q !== '') {
    $sql .= ' WHERE nombre LIKE ? OR slug LIKE ?';
    $like = '%' . $q . '%';
    $params = [$like, $like];
    $types = 'ss';
}
$sql .= ' ORDER BY orden ASC, nombre';

if ($types !== '') {
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($con, $sql);
}

$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}
$total = count($rows);

include __DIR__ . '/../header.php';
echo agregado($_GET['agregado'] ?? '', '', 'categoría');
echo modificado($_GET['modificado'] ?? '', '', 'categoría');
echo borrado($_GET['borrado'] ?? '', '', 'categoría');
?>

<div class="admin-section-header">
    <div>
        <h1>Categorías</h1>
        <p class="subtitle"><?= (int)$total ?> categoría<?= $total === 1 ? '' : 's' ?><?= $q !== '' ? ' encontradas' : ' en el catálogo' ?></p>
    </div>
    <div class="admin-section-actions">
        <a href="a_categoria.php" class="btn btn-ink">
            <i class="bi bi-plus-lg"></i> Nueva categoría
        </a>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 gap-3 flex-wrap">
    <form method="get" action="listado.php" class="admin-search-bar flex-grow-1" style="max-width:360px">
        <i class="bi bi-search"></i>
        <input name="q" placeholder="Buscar por nombre o slug…" type="search" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
    </form>
    <?php if ($q !== ''): ?>
    <a href="listado.php" class="btn btn-sm btn-outline-ink">Limpiar búsqueda</a>
    <?php endif; ?>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="admin-table mb-0">
                <thead>
                    <tr>
                        <th style="width:56px"></th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Publicada</th>
                        <th>Destacada</th>
                        <th style="width:88px"></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($total > 0): ?>
                    <?php foreach ($rows as $row):
                        $thumb = !empty($row['imagen'])
                            ? imgprod_path((string)$row['imagen'])
                            : imgprod_path('placeholder.jpg');
                    ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8') ?>" alt="" class="product-thumb">
                            </td>
                            <td><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="product-cell-code"><?= htmlspecialchars($row['slug'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input toggle-estado" type="checkbox"
                                           data-endpoint="<?= htmlspecialchars(app_path('admin/api/toggle-categoria-publicada.php'), ENT_QUOTES, 'UTF-8') ?>"
                                           data-id-key="id_cate"
                                           data-id="<?= (int)$row['id_cate'] ?>"
                                           aria-label="Categoría <?= (int)$row['publicado'] ? 'publicada' : 'no publicada' ?>"
                                           <?= (int)$row['publicado'] ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input toggle-estado" type="checkbox"
                                           data-endpoint="<?= htmlspecialchars(app_path('admin/api/toggle-categoria-destacada-home.php'), ENT_QUOTES, 'UTF-8') ?>"
                                           data-id-key="id_cate"
                                           data-id="<?= (int)$row['id_cate'] ?>"
                                           aria-label="Categoría <?= (int)($row['destacado_home'] ?? 0) ? 'destacada en home' : 'no destacada en home' ?>"
                                           <?= (int)($row['destacado_home'] ?? 0) ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="a_categoria.php?id=<?= (int)$row['id_cate'] ?>" class="btn-table-action" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="post" action="b_categoria.php" class="d-inline" onsubmit="return confirm('¿Eliminar esta categoría?')">
                                        <?= admin_csrf_field() ?>
                                        <input type="hidden" name="id_cate" value="<?= (int)$row['id_cate'] ?>">
                                        <button type="submit" class="btn-table-action btn-table-action-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-5"><?= $q !== '' ? 'Sin resultados para esta búsqueda' : 'Sin categorías' ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../pie.php'; ?>
