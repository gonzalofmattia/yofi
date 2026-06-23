<?php
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listado.php');
    exit();
}

$id_cate = (int)($_POST['id_cate'] ?? 0);
if ($id_cate <= 0) {
    header('Location: listado.php');
    exit();
}

// Solo productos activos bloquean la eliminación en el admin
$stmt = mysqli_prepare($con, 'SELECT COUNT(*) AS n FROM tbl_productos WHERE id_cate = ? AND borrado = 0');
mysqli_stmt_bind_param($stmt, 'i', $id_cate);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$numProductosActivos = (int)($row['n'] ?? 0);

if ($numProductosActivos > 0) {
    header('Location: listado.php?error=en_uso_productos');
    exit();
}

$stmt = mysqli_prepare($con, 'SELECT COUNT(*) AS n FROM tbl_categorias WHERE id_cate_padre = ?');
mysqli_stmt_bind_param($stmt, 'i', $id_cate);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$numSubcategorias = (int)($row['n'] ?? 0);

if ($numSubcategorias > 0) {
    header('Location: listado.php?error=en_uso_subcategorias');
    exit();
}

// Productos en papelera (borrado=1) siguen referenciando la FK: reasignarlos
$stmt = mysqli_prepare($con, 'SELECT COUNT(*) AS n FROM tbl_productos WHERE id_cate = ? AND borrado = 1');
mysqli_stmt_bind_param($stmt, 'i', $id_cate);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$numProductosPapelera = (int)($row['n'] ?? 0);

if ($numProductosPapelera > 0) {
    $stmt = mysqli_prepare($con, 'SELECT id_cate FROM tbl_categorias WHERE id_cate != ? ORDER BY orden ASC, nombre ASC LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $id_cate);
    mysqli_stmt_execute($stmt);
    $fallbackRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $idCateFallback = (int)($fallbackRow['id_cate'] ?? 0);

    if ($idCateFallback <= 0) {
        header('Location: listado.php?error=delete_failed');
        exit();
    }

    $stmt = mysqli_prepare($con, 'UPDATE tbl_productos SET id_cate = ? WHERE id_cate = ? AND borrado = 1');
    mysqli_stmt_bind_param($stmt, 'ii', $idCateFallback, $id_cate);
    mysqli_stmt_execute($stmt);
}

try {
    $stmt = mysqli_prepare($con, 'DELETE FROM tbl_categorias WHERE id_cate = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id_cate);
    mysqli_stmt_execute($stmt);
} catch (mysqli_sql_exception $e) {
    header('Location: listado.php?error=delete_failed');
    exit();
}

header('Location: listado.php?borrado=1');
exit();
