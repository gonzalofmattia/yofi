<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/php/db.php';
require_once __DIR__ . '/../src/php/auth.php';

$page_title = 'Detalle del pedido | ' . SITE_NAME;

$orderId = isset($_GET['order']) ? (int) $_GET['order'] : 0;
$order = null;
$items = [];

if ($orderId > 0) {
    $stmt = db_ro()->prepare('SELECT * FROM tbl_ordenes WHERE id_orden = ? AND deleted_at IS NULL LIMIT 1');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($order) {
        $decoded = json_decode((string) ($order['items'] ?? '[]'), true);
        $items = is_array($decoded) ? $decoded : [];
    }
}

if (!$order) {
    http_response_code(404);
    echo '<section class="container mx-auto px-4 py-16 text-center"><h1 class="text-xl font-bold">Pedido no encontrado</h1></section>';
    return;
}

$estadoLabels = [
    'pendiente' => 'Pendiente de pago',
    'confirmado' => 'Confirmado',
    'en_preparacion' => 'En preparación',
    'enviado' => 'Enviado',
    'entregado' => 'Entregado',
    'cancelado' => 'Cancelado',
];
$estado = strtolower(trim((string) $order['estado']));
$estadoLabel = $estadoLabels[$estado] ?? ucfirst($estado);

?>
<section class="container mx-auto px-4 py-10 max-w-3xl">
    <h1 class="text-2xl font-extrabold text-dark">Pedido #<?php echo htmlspecialchars((string) $order['numero_orden'], ENT_QUOTES, 'UTF-8'); ?></h1>
    <p class="text-sm text-earth mt-1">
        Estado: <strong><?php echo htmlspecialchars($estadoLabel, ENT_QUOTES, 'UTF-8'); ?></strong>
        · <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime((string) $order['fecha_creacion'])), ENT_QUOTES, 'UTF-8'); ?>
    </p>

    <div class="mt-8 bg-white rounded-2xl border border-cream p-6 space-y-4">
        <h2 class="font-extrabold text-dark">Productos</h2>
        <ul class="divide-y divide-cream text-sm">
            <?php foreach ($items as $item): ?>
                <?php if (!is_array($item)) {
                    continue;
                } ?>
                <li class="py-3 flex justify-between gap-4">
                    <span>
                        <?php echo htmlspecialchars((string) ($item['nombre'] ?? 'Producto'), ENT_QUOTES, 'UTF-8'); ?>
                        <?php
                        $variant = trim((string) ($item['color_nombre'] ?? '') . ' ' . (string) ($item['talle_nombre'] ?? ''));
                if ($variant !== ''): ?>
                            <span class="block text-xs text-earth"><?php echo htmlspecialchars($variant, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                        × <?php echo (int) ($item['cantidad'] ?? 1); ?>
                    </span>
                    <strong><?php echo htmlspecialchars(format_money_ars((float) ($item['precio_unitario'] ?? 0) * (int) ($item['cantidad'] ?? 1)), ENT_QUOTES, 'UTF-8'); ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="pt-4 border-t border-cream space-y-1 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span><?php echo htmlspecialchars(format_money_ars((float) $order['subtotal']), ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div class="flex justify-between"><span>Envío</span><span><?php echo htmlspecialchars(format_money_ars((float) $order['envio']), ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div class="flex justify-between font-extrabold text-base pt-2"><span>Total</span><span><?php echo htmlspecialchars(format_money_ars((float) $order['total']), ENT_QUOTES, 'UTF-8'); ?></span></div>
        </div>
    </div>

    <div class="mt-6 bg-cream/30 rounded-2xl p-6 text-sm text-earth">
        <p><strong>Envío a:</strong> <?php echo htmlspecialchars((string) $order['direccion'], ENT_QUOTES, 'UTF-8'); ?>,
            <?php echo htmlspecialchars((string) $order['ciudad'], ENT_QUOTES, 'UTF-8'); ?>,
            <?php echo htmlspecialchars((string) $order['provincia'], ENT_QUOTES, 'UTF-8'); ?>
            (<?php echo htmlspecialchars((string) $order['codigo_postal'], ENT_QUOTES, 'UTF-8'); ?>)</p>
        <?php if (!empty($order['shipping_carrier'])): ?>
            <p class="mt-2"><strong>Transporte:</strong> <?php echo htmlspecialchars((string) $order['shipping_carrier'], ENT_QUOTES, 'UTF-8'); ?>
                <?php if (!empty($order['shipping_eta'])): ?>
                    · <?php echo htmlspecialchars((string) $order['shipping_eta'], ENT_QUOTES, 'UTF-8'); ?>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>

    <?php if (!isUserLoggedIn()): ?>
        <p class="mt-8 text-sm text-center text-earth">
            ¿Querés ver tus pedidos en un solo lugar?
            <a class="text-accent underline font-semibold" href="<?php echo htmlspecialchars(page_path('registro'), ENT_QUOTES, 'UTF-8'); ?>">Creá tu cuenta</a>
            (opcional).
        </p>
    <?php endif; ?>
</section>
