<?php
ob_start();
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$pageTitle = 'Nuevo producto';
$error_message = '';
$producto = null;
$isDrawer = admin_is_partial_request();

$categorias = mysqli_query($con, 'SELECT id_cate, nombre FROM tbl_categorias WHERE publicado = 1 ORDER BY nombre');

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

    if ($nombre === '' || $codigo === '' || $id_cate <= 0) {
        $error_message = 'Nombre, código y categoría son obligatorios.';
    } else {
        $precio_oferta_val = $precio_oferta !== null ? $precio_oferta : 0.0;
        $stmt = mysqli_prepare($con, '
            INSERT INTO tbl_productos
            (id_cate, nombre, slug, codigo, precio_base, precio_oferta, descripcion, composicion, cuidados,
             peso, alto, ancho, profundidad, publicado, destacado, oferta, promo_badge)
            VALUES (?, ?, ?, ?, ?, NULLIF(?, 0), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        mysqli_stmt_bind_param(
            $stmt,
            'isssddsssddddiiis',
            $id_cate,
            $nombre,
            $slug,
            $codigo,
            $precio_base,
            $precio_oferta_val,
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
            $promo_badge
        );

        if (mysqli_stmt_execute($stmt)) {
            $id_prod = (int)mysqli_insert_id($con);
            if (admin_wants_json_response()) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => true, 'id_prod' => $id_prod]);
                exit;
            }
            header('Location: e_producto.php?id=' . $id_prod . '&nuevo=1');
            exit();
        }
        $error_message = 'Error al guardar: ' . mysqli_error($con);
    }
}

if ($isDrawer) {
    include __DIR__ . '/_producto_editor.php';
    exit;
}

include __DIR__ . '/../header.php';
?>
<div class="admin-section-header">
    <div>
        <h1>Nuevo producto</h1>
        <p class="subtitle">Completá los datos generales y luego agregá colores</p>
    </div>
    <div class="admin-section-actions">
        <a href="listado.php" class="btn btn-outline-ink">Volver</a>
    </div>
</div>
<?php include __DIR__ . '/_producto_editor.php'; ?>
<div class="d-flex justify-content-end gap-2 mt-3 mb-4">
    <a href="listado.php" class="btn btn-outline-ink">Cancelar</a>
    <button type="button" class="btn btn-ink" onclick="document.getElementById('product-main-form').requestSubmit()">Crear producto</button>
</div>
<?php include __DIR__ . '/../pie.php'; ?>
