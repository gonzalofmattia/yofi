<?php
declare(strict_types=1);

/**
 * Sincroniza un pago de Mercado Pago con la BD (tbl_mp_payments, orden, mail).
 * Usado por webhooks/IPN y por la página de retorno (pago-exitoso) con payment_id en la URL.
 */

require_once dirname(__DIR__, 2) . '/config/mercadopago.php';
require_once __DIR__ . '/stock_reservation.php';

function mp_sync_log(string $message, string $type = 'INFO'): void
{
    $logFile = dirname(__DIR__, 2) . '/logs/mercadopago-webhook.log';
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $timestamp = date('Y-m-d H:i:s');
    @file_put_contents($logFile, "[$timestamp] [$type] [sync] $message" . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function mp_sync_request(string $method, string $url, array $headers = [], ?array $jsonBody = null): array
{
    $ch = curl_init($url);
    if ($ch === false) {
        throw new RuntimeException('No se pudo inicializar cURL');
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $sendHeaders = $headers;
    if ($jsonBody !== null) {
        $body = json_encode($jsonBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $sendHeaders[] = 'Content-Type: application/json';
    }
    if (!empty($sendHeaders)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);
    }

    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false) {
        throw new RuntimeException('Error cURL: ' . $err);
    }

    $decoded = json_decode($raw, true);
    return [
        'status_code' => $code,
        'raw' => $raw,
        'json' => is_array($decoded) ? $decoded : null,
    ];
}

function mp_sync_map_status(string $mpStatus): string
{
    $s = strtolower(trim($mpStatus));
    switch ($s) {
        case 'approved':
        case 'authorized':
        case 'in_process':
            return 'confirmado';
        case 'pending':
        case 'in_mediation':
            return 'pendiente';
        case 'rejected':
        case 'cancelled':
        case 'charged_back':
        case 'refunded':
            return 'cancelado';
        default:
            return 'pendiente';
    }
}

function mp_sync_transition_allowed(string $from, string $to): bool
{
    $from = strtolower(trim($from));
    if ($from === '') {
        $from = 'pendiente';
    }
    $allowed = [
        'pendiente' => ['confirmado', 'cancelado'],
        'confirmado' => ['enviado', 'cancelado'],
        'enviado' => ['entregado'],
        'entregado' => [],
        'cancelado' => [],
    ];
    return isset($allowed[$from]) && in_array($to, $allowed[$from], true);
}

function mp_sync_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->query("SHOW COLUMNS FROM {$table} LIKE " . $pdo->quote($column));
    if ($stmt === false) {
        return false;
    }
    return (bool)$stmt->fetch();
}

/**
 * Confirma reserva de stock al acreditar pago MP (descuento definitivo).
 *
 * @param array<string, mixed> $orderRow
 */
function mp_sync_confirm_sku_stock(PDO $pdo, array $orderRow): void
{
    stock_confirm_order_reservation($pdo, $orderRow);
}

/**
 * @deprecated Usar mp_sync_confirm_sku_stock
 */
function mp_sync_deduct_sku_stock(PDO $pdo, array $orderRow): void
{
    mp_sync_confirm_sku_stock($pdo, $orderRow);
}

/**
 * Descarga el pago en MP, actualiza tablas y envía mail si cambió el estado.
 *
 * Confirmación automática pendiente -> confirmado (ya implementada):
 * cuando MP informa un pago con status 'approved'/'authorized',
 * mp_sync_map_status() lo traduce a 'confirmado' y, si la transición está
 * permitida (mp_sync_transition_allowed()) y el estado realmente cambió
 * (evita reprocesar notificaciones repetidas), esta función actualiza
 * tbl_ordenes, confirma la reserva de stock (mp_sync_confirm_sku_stock) y
 * dispara los mails de cambio de estado al cliente y de pago acreditado al
 * admin. No hace falta agregar nada nuevo para esto.
 *
 * Importante: esta función NO confía en el body del webhook para decidir el
 * estado del pago — siempre vuelve a consultar GET /v1/payments/{id} contra
 * la API de MP con el access_token propio y solo actúa según esa respuesta.
 * Esa doble verificación es la mitigación real contra payloads falsificados
 * (no hay validación del header x-signature que MP sí envía; queda pendiente
 * como posible mejora de hardening, fuera del alcance de esta tarea).
 */
function mp_mercadopago_sync_payment(PDO $pdo, string $paymentId): void
{
    stock_expire_pending_reservations($pdo);

    $paymentId = trim($paymentId);
    if ($paymentId === '') {
        return;
    }

    $creds = mp_credentials();
    if (empty($creds['access_token'])) {
        mp_sync_log('MP access_token vacío', 'ERROR');
        return;
    }

    $apiBase = mp_api_base_url();
    $paymentUrl = $apiBase . '/v1/payments/' . rawurlencode($paymentId);

    try {
        $mpResp = mp_sync_request('GET', $paymentUrl, [
            'Authorization: Bearer ' . $creds['access_token'],
        ]);
    } catch (Throwable $e) {
        mp_sync_log('Error consultando pago: ' . $e->getMessage(), 'ERROR');
        return;
    }

    $mpJson = $mpResp['json'] ?? null;
    if (!is_array($mpJson)) {
        mp_sync_log('Respuesta inválida al consultar pago. code=' . ($mpResp['status_code'] ?? 0) . ' raw=' . substr((string)($mpResp['raw'] ?? ''), 0, 300), 'ERROR');
        return;
    }

    if (($mpResp['status_code'] ?? 0) < 200 || ($mpResp['status_code'] ?? 0) >= 300) {
        mp_sync_log('GET payment HTTP ' . ($mpResp['status_code'] ?? 0) . ' payment_id=' . $paymentId . ' body=' . substr((string)($mpResp['raw'] ?? ''), 0, 400), 'ERROR');
        return;
    }

    $preferenceId = trim((string)($mpJson['preference_id'] ?? ''));
    $mpStatus = (string)($mpJson['status'] ?? 'pending');
    $statusDetail = (string)($mpJson['status_detail'] ?? '');
    $paymentType = (string)($mpJson['payment_type_id'] ?? ($mpJson['payment_type'] ?? ''));
    $paymentMethod = (string)($mpJson['payment_method_id'] ?? ($mpJson['payment_method'] ?? ''));
    $amount = (float)($mpJson['transaction_amount'] ?? 0);

    // En Checkout Pro a veces el GET /payments no trae preference_id en el raíz; viene vía merchant_order.
    $moIdForPref = '';
    if (isset($mpJson['order']['id'])) {
        $moIdForPref = trim((string)$mpJson['order']['id']);
    } elseif (isset($mpJson['merchant_order_id'])) {
        $moIdForPref = trim((string)$mpJson['merchant_order_id']);
    }
    if ($preferenceId === '' && $moIdForPref !== '') {
        $moJson = mp_mercadopago_fetch_merchant_order_json($moIdForPref);
        if (is_array($moJson)) {
            $preferenceId = trim((string)($moJson['preference_id'] ?? ''));
            if ($preferenceId !== '') {
                mp_sync_log('preference_id vía merchant_order mo=' . $moIdForPref, 'INFO');
            }
        }
    }

    // Último recurso: external_reference = id_orden (lo seteamos en create-preference).
    if ($preferenceId === '') {
        $extRef = trim((string)($mpJson['external_reference'] ?? ''));
        if ($extRef !== '' && ctype_digit($extRef)) {
            $like = '%"order_id":' . (int)$extRef . '%';
            $stmtLookup = $pdo->prepare('SELECT preference_id FROM tbl_mp_preferences WHERE shipping_info LIKE ? ORDER BY id DESC LIMIT 1');
            $stmtLookup->execute([$like]);
            $rowL = $stmtLookup->fetch(PDO::FETCH_ASSOC);
            if ($rowL && !empty($rowL['preference_id'])) {
                $preferenceId = (string)$rowL['preference_id'];
                mp_sync_log('preference_id por external_reference order=' . $extRef, 'INFO');
            }
        }
    }

    if ($preferenceId === '') {
        mp_sync_log('payment_id=' . $paymentId . ' sin preference_id (revisá order/merchant_order en respuesta MP)', 'ERROR');
        return;
    }

    try {
        $sql = '
            INSERT INTO tbl_mp_payments
                (payment_id, preference_id, status, status_detail, payment_type, payment_method, amount, created_at, updated_at)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                status = VALUES(status),
                status_detail = VALUES(status_detail),
                payment_type = VALUES(payment_type),
                payment_method = VALUES(payment_method),
                amount = VALUES(amount),
                updated_at = NOW()
        ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $paymentId,
            $preferenceId,
            $mpStatus,
            $statusDetail,
            $paymentType,
            $paymentMethod,
            $amount,
        ]);
    } catch (Throwable $e) {
        mp_sync_log('Error al upsert tbl_mp_payments: ' . $e->getMessage(), 'ERROR');
    }

    $stmtPref = $pdo->prepare('SELECT shipping_info FROM tbl_mp_preferences WHERE preference_id = ? LIMIT 1');
    $stmtPref->execute([$preferenceId]);
    $prefRow = $stmtPref->fetch();
    if (!$prefRow || empty($prefRow['shipping_info'])) {
        mp_sync_log('No tbl_mp_preferences para preference_id=' . $preferenceId, 'ERROR');
        return;
    }

    $shippingInfo = json_decode((string)$prefRow['shipping_info'], true);
    if (!is_array($shippingInfo)) {
        mp_sync_log('shipping_info JSON inválido. preference_id=' . $preferenceId, 'ERROR');
        return;
    }

    $orderId = isset($shippingInfo['order_id']) ? (int)$shippingInfo['order_id'] : 0;
    if ($orderId <= 0) {
        mp_sync_log('order_id inválido en shipping_info. preference_id=' . $preferenceId, 'ERROR');
        return;
    }

    $stmtOrder = $pdo->prepare('SELECT * FROM tbl_ordenes WHERE id_orden = ? LIMIT 1');
    $stmtOrder->execute([$orderId]);
    $orderRow = $stmtOrder->fetch();
    if (!$orderRow) {
        mp_sync_log('No orden id_orden=' . $orderId, 'ERROR');
        return;
    }

    $estadoAnterior = strtolower(trim((string)($orderRow['estado'] ?? 'pendiente')));
    if ($estadoAnterior === '') {
        $estadoAnterior = 'pendiente';
    }
    $estadoNuevo = mp_sync_map_status($mpStatus);

    if ($estadoAnterior === $estadoNuevo) {
        try {
            $stmtPrefUpd = $pdo->prepare('UPDATE tbl_mp_preferences SET status = ?, updated_at = NOW() WHERE preference_id = ?');
            $stmtPrefUpd->execute([$estadoNuevo, $preferenceId]);
        } catch (Throwable $e) {
            mp_sync_log('Error tbl_mp_preferences status: ' . $e->getMessage(), 'ERROR');
        }
        return;
    }

    if (!mp_sync_transition_allowed($estadoAnterior, $estadoNuevo)) {
        mp_sync_log("Transición no permitida {$estadoAnterior} -> {$estadoNuevo}. order_id={$orderId}", 'WARN');
        return;
    }

    try {
        $set = 'estado = ?';
        $params = [$estadoNuevo];

        if (mp_sync_column_exists($pdo, 'tbl_ordenes', 'fecha_cambio_estado')) {
            $set .= ', fecha_cambio_estado = NOW()';
        }

        if ($estadoNuevo === 'cancelado' && mp_sync_column_exists($pdo, 'tbl_ordenes', 'motivo_cancelacion')) {
            $set .= ', motivo_cancelacion = ?';
            $params[] = ($statusDetail !== '' ? $statusDetail : 'Cancelado por MercadoPago');
        }

        $params[] = $orderId;

        $stmtUpd = $pdo->prepare('UPDATE tbl_ordenes SET ' . $set . ' WHERE id_orden = ?');
        $stmtUpd->execute($params);
    } catch (Throwable $e) {
        mp_sync_log('Error UPDATE tbl_ordenes: ' . $e->getMessage(), 'ERROR');
        return;
    }

    try {
        $stmtTbl = $pdo->query("SHOW TABLES LIKE 'tbl_ordenes_historial'");
        $hasHist = (bool)$stmtTbl->fetch();
        if ($hasHist) {
            $stmtHist = $pdo->prepare('
                INSERT INTO tbl_ordenes_historial
                    (id_orden, estado_anterior, estado_nuevo, usuario_admin, notas, tracking_number, motivo_cancelacion)
                VALUES
                    (?, ?, ?, ?, ?, NULL, ?)
            ');
            $stmtHist->execute([
                $orderId,
                $estadoAnterior,
                $estadoNuevo,
                'MercadoPago',
                ($statusDetail !== '' ? $statusDetail : null),
                ($estadoNuevo === 'cancelado' ? ($statusDetail !== '' ? $statusDetail : 'Cancelado por MercadoPago') : null),
            ]);
        }
    } catch (Throwable $e) {
        mp_sync_log('Error historial: ' . $e->getMessage(), 'ERROR');
    }

    if ($estadoNuevo === 'confirmado') {
        mp_sync_confirm_sku_stock($pdo, $orderRow);
    } elseif ($estadoNuevo === 'cancelado') {
        stock_release_order_reservation($pdo, $orderRow, 'Pago rechazado/cancelado MercadoPago');
    }

    try {
        $orderEmailsFile = __DIR__ . '/order_emails.php';
        $emailFile = __DIR__ . '/email.php';
        if (file_exists($orderEmailsFile)) {
            require_once $orderEmailsFile;
        }
        if (file_exists($emailFile)) {
            require_once $emailFile;
        }

        $clienteEmail = (string)($orderRow['email'] ?? '');
        if (!empty($clienteEmail) && function_exists('generateEstadoChangeEmail') && function_exists('sendEmail')) {
            $itemsDecoded = json_decode((string)($orderRow['items'] ?? '[]'), true);
            $orderData = [
                'numero_orden' => (string)($orderRow['numero_orden'] ?? ('ORD-' . $orderId)),
                'nombre' => (string)($orderRow['nombre'] ?? ''),
                'apellido' => (string)($orderRow['apellido'] ?? ''),
                'total' => (float)($orderRow['total'] ?? 0),
                'id_orden' => $orderId,
                'subtotal' => (float)($orderRow['subtotal'] ?? 0),
                'envio' => (float)($orderRow['envio'] ?? 0),
                'items' => is_array($itemsDecoded) ? $itemsDecoded : [],
                'direccion' => (string)($orderRow['direccion'] ?? ''),
                'ciudad' => (string)($orderRow['ciudad'] ?? ''),
                'provincia' => (string)($orderRow['provincia'] ?? ''),
                'codigo_postal' => (string)($orderRow['codigo_postal'] ?? ''),
            ];

            $motivoCancelacion = $estadoNuevo === 'cancelado' ? ($statusDetail !== '' ? $statusDetail : 'Cancelado por MercadoPago') : null;
            $emailBody = generateEstadoChangeEmail($orderData, $estadoNuevo, $estadoAnterior, $statusDetail, null, $motivoCancelacion);

            // MP solo produce pendiente/confirmado/cancelado; enviado/entregado quedan
            // acá por si alguna vez se llegara a usar esta misma función desde otro flujo.
            $titulos = [
                'confirmado' => 'Tu pedido ha sido confirmado',
                'enviado' => '¡Tu pedido ha sido enviado!',
                'entregado' => 'Tu pedido ha sido entregado',
                'cancelado' => 'Tu pedido ha sido cancelado',
            ];
            $emailSubject = ($titulos[$estadoNuevo] ?? 'Actualización de tu pedido') . ' - Pedido #' . $orderData['numero_orden'] . ' - Yofi';

            sendEmail($clienteEmail, $emailSubject, $emailBody, true);
        }

        if ($estadoNuevo === 'confirmado' && function_exists('generateAdminPaymentApprovedEmail') && function_exists('sendEmail')) {
            $xtVars = dirname(__DIR__, 2) . '/admin/include/xt_variables.php';
            if (is_file($xtVars)) {
                require_once $xtVars;
            }
            $adminTo = defined('MAIL_ADMIN') ? MAIL_ADMIN : 'hola@yofi.com.ar';
            $adminSub = 'Pago acreditado MP — Pedido #' . (string)($orderRow['numero_orden'] ?? $orderId) . ' — Yofi';
            $adminBody = generateAdminPaymentApprovedEmail($orderRow, $paymentId, $mpStatus, $statusDetail);
            sendEmail($adminTo, $adminSub, $adminBody, true);
        }
    } catch (Throwable $e) {
        mp_sync_log('Error al enviar email: ' . $e->getMessage(), 'ERROR');
    }

    try {
        $stmtPrefUpd = $pdo->prepare('UPDATE tbl_mp_preferences SET status = ?, updated_at = NOW() WHERE preference_id = ?');
        $stmtPrefUpd->execute([$estadoNuevo, $preferenceId]);
    } catch (Throwable $e) {
        mp_sync_log('Error tbl_mp_preferences final: ' . $e->getMessage(), 'ERROR');
    }

    mp_sync_log("OK payment_id={$paymentId} order_id={$orderId} {$estadoAnterior}->{$estadoNuevo}");
}

