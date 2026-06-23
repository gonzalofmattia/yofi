<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once __DIR__ . '/../../../src/php/db.php';
require_once __DIR__ . '/../../../src/php/stock_reservation.php';

$idSku = isset($_GET['id_sku']) ? (int)$_GET['id_sku'] : 0;

if ($idSku <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'id_sku requerido'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = db_ro();
    stock_expire_pending_reservations($pdo);

    $dispSql = stock_disponible_sql('s');
    $stmt = $pdo->prepare(
        "SELECT s.id_sku, s.stock, {$dispSql} AS stock_disponible, s.activo
         FROM tbl_skus s
         WHERE s.id_sku = :id AND s.activo = 1
         LIMIT 1"
    );
    $stmt->execute([':id' => $idSku]);
    $row = $stmt->fetch();

    if (!$row) {
        http_response_code(404);
        echo json_encode(['error' => 'SKU no encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $disponible = max(0, (int)($row['stock_disponible'] ?? $row['stock']));

    echo json_encode([
        'id_sku' => (int)$row['id_sku'],
        'stock' => $disponible,
        'stock_fisico' => (int)$row['stock'],
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar stock'], JSON_UNESCAPED_UNICODE);
}
