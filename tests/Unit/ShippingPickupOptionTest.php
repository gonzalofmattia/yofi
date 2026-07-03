<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/php/shipping.php';

final class ShippingPickupOptionTest extends TestCase
{
    public function testReturnsNullWhenDisabled(): void
    {
        $this->assertNull(shipping_pickup_option(false, 'Retiro en local', 'Av. Siempre Viva 123'));
    }

    public function testReturnsZeroCostOptionWhenEnabled(): void
    {
        $option = shipping_pickup_option(true, 'Retiro en el local', 'Av. Siempre Viva 123');

        $this->assertNotNull($option);
        $this->assertSame(0.0, $option['price']);
        $this->assertSame('pickup', $option['code']);
        $this->assertSame('pickup', $option['logistic_type']);
        $this->assertSame(0, $option['carrier_id']);
        $this->assertSame('Retiro en el local', $option['carrier']);
        $this->assertSame('Av. Siempre Viva 123', $option['eta']);
    }

    public function testFallsBackToDefaultsWhenLabelAndAddressAreEmpty(): void
    {
        $option = shipping_pickup_option(true, '', '');

        $this->assertNotNull($option);
        $this->assertSame('Retiro en local', $option['carrier']);
        $this->assertSame('Retiro en el local', $option['eta']);
    }
}
