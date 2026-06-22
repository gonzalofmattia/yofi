<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$pageTitle = 'Colores';
$q = trim((string)($_GET['q'] ?? ''));
$adminSearchAction = 'listado.php';
$adminSearchQuery = $q;
$adminSearchPlaceholder = 'Buscar colores…';

$sql = 'SELECT id_color, nombre, hex_code, activo FROM tbl_colores';
$params = [];
$types = '';
if ($q !== '') {
    $sql .= ' WHERE nombre LIKE ? OR hex_code LIKE ?';
    $like = '%' . $q . '%';
    $params = [$like, $like];
    $types = 'ss';
}
$sql .= ' ORDER BY nombre';

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
?>

<div class="admin-section-header">
    <div>
        <h1>Colores</h1>
        <p class="subtitle"><?= (int)$total ?> color<?= $total === 1 ? '' : 'es' ?><?= $q !== '' ? ' encontrados' : ' disponibles' ?></p>
    </div>
    <div class="admin-section-actions">
        <a href="a_color.php" class="btn btn-ink">
            <i class="bi bi-plus-lg"></i> Nuevo color
        </a>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 gap-3 flex-wrap">
    <form method="get" action="listado.php" class="admin-search-bar flex-grow-1" style="max-width:360px">
        <i class="bi bi-search"></i>
        <input name="q" placeholder="Buscar por nombre o hex…" type="search" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
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
                        <th>Color</th>
                        <th>Hex</th>
                        <th>Estado</th>
                        <th style="width:48px"></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($total > 0): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td>
                                <span class="d-inline-block rounded-circle me-2 align-middle" style="width:16px;height:16px;background:<?= htmlspecialchars($row['hex_code'], ENT_QUOTES, 'UTF-8') ?>;border:1px solid var(--yofi-border)"></span>
                                <?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td><span class="product-cell-code"><?= htmlspecialchars($row['hex_code'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input toggle-estado" type="checkbox"
                                           data-endpoint="<?= htmlspecialchars(app_path('admin/api/toggle-color-activo.php'), ENT_QUOTES, 'UTF-8') ?>"
                                           data-id-key="id_color"
                                           data-id="<?= (int)$row['id_color'] ?>"
                                           aria-label="Color <?= (int)$row['activo'] ? 'activo' : 'inactivo' ?>"
                                           <?= (int)$row['activo'] ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="a_color.php?id=<?= (int)$row['id_color'] ?>" class="btn-table-action" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted py-5"><?= $q !== '' ? 'Sin resultados para esta búsqueda' : 'Sin colores' ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../pie.php'; ?>
