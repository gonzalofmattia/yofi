    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.YOFI_ADMIN = {
    csrfToken: <?= json_encode($_SESSION['admin_csrf_token'] ?? '', JSON_UNESCAPED_UNICODE) ?>,
    basePath: <?= json_encode(app_path(''), JSON_UNESCAPED_UNICODE) ?>
};
</script>
</body>
</html>
