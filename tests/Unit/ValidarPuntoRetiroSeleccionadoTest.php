<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/php/shipping.php';

final class ValidarPuntoRetiroSeleccionadoTest extends TestCase
{
    public function testDevuelveNullSiNoEsPickupPoint(): void
    {
        $this->assertNull(validar_punto_retiro_seleccionado('standard_delivery', null));
        $this->assertNull(validar_punto_retiro_seleccionado('pickup', ['pickup_point' => null]));
    }

    public function testDevuelveErrorSiFaltaShippingMeta(): void
    {
        $this->assertNotNull(validar_punto_retiro_seleccionado('pickup_point', null));
        $this->assertNotNull(validar_punto_retiro_seleccionado('pickup_point', []));
    }

    public function testDevuelveErrorSiPointIdInvalidoODescripcionVacia(): void
    {
        $this->assertNotNull(validar_punto_retiro_seleccionado('pickup_point', [
            'pickup_point' => ['point_id' => 0, 'description' => 'OCA Locker A'],
        ]));
        $this->assertNotNull(validar_punto_retiro_seleccionado('pickup_point', [
            'pickup_point' => ['point_id' => 111, 'description' => ''],
        ]));
    }

    public function testDevuelveNullSiPuntoValido(): void
    {
        $this->assertNull(validar_punto_retiro_seleccionado('pickup_point', [
            'pickup_point' => ['point_id' => 111, 'description' => 'OCA Locker A'],
        ]));
    }
}
