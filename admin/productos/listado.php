<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['palabra'])) {
    header('Location: listado.php?q=' . urlencode(trim((string)$_POST['palabra'])));
    exit();
}

$pageTitle = 'Productos';
$q = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = 'WHERE p.borrado = 0';
$params = [];
$types = '';

if ($q !== '') {
    $where .= ' AND (p.nombre LIKE ? OR p.codigo LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

$sqlCount = "SELECT COUNT(*) AS total FROM tbl_productos p $where";
$stmtCount = mysqli_prepare($con, $sqlCount);
if ($types !== '') {
    mysqli_stmt_bind_param($stmtCount, $types, ...$params);
}
mysqli_stmt_execute($stmtCount);
$total = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCount))['total'] ?? 0);
$totalPages = max(1, (int)ceil($total / $perPage));

$sql = "
    SELECT p.id_prod, p.codigo, p.nombre, p.precio_base, p.publicado,
           c.nombre AS categoria,
           COALESCE((SELECT SUM(s.stock) FROM tbl_skus s WHERE s.id_prod = p.id_prod), 0) AS stock_total
    FROM tbl_productos p
    LEFT JOIN tbl_categorias c ON c.id_cate = p.id_cate
    $where
    ORDER BY p.fecha_creacion DESC
    LIMIT ? OFFSET ?
";
$stmt = mysqli_prepare($con, $sql);
if ($types !== '') {
    $params[] = $perPage;
    $params[] = $offset;
    mysqli_stmt_bind_param($stmt, $types . 'ii', ...$params);
} else {
    mysqli_stmt_bind_param($stmt, 'ii', $perPage, $offset);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

include __DIR__ . '/../header.php';
echo buscador('listado.php', $q);
echo modificado($_GET['modificado'] ?? '', '', 'producto');
echo borrado($_GET['borrado'] ?? '', '', 'producto');
?>

<div class="card shadow-sm border-0">
    <div class="card-header card-header-yofi d-flex justify-content-between align-items-center">
        <strong>Listado de productos</strong>
        <a href="a_producto.php" class="btn btn-light btn-sm">Nuevo producto</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio base</th>
                        <th>Stock total</th>
                        <th>Publicado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['categoria'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= format_money((float)$row['precio_base']) ?></td>
                            <td><?= (int)$row['stock_total'] ?></td>
                            <td>
                                <button type="button" class="btn btn-sm <?= (int)$row['publicado'] ? 'btn-success' : 'btn-secondary' ?> toggle-publicado"
                                        data-id="<?= (int)$row['id_prod'] ?>"
                                        data-publicado="<?= (int)$row['publicado'] ?>">
                                    <?= (int)$row['publicado'] ? 'Sí' : 'No' ?>
                                </button>
                            </td>
                            <td class="text-end table-actions">
                                <a href="e_producto.php?id=<?= (int)$row['id_prod'] ?>" class="btn btn-outline-primary btn-sm">Editar</a>
                                <form method="post" action="b_producto.php" class="d-inline" onsubmit="return confirm('¿Eliminar producto?')">
                                    <?= admin_csrf_field() ?>
                                    <input type="hidden" name="chkborrar" value="<?= (int)$row['id_prod'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Borrar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Sin productos</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($totalPages > 1): ?>
        <div class="card-footer d-flex justify-content-between">
            <span class="text-muted small">Página <?= $page ?> de <?= $totalPages ?> (<?= $total ?> productos)</span>
            <div class="btn-group">
                <?php if ($page > 1): ?>
                    <a class="btn btn-sm btn-outline-secondary" href="?page=<?= $page - 1 ?>&q=<?= urlencode($q) ?>">Anterior</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a class="btn btn-sm btn-outline-secondary" href="?page=<?= $page + 1 ?>&q=<?= urlencode($q) ?>">Siguiente</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.toggle-publicado').forEach(btn => {
    btn.addEventListener('click', async () => {
        const id = btn.dataset.id;
        const res = await fetch('<?= app_path('admin/api/toggle-producto-publicado.php') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-Token': window.YOFI_ADMIN.csrfToken},
            body: JSON.stringify({id_prod: parseInt(id, 10)})
        });
        const data = await res.json();
        if (data.success) {
            btn.dataset.publicado = data.publicado;
            btn.textContent = data.publicado ? 'Sí' : 'No';
            btn.classList.toggle('btn-success', data.publicado === 1);
            btn.classList.toggle('btn-secondary', data.publicado !== 1);
        }
    });
});
</script>

<?php include __DIR__ . '/../pie.php'; ?>
