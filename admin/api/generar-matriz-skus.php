<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/include/includes.php';
require_once dirname(__DIR__) . '/check_session.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '', true);
if (!is_array($data)) {
    echo json_encode(['success' => false, 'error' => 'JSON inválido']);
    exit;
}

$id_prod = (int)($data['id_prod'] ?? 0);
if ($id_prod <= 0) {
    echo json_encode(['success' => false, 'error' => 'id_prod requerido']);
    exit;
}

$colores = mysqli_query($con, 'SELECT id_color FROM tbl_colores WHERE activo = 1');
$talles = mysqli_query($con, 'SELECT id_talle FROM tbl_talles WHERE activo = 1');

$coloresList = [];
$tallesList = [];
while ($colores && ($c = mysqli_fetch_assoc($colores))) {
    $coloresList[] = $c;
}
while ($talles && ($t = mysqli_fetch_assoc($talles))) {
    $tallesList[] = $t;
}

$stmtMat = mysqli_prepare($con, '
    INSERT IGNORE INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock, activo)
    VALUES (?, ?, ?, ?, 0, 1)
');
$insertados = 0;
foreach ($coloresList as $c) {
    foreach ($tallesList as $t) {
        $id_color = (int)$c['id_color'];
        $id_talle = (int)$t['id_talle'];
        $codigo = 'YOFI-' . $id_prod . '-' . $id_color . '-' . $id_talle;
        mysqli_stmt_bind_param($stmtMat, 'iiis', $id_prod, $id_color, $id_talle, $codigo);
        if (mysqli_stmt_execute($stmtMat) && mysqli_stmt_affected_rows($stmtMat) > 0) {
            $insertados++;
        }
    }
}

echo json_encode(['success' => true, 'insertados' => $insertados]);
