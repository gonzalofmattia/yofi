<?php

declare(strict_types=1);

require_once __DIR__ . '/email.php';

/**
 * Mails transaccionales de pedidos — paleta Yofi, línea de tiempo horizontal.
 *
 * Colores de marca:
 *   Durazno #FAAF7D · Azul grisáceo #96AFC8 · Terracota #E1644B ·
 *   Crema #FAE1C8 · Oliva #7D7D64 · tinta #2C2A27
 * Font: Nunito (Google Fonts), fallback Arial/sans-serif.
 */

const ORDER_EMAIL_PASOS = ['pendiente' => 0, 'confirmado' => 1, 'enviado' => 2, 'entregado' => 3];

/**
 * empresa_config_get_all()/whatsapp_href() viven en src/php/content.php (front público)
 * y también existen — con otra implementación (mysqli) — en admin/include/funciones.php.
 * No hacemos require_once directo de content.php para no chocar con la versión ya
 * cargada por el admin (redeclaración fatal); solo la cargamos si todavía no existe.
 */
function order_email_empresa_config(): array
{
    if (!function_exists('empresa_config_get_all')) {
        $contentFile = __DIR__ . '/content.php';
        if (file_exists($contentFile)) {
            require_once $contentFile;
        }
    }
    if (!function_exists('empresa_config_get_all')) {
        return [];
    }
    try {
        return empresa_config_get_all();
    } catch (Throwable $e) {
        return [];
    }
}

function order_email_whatsapp_href(string $number): string
{
    if (function_exists('whatsapp_href')) {
        return whatsapp_href($number);
    }
    $digits = preg_replace('/\D+/', '', $number) ?? '';

    return $digits === '' ? '' : 'https://wa.me/' . $digits;
}

/**
 * URL absoluta (con dominio) para usar dentro de un mail — a diferencia de
 * app_path()/imgprod_path(), que devuelven rutas relativas al root del sitio
 * y no sirven para que Gmail/Outlook puedan cargar imágenes o armar links.
 */
function order_email_absolute_url(string $path): string
{
    $path = trim($path);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $base = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';

    return $base . '/' . ltrim($path, '/');
}

function order_email_item_image_url(string $imagen): string
{
    $imagen = trim($imagen);
    $base = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
    if ($imagen === '') {
        return $base . '/imgprod/placeholder.jpg';
    }
    if (preg_match('#^https?://#i', $imagen)) {
        return $imagen;
    }
    if (preg_match('#/imgprod/(.+)$#', $imagen, $m)) {
        return $base . '/imgprod/' . $m[1];
    }

    return $base . '/imgprod/' . ltrim($imagen, '/');
}

function order_email_logo_url(): string
{
    return order_email_absolute_url('/assets/img/logo-yofi.png');
}

function order_email_order_url(int $orderId): string
{
    return order_email_absolute_url('/index.php?p=mi-cuenta&tab=pedido&id=' . $orderId);
}

/**
 * Línea de tiempo horizontal (tabla HTML para compatibilidad con Outlook).
 * Terracota = paso actual, azul grisáceo = completado, gris = pendiente.
 */
function order_email_timeline_html(string $estado): string
{
    if ($estado === 'cancelado') {
        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;">'
            . '<tr><td align="center" style="background:#FBE4E1;border-radius:12px;padding:20px;">'
            . '<div style="width:44px;height:44px;line-height:44px;border-radius:50%;background:#E1644B;color:#fff;font-size:22px;margin:0 auto 8px;">✕</div>'
            . '<div style="font-size:14px;font-weight:700;color:#E1644B;">Pedido cancelado</div>'
            . '</td></tr></table>';
    }

    $pasos = [
        'pendiente' => ['📥', 'Recibido'],
        'confirmado' => ['✅', 'Confirmado'],
        'enviado' => ['🚚', 'Enviado'],
        'entregado' => ['🎉', 'Entregado'],
    ];
    $actualIndex = ORDER_EMAIL_PASOS[$estado] ?? 0;

    $celdas = '';
    $i = 0;
    foreach ($pasos as $key => [$emoji, $label]) {
        if ($i < $actualIndex) {
            $color = '#96AFC8';
        } elseif ($i === $actualIndex) {
            $color = '#E1644B';
        } else {
            $color = '#D9D4C7';
        }
        $textColor = $i <= $actualIndex ? $color : '#7D7D64';

        $celdas .= '<td align="center" style="width:25%;padding:10px 4px 0;border-top:3px solid ' . $color . ';">'
            . '<div style="width:32px;height:32px;line-height:32px;border-radius:50%;background:' . $color . ';color:#fff;font-size:15px;margin:-19px auto 6px;">' . $emoji . '</div>'
            . '<div style="font-size:11px;font-weight:700;color:' . $textColor . ';">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</div>'
            . '</td>';
        $i++;
    }

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0 8px;"><tr>' . $celdas . '</tr></table>';
}

