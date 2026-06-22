<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/zipnova.php';
require_once __DIR__ . '/db.php';

class ZipnovaService
{
    private int $accountId;
    private string $key;
    private string $secret;
    private string $baseUrl;
    private string $cpOrigen;
    private int $originId;
    private int $classificationId;
    private string $quoteEndpoint;
    private ?PDO $pdo = null;

    public function __construct(?PDO $pdo = null)
    {
        $this->accountId = (int)zipnova_credential('ZIPNOVA_ACCOUNT_ID');
        $this->key = zipnova_credential('ZIPNOVA_KEY');
        $this->secret = zipnova_credential('ZIPNOVA_SECRET');
        if (defined('ZIPNOVA_BASE_URL')) {
            $this->baseUrl = ZIPNOVA_BASE_URL;
        } elseif (defined('ZIPNOVA_API_BASE_URL')) {
            $this->baseUrl = ZIPNOVA_API_BASE_URL;
        } else {
            $this->baseUrl = 'https://api.zipnova.com.ar/v2';
        }
        $this->quoteEndpoint = defined('ZIPNOVA_QUOTE_ENDPOINT') ? ZIPNOVA_QUOTE_ENDPOINT : '/shipments/quote';
        $this->cpOrigen = zipnova_credential('ZIPNOVA_CP_ORIGEN') ?: (defined('ZIPNOVA_CP_ORIGEN') ? (string)ZIPNOVA_CP_ORIGEN : '');
        $this->originId = (int)(defined('ZIPNOVA_ORIGIN_ID') ? ZIPNOVA_ORIGIN_ID : 0);
        $this->classificationId = (int)(defined('ZIPNOVA_CLASSIFICATION_ID') ? ZIPNOVA_CLASSIFICATION_ID : 1);
        $this->pdo = $pdo;
    }

    private function getPdo(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $this->pdo = db_rw();
        return $this->pdo;
    }

    /**
     * @return array<int, array{
     *   carrier:string,
     *   service:string,
     *   price:float,
     *   eta:string,
     *   code:string,
     *   logistic_type:string,
     *   carrier_id:int
     * }>
     */
    public function cotizar(
        string $cp_destino,
        float $peso_kg,
        float $alto,
        float $ancho,
        float $prof,
        float $declared_value = 1000.0,
        string $ciudad = '',
        string $provincia = ''
    ): array {
        $raw = $this->fetchRates(
            $cp_destino,
            $peso_kg,
            $alto,
            $ancho,
            $prof,
            $declared_value,
            $ciudad,
            $provincia
        );
        if ($raw === null) {
            return [];
        }

        return $this->mapRatesResponse($raw);
    }

    /**
     * @param array<string, mixed> $json
     * @return array<int, array<string, mixed>>
     */
    public function mapRatesFromResponse(array $json): array
    {
        return $this->mapRatesResponse($json);
    }

    /**
     * Respuesta cruda de la API (útil para debug/tests).
     *
     * @return array<string, mixed>|null
     */
    public function fetchRates(
        string $cp_destino,
        float $peso_kg,
        float $alto,
        float $ancho,
        float $prof,
        float $declared_value = 1000.0,
        string $ciudad = '',
        string $provincia = ''
    ): ?array {
        $cp_destino = preg_replace('/\D/', '', $cp_destino) ?? '';
        if (strlen($cp_destino) < 4) {
            logZipnova('CP destino inválido: ' . $cp_destino, 'WARN');
            return null;
        }

        if ($this->accountId <= 0 || $this->key === '' || $this->secret === '') {
            logZipnova('Credenciales Zipnova incompletas', 'ERROR');
            return null;
        }

        if ($this->originId <= 0) {
            logZipnova('ZIPNOVA_ORIGIN_ID no configurado', 'ERROR');
            return null;
        }

        $payload = $this->buildQuotePayload(
            $cp_destino,
            $peso_kg,
            $alto,
            $ancho,
            $prof,
            $declared_value,
            $this->classificationId,
            $ciudad,
            $provincia
        );

        $response = $this->request('POST', $this->quoteEndpoint, $payload);

        if ($response === null || ($response['status_code'] ?? 0) < 200 || ($response['status_code'] ?? 0) >= 300) {
            logZipnova(
                'Error cotización CP=' . $cp_destino . ' HTTP=' . ($response['status_code'] ?? 0)
                . ' body=' . substr((string)($response['raw'] ?? ''), 0, 500),
                'ERROR'
            );
            return null;
        }

        return is_array($response['json']) ? $response['json'] : null;
    }

