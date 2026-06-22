/**
 * Módulo de gestión del carrito de compras — Yofi
 * Utiliza localStorage para persistir el carrito entre sesiones.
 *
 * Cada item: { id_sku, id_prod, nombre, color_nombre, color_hex,
 *              talle_nombre, precio, cantidad, imagen }
 */

const CART_STORAGE_KEY = 'yofi_cart';

function saveCart(cart) {
    try {
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
    } catch (e) {
        // localStorage no disponible o lleno
    }
    updateCartBadge();
    document.dispatchEvent(new CustomEvent('cart:updated', { detail: { cart } }));
}

function getCart() {
    try {
        const cartData = localStorage.getItem(CART_STORAGE_KEY);
        return cartData ? JSON.parse(cartData) : [];
    } catch (e) {
        return [];
    }
}

function normalizeItem(item) {
    if (!item || item.id_sku === undefined || item.id_sku === null) {
        return null;
    }

    return {
        id_sku: String(item.id_sku),
        id_prod: item.id_prod != null ? String(item.id_prod) : '',
        nombre: item.nombre || 'Producto sin nombre',
        color_nombre: item.color_nombre || '',
        color_hex: item.color_hex || '',
        talle_nombre: item.talle_nombre || '',
        precio: parseFloat(item.precio) || 0,
        cantidad: Math.max(1, parseInt(item.cantidad, 10) || 1),
        imagen: item.imagen || '',
    };
}

function addToCart(item) {
    const product = normalizeItem(typeof item === 'object' && item !== null
        ? { ...item, cantidad: item.cantidad || 1 }
        : null);

    if (!product) {
        return { success: false, message: 'Producto inválido', cart: getCart() };
    }

    if (product.precio <= 0) {
        return { success: false, message: 'Este producto no tiene precio disponible', cart: getCart() };
    }

    const cart = getCart();
    const existingIndex = cart.findIndex(function (entry) {
        return entry.id_sku === product.id_sku;
    });

    if (existingIndex >= 0) {
        cart[existingIndex].cantidad += product.cantidad;
    } else {
        cart.push(product);
    }

    saveCart(cart);
    return { success: true, message: 'Producto agregado al carrito', cart: cart };
}

function removeFromCart(id_sku) {
    const sku = String(id_sku);
    const cart = getCart().filter(function (item) {
        return item.id_sku !== sku;
    });
    saveCart(cart);
    return { success: true, message: 'Producto removido del carrito', cart: cart };
}

function updateQty(id_sku, qty) {
    const sku = String(id_sku);
    const quantity = parseInt(qty, 10);

    if (isNaN(quantity) || quantity < 1) {
        return removeFromCart(sku);
    }

    const cart = getCart();
    const index = cart.findIndex(function (item) {
        return item.id_sku === sku;
    });

    if (index < 0) {
        return { success: false, message: 'Producto no encontrado en el carrito', cart: cart };
    }

    cart[index].cantidad = quantity;
    saveCart(cart);
    return { success: true, message: 'Cantidad actualizada', cart: cart };
}

function getTotal() {
    return getCart().reduce(function (total, item) {
        return total + (parseFloat(item.precio) * parseInt(item.cantidad, 10));
    }, 0);
}

function getCount() {
    return getCart().reduce(function (total, item) {
        return total + parseInt(item.cantidad, 10);
    }, 0);
}

function updateCartBadge() {
    var badge = document.querySelector('[data-cart-count]');
    if (!badge) {
        return;
    }

    var count = getCount();
    if (count > 0) {
        badge.textContent = String(count);
        badge.style.display = '';
    } else {
        badge.style.display = 'none';
    }

    var countBadge = document.querySelector('[data-cart-count-badge]');
    if (countBadge) {
        if (count > 0) {
            countBadge.textContent = count + (count === 1 ? ' producto' : ' productos');
            countBadge.style.display = '';
        } else {
            countBadge.style.display = 'none';
        }
    }
}

function formatMoney(amount) {
    return '$' + Math.round(parseFloat(amount) || 0).toLocaleString('es-AR');
}

