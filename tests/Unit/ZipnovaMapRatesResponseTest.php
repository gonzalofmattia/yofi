<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/php/shipping.php';

final class ZipnovaMapRatesResponseTest extends TestCase
{
    private function rate(string $code, string $carrierName, array $overrides = []): array
    {
        return array_merge([
            'selectable' => true,
            'carrier' => ['id' => 208, 'name' => $carrierName],
            'service_type' => ['code' => $code, 'name' => $code === 'pickup_point' ? 'Entrega en punto de entrega' : 'Entrega a domicilio'],
            'logistic_type' => 'xd_dropoff',
            'amounts' => ['price_incl_tax' => 9500.0],
            'delivery_time' => ['min' => 3, 'max' => 4],
        ], $overrides);
    }

    public function testExponePickupPointsCuandoCodeEsPickupPoint(): void
    {
        $rate = $this->rate('pickup_point', 'OCA', [
            'pickup_points' => [
                ['point_id' => 111, 'description' => 'OCA Locker A', 'location' => ['street' => 'Rivadavia', 'street_number' => '100', 'city' => 'CABA', 'state' => 'Capital Federal', 'zipcode' => '1000']],
                ['point_id' => 222, 'description' => 'OCA Locker B', 'location' => ['street' => 'Gaona', 'street_number' => '200', 'city' => 'CABA', 'state' => 'Capital Federal', 'zipcode' => '1001']],
            ],
        ]);

        $service = new ZipnovaService();
        $opciones = $service->mapRatesFromResponse(['all_results' => [$rate]]);

        $this->assertCount(1, $opciones);
        $this->assertSame('pickup_point', $opciones[0]['code']);
        $this->assertArrayHasKey('pickup_points', $opciones[0]);
        $this->assertCount(2, $opciones[0]['pickup_points']);
        $this->assertSame(111, $opciones[0]['pickup_points'][0]['point_id']);
        $this->assertSame('OCA Locker A', $opciones[0]['pickup_points'][0]['description']);
        $this->assertStringContainsString('Rivadavia', $opciones[0]['pickup_points'][0]['address']);
    }

    public function testNoAgregaPickupPointsAOpcionStandard(): void
    {
        $rate = $this->rate('standard_delivery', 'Correo Argentino');

        $service = new ZipnovaService();
        $opciones = $service->mapRatesFromResponse(['all_results' => [$rate]]);

        $this->assertCount(1, $opciones);
        $this->assertArrayNotHasKey('pickup_points', $opciones[0]);
    }

    public function testDescartaOpcionPickupPointSinPuntos(): void
    {
        $rate = $this->rate('pickup_point', 'Correo Argentino', ['pickup_points' => []]);

        $service = new ZipnovaService();
        $opciones = $service->mapRatesFromResponse(['all_results' => [$rate]]);

        $this->assertSame([], $opciones);
    }
}
