<?php
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';

$id_slide = (int)($_POST['id_slide'] ?? 0);
if ($id_slide > 0) {
    $stmt = mysqli_prepare($con, 'DELETE FROM tbl_slider WHERE id_slide = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id_slide);
    mysqli_stmt_execute($stmt);
}
header('Location: slider.php?borrado=1');
exit();
