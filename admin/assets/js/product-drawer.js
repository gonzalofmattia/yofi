/**
 * Drawer lateral para crear/editar productos desde listado.php.
 * Carga HTML parcial vía fetch (?partial=1) desde e_producto.php / a_producto.php.
 */
window.YofiProductDrawer = (function () {
    var overlay, drawer, bodyEl, titleEl, saveBtn, cancelBtn;
    var currentId = null;

    function ensureElements() {
        overlay = document.getElementById('productDrawerOverlay');
        drawer = document.getElementById('productDrawer');
        bodyEl = document.getElementById('productDrawerBody');
        titleEl = document.getElementById('productDrawerTitle');
        saveBtn = document.getElementById('productDrawerSave');
        cancelBtn = document.getElementById('productDrawerCancel');
    }

    function open() {
        ensureElements();
        overlay.classList.add('open');
        drawer.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        ensureElements();
        overlay.classList.remove('open');
        drawer.classList.remove('open');
        document.body.style.overflow = '';
        currentId = null;
        if (bodyEl) bodyEl.innerHTML = '';
    }

    function runScripts(container) {
        container.querySelectorAll('script').forEach(function (oldScript) {
            var script = document.createElement('script');
            if (oldScript.src) {
                script.src = oldScript.src;
            } else {
                script.textContent = oldScript.textContent;
            }
            oldScript.parentNode.replaceChild(script, oldScript);
        });
    }

    function bindFormSubmit() {
        var form = document.getElementById('product-main-form');
        if (!form || form.dataset.drawerBound === '1') return;
        form.dataset.drawerBound = '1';

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var fd = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (data) {
                if (data.success) {
                    if (data.id_prod && !currentId) {
                        loadEdit(data.id_prod);
                    } else {
                        close();
                        location.reload();
                    }
                } else {
                    alert(data.error || 'Error al guardar');
                }
            })
            .catch(function () {
                alert('Error al guardar el producto');
            });
        });
    }

    function loadContent(url, title) {
        ensureElements();
        titleEl.textContent = title;
        bodyEl.innerHTML = '<div class="text-center py-5 text-muted">Cargando…</div>';
        open();

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                bodyEl.innerHTML = html;
                runScripts(bodyEl);
                if (window.YofiProductEditor) {
                    window.YofiProductEditor.init(bodyEl);
                }
                bindFormSubmit();
            })
            .catch(function () {
                bodyEl.innerHTML = '<div class="alert alert-danger">No se pudo cargar el formulario.</div>';
            });
    }

    function loadEdit(id) {
        currentId = id;
        loadContent('e_producto.php?id=' + id + '&partial=1', 'Editar producto');
    }

    function loadCreate() {
        currentId = null;
        loadContent('a_producto.php?partial=1', 'Nuevo producto');
    }

    function reload(id) {
        loadEdit(id);
    }

    function init() {
        ensureElements();
        if (cancelBtn) cancelBtn.addEventListener('click', close);
        if (overlay) overlay.addEventListener('click', close);
        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                var form = document.getElementById('product-main-form');
                if (form) form.requestSubmit();
            });
        }

        document.querySelectorAll('[data-open-product-drawer]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var id = el.getAttribute('data-product-id');
                if (id) loadEdit(parseInt(id, 10));
                else loadCreate();
            });
        });

        var params = new URLSearchParams(window.location.search);
        if (params.get('editar')) {
            loadEdit(parseInt(params.get('editar'), 10));
        }
    }

    return { init: init, open: open, close: close, loadEdit: loadEdit, loadCreate: loadCreate, reload: reload };
})();

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('productDrawer')) {
        window.YofiProductDrawer.init();
    }
});
