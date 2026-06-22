<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

const ADMIN_STOCK_BAJO = 10;

$pageTitle = 'Productos';
$q = trim((string)($_GET['q'] ?? ''));
$estadoTab = trim((string)($_GET['estado'] ?? ''));
$adminSearchAction = 'listado.php';
$adminSearchQuery = $q;
$adminSearchPlaceholder = 'Buscar por nombre o código…';
$adminSearchHidden = $estadoTab !== ''
    ? '<input type="hidden" name="estado" value="' . htmlspecialchars($estadoTab, ENT_QUOTES, 'UTF-8') . '">'
    : '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = 'WHERE p.borrado = 0';
$params = [];
$types = '';

if ($estadoTab === 'publicados') {
    $where .= ' AND p.publicado = 1';
} elseif ($estadoTab === 'borradores') {
    $where .= ' AND p.publicado = 0';
}

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
           COALESCE((SELECT SUM(s.stock) FROM tbl_skus s WHERE s.id_prod = p.id_prod AND s.activo = 1), 0) AS stock_total,
           (SELECT pi.path FROM tbl_prod_imagenes pi
            WHERE pi.id_prod = p.id_prod
            ORDER BY pi.es_principal DESC, pi.id_imagen ASC LIMIT 1) AS imagen_thumb
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

function listado_tab_url(string $estado, string $q, int $page = 1): string
{
    $parts = ['page=' . $page];
    if ($estado !== '') {
        $parts[] = 'estado=' . urlencode($estado);
    }
    if ($q !== '') {
        $parts[] = 'q=' . urlencode($q);
    }

    return 'listado.php?' . implode('&', $parts);
}

function stock_class(int $stock): string
{
    if ($stock <= 0) {
        return 'stock-cero';
    }
    if ($stock <= ADMIN_STOCK_BAJO) {
        return 'stock-bajo';
    }

    return 'stock-ok';
}

include __DIR__ . '/../header.php';
echo modificado($_GET['modificado'] ?? '', '', 'producto');
echo borrado($_GET['borrado'] ?? '', '', 'producto');
?>

<div class="admin-section-header">
    <div>
        <h1>Productos</h1>
        <p class="subtitle"><?= (int)$total ?> producto<?= $total === 1 ? '' : 's' ?> en el catálogo</p>
    </div>
    <div class="admin-section-actions">
        <button type="button" class="btn btn-ink" data-open-product-drawer>
            <i class="bi bi-plus-lg"></i> Agregar producto
        </button>
    </div>
</div>

<div class="admin-tabs">
    <a href="<?= htmlspecialchars(listado_tab_url('', $q), ENT_QUOTES, 'UTF-8') ?>" class="admin-tab<?= $estadoTab === '' ? ' active' : '' ?>">Todos</a>
    <a href="<?= htmlspecialchars(listado_tab_url('publicados', $q), ENT_QUOTES, 'UTF-8') ?>" class="admin-tab<?= $estadoTab === 'publicados' ? ' active' : '' ?>">Publicados</a>
    <a href="<?= htmlspecialchars(listado_tab_url('borradores', $q), ENT_QUOTES, 'UTF-8') ?>" class="admin-tab<?= $estadoTab === 'borradores' ? ' active' : '' ?>">Borradores</a>
</div>

