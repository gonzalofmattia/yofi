<?php
require_once dirname(__DIR__) . '/include/includes.php';
require_once dirname(__DIR__) . '/check_session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_path('admin/productos/listado.php'));
    exit();
}

$id_prod = (int)($_POST['id_prod'] ?? 0);
$id_color = (int)($_POST['id_color'] ?? 0);

if ($id_prod <= 0 || $id_color <= 0) {
    header('Location: ' . app_path('admin/productos/e_producto.php?id=' . $id_prod));
    exit();
}

$imgDir = dirname(__DIR__, 2) . '/imgprod/';
if (!is_dir($imgDir)) {
    mkdir($imgDir, 0755, true);
}

$files = $_FILES['imagenes'] ?? null;
if (!$files || !isset($files['tmp_name'])) {
    header('Location: ' . app_path('admin/productos/e_producto.php?id=' . $id_prod . '&modificado=1'));
    exit();
}

$tmpNames = is_array($files['tmp_name']) ? $files['tmp_name'] : [$files['tmp_name']];
$origNames = is_array($files['name']) ? $files['name'] : [$files['name']];
$errors = is_array($files['error']) ? $files['error'] : [$files['error']];

$countStmt = mysqli_prepare($con, 'SELECT COUNT(*) FROM tbl_prod_imagenes WHERE id_prod = ? AND id_color = ?');
mysqli_stmt_bind_param($countStmt, 'ii', $id_prod, $id_color);
mysqli_stmt_execute($countStmt);
$existingCount = (int)mysqli_fetch_row(mysqli_stmt_get_result($countStmt))[0];

$orden = $existingCount;

foreach ($tmpNames as $i => $tmp) {
    if (empty($tmp) || ($errors[$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        continue;
    }

    $ext = strtolower(pathinfo((string)$origNames[$i], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
        continue;
    }

    $filename = 'prod-' . $id_prod . '-' . $id_color . '-' . time() . '-' . $i . '.' . $ext;
    if (!move_uploaded_file($tmp, $imgDir . $filename)) {
        continue;
    }

    $esPrincipal = $existingCount === 0 ? 1 : 0;
    $stmt = mysqli_prepare($con, 'INSERT INTO tbl_prod_imagenes (id_prod, id_color, path, orden, es_principal) VALUES (?, ?, ?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'iisii', $id_prod, $id_color, $filename, $orden, $esPrincipal);
    mysqli_stmt_execute($stmt);

    $existingCount++;
    $orden++;
}

$redirect = isset($_POST['redirect']) ? trim((string)$_POST['redirect']) : '';
if ($redirect !== '' && preg_match('/^listado\.php(\?.*)?$/', $redirect)) {
    header('Location: ' . app_path('admin/productos/' . $redirect));
} else {
    header('Location: ' . app_path('admin/productos/e_producto.php?id=' . $id_prod . '&modificado=1'));
}
exit();
