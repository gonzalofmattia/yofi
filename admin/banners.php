<?php
ob_start();
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$pageTitle = 'Banners del home';
$posicionHome = 'home_secundario';

$result = mysqli_prepare($con, 'SELECT id_banner, eyebrow, titulo, subtitulo, texto_boton, imagen, link_url, posicion, orden, activo FROM tbl_banners WHERE posicion = ? ORDER BY orden ASC, id_banner ASC');
mysqli_stmt_bind_param($result, 's', $posicionHome);
mysqli_stmt_execute($result);
$result = mysqli_stmt_get_result($result);
$rows = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}
$total = count($rows);

include __DIR__ . '/header.php';
echo agregado($_GET['agregado'] ?? '', '', 'banner');
echo modificado($_GET['modificado'] ?? '', '', 'banner');
echo borrado($_GET['borrado'] ?? '', '', 'banner');
?>

<div class="admin-section-header">
    <div>
        <h1>Banners del home</h1>
        <p class="subtitle"><?= (int)$total ?> banner<?= $total === 1 ? '' : 's' ?> en posición <code>home_secundario</code></p>
    </div>
    <div class="admin-section-actions">
        <a href="a_banner.php" class="btn btn-ink">
            <i class="bi bi-plus-lg"></i> Nuevo banner
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
                        <th>Textos</th>
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
                                <?php if (!empty($row['eyebrow'])): ?>
                                    <div class="small text-muted text-uppercase"><?= htmlspecialchars((string)$row['eyebrow'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                                <strong><?= htmlspecialchars((string)($row['titulo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                                <?php if (!empty($row['subtitulo'])): ?>
                                    <div class="small"><?= htmlspecialchars((string)$row['subtitulo'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                                <?php if (!empty($row['texto_boton'])): ?>
                                    <div class="small text-muted">Botón: <?= htmlspecialchars((string)$row['texto_boton'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= (int)$row['orden'] ?></td>
                            <td>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input toggle-estado" type="checkbox"
                                           data-endpoint="<?= htmlspecialchars(app_path('admin/api/toggle-banner-activo.php'), ENT_QUOTES, 'UTF-8') ?>"
                                           data-id-key="id_banner"
                                           data-id="<?= (int)$row['id_banner'] ?>"
                                           aria-label="Banner <?= (int)$row['activo'] ? 'activo' : 'inactivo' ?>"
                                           <?= (int)$row['activo'] ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="a_banner.php?id=<?= (int)$row['id_banner'] ?>" class="btn-table-action" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="post" action="b_banner.php" class="d-inline" onsubmit="return confirm('¿Eliminar este banner?')">
                                        <?= admin_csrf_field() ?>
                                        <input type="hidden" name="id_banner" value="<?= (int)$row['id_banner'] ?>">
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
                        <td colspan="5" class="text-center text-muted py-5">Sin banners configurados</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
