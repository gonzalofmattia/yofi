<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/php/db.php';
require_once __DIR__ . '/../src/php/auth.php';
require_once __DIR__ . '/../config/mercadopago.php';
require_once __DIR__ . '/../src/php/mp_mercadopago_sync.php';

$page_title = 'Pago aprobado | ' . SITE_NAME;
$meta_description = 'Confirmación de tu pago con Mercado Pago.';

$orderId = isset($_GET['order']) ? (int) $_GET['order'] : 0;
$order = null;
$loggedIn = isUserLoggedIn();

if ($orderId > 0) {
    $mpPaymentId = trim((string) ($_GET['payment_id'] ?? $_GET['collection_id'] ?? ''));
    if ($mpPaymentId !== '') {
        try {
            mp_mercadopago_sync_payment(db_rw(), $mpPaymentId);
        } catch (Throwable $e) {
            error_log('pago-exitoso MP sync: ' . $e->getMessage());
        }
    }

    $stmt = db_ro()->prepare('SELECT id_orden, numero_orden, estado, total, subtotal, envio, fecha_creacion FROM tbl_ordenes WHERE id_orden = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
}

$estadoNorm = $order ? strtolower(trim((string) $order['estado'])) : '';
$yaConfirmado = $estadoNorm === 'confirmado';

?>
<section class="container mx-auto px-4 py-16 max-w-2xl text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-700 text-2xl mb-6" aria-hidden="true">✓</div>
    <p class="text-xs font-bold uppercase tracking-wide text-earth">Mercado Pago</p>
    <h1 class="text-2xl md:text-3xl font-extrabold text-dark mt-2">¡Gracias! Tu pago fue aprobado.</h1>

    <?php if ($order): ?>
        <p class="text-earth mt-4 leading-relaxed">
            <?php if ($yaConfirmado): ?>
                Tu pedido <strong>#<?php echo htmlspecialchars((string) $order['numero_orden'], ENT_QUOTES, 'UTF-8'); ?></strong> quedó <strong>confirmado</strong>.
                Te enviamos un correo con el detalle; si no lo ves, revisá spam.
            <?php else: ?>
                Estamos actualizando el estado de tu pedido <strong>#<?php echo htmlspecialchars((string) $order['numero_orden'], ENT_QUOTES, 'UTF-8'); ?></strong>.
                Estado actual: <strong><?php echo htmlspecialchars((string) $order['estado'], ENT_QUOTES, 'UTF-8'); ?></strong>.
                Suele pasar a confirmado en segundos cuando llega la notificación de Mercado Pago.
            <?php endif; ?>
        </p>
        <p class="text-sm text-earth mt-2">Total: <strong><?php echo htmlspecialchars(format_money_ars((float) $order['total']), ENT_QUOTES, 'UTF-8'); ?></strong></p>
    <?php else: ?>
        <p class="text-earth mt-4">No pudimos identificar tu pedido. Contactanos si necesitás ayuda.</p>
    <?php endif; ?>

    <div class="flex flex-wrap justify-center gap-3 mt-8">
        <?php if ($order): ?>
            <a href="<?php echo htmlspecialchars(page_path('confirmacion') . '&order=' . (int) $order['id_orden'], ENT_QUOTES, 'UTF-8'); ?>" class="h-12 px-8 inline-flex items-center rounded-full bg-primary text-dark font-extrabold text-sm">
                Ver detalle del pedido
            </a>
        <?php endif; ?>
        <?php if ($loggedIn): ?>
            <a href="<?php echo htmlspecialchars(page_path('mi-cuenta') . '&tab=pedidos', ENT_QUOTES, 'UTF-8'); ?>" class="h-12 px-8 inline-flex items-center rounded-full border-2 border-dark text-dark font-bold text-sm">
                Mis pedidos
            </a>
        <?php else: ?>
            <a href="<?php echo htmlspecialchars(page_path('registro'), ENT_QUOTES, 'UTF-8'); ?>" class="h-12 px-8 inline-flex items-center rounded-full border-2 border-dark text-dark font-bold text-sm">
                Crear cuenta (opcional)
            </a>
        <?php endif; ?>
        <a href="<?php echo htmlspecialchars(page_path('catalogo'), ENT_QUOTES, 'UTF-8'); ?>" class="h-12 px-8 inline-flex items-center rounded-full border border-cream text-earth font-semibold text-sm">
            Seguir comprando
        </a>
    </div>
</section>
<script>
try { localStorage.removeItem('yofi_cart'); } catch (e) {}
if (window.YofiCart && window.YofiCart.updateCartBadge) window.YofiCart.updateCartBadge();
</script>