<div class="bulk-bar" id="bulkBar">
    <span id="bulkCount">0 seleccionados</span>
    <button type="button" class="btn-bulk" data-bulk-action="publicar">Publicar</button>
    <button type="button" class="btn-bulk" data-bulk-action="despublicar">Despublicar</button>
    <button type="button" class="btn-bulk btn-bulk-danger" data-bulk-action="eliminar">Eliminar</button>
    <button type="button" class="btn-bulk ms-auto" id="bulkCancel">Cancelar</button>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 gap-3 flex-wrap" id="listToolbar">
    <form method="get" action="listado.php" class="admin-search-bar flex-grow-1" style="max-width:360px">
        <?php if ($estadoTab !== ''): ?>
        <input type="hidden" name="estado" value="<?= htmlspecialchars($estadoTab, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
        <i class="bi bi-search"></i>
        <input name="q" placeholder="Buscar por nombre o código…" type="search" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
    </form>
    <?php if ($q !== ''): ?>
    <a href="<?= htmlspecialchars(listado_tab_url($estadoTab, ''), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-outline-ink">Limpiar búsqueda</a>
    <?php endif; ?>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="admin-table mb-0" id="productsTable">
                <thead>
                    <tr>
                        <th style="width:40px"><input type="checkbox" class="form-check-input" id="checkAll" aria-label="Seleccionar todos"></th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th style="width:48px"></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)):
                        $thumb = !empty($row['imagen_thumb'])
                            ? imgprod_path((string)$row['imagen_thumb'])
                            : imgprod_path('placeholder.jpg');
                        $stock = (int)$row['stock_total'];
                    ?>
                        <tr data-id="<?= (int)$row['id_prod'] ?>">
                            <td><input type="checkbox" class="form-check-input row-check" value="<?= (int)$row['id_prod'] ?>" aria-label="Seleccionar"></td>
                            <td>
                                <a href="#" class="product-cell-link" data-open-product-drawer data-product-id="<?= (int)$row['id_prod'] ?>">
                                    <img src="<?= htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8') ?>" alt="" class="product-thumb">
                                    <div>
                                        <div class="product-cell-name"><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="product-cell-code"><?= htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') ?></div>
                                    </div>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row['categoria'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= format_money((float)$row['precio_base']) ?></td>
                            <td><span class="<?= stock_class($stock) ?>"><?= $stock ?></span></td>
                            <td><?= producto_estado_badge((int)$row['publicado']) ?></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown" aria-label="Acciones">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-actions dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#" data-open-product-drawer data-product-id="<?= (int)$row['id_prod'] ?>">Editar</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="post" action="b_producto.php" onsubmit="return confirm('¿Eliminar este producto?')">
                                                <?= admin_csrf_field() ?>
                                                <input type="hidden" name="chkborrar" value="<?= (int)$row['id_prod'] ?>">
                                                <button type="submit" class="dropdown-item text-danger">Eliminar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted py-5">Sin productos</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($totalPages > 1): ?>
    <div class="admin-card-footer">
        <span>Página <?= $page ?> de <?= $totalPages ?></span>
        <div class="btn-group">
            <?php if ($page > 1): ?>
            <a class="btn btn-sm btn-outline-ink" href="<?= htmlspecialchars(listado_tab_url($estadoTab, $q, $page - 1), ENT_QUOTES, 'UTF-8') ?>">Anterior</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a class="btn btn-sm btn-outline-ink" href="<?= htmlspecialchars(listado_tab_url($estadoTab, $q, $page + 1), ENT_QUOTES, 'UTF-8') ?>">Siguiente</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="drawer-overlay" id="productDrawerOverlay"></div>
<aside class="product-drawer" id="productDrawer" aria-hidden="true">
    <div class="product-drawer-header">
        <h2 id="productDrawerTitle">Producto</h2>
        <button type="button" class="btn btn-sm btn-link text-dark" id="productDrawerClose" aria-label="Cerrar"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="product-drawer-body" id="productDrawerBody"></div>
    <div class="product-drawer-footer">
        <button type="button" class="btn btn-outline-ink" id="productDrawerCancel">Cancelar</button>
        <button type="button" class="btn btn-ink" id="productDrawerSave">Guardar</button>
    </div>
</aside>

<script src="<?= app_path('admin/assets/js/product-editor.js') ?>"></script>
<script src="<?= app_path('admin/assets/js/product-drawer.js') ?>"></script>
<script>
(function () {
    var checkAll = document.getElementById('checkAll');
    var bulkBar = document.getElementById('bulkBar');
    var bulkCount = document.getElementById('bulkCount');
    var rows = document.querySelectorAll('.row-check');

    function selectedIds() {
        return Array.from(document.querySelectorAll('.row-check:checked')).map(function (c) { return parseInt(c.value, 10); });
    }

    function updateBulkBar() {
        var ids = selectedIds();
        if (ids.length > 0) {
            bulkBar.classList.add('visible');
            bulkCount.textContent = ids.length + ' seleccionado' + (ids.length === 1 ? '' : 's');
        } else {
            bulkBar.classList.remove('visible');
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            rows.forEach(function (c) { c.checked = checkAll.checked; });
            updateBulkBar();
        });
    }
    rows.forEach(function (c) {
        c.addEventListener('change', updateBulkBar);
    });

    document.getElementById('bulkCancel')?.addEventListener('click', function () {
        rows.forEach(function (c) { c.checked = false; });
        if (checkAll) checkAll.checked = false;
        updateBulkBar();
    });

    document.querySelectorAll('[data-bulk-action]').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            var action = btn.getAttribute('data-bulk-action');
            var ids = selectedIds();
            if (!ids.length) return;
            if (action === 'eliminar' && !confirm('¿Eliminar ' + ids.length + ' producto(s)?')) return;

            var res = await fetch('<?= app_path('admin/api/bulk-productos.php') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-Token': window.YOFI_ADMIN.csrfToken},
                body: JSON.stringify({action: action, ids: ids})
            });
            var data = await res.json();
            if (data.success) location.reload();
            else alert(data.error || 'Error en acción masiva');
        });
    });

    document.getElementById('productDrawerClose')?.addEventListener('click', function () {
        window.YofiProductDrawer.close();
    });
})();
</script>

<?php include __DIR__ . '/../pie.php'; ?>
