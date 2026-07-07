<?php

declare(strict_types=1);

/**
 * Procesa checkout — Yofi
 * Items del carrito por id_sku con transacción de stock en tbl_skus.
 */

ob_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/php/db.php';
require_once __DIR__ . '/../src/php/auth.php';
require_once __DIR__ . '/../src/php/stock_reservation.php';
require_once __DIR__ . '/../src/php/shipping.php';
ob_clean();

header('Content-Type: application/json; charset=utf-8');

function checkout_json(array $payload, int $code = 200): void
{
    ob_clean();
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

/**
 * @param array<int, array<string, mixed>> $items
 * @return array<int, int>
 */
function checkout_aggregate_skus(array $items): array
{
    $bySku = [];
    foreach ($items as $item) {
        $idSku = (int)($item['id_sku'] ?? 0);
        $qty = max(1, (int)($item['cantidad'] ?? $item['quantity'] ?? 1));
        if ($idSku <= 0) {
            continue;
        }
        $bySku[$idSku] = ($bySku[$idSku] ?? 0) + $qty;
    }
    ksort($bySku, SORT_NUMERIC);
    return $bySku;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    checkout_json(['success' => false, 'message' => 'Método no permitido'], 405);
}

$csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!validatePublicCsrfToken(is_string($csrfHeader) ? $csrfHeader : null)) {
    checkout_json(['success' => false, 'message' => 'Token de seguridad inválido. Recargá la página.'], 403);
}

$input = file_get_contents('php://input');
$data = json_decode($input ?: '', true);
if (!is_array($data)) {
    checkout_json(['success' => false, 'message' => 'Datos inválidos'], 400);
}

$required = ['customer', 'shipping', 'payment', 'items', 'subtotal', 'total'];
foreach ($required as $field) {
    if (!isset($data[$field])) {
        checkout_json(['success' => false, 'message' => "Campo requerido faltante: {$field}"], 400);
    }
}

$shippingMethodCode = trim((string)($data['shipping_method'] ?? ''));
if ($shippingMethodCode === '') {
    checkout_json([
        'success' => false,
        'message' => 'Por favor, seleccioná un método de envío antes de confirmar el pedido.',
        'error_code' => 'missing_shipping_method',
    ], 400);
}

$rawItems = $data['items'];
if (!is_array($rawItems) || $rawItems === []) {
    checkout_json(['success' => false, 'message' => 'El carrito no es válido.'], 400);
}

$nombre = trim((string)($data['customer']['firstName'] ?? ''));
$apellido = trim((string)($data['customer']['lastName'] ?? ''));
$email = trim((string)($data['customer']['email'] ?? ''));
$telefono = trim((string)($data['customer']['phone'] ?? ''));
$direccion = trim((string)($data['shipping']['address'] ?? ''));
$ciudad = trim((string)($data['shipping']['city'] ?? ''));
$provincia = trim((string)($data['shipping']['province'] ?? ''));
$codigoPostal = trim((string)($data['shipping']['zip'] ?? ''));
$notas = trim((string)($data['shipping']['notes'] ?? ''));
$metodoPago = trim((string)($data['payment']['method'] ?? 'transferencia'));
$shippingCarrier = trim((string)($data['shipping_carrier'] ?? ''));
$shippingEta = trim((string)($data['shipping_eta'] ?? ''));
$shippingMeta = isset($data['shipping_meta']) && is_array($data['shipping_meta']) ? $data['shipping_meta'] : null;

$errorPuntoRetiro = validar_punto_retiro_seleccionado($shippingMethodCode, $shippingMeta);
if ($errorPuntoRetiro !== null) {
    checkout_json([
        'success' => false,
        'message' => $errorPuntoRetiro,
        'error_code' => 'missing_pickup_point',
    ], 400);
}

