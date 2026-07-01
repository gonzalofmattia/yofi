/**
 * Checkout multi-paso — Yofi
 */
(function () {
    'use strict';

    var cfg = window.YOFI_CHECKOUT || {};
    var bootstrap = cfg.bootstrap || {};
    var shippingState = {
        options: [],
        selected: null,
        quoting: false,
        quoted: false,
    };
    var paymentMethod = 'mercadopago';
    var paymentMethodLabel = 'Mercado Pago';
    var quoteTimer = null;
    var currentStep = 1;

    function basePath() {
        return document.body.getAttribute('data-base-path') || '';
    }

    function apiUrl(relative) {
        if (!relative) return basePath();
        if (relative.charAt(0) === '/') return relative;
        var bp = basePath();
        return (bp ? bp + '/' : '/') + relative.replace(/^\//, '');
    }

    function csrfHeaders() {
        var h = { 'Content-Type': 'application/json' };
        if (window.YOFI && window.YOFI.csrfToken) {
            h['X-CSRF-Token'] = window.YOFI.csrfToken;
        }
        return h;
    }

    function formatMoney(n) {
        return '$' + Math.round(parseFloat(n) || 0).toLocaleString('es-AR');
    }

    function escapeHtml(s) {
        return String(s || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function getCart() {
        return window.YofiCart ? window.YofiCart.getCart() : [];
    }

    function cartItemsForApi() {
        return getCart().map(function (item) {
            return {
                id_sku: parseInt(item.id_sku, 10),
                id_prod: parseInt(item.id_prod, 10) || 0,
                cantidad: parseInt(item.cantidad, 10) || 1,
                precio_unitario: parseFloat(item.precio) || 0,
                nombre: item.nombre,
                color_nombre: item.color_nombre || '',
                talle_nombre: item.talle_nombre || '',
                imagen: item.imagen || '',
            };
        });
    }

    function showBanner(msg, type) {
        var el = document.getElementById('checkout-banner');
        if (!el) return;
        var colors = {
            error: 'bg-accent/10 text-accent border border-accent/30',
            success: 'bg-green-50 text-green-800 border border-green-200',
            warning: 'bg-amber-50 text-amber-900 border border-amber-200',
        };
        el.className = 'mb-6 rounded-xl px-4 py-3 text-sm font-semibold ' + (colors[type] || colors.warning);
        el.textContent = msg;
        el.classList.remove('hidden');
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideBanner() {
        var el = document.getElementById('checkout-banner');
        if (el) el.classList.add('hidden');
    }

    function setStep(step) {
        currentStep = step;
        document.querySelectorAll('[data-checkout-panel]').forEach(function (panel) {
            var n = parseInt(panel.getAttribute('data-checkout-panel'), 10);
            panel.classList.toggle('hidden', n !== step);
        });
        document.querySelectorAll('[data-step-indicator]').forEach(function (li) {
            var n = parseInt(li.getAttribute('data-step-indicator'), 10);
            if (n === step) {
                li.className = 'px-3 py-1 rounded-full bg-primary text-dark';
            } else if (n < step) {
                li.className = 'px-3 py-1 rounded-full bg-secondary/30 text-dark';
            } else {
                li.className = 'px-3 py-1 rounded-full bg-white text-earth border border-cream';
            }
        });
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function readForm() {
        var depto = (document.getElementById('co-depto') || {}).value || '';
        var address = (document.getElementById('co-address') || {}).value || '';
        if (depto.trim()) {
            address = address.trim() + ', ' + depto.trim();
        }
        return {
            firstName: (document.getElementById('co-firstName') || {}).value.trim(),
            lastName: (document.getElementById('co-lastName') || {}).value.trim(),
            email: (document.getElementById('co-email') || {}).value.trim(),
            phone: (document.getElementById('co-phone') || {}).value.trim(),
            address: address.trim(),
            city: (document.getElementById('co-city') || {}).value.trim(),
            province: (document.getElementById('co-province') || {}).value.trim(),
            zip: (document.getElementById('co-zip') || {}).value.trim(),
            notes: (document.getElementById('co-notes') || {}).value.trim(),
        };
    }

    function validateStep1() {
        var f = readForm();
        var missing = [];
        if (!f.firstName) missing.push('nombre');
        if (!f.lastName) missing.push('apellido');
        if (!f.email) missing.push('email');
        if (!f.phone) missing.push('teléfono');
        if (!f.address) missing.push('dirección');
        if (!f.city) missing.push('ciudad');
        if (!f.province) missing.push('provincia');
        if (!f.zip) missing.push('código postal');
        if (missing.length) {
            showBanner('Completá: ' + missing.join(', '), 'warning');
            return false;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.email)) {
            showBanner('Email inválido', 'warning');
            return false;
        }
        if (!shippingState.selected) {
            showBanner('Seleccioná una opción de envío', 'warning');
            return false;
        }
        hideBanner();
        return true;
    }

    function getSubtotal() {
        return window.YofiCart ? window.YofiCart.getTotal() : 0;
    }

    function getShippingCost() {
        return shippingState.selected ? parseFloat(shippingState.selected.price) || 0 : 0;
    }

    function updateSummary() {
        var cart = getCart();
        var subtotal = getSubtotal();
        var ship = getShippingCost();
        var total = subtotal + ship;

        var html = cart.map(function (item) {
            var variant = [item.color_nombre, item.talle_nombre].filter(Boolean).join(' · ');
            return '<li class="flex gap-3">' +
                (item.imagen ? '<img src="' + escapeHtml(item.imagen) + '" alt="" class="w-14 h-16 object-cover bg-cream/50">' : '') +
                '<div><p class="font-semibold">' + escapeHtml(item.nombre) + '</p>' +
                (variant ? '<p class="text-xs text-earth">' + escapeHtml(variant) + '</p>' : '') +
                '<p class="text-xs">×' + item.cantidad + ' · ' + formatMoney(item.precio * item.cantidad) + '</p></div></li>';
        }).join('');

        var lists = document.querySelectorAll('[data-checkout-summary-items], [data-checkout-review-items]');
        lists.forEach(function (el, i) {
            el.innerHTML = html || '<p class="text-earth">Carrito vacío</p>';
        });

        document.querySelectorAll('[data-checkout-subtotal]').forEach(function (el) {
            el.textContent = formatMoney(subtotal);
        });
        document.querySelectorAll('[data-checkout-shipping-cost]').forEach(function (el) {
            el.textContent = shippingState.selected ? formatMoney(ship) : 'A cotizar';
        });
        document.querySelectorAll('[data-checkout-total]').forEach(function (el) {
            el.textContent = formatMoney(total);
        });

        var reviewTotals = document.querySelector('[data-checkout-review-totals]');
        if (reviewTotals) {
            reviewTotals.innerHTML =
                '<div class="flex justify-between"><span>Subtotal</span><strong>' + formatMoney(subtotal) + '</strong></div>' +
                '<div class="flex justify-between"><span>Envío</span><strong>' + formatMoney(ship) + '</strong></div>' +
                '<div class="flex justify-between text-base font-extrabold pt-2"><span>Total</span><span>' + formatMoney(total) + '</span></div>';
        }

        var reviewShip = document.querySelector('[data-checkout-review-shipping]');
        if (reviewShip && shippingState.selected) {
            var f = readForm();
            reviewShip.innerHTML = '<p><strong>Envío:</strong> ' + escapeHtml(shippingState.selected.carrier || shippingState.selected.label) +
                ' — ' + escapeHtml(shippingState.selected.eta || '') + '</p>' +
                '<p><strong>Dirección:</strong> ' + escapeHtml(f.address) + ', ' + escapeHtml(f.city) + ', ' + escapeHtml(f.province) + ' (' + escapeHtml(f.zip) + ')</p>';
        }
    }

    function renderShippingOptions(opciones) {
        var wrap = document.querySelector('[data-shipping-options]');
        var placeholder = document.querySelector('[data-shipping-placeholder]');
        var errEl = document.querySelector('[data-shipping-error]');
        if (!wrap) return;

        if (errEl) errEl.classList.add('hidden');
        if (!opciones || !opciones.length) {
            wrap.classList.add('hidden');
            if (placeholder) {
                placeholder.classList.remove('hidden');
                placeholder.textContent = 'No hay opciones de envío para este CP.';
            }
            shippingState.selected = null;
            updateSummary();
            return;
        }

        if (placeholder) placeholder.classList.add('hidden');
        wrap.classList.remove('hidden');
        wrap.innerHTML = opciones.map(function (opt, idx) {
            var id = 'ship-opt-' + idx;
            var label = (opt.carrier || opt.service || 'Envío') + (opt.eta ? ' · ' + opt.eta : '');
            return '<label class="flex items-start gap-3 p-3 rounded-xl border border-cream cursor-pointer hover:border-primary has-[:checked]:border-primary has-[:checked]:bg-cream/40">' +
                '<input type="radio" name="shipping_option" value="' + idx + '" id="' + id + '" class="mt-1"' + (idx === 0 ? ' checked' : '') + '>' +
                '<span class="flex-1"><span class="font-semibold block">' + escapeHtml(label) + '</span>' +
                '<span class="text-sm text-earth">' + formatMoney(opt.price) + '</span></span></label>';
        }).join('');

        shippingState.selected = opciones[0];
        wrap.querySelectorAll('input[name="shipping_option"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                var i = parseInt(radio.value, 10);
                shippingState.selected = opciones[i] || null;
                updateSummary();
            });
        });
        updateSummary();
    }

    function requestQuote() {
        var f = readForm();
        var cp = (f.zip || '').replace(/\D/g, '');
        if (cp.length < 4 || !f.city || !f.province) return;

        var cart = getCart();
        if (!cart.length) return;

        var loading = document.querySelector('[data-shipping-loading]');
        if (loading) loading.classList.remove('hidden');

        shippingState.quoting = true;

        fetch(apiUrl(cfg.apiCotizar || 'public/api/zipnova/cotizar.php'), {
            method: 'POST',
            headers: csrfHeaders(),
            body: JSON.stringify({
                cp: cp,
                ciudad: f.city,
                provincia: f.province,
                declared_value: getSubtotal(),
                items: cartItemsForApi(),
            }),
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                shippingState.quoting = false;
                shippingState.quoted = true;
                if (loading) loading.classList.add('hidden');
                if (data.success && data.opciones) {
                    shippingState.options = data.opciones;
                    renderShippingOptions(data.opciones);
                } else {
                    var errEl = document.querySelector('[data-shipping-error]');
                    if (errEl) {
                        errEl.textContent = data.error || 'No se pudo cotizar el envío';
                        errEl.classList.remove('hidden');
                    }
                }
            })
            .catch(function () {
                shippingState.quoting = false;
                if (loading) loading.classList.add('hidden');
                showBanner('Error al cotizar envío. Intentá de nuevo.', 'error');
            });
    }

    function scheduleQuote() {
        clearTimeout(quoteTimer);
        quoteTimer = setTimeout(requestQuote, 600);
    }

    function buildOrderPayload() {
        var f = readForm();
        var cart = getCart();
        var subtotal = getSubtotal();
        var ship = getShippingCost();
        var sel = shippingState.selected || {};

        return {
            customer: {
                firstName: f.firstName,
                lastName: f.lastName,
                email: f.email,
                phone: f.phone,
            },
            shipping: {
                address: f.address,
                city: f.city,
                province: f.province,
                zip: f.zip,
                notes: f.notes,
            },
            payment: { method: paymentMethod },
            items: cartItemsForApi(),
            subtotal: subtotal,
            shipping_cost: ship,
            total: subtotal + ship,
            shipping_method: sel.code || sel.id || '',
            shipping_carrier: sel.carrier || sel.service || '',
            shipping_eta: sel.eta || '',
            shipping_meta: {
                carrier_id: sel.carrier_id || null,
                logistic_type: sel.logistic_type || '',
                service: sel.service || '',
            },
        };
    }

    function markCartStockIssues(issues) {
        if (!issues || !issues.length) return;
        issues.forEach(function (issue) {
            var sku = String(issue.id_sku || '');
            var cart = getCart();
            cart.forEach(function (item) {
                if (String(item.id_sku) === sku) {
                    item._stockIssue = true;
                }
            });
            try {
                localStorage.setItem('yofi_cart', JSON.stringify(cart));
            } catch (e) {}
        });
    }

    function submitButtonLabel() {
        return paymentMethod === 'mercadopago' ? 'Pagar con Mercado Pago' : 'Confirmar pedido — ' + paymentMethodLabel;
    }

    function updatePaymentMethodUi() {
        var btn = document.querySelector('[data-checkout-submit]');
        if (btn && !btn.disabled) btn.textContent = submitButtonLabel();

        var hint = document.querySelector('[data-checkout-payment-hint]');
        if (hint) {
            hint.textContent = paymentMethod === 'mercadopago'
                ? 'Serás redirigido a Mercado Pago para completar el pago de forma segura.'
                : 'Al confirmar, tu pedido quedará registrado con el método de pago seleccionado.';
        }
    }

    function submitOrder() {
        var btn = document.querySelector('[data-checkout-submit]');
        if (!btn || btn.disabled) return;
        if (!validateStep1()) {
            setStep(1);
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Procesando…';

        var orderData = buildOrderPayload();

        fetch(apiUrl(cfg.apiProcess || 'checkout/process.php'), {
            method: 'POST',
            headers: csrfHeaders(),
            body: JSON.stringify(orderData),
        })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d, status: r.status }; }); })
            .then(function (res) {
                if (!res.data || !res.data.success) {
                    if (res.data && res.data.error_code === 'insufficient_stock') {
                        markCartStockIssues(res.data.issues || []);
                        showBanner(res.data.message || 'Sin stock suficiente. Revisá tu carrito.', 'error');
                        setStep(1);
                        setTimeout(function () {
                            window.location.href = apiUrl(cfg.pageCatalogo || 'index.php?p=catalogo');
                        }, 2500);
                    } else {
                        showBanner((res.data && res.data.message) || 'Error al procesar el pedido', 'error');
                    }
                    btn.disabled = false;
                    btn.textContent = submitButtonLabel();
                    return;
                }

                if (paymentMethod !== 'mercadopago') {
                    if (window.YofiCart) {
                        try { localStorage.removeItem('yofi_cart'); } catch (e) {}
                    }
                    window.location.href = apiUrl('index.php?p=confirmacion&order=' + res.data.order_id);
                    return;
                }

                fetch(apiUrl(cfg.apiPreference || 'public/api/mercadopago/create-preference.php'), {
                    method: 'POST',
                    headers: csrfHeaders(),
                    body: JSON.stringify({
                        order_id: res.data.order_id,
                        numero_orden: res.data.numero_orden,
                        orderData: orderData,
                    }),
                })
                    .then(function (r) { return r.json(); })
                    .then(function (mp) {
                        if (mp.success && mp.init_point) {
                            window.location.href = mp.init_point;
                            return;
                        }
                        showBanner(mp.message || 'Error al iniciar Mercado Pago', 'error');
                        btn.disabled = false;
                        btn.textContent = submitButtonLabel();
                    })
                    .catch(function () {
                        showBanner('Error de conexión con Mercado Pago', 'error');
                        btn.disabled = false;
                        btn.textContent = submitButtonLabel();
                    });
            })
            .catch(function () {
                showBanner('Error de conexión. Intentá de nuevo.', 'error');
                btn.disabled = false;
                btn.textContent = submitButtonLabel();
            });
    }

    function prefillUser() {
        var user = bootstrap.user;
        if (!user) return;
        var map = {
            'co-firstName': user.firstName,
            'co-lastName': user.lastName,
            'co-email': user.email,
            'co-phone': user.phone,
        };
        Object.keys(map).forEach(function (id) {
            var el = document.getElementById(id);
            if (el && map[id] && !el.value) el.value = map[id];
        });
    }

    function renderAddresses() {
        var list = document.querySelector('[data-checkout-address-list]');
        if (!list || !bootstrap.addresses || !bootstrap.addresses.length) return;

        list.innerHTML = bootstrap.addresses.map(function (addr) {
            return '<button type="button" class="w-full text-left p-3 rounded-xl border border-cream hover:border-primary text-sm" data-address-id="' + addr.id + '">' +
                '<span class="font-semibold block">' + escapeHtml(addr.label) + '</span>' +
                '<span class="text-earth">' + escapeHtml(addr.city) + ', ' + escapeHtml(addr.province) + ' · CP ' + escapeHtml(addr.zip) + '</span></button>';
        }).join('');

        list.querySelectorAll('[data-address-id]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = parseInt(btn.getAttribute('data-address-id'), 10);
                var addr = bootstrap.addresses.find(function (a) { return a.id === id; });
                if (!addr) return;
                document.getElementById('co-address').value = addr.address || '';
                document.getElementById('co-city').value = addr.city || '';
                document.getElementById('co-province').value = addr.province || '';
                document.getElementById('co-zip').value = addr.zip || '';
                scheduleQuote();
            });
        });

        var def = bootstrap.addresses.find(function (a) { return a.default; }) || bootstrap.addresses[0];
        if (def) {
            document.getElementById('co-address').value = def.address || '';
            document.getElementById('co-city').value = def.city || '';
            document.getElementById('co-province').value = def.province || '';
            document.getElementById('co-zip').value = def.zip || '';
        }
    }

    function loadPaymentMethods() {
        fetch(apiUrl(cfg.apiConfig || 'public/api/checkout-config.php'))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var wrap = document.querySelector('[data-checkout-payment-methods]');
                if (!wrap || !data.metodos_pago) return;
                var defaultMethod = data.metodos_pago.some(function (m) { return m.codigo === 'mercadopago'; })
                    ? 'mercadopago'
                    : data.metodos_pago[0].codigo;
                wrap.innerHTML = data.metodos_pago.map(function (m) {
                    var checked = m.codigo === defaultMethod ? ' checked' : '';
                    if (checked) {
                        paymentMethod = m.codigo;
                        paymentMethodLabel = m.nombre;
                    }
                    return '<label class="flex items-center gap-3 p-3 rounded-xl border border-cream">' +
                        '<input type="radio" name="pay_method" value="' + escapeHtml(m.codigo) + '"' + checked + '>' +
                        '<span><strong>' + escapeHtml(m.nombre) + '</strong>' +
                        (m.descripcion ? '<br><span class="text-xs text-earth">' + escapeHtml(m.descripcion) + '</span>' : '') +
                        '</span></label>';
                }).join('');
                wrap.querySelectorAll('input[name="pay_method"]').forEach(function (radio) {
                    radio.addEventListener('change', function () {
                        paymentMethod = radio.value;
                        var m = data.metodos_pago.find(function (x) { return x.codigo === radio.value; });
                        paymentMethodLabel = m ? m.nombre : radio.value;
                        updatePaymentMethodUi();
                    });
                });
                updatePaymentMethodUi();
            })
            .catch(function () {});
    }

    function init() {
        var cart = getCart();
        if (!cart.length) {
            showBanner('Tu carrito está vacío. Agregá productos antes de continuar.', 'warning');
            document.querySelectorAll('[data-checkout-next], [data-checkout-submit]').forEach(function (b) {
                b.disabled = true;
            });
        }

        prefillUser();
        renderAddresses();
        updateSummary();
        loadPaymentMethods();

        ['co-zip', 'co-city', 'co-province'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', scheduleQuote);
                el.addEventListener('blur', scheduleQuote);
            }
        });

        document.querySelectorAll('[data-checkout-next]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var step = parseInt(btn.getAttribute('data-checkout-next'), 10);
                if (step === 2 && !validateStep1()) return;
                if (step === 3) {
                    if (!validateStep1()) {
                        setStep(1);
                        return;
                    }
                    updateSummary();
                }
                setStep(step);
            });
        });

        document.querySelectorAll('[data-checkout-back]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                setStep(parseInt(btn.getAttribute('data-checkout-back'), 10));
            });
        });

        var submitBtn = document.querySelector('[data-checkout-submit]');
        if (submitBtn) submitBtn.addEventListener('click', submitOrder);

        var manualBtn = document.querySelector('[data-checkout-use-manual]');
        if (manualBtn) {
            manualBtn.addEventListener('click', function () {
                document.getElementById('co-address').value = '';
                document.getElementById('co-city').value = '';
                document.getElementById('co-province').value = '';
                document.getElementById('co-zip').value = '';
            });
        }

        if (bootstrap.addresses && bootstrap.addresses.length) {
            setTimeout(scheduleQuote, 400);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
