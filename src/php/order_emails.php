<?php

declare(strict_types=1);

require_once __DIR__ . '/email.php';

function order_email_wrap(string $title, string $bodyHtml): string
{
    $site = defined('SITE_NAME') ? SITE_NAME : 'Yofi';

    return '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"></head><body style="font-family:Nunito,Arial,sans-serif;background:#FAE1C8;padding:24px;">'
        . '<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:28px;">'
        . '<h1 style="color:#2C2A27;font-size:20px;margin:0 0 16px;">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>'
        . $bodyHtml
        . '<p style="color:#7D7D64;font-size:12px;margin-top:24px;">© ' . date('Y') . ' ' . htmlspecialchars($site, ENT_QUOTES, 'UTF-8') . '</p>'
        . '</div></body></html>';
}

/**
 * @param array<string, mixed> $orderData
 */
function generateEstadoChangeEmail(
    array $orderData,
    string $estadoNuevo,
    ?string $estadoAnterior = null,
    ?string $notas = null,
    ?string $trackingNumber = null,
    ?string $motivoCancelacion = null
): string {
    $numeroOrden = (string) ($orderData['numero_orden'] ?? '');
    $nombre = trim((string) ($orderData['nombre'] ?? '') . ' ' . (string) ($orderData['apellido'] ?? ''));
    $total = number_format((float) ($orderData['total'] ?? 0), 0, ',', '.');

    $titulos = [
        'confirmado' => 'Tu pedido fue confirmado',
        'en_preparacion' => 'Tu pedido está en preparación',
        'enviado' => 'Tu pedido fue enviado',
        'entregado' => 'Tu pedido fue entregado',
        'cancelado' => 'Tu pedido fue cancelado',
        'pendiente' => 'Actualización de tu pedido',
    ];
    $mensajes = [
        'confirmado' => 'Recibimos tu pago y ya estamos preparando tu pedido.',
        'en_preparacion' => 'Estamos empaquetando tus productos.',
        'enviado' => 'Tu pedido ya salió hacia tu domicilio.',
        'entregado' => '¡Gracias por comprar en Yofi!',
        'cancelado' => 'Lamentablemente tu pedido fue cancelado.',
        'pendiente' => 'El estado de tu pedido fue actualizado.',
    ];

    $titulo = $titulos[$estadoNuevo] ?? 'Actualización de pedido';
    $mensaje = $mensajes[$estadoNuevo] ?? 'Hay novedades sobre tu compra.';

    $extra = '';
    if ($estadoNuevo === 'cancelado' && $motivoCancelacion) {
        $extra .= '<p style="color:#E1644B;"><strong>Motivo:</strong> ' . htmlspecialchars($motivoCancelacion, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    if ($trackingNumber) {
        $extra .= '<p><strong>Seguimiento:</strong> ' . htmlspecialchars($trackingNumber, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    if ($notas) {
        $extra .= '<p style="color:#7D7D64;font-size:14px;">' . htmlspecialchars($notas, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    $body = '<p>Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ',</p>'
        . '<p>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p><strong>Pedido:</strong> #' . htmlspecialchars($numeroOrden, ENT_QUOTES, 'UTF-8') . '<br>'
        . '<strong>Total:</strong> $' . $total . '</p>'
        . $extra;

    return order_email_wrap($titulo, $body);
}

/**
 * @param array<string, mixed> $orderRow
 */
function generateAdminPaymentApprovedEmail(
    array $orderRow,
    string $paymentId,
    string $mpStatus,
    string $statusDetail = ''
): string {
    $numero = (string) ($orderRow['numero_orden'] ?? '');
    $cliente = trim((string) ($orderRow['nombre'] ?? '') . ' ' . (string) ($orderRow['apellido'] ?? ''));
    $email = (string) ($orderRow['email'] ?? '');
    $total = number_format((float) ($orderRow['total'] ?? 0), 0, ',', '.');

    $body = '<p>Se acreditó un pago de Mercado Pago.</p>'
        . '<ul style="line-height:1.6;">'
        . '<li><strong>Pedido:</strong> #' . htmlspecialchars($numero, ENT_QUOTES, 'UTF-8') . '</li>'
        . '<li><strong>Cliente:</strong> ' . htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8') . ' (' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . ')</li>'
        . '<li><strong>Total:</strong> $' . $total . '</li>'
        . '<li><strong>Payment ID:</strong> ' . htmlspecialchars($paymentId, ENT_QUOTES, 'UTF-8') . '</li>'
        . '<li><strong>Estado MP:</strong> ' . htmlspecialchars($mpStatus, ENT_QUOTES, 'UTF-8') . '</li>'
        . ($statusDetail !== '' ? '<li><strong>Detalle:</strong> ' . htmlspecialchars($statusDetail, ENT_QUOTES, 'UTF-8') . '</li>' : '')
        . '</ul>';

    return order_email_wrap('Pago acreditado — ' . $numero, $body);
}