$missing = [];
if ($nombre === '') {
    $missing[] = 'nombre';
}
if ($apellido === '') {
    $missing[] = 'apellido';
}
if ($email === '') {
    $missing[] = 'email';
}
if ($telefono === '') {
    $missing[] = 'teléfono';
}
if ($direccion === '') {
    $missing[] = 'dirección';
}
if ($ciudad === '') {
    $missing[] = 'ciudad';
}
if ($provincia === '') {
    $missing[] = 'provincia';
}
if ($codigoPostal === '') {
    $missing[] = 'código postal';
}

if ($missing !== []) {
    checkout_json(['success' => false, 'message' => 'Faltan los siguientes campos: ' . implode(', ', $missing)], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    checkout_json(['success' => false, 'message' => 'Email inválido'], 400);
}

$itemsCorregidos = [];
$subtotalRecalculado = 0.0;

foreach ($rawItems as $item) {
    if (!is_array($item)) {
        continue;
    }
    $idSku = (int)($item['id_sku'] ?? 0);
    $idProd = (int)($item['id_prod'] ?? 0);
    $cantidad = max(1, (int)($item['cantidad'] ?? $item['quantity'] ?? 1));
    $precio = (float)($item['precio_unitario'] ?? $item['precio'] ?? $item['price'] ?? 0);

    if ($idSku <= 0 || $precio <= 0) {
        checkout_json(['success' => false, 'message' => 'El carrito contiene productos no válidos.'], 400);
    }

    $line = round($precio * $cantidad, 2);
    $subtotalRecalculado = round($subtotalRecalculado + $line, 2);

    $itemsCorregidos[] = [
        'id_sku' => $idSku,
        'id_prod' => $idProd,
        'nombre' => (string)($item['nombre'] ?? $item['name'] ?? 'Producto'),
        'color_nombre' => (string)($item['color_nombre'] ?? ''),
        'talle_nombre' => (string)($item['talle_nombre'] ?? ''),
        'precio_unitario' => $precio,
        'cantidad' => $cantidad,
        'imagen' => (string)($item['imagen'] ?? ''),
    ];
}

if ($itemsCorregidos === []) {
    checkout_json(['success' => false, 'message' => 'El carrito no tiene productos válidos.'], 400);
}

$subtotalCliente = round((float)($data['subtotal'] ?? 0), 2);
if (abs($subtotalRecalculado - $subtotalCliente) > 1.0) {
    checkout_json([
        'success' => false,
        'message' => 'Los precios han cambiado. Por favor recargá la página.',
        'error_code' => 'price_changed',
        'subtotal_server' => $subtotalRecalculado,
        'subtotal_client' => $subtotalCliente,
    ], 409);
}

$envio = round((float)($data['shipping_cost'] ?? 0), 2);
$total = round((float)($data['total'] ?? ($subtotalRecalculado + $envio)), 2);
$subtotal = $subtotalRecalculado;
$itemsJson = json_encode($itemsCorregidos, JSON_UNESCAPED_UNICODE);
$shippingMetaJson = $shippingMeta ? json_encode($shippingMeta, JSON_UNESCAPED_UNICODE) : null;
$aggSkus = checkout_aggregate_skus($itemsCorregidos);

if ($aggSkus === []) {
    checkout_json(['success' => false, 'message' => 'El carrito no tiene SKUs válidos.'], 400);
}

try {
    $pdo = db_rw();
    stock_expire_pending_reservations($pdo);

    $numeroOrden = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

    $pdo->beginTransaction();

    $usuarioId = getLoggedInUserId();

    $dispSql = stock_disponible_sql('s');
    $lockedStock = [];
    foreach ($aggSkus as $idSku => $qty) {
        $stLock = $pdo->prepare("
            SELECT s.id_sku, s.stock, s.id_prod, p.nombre, {$dispSql} AS disponible
            FROM tbl_skus s
            INNER JOIN tbl_productos p ON p.id_prod = s.id_prod
            WHERE s.id_sku = ?
            FOR UPDATE
        ");
        $stLock->execute([(int)$idSku]);
        $rowLock = $stLock->fetch(PDO::FETCH_ASSOC);

        if (!$rowLock) {
            $pdo->rollBack();
            checkout_json([
                'success' => false,
                'message' => 'Un producto del carrito ya no está disponible.',
                'error_code' => 'insufficient_stock',
            ], 409);
        }

        $avail = (int)$rowLock['disponible'];
        if ((int)$qty > $avail) {
            $pdo->rollBack();
            checkout_json([
                'success' => false,
                'message' => 'Hay productos sin stock suficiente para completar el pedido.',
                'error_code' => 'insufficient_stock',
                'issues' => [[
                    'id_sku' => (int)$idSku,
                    'productName' => (string)$rowLock['nombre'],
                    'requested' => (int)$qty,
                    'available' => $avail,
                ]],
            ], 409);
        }

        $lockedStock[(int)$idSku] = [
            'disponible' => $avail,
            'id_prod' => (int)$rowLock['id_prod'],
            'nombre' => (string)$rowLock['nombre'],
        ];
    }

    $stmtInsert = $pdo->prepare('
        INSERT INTO tbl_ordenes (
            numero_orden, estado, nombre, apellido, email, usuario_id, telefono,
            direccion, ciudad, provincia, codigo_postal, notas,
            metodo_pago, shipping_method_code, shipping_carrier, shipping_eta, shipping_meta,
            subtotal, envio, total, items
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?
        )
    ');

    $stmtInsert->execute([
        $numeroOrden,
        'pendiente',
        $nombre,
        $apellido,
        $email,
        $usuarioId,
        $telefono,
        $direccion,
        $ciudad,
        $provincia,
        $codigoPostal,
        $notas,
        $metodoPago,
        $shippingMethodCode,
        $shippingCarrier !== '' ? $shippingCarrier : null,
        $shippingEta !== '' ? $shippingEta : null,
        $shippingMetaJson,
        $subtotal,
        $envio,
        $total,
        $itemsJson,
    ]);

    $orderId = (int)$pdo->lastInsertId();
    if ($orderId <= 0) {
        throw new RuntimeException('No se pudo obtener el ID de la orden');
    }

    stock_reserve_for_order($pdo, $orderId, $aggSkus);

    $pdo->commit();

    try {
        require_once __DIR__ . '/../src/php/order_emails.php';

        $orderData = [
            'numero_orden' => $numeroOrden,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'subtotal' => $subtotal,
            'envio' => $envio,
            'total' => $total,
            'id_orden' => $orderId,
            'direccion' => $direccion,
            'ciudad' => $ciudad,
            'provincia' => $provincia,
            'codigo_postal' => $codigoPostal,
        ];
        $emailBody = generateOrderReceivedEmail($orderData, $itemsCorregidos, $metodoPago);
        $emailSubject = 'Recibimos tu pedido #' . $numeroOrden . ' - Yofi';
        sendEmail($email, $emailSubject, $emailBody, true);

        $adminOrderData = $orderData + [
            'email' => $email,
            'telefono' => $telefono,
        ];
        $adminUrl = (defined('SITE_URL') ? SITE_URL : '') . '/admin/pedidos/detalle.php?id=' . $orderId;
        $adminBody = generateAdminNewOrderEmail($adminOrderData, $itemsCorregidos, $metodoPago, $adminUrl);
        $adminTo = defined('MAIL_ADMIN') ? MAIL_ADMIN : 'hola@yofi.com.ar';
        sendEmail($adminTo, 'Nuevo pedido #' . $numeroOrden . ' - Yofi', $adminBody, true);
    } catch (Throwable $e) {
        error_log('checkout/process.php: error al enviar email de confirmación: ' . $e->getMessage());
    }

    checkout_json([
        'success' => true,
        'message' => 'Pedido procesado correctamente',
        'order_id' => $orderId,
        'numero_orden' => $numeroOrden,
    ]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('checkout/process.php: ' . $e->getMessage());
    checkout_json([
        'success' => false,
        'message' => ENV === 'dev'
            ? 'Error al procesar el pedido: ' . $e->getMessage()
            : 'Error al procesar el pedido. Por favor, intentá nuevamente.',
    ], 500);
}
