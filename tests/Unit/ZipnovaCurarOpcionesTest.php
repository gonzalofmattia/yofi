<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/php/shipping.php';

final class ZipnovaCurarOpcionesTest extends TestCase
{
    private function opt(string $carrier, string $eta, float $price, string $code = 'standard_delivery'): array
    {
        return [
            'carrier' => $carrier,
            'service' => 'Estándar',
            'price' => $price,
            'eta' => $eta,
            'code' => $code,
            'logistic_type' => 'crossdock',
            'carrier_id' => 1,
        ];
    }

    public function testNoFusionaOpcionesConMismoCarrierYEtaPeroDistintoCode(): void
    {
        $opciones = [
            $this->opt('OCA', '3 a 4 días hábiles', 8600.0, 'standard_delivery'),
            $this->opt('OCA', '3 a 4 días hábiles', 8800.0, 'pickup_point'),
        ];

        $service = new ZipnovaService();
        $curadas = $service->curarOpcionesDesdeArray($opciones);

        $this->assertCount(2, $curadas);
        $codes = array_column($curadas, 'code');
        $this->assertContains('standard_delivery', $codes);
        $this->assertContains('pickup_point', $codes);
    }

    public function testSigueDeduplicandoMismoCarrierEtaYCode(): void
    {
        $opciones = [
            $this->opt('Correo Argentino', '5 a 6 días hábiles', 17496.0),
            $this->opt('Correo Argentino', '5 a 6 días hábiles', 10380.0),
            $this->opt('Correo Argentino', '5 a 6 días hábiles', 19633.0),
        ];

        $service = new ZipnovaService();
        $curadas = $service->curarOpcionesDesdeArray($opciones);

        $this->assertCount(1, $curadas);
        $this->assertSame(10380.0, $curadas[0]['price']);
    }

    public function testSigueLimitandoAMaxShippingOptions(): void
    {
        $opciones = [];
        for ($i = 0; $i < 5; $i++) {
            $opciones[] = $this->opt('Carrier' . $i, $i . ' días hábiles', 1000.0 + $i);
        }

        $service = new ZipnovaService();
        $curadas = $service->curarOpcionesDesdeArray($opciones);

        $this->assertCount(ZipnovaService::MAX_SHIPPING_OPTIONS, $curadas);
    }
}