    /**
     * Payload de cotización V2 (POST /shipments/quote).
     *
     * @return array<string, mixed>
     */
    public function buildQuotePayload(
        string $cp_destino,
        float $peso_kg,
        float $alto,
        float $ancho,
        float $prof,
        float $declared_value = 1000.0,
        ?int $classification_id = null,
        string $ciudad = '',
        string $provincia = ''
    ): array {
        return [
            'account_id' => $this->accountId,
            'origin_id' => $this->originId,
            'destination' => $this->buildQuoteDestination($cp_destino, $ciudad, $provincia),
            'declared_value' => (int)round($declared_value),
            'packages' => $this->buildPackagesPayload(
                $peso_kg,
                $alto,
                $ancho,
                $prof,
                'Ropa infantil',
                $classification_id ?? $this->classificationId
            ),
        ];
    }

    /**
     * @return array{zipcode:string,city:string,state:string}
     */
    private function buildQuoteDestination(string $cp_destino, string $ciudad, string $provincia): array
    {
        $zipcode = preg_replace('/\D/', '', $cp_destino) ?? '';
        $city = trim($ciudad) !== '' ? trim($ciudad) : 'Buenos Aires';
        $state = trim($provincia) !== '' ? trim($provincia) : 'Buenos Aires';

        return [
            'zipcode' => $zipcode,
            'city' => $city,
            'state' => $state,
        ];
    }

    /**
     * @deprecated Usar buildQuotePayload()
     * @return array<string, mixed>
     */
    public function buildQuotePayloadWithoutAccount(
        string $cp_destino,
        float $peso_kg,
        float $alto,
        float $ancho,
        float $prof,
        float $declared_value = 1000.0,
        string $ciudad = '',
        string $provincia = ''
    ): array {
        return $this->buildQuotePayload(
            $cp_destino,
            $peso_kg,
            $alto,
            $ancho,
            $prof,
            $declared_value,
            null,
            $ciudad,
            $provincia
        );
    }

    /**
     * Request de diagnóstico con logging (scripts de test).
     *
     * @return array{method:string,endpoint:string,status:int,body_preview:string,hit:bool,raw:string}
     */
    public function probeHttpRequest(
        string $method,
        string $endpoint,
        ?array $body = null,
        int $previewLength = 300
    ): array {
        $method = strtoupper(trim($method));
        $response = $this->request($method, $endpoint, $body);
        $status = (int)($response['status_code'] ?? 0);
        $raw = (string)($response['raw'] ?? '');
        $bodyPreview = substr($raw, 0, $previewLength);

        logZipnova(
            'Probe ' . $method . ' ' . $endpoint . ' HTTP=' . $status . ' body=' . $bodyPreview,
            'INFO'
        );

        return [
            'method' => $method,
            'endpoint' => $endpoint,
            'status' => $status,
            'body_preview' => $bodyPreview,
            'raw' => $raw,
            'hit' => $this->isQuoteSuccess($response),
        ];
    }

    /**
     * @param array{status_code:int,raw:string,json:?array}|null $response
     */
    private function isQuoteSuccess(?array $response): bool
    {
        if ($response === null) {
            return false;
        }

        $code = (int)($response['status_code'] ?? 0);
        return $code >= 200 && $code < 300;
    }

