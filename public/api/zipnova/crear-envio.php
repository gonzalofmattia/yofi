<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../src/php/shipping.php';

function json_response(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Método no permitido'], 405);
}

$internalKey = $_SERVER['HTTP_X_INTERNAL_KEY'] ?? '';
if ($internalKey === '' || !defined('INTERNAL_API_KEY') || INTERNAL_API_KEY === '' || !hash_equals(INTERNAL_API_KEY, $internalKey)) {
    json_response(['success' => false, 'error' => 'No autorizado'], 401);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '', true);
if (!is_array($data)) {
    json_response(['success' => false, 'error' => 'JSON inválido'], 400);
}

$orderId = (int)($data['order_id'] ?? 0);
if ($orderId <= 0) {
    json_response(['success' => false, 'error' => 'order_id requerido'], 400);
}

try {
    $pdo = db_rw();
    $stmt = $pdo->prepare('SELECT * FROM tbl_ordenes WHERE id_orden = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $orden = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$orden) {
        json_response(['success' => false, 'error' => 'Orden no encontrada'], 404);
    }

    $items = json_decode((string)($orden['items'] ?? '[]'), true);
    if (!is_array($items)) {
        $items = [];
    }

    $service = new ZipnovaService($pdo);
    $result = $service->crearEnvio($orderId, $orden, $items);

    if ($result === null) {
        json_response(['success' => false, 'error' => 'No se pudo crear el envío en Zipnova'], 502);
    }

    $estadoAnterior = (string)($orden['estado'] ?? 'pendiente');
    // Generar la etiqueta no mueve a un estado intermedio propio: el pedido
    // sigue 'confirmado' hasta que Zipnova avise 'in_transit' (-> 'enviado').
    $estadoNuevo = 'confirmado';

    $pdo->prepare('UPDATE tbl_ordenes SET estado = ?, fecha_actualizacion = NOW() WHERE id_orden = ?')
        ->execute([$estadoNuevo, $orderId]);

    $pdo->prepare('
        INSERT INTO tbl_ordenes_historial
            (id_orden, estado_anterior, estado_nuevo, usuario_admin, notas, tracking_number)
        VALUES
            (?, ?, ?, ?, ?, ?)
    ')->execute([
        $orderId,
        $estadoAnterior,
        $estadoNuevo,
        'Zipnova API',
        'Envío creado en Zipnova',
        $result['tracking_number'] !== '' ? $result['tracking_number'] : null,
    ]);

    json_response([
        'success' => true,
        'order_id' => $orderId,
        'shipment_id' => $result['shipment_id'],
        'tracking_number' => $result['tracking_number'],
        'label_url' => $result['label_url'],
    ]);
} catch (Throwable $e) {
    logZipnova('crear-envio.php excepción: ' . $e->getMessage(), 'ERROR');
    json_response(['success' => false, 'error' => 'Error interno al crear envío'], 500);
}