function order_email_header_html(string $numeroOrden, string $fecha): string
{
    $site = defined('SITE_NAME') ? SITE_NAME : 'Yofi';
    $logo = order_email_logo_url();

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#FAAF7D;">'
        . '<tr><td style="padding:24px 28px;text-align:center;">'
        . '<img src="' . htmlspecialchars($logo, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($site, ENT_QUOTES, 'UTF-8') . '" height="36" style="height:36px;max-width:180px;">'
        . ($numeroOrden !== '' ? '<div style="margin-top:10px;color:#2C2A27;font-size:13px;font-weight:700;">Pedido #' . htmlspecialchars($numeroOrden, ENT_QUOTES, 'UTF-8') . ($fecha !== '' ? ' · ' . htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') : '') . '</div>' : '')
        . '</td></tr></table>';
}

function order_email_footer_html(): string
{
    $cfg = order_email_empresa_config();
    $whatsapp = trim($cfg['whatsapp'] ?? '');
    $emailContacto = trim($cfg['email_contacto'] ?? '');

    $contacto = [];
    if ($whatsapp !== '') {
        $href = order_email_whatsapp_href($whatsapp);
        $contacto[] = $href !== ''
            ? '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" style="color:#7D7D64;text-decoration:underline;">WhatsApp: ' . htmlspecialchars($whatsapp, ENT_QUOTES, 'UTF-8') . '</a>'
            : htmlspecialchars($whatsapp, ENT_QUOTES, 'UTF-8');
    }
    if ($emailContacto !== '') {
        $contacto[] = '<a href="mailto:' . htmlspecialchars($emailContacto, ENT_QUOTES, 'UTF-8') . '" style="color:#7D7D64;text-decoration:underline;">' . htmlspecialchars($emailContacto, ENT_QUOTES, 'UTF-8') . '</a>';
    }

    $site = defined('SITE_NAME') ? SITE_NAME : 'Yofi';

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f3ef;">'
        . '<tr><td style="padding:20px 28px;text-align:center;font-size:12px;color:#7D7D64;line-height:1.7;">'
        . ($contacto !== [] ? implode(' &nbsp;·&nbsp; ', $contacto) . '<br>' : '')
        . '© ' . date('Y') . ' ' . htmlspecialchars($site, ENT_QUOTES, 'UTF-8') . '. Todos los derechos reservados.<br>'
        . 'Este es un mail automático, por favor no respondas a esta dirección.'
        . '</td></tr></table>';
}

function order_email_cta_html(int $orderId): string
{
    if ($orderId <= 0) {
        return '';
    }
    $url = order_email_order_url($orderId);

    return '<p style="text-align:center;margin:24px 0;">'
        . '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;background:#E1644B;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:700;">Ver mi pedido</a>'
        . '</p>';
}

/**
 * @param array<int, array<string, mixed>> $items
 */
function order_email_items_table_html(array $items): string
{
    if ($items === []) {
        return '';
    }

    $filas = '';
    foreach ($items as $item) {
        $nombre = (string) ($item['nombre'] ?? 'Producto');
        $variante = trim((string) ($item['color_nombre'] ?? '') . ' ' . (string) ($item['talle_nombre'] ?? ''));
        $cantidad = (int) ($item['cantidad'] ?? 1);
        $precioUnitario = (float) ($item['precio_unitario'] ?? 0);
        $imagen = order_email_item_image_url((string) ($item['imagen'] ?? ''));

        $filas .= '<tr>'
            . '<td style="padding:8px 0;width:52px;"><img src="' . htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') . '" width="48" height="48" style="width:48px;height:48px;object-fit:cover;border-radius:8px;display:block;"></td>'
            . '<td style="padding:8px 0 8px 12px;">'
            . '<div style="font-size:14px;color:#2C2A27;font-weight:700;">' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '</div>'
            . ($variante !== '' ? '<div style="font-size:12px;color:#7D7D64;">' . htmlspecialchars($variante, ENT_QUOTES, 'UTF-8') . '</div>' : '')
            . '<div style="font-size:12px;color:#7D7D64;">Cantidad: ' . $cantidad . '</div>'
            . '</td>'
            . '<td style="padding:8px 0;text-align:right;font-size:14px;color:#2C2A27;white-space:nowrap;">$' . number_format($precioUnitario * $cantidad, 0, ',', '.') . '</td>'
            . '</tr>';
    }

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:12px 0;">' . $filas . '</table>';
}

function order_email_totals_html(float $subtotal, float $envio, float $total): string
{
    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #eee;margin-top:4px;font-size:14px;color:#2C2A27;">'
        . '<tr><td style="padding:4px 0;">Subtotal</td><td style="padding:4px 0;text-align:right;">$' . number_format($subtotal, 0, ',', '.') . '</td></tr>'
        . '<tr><td style="padding:4px 0;">Envío</td><td style="padding:4px 0;text-align:right;">$' . number_format($envio, 0, ',', '.') . '</td></tr>'
        . '<tr><td style="padding:6px 0;font-weight:700;">Total</td><td style="padding:6px 0;text-align:right;font-weight:700;">$' . number_format($total, 0, ',', '.') . '</td></tr>'
        . '</table>';
}

/**
 * @param array<string, mixed> $orderData
 */
function order_email_direccion_html(array $orderData): string
{
    $direccion = trim((string) ($orderData['direccion'] ?? ''));
    if ($direccion === '') {
        return '';
    }
    $ciudad = trim((string) ($orderData['ciudad'] ?? ''));
    $provincia = trim((string) ($orderData['provincia'] ?? ''));
    $cp = trim((string) ($orderData['codigo_postal'] ?? ''));
    $linea2 = trim($ciudad . ($provincia !== '' ? ', ' . $provincia : '') . ($cp !== '' ? ' (CP ' . $cp . ')' : ''));

    return '<p style="font-size:14px;color:#2C2A27;margin:16px 0 0;"><strong>Dirección de entrega</strong><br>'
        . htmlspecialchars($direccion, ENT_QUOTES, 'UTF-8')
        . ($linea2 !== '' ? '<br>' . htmlspecialchars($linea2, ENT_QUOTES, 'UTF-8') : '')
        . '</p>';
}

function order_email_transporte_html(string $carrier, string $eta): string
{
    if ($carrier === '' && $eta === '') {
        return '';
    }

    return '<p style="font-size:14px;color:#2C2A27;margin:12px 0 0;"><strong>Transporte</strong><br>'
        . ($carrier !== '' ? htmlspecialchars($carrier, ENT_QUOTES, 'UTF-8') : 'A coordinar')
        . ($eta !== '' ? ' · ' . htmlspecialchars($eta, ENT_QUOTES, 'UTF-8') : '')
        . '</p>';
}

function order_email_tracking_html(string $estado, string $trackingNumber): string
{
    if ($trackingNumber === '' || !in_array($estado, ['enviado', 'entregado'], true)) {
        return '';
    }

    return '<p style="font-size:14px;color:#2C2A27;margin:12px 0 0;"><strong>Número de seguimiento</strong><br>'
        . htmlspecialchars($trackingNumber, ENT_QUOTES, 'UTF-8') . '</p>';
}

/**
 * Envuelve el cuerpo del mail con header + timeline + footer.
 */
function order_email_layout(string $title, string $estado, string $numeroOrden, string $fecha, string $bodyHtml): string
{
    $site = defined('SITE_NAME') ? SITE_NAME : 'Yofi';

    return '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'
        . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title></head>'
        . '<body style="margin:0;padding:0;background:#FAE1C8;font-family:Nunito,Arial,sans-serif;">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;"><tr><td align="center">'
        . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#fff;border-radius:16px;overflow:hidden;">'
        . '<tr><td>' . order_email_header_html($numeroOrden, $fecha) . '</td></tr>'
        . '<tr><td style="padding:8px 28px 4px;">' . order_email_timeline_html($estado) . '</td></tr>'
        . '<tr><td style="padding:12px 28px 28px;">'
        . '<h1 style="margin:0 0 12px;color:#2C2A27;font-size:19px;">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>'
        . $bodyHtml
        . '</td></tr>'
        . '<tr><td>' . order_email_footer_html() . '</td></tr>'
        . '</table>'
        . '<p style="font-size:11px;color:#B8A98D;margin-top:16px;">' . htmlspecialchars($site, ENT_QUOTES, 'UTF-8') . '</p>'
        . '</td></tr></table></body></html>';
}

/**
 * @param array<string, mixed> $orderData claves: numero_orden, nombre, apellido, subtotal, envio, total,
 *   opcionalmente id_orden, direccion, ciudad, provincia, codigo_postal
 * @param array<int, array<string, mixed>> $items
 */
function generateOrderReceivedEmail(array $orderData, array $items, string $metodoPago): string
{
    $numeroOrden = (string) ($orderData['numero_orden'] ?? '');
    $nombre = trim((string) ($orderData['nombre'] ?? '') . ' ' . (string) ($orderData['apellido'] ?? ''));
    $orderId = (int) ($orderData['id_orden'] ?? 0);
    $subtotal = (float) ($orderData['subtotal'] ?? 0);
    $envio = (float) ($orderData['envio'] ?? 0);
    $total = (float) ($orderData['total'] ?? 0);
    $fecha = date('d/m/Y');

    $metodoTexto = $metodoPago === 'transferencia'
        ? 'Transferencia bancaria. En breve nos pondremos en contacto para coordinar el pago.'
        : htmlspecialchars($metodoPago, ENT_QUOTES, 'UTF-8');

    $body = '<p style="font-size:15px;color:#2C2A27;">Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ',</p>'
        . '<p style="font-size:15px;color:#2C2A27;">¡Recibimos tu pedido! Ya lo estamos procesando y te vamos a avisar en cuanto se acredite el pago.</p>'
        . order_email_items_table_html($items)
        . order_email_totals_html($subtotal, $envio, $total)
        . '<p style="font-size:14px;color:#2C2A27;margin-top:16px;"><strong>Método de pago:</strong> ' . $metodoTexto . '</p>'
        . order_email_direccion_html($orderData)
        . order_email_cta_html($orderId);

    return order_email_layout('Recibimos tu pedido', 'pendiente', $numeroOrden, $fecha, $body);
}

/**
 * @param array<string, mixed> $orderData claves: numero_orden, nombre, apellido, total, opcionalmente
 *   id_orden, subtotal, envio, items (array), direccion, ciudad, provincia, codigo_postal,
 *   shipping_carrier, shipping_eta
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
    $orderId = (int) ($orderData['id_orden'] ?? 0);
    $subtotal = (float) ($orderData['subtotal'] ?? 0);
    $envio = (float) ($orderData['envio'] ?? 0);
    $total = (float) ($orderData['total'] ?? 0);
    $items = is_array($orderData['items'] ?? null) ? $orderData['items'] : [];
    $fecha = date('d/m/Y');
    $tracking = trim((string) ($trackingNumber ?? ($orderData['tracking_number'] ?? '')));
    $carrier = trim((string) ($orderData['shipping_carrier'] ?? ''));
    $eta = trim((string) ($orderData['shipping_eta'] ?? ''));

    $titulos = [
        'confirmado' => 'Tu pedido fue confirmado',
        'enviado' => 'Tu pedido fue enviado',
        'entregado' => 'Tu pedido fue entregado',
        'cancelado' => 'Tu pedido fue cancelado',
        'pendiente' => 'Actualización de tu pedido',
    ];
    $mensajes = [
        'confirmado' => '¡Buenas noticias! Confirmamos tu pago y ya estamos preparando tu pedido.',
        'enviado' => 'Tu pedido salió hacia tu domicilio. Te compartimos los datos de seguimiento más abajo.',
        'entregado' => '¡Tu pedido llegó a destino! Esperamos que disfrutes tu compra. Gracias por elegir Yofi.',
        'cancelado' => 'Lamentablemente tu pedido fue cancelado.',
        'pendiente' => 'El estado de tu pedido fue actualizado.',
    ];

    $titulo = $titulos[$estadoNuevo] ?? 'Actualización de pedido';
    $mensaje = $mensajes[$estadoNuevo] ?? 'Hay novedades sobre tu compra.';

    $extra = '';
    if ($estadoNuevo === 'cancelado' && $motivoCancelacion) {
        $extra .= '<p style="color:#E1644B;font-size:14px;"><strong>Motivo:</strong> ' . htmlspecialchars($motivoCancelacion, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    if ($notas) {
        $extra .= '<p style="color:#7D7D64;font-size:13px;">' . htmlspecialchars($notas, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    $body = '<p style="font-size:15px;color:#2C2A27;">Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ',</p>'
        . '<p style="font-size:15px;color:#2C2A27;">' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</p>'
        . $extra
        . order_email_items_table_html($items)
        . ($items !== [] ? order_email_totals_html($subtotal, $envio, $total) : '<p style="font-size:14px;color:#2C2A27;"><strong>Total:</strong> $' . number_format($total, 0, ',', '.') . '</p>')
        . order_email_direccion_html($orderData)
        . order_email_transporte_html($carrier, $eta)
        . order_email_tracking_html($estadoNuevo, $tracking)
        . order_email_cta_html($orderId);

    return order_email_layout($titulo, $estadoNuevo, $numeroOrden, $fecha, $body);
}

/**
 * @param array<string, mixed> $orderData
 * @param array<int, array<string, mixed>> $items
 */
function generateAdminNewOrderEmail(array $orderData, array $items, string $metodoPago, string $orderUrl = ''): string
{
    $numeroOrden = (string) ($orderData['numero_orden'] ?? '');
    $nombre = trim((string) ($orderData['nombre'] ?? '') . ' ' . (string) ($orderData['apellido'] ?? ''));
    $email = (string) ($orderData['email'] ?? '');
    $telefono = (string) ($orderData['telefono'] ?? '');
    $direccion = trim(
        (string) ($orderData['direccion'] ?? '') . ', '
        . (string) ($orderData['ciudad'] ?? '') . ', '
        . (string) ($orderData['provincia'] ?? '') . ' (CP ' . (string) ($orderData['codigo_postal'] ?? '') . ')'
    );
    $total = number_format((float) ($orderData['total'] ?? 0), 0, ',', '.');

    $itemsHtml = '';
    foreach ($items as $item) {
        $nombreItem = (string) ($item['nombre'] ?? 'Producto');
        $variante = trim((string) ($item['color_nombre'] ?? '') . ' ' . (string) ($item['talle_nombre'] ?? ''));
        $cantidad = (int) ($item['cantidad'] ?? 1);

        $itemsHtml .= '<li>' . htmlspecialchars($nombreItem, ENT_QUOTES, 'UTF-8')
            . ($variante !== '' ? ' (' . htmlspecialchars($variante, ENT_QUOTES, 'UTF-8') . ')' : '')
            . ' × ' . $cantidad . '</li>';
    }

    $body = '<p>Entró un pedido nuevo en Yofi.</p>'
        . '<ul style="line-height:1.6;">'
        . '<li><strong>Pedido:</strong> #' . htmlspecialchars($numeroOrden, ENT_QUOTES, 'UTF-8') . '</li>'
        . '<li><strong>Cliente:</strong> ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '</li>'
        . '<li><strong>Email:</strong> ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</li>'
        . '<li><strong>Teléfono:</strong> ' . htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') . '</li>'
        . '<li><strong>Dirección:</strong> ' . htmlspecialchars($direccion, ENT_QUOTES, 'UTF-8') . '</li>'
        . '<li><strong>Método de pago:</strong> ' . htmlspecialchars($metodoPago, ENT_QUOTES, 'UTF-8') . '</li>'
        . '<li><strong>Total:</strong> $' . $total . '</li>'
        . '</ul>'
        . '<p><strong>Productos:</strong></p>'
        . '<ul style="line-height:1.6;">' . $itemsHtml . '</ul>'
        . ($orderUrl !== '' ? '<p><a href="' . htmlspecialchars($orderUrl, ENT_QUOTES, 'UTF-8') . '" style="color:#E1644B;">Ver pedido en el admin</a></p>' : '');

    return order_email_wrap_admin('Nuevo pedido #' . $numeroOrden, $body);
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

    return order_email_wrap_admin('Pago acreditado — ' . $numero, $body);
}

/**
 * Layout simple (sin timeline, sin footer de contacto) para los mails internos
 * dirigidos a Aye — el rediseño con línea de tiempo es solo para el comprador.
 */
function order_email_wrap_admin(string $title, string $bodyHtml): string
{
    $site = defined('SITE_NAME') ? SITE_NAME : 'Yofi';

    return '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"></head><body style="font-family:Nunito,Arial,sans-serif;background:#FAE1C8;padding:24px;">'
        . '<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:28px;">'
        . '<h1 style="color:#2C2A27;font-size:20px;margin:0 0 16px;">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>'
        . $bodyHtml
        . '<p style="color:#7D7D64;font-size:12px;margin-top:24px;">© ' . date('Y') . ' ' . htmlspecialchars($site, ENT_QUOTES, 'UTF-8') . '</p>'
        . '</div></body></html>';
}
