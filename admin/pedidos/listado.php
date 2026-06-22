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
    SELECT id_orden, numero_orden, CONCAT(nombre, ' ', apellido) AS cliente, total, estado, fecha_creacion
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

$estados = ['pendiente', 'confirmado', 'en_preparacion', 'preparando_envio', 'enviado', 'entregado', 'cancelado'];

include __DIR__ . '/../header.php';
?>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($estados as $e): ?>
                        <option value="<?= $e ?>" <?= $estado === $e ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $e)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($desde, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($hasta, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-yofi w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header card-header-yofi"><strong>Listado de pedidos</strong></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
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
                            <td><?= htmlspecialchars($row['numero_orden'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['cliente'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= format_money((float)$row['total']) ?></td>
                            <td><?= estado_pedido_badge((string)$row['estado']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><a href="detalle.php?id=<?= (int)$row['id_orden'] ?>" class="btn btn-sm btn-outline-primary">Ver</a></td>
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

<?php include __DIR__ . '/../pie.php'; ?>
