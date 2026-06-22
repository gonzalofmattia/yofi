<?php
ob_start();
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$pageTitle = 'Métodos de pago';

$result = mysqli_query($con, 'SELECT * FROM tbl_metodos_pago ORDER BY orden ASC, nombre ASC');
$rows = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}
$total = count($rows);

include __DIR__ . '/header.php';
?>

<div class="admin-section-header">
    <div>
        <h1>Métodos de pago</h1>
        <p class="subtitle"><?= (int)$total ?> método<?= $total === 1 ? '' : 's' ?> en el checkout</p>
    </div>
    <div class="admin-section-actions">
        <a href="a_metodo_pago.php" class="btn btn-ink">
            <i class="bi bi-plus-lg"></i> Nuevo método
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="admin-table mb-0">
                <thead>
                    <tr>
                        <th>Método</th>
                        <th>Código</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th style="width:48px"></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($total > 0): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <?php if (!empty($row['descripcion'])): ?>
                                    <div class="small text-muted"><?= htmlspecialchars($row['descripcion'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </td>
                            <td><span class="product-cell-code"><?= htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td><?= (int)$row['orden'] ?></td>
                            <td>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input toggle-estado" type="checkbox"
                                           data-endpoint="<?= htmlspecialchars(app_path('admin/api/toggle-metodo-pago-activo.php'), ENT_QUOTES, 'UTF-8') ?>"
                                           data-id-key="id_metodo"
                                           data-id="<?= (int)$row['id_metodo'] ?>"
                                           aria-label="Método <?= (int)$row['activo'] ? 'activo' : 'inactivo' ?>"
                                           <?= (int)$row['activo'] ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="a_metodo_pago.php?id=<?= (int)$row['id_metodo'] ?>" class="btn-table-action" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">Sin métodos de pago configurados</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