    /**
     * @param array<string, mixed> $orden
     * @param array<int, array<string, mixed>> $items
     * @return array{shipment_id:string,tracking_number:string,label_url:string}|null
     */
    public function crearEnvio(int $order_id, array $orden, array $items): ?array
    {
        $dims = $this->resolvePackageFromItems($items);
        $shippingMeta = $this->parseShippingMeta($orden);
        $destination = $this->buildDestinationPayload($orden, $shippingMeta);
        $declaredValue = (int)round((float)($orden['total'] ?? 1000));
        $externalId = $this->buildExternalId($orden, $order_id);

        $serviceType = (string)($shippingMeta['service_type'] ?? $orden['shipping_method_code'] ?? 'standard');
        $logisticType = (string)($shippingMeta['logistic_type'] ?? 'crossdock');
        $carrierId = (int)($shippingMeta['carrier_id'] ?? 0);

        if ($carrierId <= 0) {
            logZipnova('crearEnvio sin carrier_id order_id=' . $order_id, 'ERROR');
            return null;
        }

        $payload = [
            'account_id' => $this->accountId,
            'external_id' => $externalId,
            'service_type' => $serviceType,
            'logistic_type' => $logisticType,
            'carrier_id' => $carrierId,
            'origin_id' => $this->originId,
            'declared_value' => $declaredValue,
            'source' => 'yofi-tienda',
            'destination' => $destination,
            'packages' => $this->buildPackagesPayload(
                (float)$dims['weight'],
                (float)$dims['height'],
                (float)$dims['width'],
                (float)$dims['depth'],
                'Ropa infantil Yofi',
                $this->classificationId
            ),
        ];

        $response = $this->request('POST', '/shipments', $payload);

        if ($response === null || ($response['status_code'] ?? 0) < 200 || ($response['status_code'] ?? 0) >= 300) {
            logZipnova(
                'Error crearEnvio order_id=' . $order_id . ' HTTP=' . ($response['status_code'] ?? 0)
                . ' body=' . substr((string)($response['raw'] ?? ''), 0, 500),
                'ERROR'
            );
            return null;
        }

        $json = is_array($response['json']) ? $response['json'] : [];
        $shipmentId = (string)($json['shipment_id'] ?? $json['id'] ?? '');
        $tracking = (string)($json['tracking_number'] ?? $json['tracking'] ?? '');
        $labelUrl = (string)($json['label_url'] ?? $json['label'] ?? '');

        if ($shipmentId === '') {
            logZipnova('crearEnvio sin shipment_id order_id=' . $order_id, 'ERROR');
            return null;
        }

        $stmt = $this->getPdo()->prepare('
            UPDATE tbl_ordenes
            SET zipnova_shipment_id = ?, tracking_number = ?, fecha_actualizacion = NOW()
            WHERE id_orden = ?
        ');
        $stmt->execute([$shipmentId, $tracking !== '' ? $tracking : null, $order_id]);

        return [
            'shipment_id' => $shipmentId,
            'tracking_number' => $tracking,
            'label_url' => $labelUrl,
        ];
    }

    public function getEtiqueta(string $shipment_id): ?string
    {
        $shipment_id = trim($shipment_id);
        if ($shipment_id === '') {
            return null;
        }

        $response = $this->request('GET', '/shipments/' . rawurlencode($shipment_id) . '/label');

        if ($response === null || ($response['status_code'] ?? 0) < 200 || ($response['status_code'] ?? 0) >= 300) {
            logZipnova('Error getEtiqueta shipment_id=' . $shipment_id, 'ERROR');
            return null;
        }

        $json = is_array($response['json']) ? $response['json'] : [];
        if (!empty($json['label_base64'])) {
            return (string)$json['label_base64'];
        }
        if (!empty($json['pdf'])) {
            return (string)$json['pdf'];
        }

        $raw = (string)($response['raw'] ?? '');
        if ($raw !== '' && base64_decode($raw, true) !== false) {
            return $raw;
        }

        return null;
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array{weight:float,height:float,width:float,depth:float}
     */
    public function resolvePackageFromItems(array $items): array
    {
        $peso = 0.0;
        $alto = 0.0;
        $ancho = 0.0;
        $prof = 0.0;

        $stmt = $this->getPdo()->prepare('
            SELECT p.peso, p.alto, p.ancho, p.profundidad
            FROM tbl_skus s
            INNER JOIN tbl_productos p ON p.id_prod = s.id_prod
            WHERE s.id_sku = ?
            LIMIT 1
        ');

        foreach ($items as $item) {
            $idSku = (int)($item['id_sku'] ?? 0);
            $cantidad = max(1, (int)($item['cantidad'] ?? $item['quantity'] ?? 1));
            if ($idSku <= 0) {
                continue;
            }

            $stmt->execute([$idSku]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                continue;
            }

            $itemPeso = (float)($row['peso'] ?? 0);
            $itemAlto = (float)($row['alto'] ?? 0);
            $itemAncho = (float)($row['ancho'] ?? 0);
            $itemProf = (float)($row['profundidad'] ?? 0);

            if ($itemPeso <= 0) {
                $itemPeso = 0.5;
            }
            if ($itemAlto <= 0) {
                $itemAlto = 20;
            }
            if ($itemAncho <= 0) {
                $itemAncho = 20;
            }
            if ($itemProf <= 0) {
                $itemProf = 5;
            }

            $peso += $itemPeso * $cantidad;
            $alto = max($alto, $itemAlto);
            $ancho = max($ancho, $itemAncho);
            $prof = max($prof, $itemProf);
        }

        if ($peso <= 0) {
            $peso = 0.5;
        }
        if ($alto <= 0) {
            $alto = 20;
        }
        if ($ancho <= 0) {
            $ancho = 20;
        }
        if ($prof <= 0) {
            $prof = 5;
        }

        return [
            'weight' => $peso,
            'height' => $alto,
            'width' => $ancho,
            'depth' => $prof,
        ];
    }

    /**
     * Convierte medidas internas (kg, cm) al formato V2 (gramos, mm).
     *
     * @return array<int, array<string, int|string>>
     */
    private function buildPackagesPayload(
        float $peso_kg,
        float $alto_cm,
        float $ancho_cm,
        float $prof_cm,
        string $description = 'Ropa infantil',
        ?int $classification_id = null
    ): array {
        return [[
            'weight' => (int)round($peso_kg * 1000),
            'height' => (int)round($alto_cm * 10),
            'width' => (int)round($ancho_cm * 10),
            'length' => (int)round($prof_cm * 10),
            'description_1' => $description,
            'classification_id' => $classification_id ?? $this->classificationId,
        ]];
    }

    /**
     * @param array<string, mixed> $orden
     * @return array<string, mixed>
     */
    private function parseShippingMeta(array $orden): array
    {
        $meta = $orden['shipping_meta'] ?? null;
        if (is_string($meta)) {
            $decoded = json_decode($meta, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($meta) ? $meta : [];
    }

    /**
     * @param array<string, mixed> $orden
     * @param array<string, mixed> $shippingMeta
     * @return array<string, string>
     */
    private function buildDestinationPayload(array $orden, array $shippingMeta): array
    {
        $addressParts = $this->parseAddress((string)($orden['direccion'] ?? ''));
        $document = (string)($shippingMeta['document'] ?? $shippingMeta['dni'] ?? '00000000');
        $phone = preg_replace('/\D+/', '', (string)($orden['telefono'] ?? '')) ?? '';

        return [
            'name' => trim((string)($orden['nombre'] ?? '') . ' ' . (string)($orden['apellido'] ?? '')),
            'document' => $document,
            'email' => (string)($orden['email'] ?? ''),
            'phone' => $phone,
            'street' => $addressParts['street'],
            'street_number' => $addressParts['street_number'],
            'city' => (string)($orden['ciudad'] ?? ''),
            'state' => (string)($orden['provincia'] ?? ''),
            'zipcode' => preg_replace('/\D/', '', (string)($orden['codigo_postal'] ?? '')) ?? '',
        ];
    }

    /**
     * @return array{street:string,street_number:string}
     */
    private function parseAddress(string $address): array
    {
        $address = trim($address);
        if ($address !== '' && preg_match('/^(.+?)\s+(\d+\w*)$/u', $address, $matches)) {
            return [
                'street' => trim($matches[1]),
                'street_number' => $matches[2],
            ];
        }

        return [
            'street' => $address !== '' ? $address : 'Sin calle',
            'street_number' => 'S/N',
        ];
    }

    /**
     * @param array<string, mixed> $orden
     */
    private function buildExternalId(array $orden, int $order_id): string
    {
        $externalId = trim((string)($orden['numero_orden'] ?? ('YOFI-' . $order_id)));
        if (strlen($externalId) > 30) {
            $externalId = substr($externalId, 0, 30);
        }

        return $externalId;
    }

    /**
     * @param array<string, mixed>|null $body
     * @return array{status_code:int,raw:string,json:?array}|null
     */
    private function request(string $method, string $endpoint, ?array $body = null): ?array
    {
        $method = strtoupper(trim($method));
        if (!in_array($method, ['GET', 'POST', 'PUT'], true)) {
            logZipnova('Método HTTP no soportado: ' . $method, 'ERROR');
            return null;
        }

        $url = rtrim($this->baseUrl, '/') . $endpoint;
        $auth = base64_encode($this->key . ':' . $this->secret);

        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic ' . $auth,
        ];

        $bodyJson = null;
        if ($body !== null) {
            $bodyJson = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $ch = curl_init($url);
        if ($ch === false) {
            logZipnova('No se pudo inicializar cURL para ' . $endpoint, 'ERROR');
            return null;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        switch ($method) {
            case 'GET':
                if ($bodyJson !== null) {
                    // GET con body JSON (Variante A)
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyJson);
                } else {
                    curl_setopt($ch, CURLOPT_HTTPGET, true);
                }
                break;

            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($bodyJson !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyJson);
                }
                break;

            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($bodyJson !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyJson);
                }
                break;
        }

        if (defined('ZIPNOVA_DEBUG') && ZIPNOVA_DEBUG) {
            logZipnova(
                "DEBUG REQUEST\n"
                . 'Method: ' . $method . "\n"
                . 'URL: ' . $url . "\n"
                . 'Headers: ' . json_encode($headers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n"
                . 'Body: ' . ($bodyJson ?? '(null)'),
                'DEBUG'
            );
        }

        $raw = curl_exec($ch);
        $err = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) {
            logZipnova('cURL error ' . $endpoint . ': ' . $err, 'ERROR');
            if (defined('ZIPNOVA_DEBUG') && ZIPNOVA_DEBUG) {
                logZipnova("DEBUG RESPONSE\nHTTP: (error)\nBody: cURL error: {$err}", 'DEBUG');
            }
            return null;
        }

        if (defined('ZIPNOVA_DEBUG') && ZIPNOVA_DEBUG) {
            logZipnova(
                "DEBUG RESPONSE\n"
                . 'HTTP: ' . $code . "\n"
                . 'Body: ' . $raw,
                'DEBUG'
            );
        }

        $decoded = json_decode($raw, true);
        logZipnova($method . ' ' . $endpoint . ' HTTP=' . $code);

        return [
            'status_code' => $code,
            'raw' => $raw,
            'json' => is_array($decoded) ? $decoded : null,
        ];
    }

    /**
     * @param array<string, mixed> $json
     * @return array<int, array{
     *   carrier:string,
     *   service:string,
     *   price:float,
     *   eta:string,
     *   code:string,
     *   logistic_type:string,
     *   carrier_id:int
     * }>
     */
    private function mapRatesResponse(array $json): array
    {
        $rates = $json['all_results'] ?? [];
        if (!is_array($rates) || $rates === []) {
            return [];
        }

        $options = [];
        foreach ($rates as $rate) {
            if (!is_array($rate)) {
                continue;
            }

            if (($rate['selectable'] ?? true) !== true) {
                continue;
            }

            $carrierData = is_array($rate['carrier'] ?? null) ? $rate['carrier'] : [];
            $serviceTypeData = is_array($rate['service_type'] ?? null) ? $rate['service_type'] : [];
            $amounts = is_array($rate['amounts'] ?? null) ? $rate['amounts'] : [];
            $deliveryTime = is_array($rate['delivery_time'] ?? null) ? $rate['delivery_time'] : [];

            $carrierName = (string)($carrierData['name'] ?? 'Transportista');
            $carrierId = (int)($carrierData['id'] ?? 0);
            $serviceCode = (string)($serviceTypeData['code'] ?? 'standard_delivery');
            $serviceName = (string)($serviceTypeData['name'] ?? $this->formatServiceLabel($serviceCode));
            $logisticType = (string)($rate['logistic_type'] ?? '');
            $price = (float)($amounts['price_incl_tax'] ?? 0);

            if ($price <= 0 || $carrierId <= 0) {
                continue;
            }

            $min = (int)($deliveryTime['min'] ?? 0);
            $max = (int)($deliveryTime['max'] ?? 0);
            if ($min > 0 && $max > 0) {
                $eta = $min === $max
                    ? $min . ' día' . ($min === 1 ? '' : 's') . ' hábiles'
                    : $min . ' a ' . $max . ' días hábiles';
            } else {
                $eta = $this->formatDeliveryEta($rate);
            }

            $options[] = [
                'carrier' => $carrierName,
                'service' => $serviceName,
                'price' => $price,
                'eta' => $eta,
                'code' => $serviceCode,
                'logistic_type' => $logisticType,
                'carrier_id' => $carrierId,
            ];
        }

        return $options;
    }

    /**
     * @param array<string, mixed> $rate
     */
    private function formatDeliveryEta(array $rate): string
    {
        $deliveryTime = is_array($rate['delivery_time'] ?? null) ? $rate['delivery_time'] : [];
        $min = (int)($deliveryTime['min'] ?? 0);
        $max = (int)($deliveryTime['max'] ?? 0);
        if ($min > 0 && $max > 0) {
            if ($min === $max) {
                return $min . ' día' . ($min === 1 ? '' : 's') . ' hábiles';
            }

            return $min . ' a ' . $max . ' días hábiles';
        }

        $duration = $deliveryTime['times']['total'] ?? '';
        if (!is_string($duration) || $duration === '') {
            return 'Consultar plazo';
        }

        if (preg_match('/P(?:(\d+)D)?(?:T(?:(\d+)H)?)?/', $duration, $matches)) {
            $days = isset($matches[1]) && $matches[1] !== '' ? (int)$matches[1] : 0;
            if ($days > 0) {
                return $days . ' día' . ($days === 1 ? '' : 's') . ' hábiles';
            }
        }

        return 'Consultar plazo';
    }

    private function formatServiceLabel(string $serviceType): string
    {
        $labels = [
            'standard' => 'Estándar',
            'express' => 'Express',
            'priority' => 'Prioritario',
        ];

        return $labels[strtolower($serviceType)] ?? ucfirst(str_replace('_', ' ', $serviceType));
    }
}

/**
 * Calcula dimensiones/peso agregados para cotización desde items del carrito.
 *
 * @param array<int, array<string, mixed>> $items
 * @return array{weight:float,height:float,width:float,depth:float}
 */
function zipnova_aggregate_package(PDO $pdo, array $items): array
{
    $service = new ZipnovaService($pdo);
    return $service->resolvePackageFromItems($items);
}
