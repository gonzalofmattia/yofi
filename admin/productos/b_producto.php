<?php
require_once __DIR__ . '/../include/includes.php';
require_once __DIR__ . '/../check_session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listado.php');
    exit();
}

$ids = trim((string)($_POST['chkborrar'] ?? ''));
if ($ids === '') {
    header('Location: listado.php');
    exit();
}

$idList = array_filter(array_map('intval', explode(',', $ids)));
foreach ($idList as $id_prod) {
    $stmt = mysqli_prepare($con, 'UPDATE tbl_productos SET borrado = 1, publicado = 0, fecha_actualizacion = NOW() WHERE id_prod = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id_prod);
    mysqli_stmt_execute($stmt);
}

header('Location: listado.php?borrado=1');
exit();