function escapeHtml(str) {
    return String(str || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function renderCartDrawer() {
    var panel = document.querySelector('[data-cart-panel]');
    var content = panel ? panel.querySelector('[data-cart-content]') : null;
    var subtotalEl = panel ? panel.querySelector('[data-cart-subtotal]') : null;
    var totalEl = panel ? panel.querySelector('[data-cart-total]') : null;
    var freeShipEl = panel ? panel.querySelector('[data-cart-free-shipping]') : null;
    var freeShipAmt = panel ? panel.querySelector('[data-cart-free-shipping-amount]') : null;

    if (!content) {
        return;
    }

    var cart = getCart();

    if (cart.length === 0) {
        content.innerHTML = '<p class="text-sm text-earth text-center py-12">Tu carrito está vacío.</p>';
    } else {
        content.innerHTML = '<ul class="space-y-4">' + cart.map(function (item) {
            var variant = [item.color_nombre, item.talle_nombre].filter(Boolean).join(' · ');
            return '<li class="flex gap-3 pb-4 border-b border-cream" data-cart-item data-id-sku="' + escapeHtml(item.id_sku) + '">' +
                '<div class="w-20 aspect-[3/4] shrink-0 overflow-hidden bg-[#f6f3ef]">' +
                '<img src="' + escapeHtml(item.imagen) + '" alt="" class="w-full h-full object-cover">' +
                '</div>' +
                '<div class="flex-1 min-w-0">' +
                '<p class="text-sm font-semibold truncate">' + escapeHtml(item.nombre) + '</p>' +
                (variant ? '<p class="text-xs text-earth mt-0.5">' + escapeHtml(variant) + '</p>' : '') +
                '<p class="text-sm font-bold mt-1">' + formatMoney(item.precio) + '</p>' +
                '<div class="flex items-center justify-between mt-2">' +
                '<div class="inline-flex items-center border border-cream rounded-full">' +
                '<button type="button" class="w-8 h-8 text-sm font-bold hover:bg-cream rounded-l-full" data-action="decrease" aria-label="Disminuir">−</button>' +
                '<span class="w-8 text-center text-sm font-semibold" data-qty-display>' + item.cantidad + '</span>' +
                '<button type="button" class="w-8 h-8 text-sm font-bold hover:bg-cream rounded-r-full" data-action="increase" aria-label="Aumentar">+</button>' +
                '</div>' +
                '<button type="button" class="text-xs text-accent underline" data-action="remove">Eliminar</button>' +
                '</div>' +
                '</div></li>';
        }).join('') + '</ul>';
    }

    var subtotal = getTotal();
    if (subtotalEl) subtotalEl.textContent = formatMoney(subtotal);
    if (totalEl) totalEl.textContent = formatMoney(subtotal);

    var freeShippingMin = parseInt(document.body.getAttribute('data-free-shipping-threshold') || '0', 10);
    if (freeShipEl && freeShipAmt) {
        if (freeShippingMin > 0 && subtotal > 0 && subtotal < freeShippingMin) {
            freeShipEl.classList.remove('hidden');
            freeShipAmt.textContent = Math.round(freeShippingMin - subtotal).toLocaleString('es-AR');
        } else {
            freeShipEl.classList.add('hidden');
        }
    }
}

function openCartDrawer() {
    var panel = document.querySelector('[data-cart-panel]');
    var drawer = panel ? panel.querySelector('[data-cart-drawer]') : null;
    if (!panel || !drawer) return;

    renderCartDrawer();
    panel.classList.remove('hidden');
    panel.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(function () {
        drawer.classList.remove('translate-x-full');
    });
}

function closeCartDrawer() {
    var panel = document.querySelector('[data-cart-panel]');
    var drawer = panel ? panel.querySelector('[data-cart-drawer]') : null;
    if (!panel || !drawer) return;

    drawer.classList.add('translate-x-full');
    panel.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    setTimeout(function () {
        panel.classList.add('hidden');
    }, 300);
}

function initCartDrawer() {
    var panel = document.querySelector('[data-cart-panel]');
    if (!panel) return;

    document.querySelectorAll('[data-cart-trigger]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            openCartDrawer();
        });
    });

    panel.querySelectorAll('[data-cart-dismiss]').forEach(function (el) {
        el.addEventListener('click', closeCartDrawer);
    });

    panel.addEventListener('click', function (e) {
        var item = e.target.closest('[data-cart-item]');
        if (!item) return;
        var sku = item.getAttribute('data-id-sku');

        if (e.target.closest('[data-action="remove"]')) {
            removeFromCart(sku);
            renderCartDrawer();
            return;
        }

        if (e.target.closest('[data-action="increase"]')) {
            var current = getCart().find(function (i) { return i.id_sku === sku; });
            if (current) updateQty(sku, current.cantidad + 1);
            renderCartDrawer();
            return;
        }

        if (e.target.closest('[data-action="decrease"]')) {
            var cur = getCart().find(function (i) { return i.id_sku === sku; });
            if (cur) updateQty(sku, cur.cantidad - 1);
            renderCartDrawer();
        }
    });

    document.addEventListener('cart:updated', renderCartDrawer);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !panel.classList.contains('hidden')) {
            closeCartDrawer();
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    updateCartBadge();
    initCartDrawer();
});

window.YofiCart = {
    CART_STORAGE_KEY: CART_STORAGE_KEY,
    getCart: getCart,
    addToCart: addToCart,
    removeFromCart: removeFromCart,
    updateQty: updateQty,
    getTotal: getTotal,
    getCount: getCount,
    updateCartBadge: updateCartBadge,
    openDrawer: openCartDrawer,
    closeDrawer: closeCartDrawer,
    renderDrawer: renderCartDrawer,
};
