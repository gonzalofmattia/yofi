<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$id_orden = (int)($_GET['id'] ?? 0);
if ($id_orden <= 0) {
    header('Location: listado.php');
    exit();
}

$stmt = mysqli_prepare($con, 'SELECT * FROM tbl_ordenes WHERE id_orden = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id_orden);
mysqli_stmt_execute($stmt);
$orden = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$orden) {
    header('Location: listado.php');
    exit();
}

$pageTitle = 'Pedido ' . $orden['numero_orden'];
$items = json_decode((string)($orden['items'] ?? '[]'), true);
if (!is_array($items)) {
    $items = [];
}

$historial = mysqli_query($con, "
    SELECT estado_anterior, estado_nuevo, fecha_cambio, usuario_admin, notas, tracking_number
    FROM tbl_ordenes_historial
    WHERE id_orden = $id_orden
    ORDER BY fecha_cambio DESC
");

$estados = ['pendiente', 'confirmado', 'enviado', 'entregado', 'cancelado'];

include __DIR__ . '/../header.php';
?>

<div class="admin-section-header">
    <div>
        <h1>Pedido <?= htmlspecialchars($orden['numero_orden'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="subtitle"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($orden['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="admin-section-actions">
        <?= estado_pedido_badge((string)$orden['estado']) ?>
        <a href="listado.php" class="btn btn-outline-ink btn-sm">Volver</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="admin-card-header"><span>Items del pedido</span></div>
            <div class="admin-card-body p-0">
                <table class="admin-table mb-0">
                    <thead><tr><th></th><th>Producto</th><th>Variante</th><th>Cant.</th><th>Precio</th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td style="width:60px">
                                <?php if (!empty($item['imagen'])): ?>
                                    <img src="<?= htmlspecialchars(order_item_image_url((string)$item['imagen']), ENT_QUOTES, 'UTF-8') ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:6px">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars((string)($item['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars(trim(($item['color_nombre'] ?? '') . ' / ' . ($item['talle_nombre'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int)($item['cantidad'] ?? 0) ?></td>
                            <td><?= format_money((float)($item['precio_unitario'] ?? 0)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="admin-card-footer justify-content-end">
                Subtotal: <?= format_money((float)$orden['subtotal']) ?> ·
                Envío: <?= format_money((float)$orden['envio']) ?> ·
                <strong>Total: <?= format_money((float)$orden['total']) ?></strong>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><span>Historial de estados</span></div>
            <div class="admin-card-body p-0">
                <table class="admin-table mb-0">
                    <thead><tr><th>Fecha</th><th>Cambio</th><th>Usuario</th><th>Notas</th></tr></thead>
                    <tbody>
                    <?php if ($historial && mysqli_num_rows($historial) > 0): while ($h = mysqli_fetch_assoc($historial)): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($h['fecha_cambio'])), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars(($h['estado_anterior'] ?? '-') . ' → ' . ($h['estado_nuevo'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)($h['usuario_admin'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)($h['notas'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="4" class="text-muted text-center py-3">Sin historial</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header card-header-yofi"><strong>Cliente y envío</strong></div>
            <div class="card-body">
                <p class="mb-1"><strong><?= htmlspecialchars($orden['nombre'] . ' ' . $orden['apellido'], ENT_QUOTES, 'UTF-8') ?></strong></p>
                <p class="mb-1"><?= htmlspecialchars($orden['email'], ENT_QUOTES, 'UTF-8') ?></p>
                <p class="mb-1"><?= htmlspecialchars($orden['telefono'], ENT_QUOTES, 'UTF-8') ?></p>
                <hr>
                <p class="mb-1"><?= htmlspecialchars($orden['direccion'], ENT_QUOTES, 'UTF-8') ?></p>
                <p class="mb-1"><?= htmlspecialchars($orden['ciudad'] . ', ' . $orden['provincia'] . ' (' . $orden['codigo_postal'] . ')', ENT_QUOTES, 'UTF-8') ?></p>
                <?php if ($orden['shipping_carrier']): ?>
                    <p class="mb-0 small text-muted">Transporte: <?= htmlspecialchars($orden['shipping_carrier'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars((string)$orden['shipping_eta'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header card-header-yofi"><strong>Tracking</strong></div>
            <div class="card-body">
                <input type="text" id="trackingNumber" class="form-control mb-2" value="<?= htmlspecialchars((string)($orden['tracking_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Número de seguimiento">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnGuardarTracking">Guardar tracking</button>
                <?php if ($orden['zipnova_shipment_id']): ?>
                    <p class="small text-muted mt-2 mb-0">Shipment ID: <?= htmlspecialchars($orden['zipnova_shipment_id'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header card-header-yofi"><strong>Estado</strong></div>
            <div class="card-body">
                <select id="estadoOrden" class="form-select mb-2">
                    <?php foreach ($estados as $e): ?>
                        <option value="<?= $e ?>" <?= $orden['estado'] === $e ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $e)) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-yofi btn-sm w-100" id="btnCambiarEstado">Actualizar estado</button>
            </div>
        </div>

        <?php if (empty($orden['zipnova_shipment_id']) && $orden['shipping_method_code'] !== 'pickup'): ?>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <button type="button" class="btn btn-yofi w-100" id="btnZipnova">Generar etiqueta Zipnova</button>
                <p class="small text-muted mt-2 mb-0">Requiere carrier_id en shipping_meta de la orden.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
const orderId = <?= $id_orden ?>;

async function postJson(url, body) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-Token': window.YOFI_ADMIN.csrfToken},
        body: JSON.stringify(body)
    });
    return res.json();
}

const btnTracking = document.getElementById('btnGuardarTracking');
btnTracking.addEventListener('click', async () => {
    window.YofiAdmin.setButtonLoading(btnTracking, true, 'Guardando...');
    const data = await postJson('<?= app_path('admin/api/guardar-tracking.php') ?>', {
        id_orden: orderId,
        tracking_number: document.getElementById('trackingNumber').value
    });
    window.YofiAdmin.setButtonLoading(btnTracking, false);
    alert(data.success ? 'Tracking guardado' : (data.error || 'Error'));
});

const btnEstado = document.getElementById('btnCambiarEstado');
btnEstado.addEventListener('click', async () => {
    window.YofiAdmin.setButtonLoading(btnEstado, true, 'Actualizando...');
    const data = await postJson('<?= app_path('admin/api/cambiar-estado-pedido.php') ?>', {
        id_orden: orderId,
        estado: document.getElementById('estadoOrden').value
    });
    if (data.success) {
        location.reload();
    } else {
        window.YofiAdmin.setButtonLoading(btnEstado, false);
        alert(data.error || 'Error');
    }
});

const btnZip = document.getElementById('btnZipnova');
if (btnZip) {
    btnZip.addEventListener('click', async () => {
        window.YofiAdmin.setButtonLoading(btnZip, true, 'Generando etiqueta...');
        const data = await postJson('<?= app_path('admin/api/crear-envio-zipnova.php') ?>', {order_id: orderId});
        if (data.success) {
            location.reload();
        } else {
            window.YofiAdmin.setButtonLoading(btnZip, false);
            alert(data.error || 'Error al crear envío');
        }
    });
}
</script>

<?php include __DIR__ . '/../pie.php'; ?>
