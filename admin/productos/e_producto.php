<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$id_prod = (int)($_GET['id'] ?? 0);
if ($id_prod <= 0) {
    if (admin_is_partial_request()) {
        http_response_code(404);
        echo '<div class="alert alert-danger">Producto no encontrado</div>';
        exit;
    }
    header('Location: listado.php');
    exit();
}

$stmt = mysqli_prepare($con, 'SELECT * FROM tbl_productos WHERE id_prod = ? AND borrado = 0 LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id_prod);
mysqli_stmt_execute($stmt);
$producto = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$producto) {
    if (admin_is_partial_request()) {
        http_response_code(404);
        echo '<div class="alert alert-danger">Producto no encontrado</div>';
        exit;
    }
    header('Location: listado.php');
    exit();
}

$pageTitle = 'Editar producto: ' . $producto['nombre'];
$error_message = '';
$isDrawer = admin_is_partial_request();

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
        if (admin_wants_json_response()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'id_prod' => $id_prod]);
            exit;
        }
        $redirect = $isDrawer ? 'listado.php?modificado=1' : 'e_producto.php?id=' . $id_prod . '&modificado=1';
        header('Location: ' . $redirect);
        exit();
    }

    $error_message = 'Error al actualizar.';
    $producto = array_merge($producto, compact('nombre', 'codigo', 'id_cate', 'precio_base', 'descripcion', 'composicion', 'cuidados', 'peso', 'alto', 'ancho', 'profundidad', 'publicado', 'destacado', 'oferta', 'promo_badge'));
}

$categorias = mysqli_query($con, 'SELECT id_cate, nombre FROM tbl_categorias WHERE publicado = 1 ORDER BY nombre');
require __DIR__ . '/_producto_load_variantes.php';

if ($isDrawer) {
    include __DIR__ . '/_producto_editor.php';
    exit;
}

include __DIR__ . '/../header.php';
if (isset($_GET['modificado'])) {
    echo modificado('1', '', 'producto');
}
?>
<div class="admin-section-header">
    <div>
        <h1>Editar producto</h1>
        <p class="subtitle"><?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="admin-section-actions">
        <a href="listado.php" class="btn btn-outline-ink">Volver al listado</a>
    </div>
</div>
<?php include __DIR__ . '/_producto_editor.php'; ?>
<script src="<?= app_path('admin/assets/js/product-editor.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var root = document.querySelector('.product-editor-root');
    if (root && window.YofiProductEditor) {
        window.YofiProductEditor.init(root.parentElement);
    }
});
</script>
<div class="d-flex justify-content-end gap-2 mt-3 mb-4">
    <a href="listado.php" class="btn btn-outline-ink">Cancelar</a>
    <button type="button" class="btn btn-ink" onclick="document.getElementById('product-main-form').requestSubmit()">Guardar cambios</button>
</div>
<?php include __DIR__ . '/../pie.php'; ?>
