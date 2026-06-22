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
})();
