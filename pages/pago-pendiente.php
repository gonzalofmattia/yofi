<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/php/db.php';
require_once __DIR__ . '/../src/php/auth.php';
require_once __DIR__ . '/../config/mercadopago.php';
require_once __DIR__ . '/../src/php/mp_mercadopago_sync.php';

$page_title = 'Pago pendiente | ' . SITE_NAME;
$meta_description = 'Tu pago con Mercado Pago está pendiente de confirmación.';

$orderId = isset($_GET['order']) ? (int) $_GET['order'] : 0;
$order = null;
$loggedIn = isUserLoggedIn();

if ($orderId > 0) {
    $mpPaymentId = trim((string) ($_GET['payment_id'] ?? $_GET['collection_id'] ?? ''));
    if ($mpPaymentId !== '') {
        try {
            mp_mercadopago_sync_payment(db_rw(), $mpPaymentId);
        } catch (Throwable $e) {
            error_log('pago-pendiente MP sync: ' . $e->getMessage());
        }
    }

    $stmt = db_ro()->prepare('SELECT id_orden, numero_orden, estado, total FROM tbl_ordenes WHERE id_orden = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
}

$estadoNorm = $order ? strtolower(trim((string) $order['estado'])) : '';

?>
<section class="container mx-auto px-4 py-16 max-w-2xl text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 text-amber-800 text-2xl mb-6" aria-hidden="true">⏳</div>
    <p class="text-xs font-bold uppercase tracking-wide text-earth">Mercado Pago</p>
    <h1 class="text-2xl md:text-3xl font-extrabold text-dark mt-2">Tu pago está pendiente</h1>

    <?php if ($order): ?>
        <p class="text-earth mt-4 leading-relaxed">
            <?php if ($estadoNorm === 'confirmado'): ?>
                ¡Buenas noticias! El pedido <strong>#<?php echo htmlspecialchars((string) $order['numero_orden'], ENT_QUOTES, 'UTF-8'); ?></strong> ya está confirmado.
            <?php else: ?>
                Mercado Pago aún está procesando el cobro (efectivo, Rapipago, etc.).
                Cuando se acredite, el pedido <strong>#<?php echo htmlspecialchars((string) $order['numero_orden'], ENT_QUOTES, 'UTF-8'); ?></strong> pasará a confirmado automáticamente.
            <?php endif; ?>
        </p>
    <?php else: ?>
        <p class="text-earth mt-4">No pudimos identificar tu pedido.</p>
    <?php endif; ?>

    <div class="flex flex-wrap justify-center gap-3 mt-8">
        <?php if ($order): ?>
            <a href="<?php echo htmlspecialchars(page_path('confirmacion') . '&order=' . (int) $order['id_orden'], ENT_QUOTES, 'UTF-8'); ?>" class="h-12 px-8 inline-flex items-center rounded-full bg-primary text-dark font-extrabold text-sm">
                Ver detalle del pedido
            </a>
        <?php endif; ?>
        <a href="<?php echo htmlspecialchars(page_path('catalogo'), ENT_QUOTES, 'UTF-8'); ?>" class="h-12 px-8 inline-flex items-center rounded-full border border-cream text-earth font-semibold text-sm">
            Seguir comprando
        </a>
    </div>
</section>
