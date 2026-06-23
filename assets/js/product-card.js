/**
 * Swatches de color en tarjetas de producto (catálogo, home, relacionados).
 */
(function () {
    function findColor(colores, colorId) {
        return colores.find(function (c) {
            return parseInt(c.id_color, 10) === colorId;
        }) || null;
    }

    function renderTalles(container, talles) {
        if (!container) return;
        container.innerHTML = '';
        if (!talles || !talles.length) {
            container.classList.add('hidden');
            return;
        }
        container.classList.remove('hidden');
        talles.forEach(function (nombre) {
            var span = document.createElement('span');
            span.className = 'inline-flex items-center justify-center min-w-[1.75rem] h-6 px-1.5 text-[10px] font-semibold border border-cream rounded-full text-earth';
            span.textContent = nombre;
            container.appendChild(span);
        });
    }

    function updateQuickAdd(article, color, precio, nombre) {
        var quickAdd = article.querySelector('[data-action="quick-add"]');
        var verLink = article.querySelector('a[data-product-link].absolute');
        if (!color || !color.id_sku_default) {
            return;
        }
        if (quickAdd) {
            quickAdd.setAttribute('data-id-sku', String(color.id_sku_default));
            quickAdd.setAttribute('data-imagen', color.imagen || '');
            quickAdd.setAttribute('data-color-nombre', color.color_nombre || '');
            quickAdd.setAttribute('data-color-hex', color.hex_code || '');
            quickAdd.setAttribute('data-talle-nombre', color.sku_talle_nombre || '');
            quickAdd.setAttribute('data-nombre', nombre);
            quickAdd.setAttribute('data-precio', String(precio));
        }
    }

    function applyColor(article, colorId) {
        var coloresRaw = article.getAttribute('data-colores');
        if (!coloresRaw) return;
        var colores;
        try {
            colores = JSON.parse(coloresRaw);
        } catch (e) {
            return;
        }
        if (!Array.isArray(colores) || colores.length <= 1) return;

        var color = findColor(colores, colorId);
        if (!color) return;

        var img = article.querySelector('[data-card-image]');
        if (img && color.imagen) {
            img.src = color.imagen;
        }

        var url = color.url || article.querySelector('[data-product-link]')?.getAttribute('href') || '';
        article.querySelectorAll('[data-product-link]').forEach(function (link) {
            if (url) link.setAttribute('href', url);
        });

        article.setAttribute('data-color-id', String(colorId));

        article.querySelectorAll('[data-card-swatch]').forEach(function (btn) {
            var active = parseInt(btn.getAttribute('data-color-id'), 10) === colorId;
            btn.setAttribute('aria-pressed', active ? 'true' : 'false');
            btn.classList.toggle('ring-2', active);
            btn.classList.toggle('ring-primary', active);
            btn.classList.toggle('ring-offset-1', active);
            btn.classList.toggle('border-dark', active);
            btn.classList.toggle('border-cream', !active);
        });

        renderTalles(article.querySelector('[data-card-talles]'), color.talles || []);

        var precio = parseFloat(article.getAttribute('data-precio') || '0');
        var nombre = article.getAttribute('data-nombre') || '';
        var quickAdd = article.querySelector('[data-action="quick-add"]');
        if (quickAdd) {
            if (color.id_sku_default) {
                quickAdd.disabled = false;
                updateQuickAdd(article, color, precio, nombre);
            } else {
                quickAdd.disabled = true;
            }
        }
    }

    function initCard(article) {
        var coloresRaw = article.getAttribute('data-colores');
        if (!coloresRaw) return;
        var colores;
        try {
            colores = JSON.parse(coloresRaw);
        } catch (e) {
            return;
        }
        if (!Array.isArray(colores) || colores.length <= 1) return;

        article.querySelectorAll('[data-card-swatch]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var colorId = parseInt(btn.getAttribute('data-color-id'), 10);
                if (!colorId) return;
                applyColor(article, colorId);
            });
        });
    }

    function initAll() {
        document.querySelectorAll('[data-product-card]').forEach(initCard);
    }

    document.addEventListener('DOMContentLoaded', initAll);

    window.YofiProductCard = {
        initAll: initAll,
        initCard: initCard
    };
})();
