window.YofiAdmin = window.YofiAdmin || {};

/**
 * Pone/saca un botón en estado "cargando" (deshabilitado + spinner + texto),
 * para que una acción que pega al servidor no quede sin feedback visual.
 * Guarda el HTML original en un data-attribute para poder restaurarlo.
 */
window.YofiAdmin.setButtonLoading = function (btn, loading, loadingText) {
    if (!btn) return;
    if (loading) {
        if (btn.dataset.loading === '1') return;
        btn.dataset.loading = '1';
        btn.dataset.originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> '
            + (loadingText || 'Actualizando...');
    } else {
        delete btn.dataset.loading;
        btn.disabled = false;
        if (btn.dataset.originalHtml !== undefined) {
            btn.innerHTML = btn.dataset.originalHtml;
            delete btn.dataset.originalHtml;
        }
    }
};

(function () {
    var toggle = document.getElementById('adminSidebarToggle');
    var sidebar = document.getElementById('adminSidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
    }

    document.addEventListener('click', function (e) {
        if (!sidebar || !sidebar.classList.contains('show')) return;
        if (window.innerWidth >= 992) return;
        if (sidebar.contains(e.target) || (toggle && toggle.contains(e.target))) return;
        sidebar.classList.remove('show');
    });

    document.addEventListener('change', function (e) {
        var input = e.target;
        if (!input.matches || !input.matches('.toggle-estado')) return;

        var endpoint = input.dataset.endpoint;
        var idKey = input.dataset.idKey;
        var id = parseInt(input.dataset.id, 10);
        if (!endpoint || !idKey || !id) {
            input.checked = !input.checked;
            return;
        }

        var body = {};
        body[idKey] = id;
        input.disabled = true;

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': (window.YOFI_ADMIN && window.YOFI_ADMIN.csrfToken) || ''
            },
            body: JSON.stringify(body)
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.success) {
                    input.checked = !input.checked;
                    alert(data.error || 'Error al actualizar estado');
                }
            })
            .catch(function () {
                input.checked = !input.checked;
                alert('Error de conexión');
            })
            .finally(function () {
                input.disabled = false;
            });
    });
})();
