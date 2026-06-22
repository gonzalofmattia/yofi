<?php
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

$id_cate = (int)($_POST['id_cate'] ?? 0);
if ($id_cate > 0) {
    $stmt = mysqli_prepare($con, 'DELETE FROM tbl_categorias WHERE id_cate = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id_cate);
    mysqli_stmt_execute($stmt);
}
header('Location: listado.php?borrado=1');
exit();
