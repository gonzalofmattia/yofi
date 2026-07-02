<?php

declare(strict_types=1);

/**
 * Prueba manual del webhook de Mercado Pago (sin credenciales reales).
 *
 * Uso:
 *   php scripts/test_webhook_mp.php <id_orden> [payment_id_sandbox_real]
 *
 * Nota de nombre de archivo: el resto de scripts/ usa guiones (test-smtp.php,
 * test-zipnova.php), este queda con guion bajo tal como se pidió.
 *
 * QUÉ HACE:
 *  1. Busca la orden local y muestra su estado actual.
 *  2. Si no existe, crea una fila de prueba en tbl_mp_preferences (marcada con
 *     preference_id = 'TEST-...') que apunta a esa orden — es el mecanismo real
 *     que usa mp_mercadopago_sync_payment() para resolver payment_id -> order_id
 *     (vía shipping_info.order_id), no toca preferencias reales existentes.
 *  3. Arma el payload JSON que Mercado Pago realmente envía en un webhook de
 *     pago (action=payment.updated), simulando SOLO los campos que el endpoint
 *     de verdad lee.
 *  4. Hace POST a webhooks/mp-notification.php (la URL local real, no
 *     "webhook.php" — ver MP_NOTIFICATION_URL en config/mercadopago.php).
 *  5. Muestra la respuesta HTTP y el estado de la orden antes/después.
 *
 * LIMITACIÓN IMPORTANTE (por diseño, no es un bug de este script):
 * mp_mercadopago_sync_payment() (src/php/mp_sync.php) NO confía en el status
 * que viene en el body del webhook — vuelve a pedirle el pago de verdad a la
 * API de Mercado Pago (GET /v1/payments/{id}) usando MP_ACCESS_TOKEN, y solo
 * actúa según esa respuesta. Es la mitigación real contra payloads
 * falsificados (no hay validación del header x-signature). Por eso:
 *   - Sin MP_ACCESS_TOKEN configurado (Laragon local típico): la sync loguea
 *     error "access_token vacío" y la orden NO cambia de estado. Es el
 *     comportamiento correcto y esperado, no un fallo del script.
 *   - Con credenciales sandbox reales Y un payment_id que exista de verdad en
 *     Mercado Pago (pasado como 2º argumento), sí se completa la transición.
 */

$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
// DOCUMENT_ROOT debe ser la raíz web real (un nivel arriba de /yofi), no el
// propio project root, para que yofi_app_base_path() arme bien "/yofi".
// Se pisa sin condicional: en CLI suele venir ya seteado como '' (string
// vacío, no null), y un simple "??" no lo reemplazaría.
$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', dirname(__DIR__, 2));
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/php/db.php';

if ($argc < 2) {
    fwrite(STDERR, "Uso: php scripts/test_webhook_mp.php <id_orden> [payment_id_sandbox_real]\n");
    exit(1);
}

$orderId = (int) $argv[1];
if ($orderId <= 0) {
    fwrite(STDERR, "id_orden inválido.\n");
    exit(1);
}

$paymentIdReal = isset($argv[2]) ? trim((string) $argv[2]) : '';

$pdo = db_rw();

$stmt = $pdo->prepare('SELECT * FROM tbl_ordenes WHERE id_orden = ? LIMIT 1');
$stmt->execute([$orderId]);
$orden = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orden) {
    fwrite(STDERR, "No existe la orden id_orden={$orderId}.\n");
    exit(1);
}

echo "Orden #{$orderId} ({$orden['numero_orden']}) — estado ANTES: {$orden['estado']}\n";

// Payment ID a simular: el real de sandbox si se pasó, o uno sintético (que la
// API de MP no va a reconocer — ver limitación explicada arriba).
$paymentId = $paymentIdReal !== '' ? $paymentIdReal : ('TEST-PAY-' . $orderId . '-' . time());

