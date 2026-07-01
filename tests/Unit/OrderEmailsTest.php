<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/php/order_emails.php';

final class OrderEmailsTest extends TestCase
{
    public function testGenerateOrderReceivedEmailIncludesOrderNumberAndTotal(): void
    {
        $orderData = [
            'numero_orden' => 'ORD-20260701-TEST01',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'subtotal' => 10000,
            'envio' => 5000,
            'total' => 15000,
        ];
        $items = [
            [
                'nombre' => 'Bandana tejida',
                'color_nombre' => 'Chocolate',
                'talle_nombre' => 'Único',
                'precio_unitario' => 5000,
                'cantidad' => 2,
            ],
        ];

        $html = generateOrderReceivedEmail($orderData, $items, 'transferencia');

        $this->assertStringContainsString('ORD-20260701-TEST01', $html);
        $this->assertStringContainsString('Juan', $html);
        $this->assertStringContainsString('Bandana tejida', $html);
        $this->assertStringContainsString('15.000', $html);
        $this->assertStringContainsString('Transferencia bancaria', $html);
    }

    public function testGenerateOrderReceivedEmailEscapesItemNames(): void
    {
        $orderData = ['numero_orden' => 'ORD-1', 'nombre' => 'A', 'apellido' => 'B', 'subtotal' => 0, 'envio' => 0, 'total' => 0];
        $items = [['nombre' => '<script>alert(1)</script>', 'cantidad' => 1, 'precio_unitario' => 0]];

        $html = generateOrderReceivedEmail($orderData, $items, 'mercadopago');

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    /**
     * @dataProvider estadoProvider
     */
    public function testGenerateEstadoChangeEmailUsesExpectedTitlePerEstado(string $estado, string $expectedFragment): void
    {
        $orderData = ['numero_orden' => 'ORD-1', 'nombre' => 'Juan', 'apellido' => 'Pérez', 'total' => 1000];

        $html = generateEstadoChangeEmail($orderData, $estado);

        $this->assertStringContainsString($expectedFragment, $html);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function estadoProvider(): array
    {
        return [
            'confirmado' => ['confirmado', 'Tu pedido fue confirmado'],
            'enviado' => ['enviado', 'Tu pedido fue enviado'],
            'cancelado' => ['cancelado', 'Tu pedido fue cancelado'],
        ];
    }

    public function testGenerateEstadoChangeEmailIncludesCancellationReason(): void
    {
        $orderData = ['numero_orden' => 'ORD-1', 'nombre' => 'Juan', 'apellido' => 'Pérez', 'total' => 1000];

        $html = generateEstadoChangeEmail($orderData, 'cancelado', 'pendiente', null, null, 'Stock insuficiente');

        $this->assertStringContainsString('Stock insuficiente', $html);
    }
}
