<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/php/db.php';

$page_title = 'Pago rechazado | ' . SITE_NAME;
$meta_description = 'No se pudo completar el pago con Mercado Pago.';

$orderId = isset($_GET['order']) ? (int) $_GET['order'] : 0;
$order = null;

if ($orderId > 0) {
    $stmt = db_ro()->prepare('SELECT id_orden, numero_orden, estado, total FROM tbl_ordenes WHERE id_orden = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
<section class="container mx-auto px-4 py-16 max-w-2xl text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-accent/15 text-accent text-2xl mb-6" aria-hidden="true">✕</div>
    <p class="text-xs font-bold uppercase tracking-wide text-earth">Mercado Pago</p>
    <h1 class="text-2xl md:text-3xl font-extrabold text-dark mt-2">No se pudo completar el pago</h1>

    <?php if ($order): ?>
        <p class="text-earth mt-4">
            Pedido <strong>#<?php echo htmlspecialchars((string) $order['numero_orden'], ENT_QUOTES, 'UTF-8'); ?></strong>
            — estado: <strong><?php echo htmlspecialchars((string) $order['estado'], ENT_QUOTES, 'UTF-8'); ?></strong>.
            Podés intentar nuevamente desde el checkout.
        </p>
    <?php else: ?>
        <p class="text-earth mt-4">No pudimos identificar tu pedido.</p>
    <?php endif; ?>

    <div class="flex flex-wrap justify-center gap-3 mt-8">
        <a href="<?php echo htmlspecialchars(page_path('checkout'), ENT_QUOTES, 'UTF-8'); ?>" class="h-12 px-8 inline-flex items-center rounded-full bg-dark text-white font-extrabold text-sm">
            Volver al checkout
        </a>
        <?php if ($order): ?>
            <a href="<?php echo htmlspecialchars(page_path('confirmacion') . '&order=' . (int) $order['id_orden'], ENT_QUOTES, 'UTF-8'); ?>" class="h-12 px-8 inline-flex items-center rounded-full border-2 border-dark text-dark font-bold text-sm">
                Ver pedido
            </a>
        <?php endif; ?>
    </div>
</section>
