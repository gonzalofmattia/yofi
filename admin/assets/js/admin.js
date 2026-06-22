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
            });
    });
})();
