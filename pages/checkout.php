<?php

require_once __DIR__ . '/../src/php/auth.php';
require_once __DIR__ . '/../src/php/addresses.php';

$page_title = 'Finalizar compra | ' . SITE_NAME;
$meta_description = 'Completá tus datos de envío y pagá con Mercado Pago.';

$checkoutUser = null;
$checkoutAddresses = [];

if (isUserLoggedIn()) {
    $uid = getLoggedInUserId();
    if ($uid) {
        $checkoutUser = getUserData($uid);
        $checkoutAddresses = getUserAddresses($uid);
    }
}

$checkoutBootstrap = [
    'user' => $checkoutUser ? [
        'firstName' => $checkoutUser['nombre'] ?? '',
        'lastName' => $checkoutUser['apellido'] ?? '',
        'email' => $checkoutUser['email'] ?? '',
        'phone' => $checkoutUser['telefono'] ?? '',
    ] : null,
    'addresses' => array_map(static function (array $a): array {
        $line = trim(($a['calle'] ?? '') . ' ' . ($a['numero'] ?? ''));
        if (!empty($a['depto'])) {
            $line .= ', ' . $a['depto'];
        }

        return [
            'id' => (int) ($a['id_direccion'] ?? 0),
            'label' => $line,
            'address' => $line,
            'city' => $a['ciudad'] ?? '',
            'province' => $a['provincia'] ?? '',
            'zip' => $a['cp'] ?? '',
            'default' => !empty($a['predeterminada']),
        ];
    }, $checkoutAddresses),
];

?>
<section class="bg-cream/30 border-b border-cream">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <h1 class="text-2xl md:text-3xl font-extrabold text-dark">Finalizar compra</h1>
        <p class="text-sm text-earth mt-1">Completá tus datos, elegí el envío y pagá con Mercado Pago.</p>
        <ol class="flex flex-wrap gap-2 mt-6 text-xs font-bold uppercase tracking-wide" data-checkout-steps>
            <li class="px-3 py-1 rounded-full bg-primary text-dark" data-step-indicator="1">1. Envío</li>
            <li class="px-3 py-1 rounded-full bg-white text-earth border border-cream" data-step-indicator="2">2. Revisión</li>
            <li class="px-3 py-1 rounded-full bg-white text-earth border border-cream" data-step-indicator="3">3. Pago</li>
        </ol>
    </div>
</section>

