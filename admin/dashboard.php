<?php
ob_start();
require_once __DIR__ . '/include/session_init.php';
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';
ob_end_flush();

$pageTitle = 'Dashboard';

$pedidos_hoy = 0;
$ventas_mes = 0.0;
$stock_bajo = 0;
$ultimos_pedidos = false;

$r = mysqli_query($con, "SELECT COUNT(*) AS total FROM tbl_ordenes WHERE DATE(fecha_creacion) = CURDATE()");
if ($r) {
    $pedidos_hoy = (int)(mysqli_fetch_assoc($r)['total'] ?? 0);
}

$r = mysqli_query($con, "SELECT COALESCE(SUM(total), 0) AS total FROM tbl_ordenes WHERE estado = 'confirmado' AND MONTH(fecha_creacion) = MONTH(NOW()) AND YEAR(fecha_creacion) = YEAR(NOW())");
if ($r) {
    $ventas_mes = (float)(mysqli_fetch_assoc($r)['total'] ?? 0);
}

$r = mysqli_query($con, "SELECT COUNT(*) AS total FROM tbl_skus WHERE stock <= 3 AND activo = 1");
if ($r) {
    $stock_bajo = (int)(mysqli_fetch_assoc($r)['total'] ?? 0);
}

$ultimos_pedidos = mysqli_query($con, "
    SELECT id_orden, numero_orden, CONCAT(nombre, ' ', apellido) AS cliente, total, estado, fecha_creacion
    FROM tbl_ordenes
    ORDER BY fecha_creacion DESC
    LIMIT 5
");

include __DIR__ . '/header.php';
?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Pedidos hoy</div>
                <div class="display-6 fw-bold"><?= $pedidos_hoy ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Ventas confirmadas (mes)</div>
                <div class="display-6 fw-bold"><?= format_money($ventas_mes) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">SKUs con stock ≤ 3</div>
                <div class="display-6 fw-bold text-danger"><?= $stock_bajo ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header card-header-yofi">
        <strong>Últimos 5 pedidos</strong>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
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
                <?php if ($ultimos_pedidos && mysqli_num_rows($ultimos_pedidos) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($ultimos_pedidos)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['numero_orden'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['cliente'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= format_money((float)$row['total']) ?></td>
                            <td><?= estado_pedido_badge((string)$row['estado']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><a href="<?= app_path('admin/pedidos/detalle.php?id=' . (int)$row['id_orden']) ?>" class="btn btn-sm btn-outline-secondary">Ver</a></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Sin pedidos</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/pie.php'; ?>
