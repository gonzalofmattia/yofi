<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$id_prod = (int)($_GET['id'] ?? 0);
if ($id_prod <= 0) {
    header('Location: listado.php');
    exit();
}

$stmt = mysqli_prepare($con, 'SELECT * FROM tbl_productos WHERE id_prod = ? AND borrado = 0 LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id_prod);
mysqli_stmt_execute($stmt);
$producto = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$producto) {
    header('Location: listado.php');
    exit();
}

$pageTitle = 'Editar producto: ' . $producto['nombre'];
$error_message = '';

if (isset($_POST['envio'])) {
    $nombre = trim((string)($_POST['nombre'] ?? ''));
    $codigo = trim((string)($_POST['codigo'] ?? ''));
    $id_cate = (int)($_POST['id_cate'] ?? 0);
    $precio_base = (float)($_POST['precio_base'] ?? 0);
    $precio_oferta = $_POST['precio_oferta'] !== '' ? (float)$_POST['precio_oferta'] : null;
    $descripcion = trim((string)($_POST['descripcion'] ?? ''));
    $composicion = trim((string)($_POST['composicion'] ?? ''));
    $cuidados = trim((string)($_POST['cuidados'] ?? ''));
    $peso = (float)($_POST['peso'] ?? 0);
    $alto = (float)($_POST['alto'] ?? 0);
    $ancho = (float)($_POST['ancho'] ?? 0);
    $profundidad = (float)($_POST['profundidad'] ?? 0);
    $publicado = isset($_POST['publicado']) ? 1 : 0;
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    $oferta = isset($_POST['oferta']) ? 1 : 0;
    $promo_badge = trim((string)($_POST['promo_badge'] ?? ''));
    $slug = yofi_slug($nombre);

    $stmtUp = mysqli_prepare($con, '
        UPDATE tbl_productos SET
            id_cate=?, nombre=?, slug=?, codigo=?, precio_base=?, precio_oferta=?, descripcion=?, composicion=?, cuidados=?,
            peso=?, alto=?, ancho=?, profundidad=?, publicado=?, destacado=?, oferta=?, promo_badge=?, fecha_actualizacion=NOW()
        WHERE id_prod=?
    ');
    mysqli_stmt_bind_param(
        $stmtUp,
        'isssddsssddddiiisi',
        $id_cate,
        $nombre,
        $slug,
        $codigo,
        $precio_base,
        $precio_oferta,
        $descripcion,
        $composicion,
        $cuidados,
        $peso,
        $alto,
        $ancho,
        $profundidad,
        $publicado,
        $destacado,
        $oferta,
        $promo_badge,
        $id_prod
    );
    if (mysqli_stmt_execute($stmtUp)) {
        header('Location: e_producto.php?id=' . $id_prod . '&modificado=1');
        exit();
    }
    $error_message = 'Error al actualizar.';
    $producto = array_merge($producto, compact('nombre', 'codigo', 'id_cate', 'precio_base', 'descripcion', 'composicion', 'cuidados', 'peso', 'alto', 'ancho', 'profundidad', 'publicado', 'destacado', 'oferta', 'promo_badge'));
}

$categorias = mysqli_query($con, 'SELECT id_cate, nombre FROM tbl_categorias WHERE publicado = 1 ORDER BY nombre');
$coloresTodos = [];
$resColores = mysqli_query($con, 'SELECT id_color, nombre, hex_code FROM tbl_colores WHERE activo = 1 ORDER BY nombre');
while ($c = mysqli_fetch_assoc($resColores)) {
    $coloresTodos[(int)$c['id_color']] = $c;
}

$tallesTodos = [];
$resTalles = mysqli_query($con, 'SELECT id_talle, nombre FROM tbl_talles WHERE activo = 1 ORDER BY orden, nombre');
while ($t = mysqli_fetch_assoc($resTalles)) {
    $tallesTodos[] = $t;
}

$coloresProducto = [];

$resSkus = mysqli_query($con, "
    SELECT s.id_sku, s.id_color, s.id_talle, s.stock, s.activo, c.nombre AS color_nombre, c.hex_code, t.nombre AS talle_nombre
    FROM tbl_skus s
    INNER JOIN tbl_colores c ON c.id_color = s.id_color
    INNER JOIN tbl_talles t ON t.id_talle = s.id_talle
    WHERE s.id_prod = $id_prod
    ORDER BY c.nombre, t.orden, t.nombre
");
while ($sku = mysqli_fetch_assoc($resSkus)) {
    $idColor = (int)$sku['id_color'];
    if (!isset($coloresProducto[$idColor])) {
        $coloresProducto[$idColor] = [
            'id_color' => $idColor,
            'nombre' => $sku['color_nombre'],
            'hex_code' => $sku['hex_code'],
            'skus' => [],
            'imagenes' => [],
        ];
    }
    $coloresProducto[$idColor]['skus'][(int)$sku['id_talle']] = [
        'id_sku' => (int)$sku['id_sku'],
        'stock' => (int)$sku['stock'],
        'activo' => (int)$sku['activo'],
        'talle_nombre' => $sku['talle_nombre'],
    ];
}

$resImg = mysqli_query($con, "
    SELECT i.id_imagen, i.path, i.id_color, i.es_principal, c.nombre AS color_nombre, c.hex_code
    FROM tbl_prod_imagenes i
    LEFT JOIN tbl_colores c ON c.id_color = i.id_color
    WHERE i.id_prod = $id_prod AND i.id_color IS NOT NULL
    ORDER BY i.id_color, i.es_principal DESC, i.orden ASC
");
while ($img = mysqli_fetch_assoc($resImg)) {
    $idColor = (int)$img['id_color'];
    if (!isset($coloresProducto[$idColor])) {
        $coloresProducto[$idColor] = [
            'id_color' => $idColor,
            'nombre' => $img['color_nombre'] ?? 'Color #' . $idColor,
            'hex_code' => $img['hex_code'] ?? '#ccc',
            'skus' => [],
            'imagenes' => [],
        ];
    }
    $coloresProducto[$idColor]['imagenes'][] = $img;
}

$coloresUsados = array_keys($coloresProducto);
$coloresDisponibles = array_filter($coloresTodos, static fn($c) => !in_array((int)$c['id_color'], $coloresUsados, true));

include __DIR__ . '/../header.php';
if (isset($_GET['modificado'])) {
    echo modificado('1', '', 'producto');
}
if (isset($_GET['nuevo'])) {
    echo agregado('1', '', 'producto');
}
include __DIR__ . '/_form_producto.php';
?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header card-header-yofi d-flex justify-content-between align-items-center">
        <strong>Colores del producto</strong>
        <?php if (!empty($coloresDisponibles)): ?>
        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#panelNuevoColor" aria-expanded="false">
            + Agregar color
        </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-4">Cada color es una variante del catálogo con sus propias fotos y stock por talle.</p>

        <?php if (!empty($coloresDisponibles)): ?>
        <div class="collapse mb-4" id="panelNuevoColor">
            <div class="card border border-primary">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Nuevo color</h6>
                    <form method="post" enctype="multipart/form-data" action="<?= app_path('admin/api/upload-imagen-producto.php') ?>" class="color-variant-form" data-id-color="">
                        <?= admin_csrf_field() ?>
                        <input type="hidden" name="id_prod" value="<?= $id_prod ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Color</label>
                                <select name="id_color" class="form-select color-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($coloresDisponibles as $c): ?>
                                    <option value="<?= (int)$c['id_color'] ?>"><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Fotos de este color</label>
                                <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple required>
                                <div class="form-text">Podés subir varias imágenes. La primera será la principal.</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="form-label fw-semibold">Stock por talle</label>
                            <div class="row g-2 stock-grid">
                                <?php foreach ($tallesTodos as $t): ?>
                                <div class="col-6 col-md-3 col-lg-2">
                                    <label class="form-label small mb-1"><?= htmlspecialchars($t['nombre'], ENT_QUOTES, 'UTF-8') ?></label>
                                    <input type="number" class="form-control form-control-sm stock-input" data-id-talle="<?= (int)$t['id_talle'] ?>" value="0" min="0">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mt-4 d-flex gap-2">
                            <button type="button" class="btn btn-yofi btn-guardar-color">Guardar color</button>
                            <button type="submit" class="btn btn-outline-secondary btn-subir-fotos d-none">Subir fotos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (empty($coloresProducto)): ?>
        <div class="alert alert-info mb-0">Todavía no hay colores cargados. Usá «Agregar color» para crear la primera variante.</div>
        <?php else: ?>
        <div class="accordion" id="accordionColores">
            <?php $idx = 0; foreach ($coloresProducto as $colorData): $idx++; $idColor = (int)$colorData['id_color']; ?>
            <div class="accordion-item border mb-2 rounded overflow-hidden">
                <h2 class="accordion-header" id="headingColor<?= $idColor ?>">
                    <button class="accordion-button <?= $idx > 1 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseColor<?= $idColor ?>" aria-expanded="<?= $idx === 1 ? 'true' : 'false' ?>">
                        <span class="d-inline-block rounded-circle border me-2" style="width:18px;height:18px;background:<?= htmlspecialchars($colorData['hex_code'], ENT_QUOTES, 'UTF-8') ?>"></span>
                        <?= htmlspecialchars($colorData['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="badge bg-secondary ms-2"><?= count($colorData['imagenes']) ?> foto(s)</span>
                    </button>
                </h2>
                <div id="collapseColor<?= $idColor ?>" class="accordion-collapse collapse <?= $idx === 1 ? 'show' : '' ?>" data-bs-parent="#accordionColores">
                    <div class="accordion-body">
                        <form method="post" enctype="multipart/form-data" action="<?= app_path('admin/api/upload-imagen-producto.php') ?>" class="mb-4 color-upload-form">
                            <?= admin_csrf_field() ?>
                            <input type="hidden" name="id_prod" value="<?= $id_prod ?>">
                            <input type="hidden" name="id_color" value="<?= $idColor ?>">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label">Agregar fotos</label>
                                    <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-outline-primary w-100">Subir fotos</button>
                                </div>
                            </div>
                        </form>

                        <?php if (!empty($colorData['imagenes'])): ?>
                        <div class="row g-3 mb-4">
                            <?php foreach ($colorData['imagenes'] as $img): ?>
                            <div class="col-6 col-md-3 text-center" data-imagen-id="<?= (int)$img['id_imagen'] ?>">
                                <div class="position-relative">
                                    <img src="<?= imgprod_path($img['path']) ?>" alt="" class="img-fluid rounded border" style="max-height:140px;object-fit:cover;width:100%">
                                    <?php if ((int)$img['es_principal'] === 1): ?>
                                    <span class="badge bg-success position-absolute top-0 start-0 m-1">Principal</span>
                                    <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-light position-absolute top-0 start-0 m-1 btn-principal" data-id-imagen="<?= (int)$img['id_imagen'] ?>" title="Marcar principal">★</button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 btn-eliminar-imagen" data-id-imagen="<?= (int)$img['id_imagen'] ?>" title="Eliminar">×</button>
                                </div>
                                <div class="small text-muted mt-1"><?= htmlspecialchars(basename($img['path']), ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted small">Sin fotos para este color.</p>
                        <?php endif; ?>

                        <label class="form-label fw-semibold">Stock por talle</label>
                        <div class="row g-2 mb-3 stock-grid" data-id-color="<?= $idColor ?>">
                            <?php foreach ($tallesTodos as $t):
                                $idTalle = (int)$t['id_talle'];
                                $stockVal = $colorData['skus'][$idTalle]['stock'] ?? 0;
                            ?>
                            <div class="col-6 col-md-3 col-lg-2">
                                <label class="form-label small mb-1"><?= htmlspecialchars($t['nombre'], ENT_QUOTES, 'UTF-8') ?></label>
                                <input type="number" class="form-control form-control-sm stock-input" data-id-talle="<?= $idTalle ?>" value="<?= (int)$stockVal ?>" min="0">
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-yofi btn-sm btn-guardar-stock" data-id-color="<?= $idColor ?>">Guardar stock</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
const API = {
    stockColor: '<?= app_path('admin/api/guardar-stock-color.php') ?>',
    deleteImg: '<?= app_path('admin/api/eliminar-imagen-producto.php') ?>',
    principalImg: '<?= app_path('admin/api/marcar-imagen-principal.php') ?>'
};
const idProd = <?= $id_prod ?>;

async function postJson(url, body) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-Token': window.YOFI_ADMIN.csrfToken},
        body: JSON.stringify(body)
    });
    return res.json();
}

function collectStocks(container) {
    const stocks = {};
    container.querySelectorAll('.stock-input').forEach(input => {
        stocks[input.dataset.idTalle] = parseInt(input.value, 10) || 0;
    });
    return stocks;
}

document.querySelectorAll('.btn-guardar-stock').forEach(btn => {
    btn.addEventListener('click', async () => {
        const idColor = parseInt(btn.dataset.idColor, 10);
        const grid = btn.previousElementSibling;
        const data = await postJson(API.stockColor, {
            id_prod: idProd,
            id_color: idColor,
            stocks: collectStocks(grid)
        });
        if (data.success) {
            btn.textContent = 'Guardado ✓';
            setTimeout(() => { btn.textContent = 'Guardar stock'; }, 2000);
        } else {
            alert(data.error || 'Error al guardar stock');
        }
    });
});

document.querySelector('.btn-guardar-color')?.addEventListener('click', async () => {
    const panel = document.getElementById('panelNuevoColor');
    const form = panel.querySelector('.color-variant-form');
    const select = form.querySelector('.color-select');
    const idColor = parseInt(select.value, 10);
    if (!idColor) {
        alert('Seleccioná un color');
        return;
    }

    const stocks = collectStocks(form);
    const data = await postJson(API.stockColor, { id_prod: idProd, id_color: idColor, stocks });
    if (!data.success) {
        alert(data.error || 'Error al guardar stock');
        return;
    }

    const fileInput = form.querySelector('input[type="file"]');
    if (fileInput.files.length > 0) {
        form.querySelector('[name="id_color"]').value = idColor;
        form.submit();
    } else {
        location.reload();
    }
});

document.querySelectorAll('.btn-eliminar-imagen').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('¿Eliminar esta imagen?')) return;
        const data = await postJson(API.deleteImg, {
            id_imagen: parseInt(btn.dataset.idImagen, 10),
            id_prod: idProd
        });
        if (data.success) location.reload();
        else alert(data.error || 'No se pudo eliminar');
    });
});

document.querySelectorAll('.btn-principal').forEach(btn => {
    btn.addEventListener('click', async () => {
        const data = await postJson(API.principalImg, {
            id_imagen: parseInt(btn.dataset.idImagen, 10),
            id_prod: idProd
        });
        if (data.success) location.reload();
    });
});
</script>

<?php include __DIR__ . '/../pie.php'; ?>