<div class="container mx-auto px-4 py-10 max-w-6xl" data-checkout-root>
    <div id="checkout-banner" class="hidden mb-6 rounded-xl px-4 py-3 text-sm font-semibold" role="alert"></div>

    <div class="grid lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2 space-y-6">
            <!-- Paso 1: datos -->
            <div data-checkout-panel="1">
                <?php if ($checkoutAddresses !== []): ?>
                <div class="bg-white rounded-2xl border border-cream p-6 mb-6">
                    <h2 class="text-lg font-extrabold text-dark mb-3">Direcciones guardadas</h2>
                    <div class="space-y-2" data-checkout-address-list></div>
                    <button type="button" class="mt-3 text-sm text-accent underline font-semibold" data-checkout-use-manual>
                        Usar otra dirección
                    </button>
                </div>
                <?php endif; ?>

                <form class="space-y-6" id="checkout-form" novalidate>
                    <div class="bg-white rounded-2xl border border-cream p-6">
                        <h2 class="text-lg font-extrabold text-dark mb-4">Contacto</h2>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <label class="block text-sm font-semibold">Nombre *
                                <input type="text" name="firstName" id="co-firstName" required class="mt-1 w-full rounded-xl border border-cream px-3 py-2.5 text-sm" autocomplete="given-name">
                            </label>
                            <label class="block text-sm font-semibold">Apellido *
                                <input type="text" name="lastName" id="co-lastName" required class="mt-1 w-full rounded-xl border border-cream px-3 py-2.5 text-sm" autocomplete="family-name">
                            </label>
                            <label class="block text-sm font-semibold sm:col-span-2">Email *
                                <input type="email" name="email" id="co-email" required class="mt-1 w-full rounded-xl border border-cream px-3 py-2.5 text-sm" autocomplete="email">
                            </label>
                            <label class="block text-sm font-semibold">Teléfono *
                                <input type="tel" name="phone" id="co-phone" required class="mt-1 w-full rounded-xl border border-cream px-3 py-2.5 text-sm" autocomplete="tel">
                            </label>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-cream p-6" data-checkout-shipping-form>
                        <h2 class="text-lg font-extrabold text-dark mb-4">Envío</h2>
                        <div class="space-y-4">
                            <label class="block text-sm font-semibold">Dirección (calle y número) *
                                <input type="text" name="address" id="co-address" required class="mt-1 w-full rounded-xl border border-cream px-3 py-2.5 text-sm" autocomplete="street-address">
                            </label>
                            <label class="block text-sm font-semibold">Depto / piso (opcional)
                                <input type="text" name="depto" id="co-depto" class="mt-1 w-full rounded-xl border border-cream px-3 py-2.5 text-sm">
                            </label>
                            <div class="grid sm:grid-cols-3 gap-4">
                                <label class="block text-sm font-semibold">Ciudad *
                                    <input type="text" name="city" id="co-city" required class="mt-1 w-full rounded-xl border border-cream px-3 py-2.5 text-sm" autocomplete="address-level2">
                                </label>
                                <label class="block text-sm font-semibold">Provincia *
                                    <input type="text" name="province" id="co-province" required class="mt-1 w-full rounded-xl border border-cream px-3 py-2.5 text-sm" autocomplete="address-level1">
                                </label>
                                <label class="block text-sm font-semibold">Código postal *
                                    <input type="text" name="zip" id="co-zip" required inputmode="numeric" class="mt-1 w-full rounded-xl border border-cream px-3 py-2.5 text-sm" autocomplete="postal-code">
                                </label>
                            </div>
                            <label class="block text-sm font-semibold">Notas (opcional)
                                <textarea name="notes" id="co-notes" rows="2" class="mt-1 w-full rounded-xl border border-cream px-3 py-2.5 text-sm"></textarea>
                            </label>
                        </div>

                        <div class="mt-6 border-t border-cream pt-6" data-shipping-options-wrap>
                            <h3 class="text-sm font-extrabold text-dark mb-3">Opciones de envío</h3>
                            <p class="text-sm text-earth" data-shipping-placeholder>Completá CP, ciudad y provincia para cotizar.</p>
                            <div class="hidden space-y-2 mt-3" data-shipping-options></div>
                            <p class="hidden text-sm text-accent mt-2" data-shipping-error></p>
                            <p class="hidden text-sm text-earth mt-2" data-shipping-loading>Cotizando envío…</p>
                        </div>
                    </div>

                    <button type="button" class="w-full sm:w-auto h-12 px-8 rounded-full bg-dark text-white font-extrabold text-sm hover:bg-dark/90 transition" data-checkout-next="2">
                        Continuar a revisión
                    </button>
                </form>
            </div>

            <!-- Paso 2: revisión -->
            <div class="hidden space-y-6" data-checkout-panel="2">
                <div class="bg-white rounded-2xl border border-cream p-6">
                    <h2 class="text-lg font-extrabold text-dark mb-4">Revisá tu pedido</h2>
                    <div data-checkout-review-items class="space-y-4 text-sm"></div>
                    <div class="mt-6 pt-4 border-t border-cream space-y-2 text-sm" data-checkout-review-totals></div>
                    <div class="mt-4 pt-4 border-t border-cream text-sm text-earth" data-checkout-review-shipping></div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button type="button" class="h-11 px-6 rounded-full border-2 border-dark text-dark font-bold text-sm" data-checkout-back="1">Volver a editar</button>
                    <a href="<?php echo htmlspecialchars(page_path('catalogo'), ENT_QUOTES, 'UTF-8'); ?>" class="h-11 px-6 inline-flex items-center rounded-full border border-cream text-earth font-semibold text-sm">Seguir comprando</a>
                    <button type="button" class="h-11 px-8 rounded-full bg-primary text-dark font-extrabold text-sm" data-checkout-next="3">Confirmar y pagar</button>
                </div>
            </div>

            <!-- Paso 3: pago -->
            <div class="hidden space-y-6" data-checkout-panel="3">
                <div class="bg-white rounded-2xl border border-cream p-6">
                    <h2 class="text-lg font-extrabold text-dark mb-2">Método de pago</h2>
                    <p class="text-sm text-earth mb-4">Serás redirigido a Mercado Pago para completar el pago de forma segura.</p>
                    <div class="space-y-2" data-checkout-payment-methods></div>
                </div>
                <button type="button" class="w-full h-14 rounded-full bg-accent text-white font-extrabold text-base tracking-wide hover:brightness-95 transition disabled:opacity-50" data-checkout-submit>
                    Pagar con Mercado Pago
                </button>
                <button type="button" class="text-sm text-earth underline" data-checkout-back="2">Volver a revisión</button>
            </div>
        </div>

        <!-- Resumen lateral -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-cream p-6 sticky top-6">
                <h2 class="text-lg font-extrabold text-dark mb-4">Resumen</h2>
                <ul class="space-y-3 text-sm max-h-64 overflow-y-auto" data-checkout-summary-items></ul>
                <div class="mt-4 pt-4 border-t border-cream space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-earth">Subtotal</span><strong data-checkout-subtotal>$0</strong></div>
                    <div class="flex justify-between"><span class="text-earth">Envío</span><strong data-checkout-shipping-cost>A cotizar</strong></div>
                    <div class="flex justify-between text-base pt-2 border-t border-cream font-extrabold"><span>Total</span><span data-checkout-total>$0</span></div>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
window.YOFI_CHECKOUT = <?php echo json_encode([
    'bootstrap' => $checkoutBootstrap,
    'apiCotizar' => app_path('public/api/zipnova/cotizar.php'),
    'apiProcess' => app_path('checkout/process.php'),
    'apiPreference' => app_path('public/api/mercadopago/create-preference.php'),
    'apiConfig' => app_path('public/api/checkout-config.php'),
    'pageCatalogo' => page_path('catalogo'),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
</script>
<script src="<?php echo asset_path('js/checkout.js'); ?>"></script>
