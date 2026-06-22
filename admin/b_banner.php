<?php
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$id_banner = (int)($_POST['id_banner'] ?? 0);
if ($id_banner > 0) {
    $stmt = mysqli_prepare($con, 'DELETE FROM tbl_banners WHERE id_banner = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id_banner);
    mysqli_stmt_execute($stmt);
}
header('Location: banners.php?borrado=1');
exit();
