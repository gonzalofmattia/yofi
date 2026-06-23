<?php
ob_start();
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$pageTitle = 'Banners de edad';

$result = mysqli_query($con, 'SELECT id_edad_banner, slug, titulo, subtitulo, imagen, link_url, orden, activo FROM tbl_home_edad_banners ORDER BY orden ASC, id_edad_banner ASC');
$rows = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}
$total = count($rows);

include __DIR__ . '/header.php';
echo modificado($_GET['modificado'] ?? '', '', 'banner');
?>

<div class="admin-section-header">
    <div>
        <h1>Banners de edad</h1>
        <p class="subtitle"><?= (int)$total ?> bloque<?= $total === 1 ? '' : 's' ?> del home — MINI, 1 a 4 y 4 a 12 años</p>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="admin-table mb-0">
                <thead>
                    <tr>
                        <th style="width:120px">Imagen</th>
                        <th>Título</th>
                        <th>Link</th>
                        <th>Estado</th>
                        <th style="width:88px"></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($total > 0): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td>
                                <?php if (!empty($row['imagen'])): ?>
                                <img
                                    src="<?= htmlspecialchars(imgprod_path((string)$row['imagen']), ENT_QUOTES, 'UTF-8') ?>"
                                    alt=""
                                    class="rounded"
                                    style="width:96px;height:72px;object-fit:cover"
                                >
                                <?php else: ?>
                                <span class="text-muted small">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars((string)$row['titulo'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php if (!empty($row['subtitulo'])): ?>
                                <div class="text-muted small"><?= htmlspecialchars((string)$row['subtitulo'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                                <div class="product-cell-code mt-1"><?= htmlspecialchars((string)$row['slug'], ENT_QUOTES, 'UTF-8') ?></div>
                            </td>
                            <td>
                                <?php if (!empty($row['link_url'])): ?>
                                    <span class="product-cell-code"><?= htmlspecialchars((string)$row['link_url'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input toggle-estado" type="checkbox"
                                           data-endpoint="<?= htmlspecialchars(app_path('admin/api/toggle-edad-banner-activo.php'), ENT_QUOTES, 'UTF-8') ?>"
                                           data-id-key="id_edad_banner"
                                           data-id="<?= (int)$row['id_edad_banner'] ?>"
                                           aria-label="Banner <?= (int)$row['activo'] ? 'activo' : 'inactivo' ?>"
                                           <?= (int)$row['activo'] ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="a_banner_edad.php?id=<?= (int)$row['id_edad_banner'] ?>" class="btn-table-action" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted py-5">Sin banners configurados</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
