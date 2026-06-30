<?php
ob_start();
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$pageTitle = 'Slider del home';

$result = mysqli_query($con, 'SELECT id_slide, imagen, imagen_mobile, link_url, orden, activo FROM tbl_slider ORDER BY orden ASC, id_slide ASC');
$rows = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}
$total = count($rows);

include __DIR__ . '/header.php';
echo agregado($_GET['agregado'] ?? '', '', 'slide');
echo modificado($_GET['modificado'] ?? '', '', 'slide');
echo borrado($_GET['borrado'] ?? '', '', 'slide');
?>

<div class="admin-section-header">
    <div>
        <h1>Slider del home</h1>
        <p class="subtitle"><?= (int)$total ?> slide<?= $total === 1 ? '' : 's' ?> — solo imágenes, sin texto superpuesto</p>
    </div>
    <div class="admin-section-actions">
        <a href="a_slide.php" class="btn btn-ink">
            <i class="bi bi-plus-lg"></i> Nuevo slide
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="admin-table mb-0">
                <thead>
                    <tr>
                        <th style="width:120px">Imagen</th>
                        <th style="width:72px">Mobile</th>
                        <th>Link</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th style="width:88px"></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($total > 0): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td>
                                <img
                                    src="<?= htmlspecialchars(imgprod_path((string)$row['imagen']), ENT_QUOTES, 'UTF-8') ?>"
                                    alt=""
                                    class="rounded"
                                    style="width:96px;height:56px;object-fit:cover"
                                >
                            </td>
                            <td>
                                <?php if (!empty($row['imagen_mobile'])): ?>
                                <img
                                    src="<?= htmlspecialchars(imgprod_path((string)$row['imagen_mobile']), ENT_QUOTES, 'UTF-8') ?>"
                                    alt=""
                                    class="rounded border border-success"
                                    style="width:40px;height:50px;object-fit:cover"
                                    title="Tiene imagen mobile"
                                >
                                <?php else: ?>
                                <span class="text-muted" title="Sin imagen mobile"><i class="bi bi-phone"></i></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row['link_url'])): ?>
                                    <span class="product-cell-code"><?= htmlspecialchars((string)$row['link_url'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?= (int)$row['orden'] ?></td>
                            <td>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input toggle-estado" type="checkbox"
                                           data-endpoint="<?= htmlspecialchars(app_path('admin/api/toggle-slide-activo.php'), ENT_QUOTES, 'UTF-8') ?>"
                                           data-id-key="id_slide"
                                           data-id="<?= (int)$row['id_slide'] ?>"
                                           aria-label="Slide <?= (int)$row['activo'] ? 'activo' : 'inactivo' ?>"
                                           <?= (int)$row['activo'] ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="a_slide.php?id=<?= (int)$row['id_slide'] ?>" class="btn-table-action" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="post" action="b_slide.php" class="d-inline" onsubmit="return confirm('¿Eliminar este slide?')">
                                        <?= admin_csrf_field() ?>
                                        <input type="hidden" name="id_slide" value="<?= (int)$row['id_slide'] ?>">
                                        <button type="submit" class="btn-table-action btn-table-action-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">Sin slides configurados</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
