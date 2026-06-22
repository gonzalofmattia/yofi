<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$pageTitle = 'Pedidos';

$estado = trim((string)($_GET['estado'] ?? ''));
$desde = trim((string)($_GET['desde'] ?? ''));
$hasta = trim((string)($_GET['hasta'] ?? ''));

$where = 'WHERE deleted_at IS NULL';
$params = [];
$types = '';

if ($estado !== '') {
    $where .= ' AND estado = ?';
    $params[] = $estado;
    $types .= 's';
}
if ($desde !== '') {
    $where .= ' AND DATE(fecha_creacion) >= ?';
    $params[] = $desde;
    $types .= 's';
}
if ($hasta !== '') {
    $where .= ' AND DATE(fecha_creacion) <= ?';
    $params[] = $hasta;
    $types .= 's';
}

$sql = "
    SELECT id_orden, numero_orden, CONCAT(nombre, ' ', apellido) AS cliente, email, total, estado, fecha_creacion
    FROM tbl_ordenes
    $where
    ORDER BY fecha_creacion DESC
    LIMIT 100
";
$stmt = mysqli_prepare($con, $sql);
if ($types !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$tabEstados = [
    '' => 'Todos',
    'pendiente' => 'Pendientes',
    'confirmado' => 'Confirmados',
    'enviado' => 'Enviados',
    'cancelado' => 'Cancelados',
];

function pedidos_tab_url(string $estado, string $desde, string $hasta): string
{
    $parts = [];
    if ($estado !== '') {
        $parts[] = 'estado=' . urlencode($estado);
    }
    if ($desde !== '') {
        $parts[] = 'desde=' . urlencode($desde);
    }
    if ($hasta !== '') {
        $parts[] = 'hasta=' . urlencode($hasta);
    }

    return 'listado.php' . ($parts ? '?' . implode('&', $parts) : '');
}

include __DIR__ . '/../header.php';
?>

<div class="admin-section-header">
    <div>
        <h1>Pedidos</h1>
        <p class="subtitle">Gestión de ventas y envíos</p>
    </div>
</div>

<div class="admin-tabs" id="pedidosTabs">
    <?php foreach ($tabEstados as $val => $label): ?>
    <a href="<?= htmlspecialchars(pedidos_tab_url($val, $desde, $hasta), ENT_QUOTES, 'UTF-8') ?>"
       class="admin-tab pedido-tab<?= $estado === $val ? ' active' : '' ?>"
       data-estado="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
    <?php endforeach; ?>
</div>

<div class="admin-card mb-3">
    <div class="admin-card-body">
        <form method="get" class="row g-2 align-items-end" id="pedidosFilterForm">
            <input type="hidden" name="estado" id="filtroEstado" value="<?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="<?= htmlspecialchars($desde, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="<?= htmlspecialchars($hasta, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-ink btn-sm w-100">Filtrar por fecha</button>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="admin-table mb-0">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($row['numero_orden'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($row['cliente'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="product-cell-code"><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></div>
                            </td>
                            <td><?= format_money((float)$row['total']) ?></td>
                            <td><?= estado_pedido_badge((string)$row['estado']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><a href="detalle.php?id=<?= (int)$row['id_orden'] ?>" class="btn btn-sm btn-outline-ink">Ver</a></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-5">Sin pedidos</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../pie.php'; ?>