/**
 * GET /merchant_orders/{id} (mismo token que la API de pagos).
 */
function mp_mercadopago_fetch_merchant_order_json(string $merchantOrderId): ?array
{
    $merchantOrderId = trim($merchantOrderId);
    if ($merchantOrderId === '') {
        return null;
    }

    $creds = mp_credentials();
    if (empty($creds['access_token'])) {
        return null;
    }

    $url = mp_api_base_url() . '/merchant_orders/' . rawurlencode($merchantOrderId);
    try {
        $resp = mp_sync_request('GET', $url, [
            'Authorization: Bearer ' . $creds['access_token'],
        ]);
    } catch (Throwable $e) {
        mp_sync_log('merchant_orders GET excepción mo=' . $merchantOrderId . ' ' . $e->getMessage(), 'ERROR');
        return null;
    }

    $code = (int)($resp['status_code'] ?? 0);
    $json = $resp['json'] ?? null;
    if ($code < 200 || $code >= 300 || !is_array($json)) {
        mp_sync_log('merchant_orders HTTP ' . $code . ' mo=' . $merchantOrderId . ' raw=' . substr((string)($resp['raw'] ?? ''), 0, 350), 'ERROR');
        return null;
    }

    return $json;
}

/**
 * @return list<string>
 */
function mp_mercadopago_extract_payment_ids_from_mo(array $json): array
{
    $ids = [];
    $payments = $json['payments'] ?? [];
    if (!is_array($payments)) {
        return [];
    }
    foreach ($payments as $p) {
        if (is_array($p) && isset($p['id'])) {
            $ids[] = (string)$p['id'];
        } elseif (is_int($p) || is_float($p)) {
            $ids[] = (string)(int)$p;
        } elseif (is_string($p) && ctype_digit($p)) {
            $ids[] = $p;
        }
    }

    return array_values(array_unique($ids));
}

/**
 * IDs de pago asociados a una merchant_order (IPN topic=merchant_order).
 *
 * @return list<string>
 */
function mp_mercadopago_payment_ids_from_merchant_order(string $merchantOrderId): array
{
    $json = mp_mercadopago_fetch_merchant_order_json($merchantOrderId);
    if (!is_array($json)) {
        return [];
    }

    $ids = mp_mercadopago_extract_payment_ids_from_mo($json);
    if ($ids === []) {
        mp_sync_log('merchant_order mo=' . $merchantOrderId . ' payments vacío keys=' . implode(',', array_keys($json)), 'WARN');
    }

    return $ids;
}