// mp_mercadopago_sync_payment() resuelve order_id a través de
// tbl_mp_preferences.shipping_info (JSON con "order_id"), no directamente del
// payment. Si no hay ya una preferencia de test para esta orden, se crea una
// — sin tocar preferencias reales.
$stmtPref = $pdo->prepare("SELECT preference_id FROM tbl_mp_preferences WHERE shipping_info LIKE ? AND preference_id LIKE 'TEST-%' LIMIT 1");
$stmtPref->execute(['%"order_id":' . $orderId . '%']);
$prefExistente = $stmtPref->fetchColumn();

if ($prefExistente) {
    $preferenceId = (string) $prefExistente;
    echo "Reutilizando preferencia de test existente: {$preferenceId}\n";
} else {
    $preferenceId = 'TEST-' . $orderId . '-' . time();
    $pdo->prepare('
        INSERT INTO tbl_mp_preferences (preference_id, items, shipping_info, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, NOW(), NOW())
    ')->execute([
        $preferenceId,
        json_encode(['test' => true], JSON_UNESCAPED_UNICODE),
        json_encode(['order_id' => $orderId], JSON_UNESCAPED_UNICODE),
        'pending',
    ]);
    echo "Creada preferencia de test: {$preferenceId}\n";
}

// Vincula el payment_id de test a esa preferencia (igual que haría el flujo
// real al crear el pago), para que el GET a la API de MP tenga de dónde salir
// si algún día se corre con credenciales reales.
if ($paymentIdReal === '') {
    $pdo->prepare('
        INSERT INTO tbl_mp_payments (payment_id, preference_id, status, status_detail, payment_type, payment_method, amount, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ON DUPLICATE KEY UPDATE preference_id = VALUES(preference_id), updated_at = NOW()
    ')->execute([$paymentId, $preferenceId, 'approved', 'accredited', 'test', 'test', (float) ($orden['total'] ?? 0)]);
}

// Payload que MP realmente manda en un webhook de pago (action=payment.updated).
// El endpoint (webhooks/mp-notification.php) solo lee data.id — el resto de los
// campos se simulan igual para que el payload sea representativo del real.
$payload = [
    'action' => 'payment.updated',
    'api_version' => 'v1',
    'data' => ['id' => $paymentId],
    'date_created' => date('c'),
    'id' => random_int(100000000, 999999999),
    'live_mode' => false,
    'type' => 'payment',
    'user_id' => '000000000',
];

$url = defined('MP_NOTIFICATION_URL') ? MP_NOTIFICATION_URL : ((defined('SITE_URL') ? SITE_URL : 'http://localhost/yofi') . '/webhooks/mp-notification.php');
echo "POST {$url}\n";
echo 'Payload: ' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT => 30,
]);
$respBody = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($respBody === false) {
    fwrite(STDERR, "Error cURL: {$curlError}\n");
    exit(1);
}

echo "\nRespuesta HTTP {$httpCode}: " . trim((string) $respBody) . "\n";

$stmt->execute([$orderId]);
$ordenDespues = $stmt->fetch(PDO::FETCH_ASSOC);
echo "\nOrden #{$orderId} — estado DESPUÉS: {$ordenDespues['estado']}\n";

if ($paymentIdReal === '') {
    echo "\n[AVISO] No se pasó un payment_id real de sandbox: mp_mercadopago_sync_payment()\n"
        . "vuelve a consultar la API real de Mercado Pago antes de confiar en este payload\n"
        . "(protección anti-spoofing, ver comentario al inicio del script), así que es\n"
        . "esperable que el estado NO haya cambiado. Para probar la transición completa,\n"
        . "corré: php scripts/test_webhook_mp.php {$orderId} <payment_id_real_de_sandbox>\n"
        . "con config/mercadopago.local.php configurado con credenciales de prueba de MP.\n";

    $logFile = __DIR__ . '/../logs/mercadopago-webhook.log';
    if (is_file($logFile)) {
        $lines = file($logFile, FILE_IGNORE_NEW_LINES);
        $ultimas = array_slice($lines ?: [], -10);
        echo "\nÚltimas líneas de logs/mercadopago-webhook.log:\n" . implode("\n", $ultimas) . "\n";
    }
}
