<?php
/**
 * Drawer lateral del carrito — contenido renderizado por cart.js (localStorage).
 */
?>
<div
    id="cart-drawer"
    class="fixed inset-0 z-50 hidden"
    data-cart-panel
    aria-hidden="true"
>
    <div class="absolute inset-0 bg-dark/50 transition-opacity" data-cart-dismiss aria-hidden="true"></div>

    <aside
        class="absolute top-0 right-0 h-full w-full max-w-md bg-white shadow-xl flex flex-col transform transition-transform duration-300 translate-x-full"
        data-cart-drawer
        role="dialog"
        aria-modal="true"
        aria-labelledby="cart-drawer-title"
    >
        <header class="flex items-center justify-between px-6 py-5 border-b border-cream">
            <div>
                <h2 id="cart-drawer-title" class="text-lg font-extrabold text-dark">Tu carrito</h2>
                <p class="text-xs text-earth mt-0.5" data-cart-count-badge style="display: none;">0 productos</p>
            </div>
            <button
                type="button"
                class="p-2 -mr-2 text-dark hover:text-accent transition-colors"
                data-cart-dismiss
                aria-label="Cerrar carrito"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </header>

        <div class="flex-1 overflow-y-auto px-6 py-4" data-cart-content>
            <p class="text-sm text-earth text-center py-12">Tu carrito está vacío.</p>
        </div>

        <footer class="border-t border-cream px-6 py-5 space-y-4 bg-cream/30">
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-earth">Subtotal</span>
                    <strong class="font-bold text-dark" data-cart-subtotal>$0</strong>
                </div>
                <div class="flex items-center justify-between text-base pt-2 border-t border-cream">
                    <span class="font-bold text-dark">Total</span>
                    <strong class="text-xl font-extrabold text-dark" data-cart-total>$0</strong>
                </div>
                <p class="text-xs text-earth hidden" data-cart-free-shipping>
                    Te faltan $<span data-cart-free-shipping-amount>0</span> para envío gratis
                </p>
            </div>

            <div class="space-y-2">
                <a
                    href="<?php echo page_path('checkout'); ?>"
                    class="block w-full h-12 rounded-full bg-primary text-dark font-extrabold text-sm tracking-wide text-center leading-[3rem] hover:brightness-95 transition"
                >
                    Finalizar compra
                </a>
                <button
                    type="button"
                    class="w-full h-11 rounded-full border-2 border-dark text-dark font-bold text-sm hover:bg-dark hover:text-white transition"
                    data-cart-dismiss
                >
                    Seguir comprando
                </button>
            </div>
        </footer>
    </aside>
</div>
