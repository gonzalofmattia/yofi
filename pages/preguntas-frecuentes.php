<?php
require_once __DIR__ . '/../src/php/content.php';

$page_title = 'Preguntas frecuentes | ' . SITE_NAME;
$meta_description = 'Respuestas a las consultas más comunes sobre compras, talles, envíos y pagos en Yofi.';

$freeShipText = free_shipping_threshold() > 0
    ? 'El envío es gratis en compras superiores a ' . format_money_ars(free_shipping_threshold()) . '.'
    : 'Consultá las opciones de envío disponibles al finalizar tu compra.';

$info_page_title = 'Preguntas frecuentes';
$info_page_intro = 'Acá reunimos las dudas más comunes para que compres con tranquilidad en Yofi.';
$info_page_sections = [
    [
        'title' => '¿Cómo compro en Yofi?',
        'html' => '<p>Elegí los productos que te gusten, seleccioná color y talle, agregalos al carrito y seguí los pasos del checkout. Podés pagar con Mercado Pago y completar tus datos de envío en el mismo flujo.</p>',
    ],
    [
        'title' => '¿Cómo elijo el talle correcto?',
        'html' => '<p>Cada prenda tiene una guía de talles en su ficha. Si estás entre dos talles, te recomendamos elegir el más grande para mayor comodidad. También podés consultarnos por WhatsApp con la altura y edad de tu chico.</p>',
    ],
    [
        'title' => '¿Cuánto tarda el envío?',
        'html' => '<p>Los tiempos varían según tu ubicación. En CABA y GBA muchas entregas se realizan en 24 a 72 hs hábiles. Al interior del país, el plazo suele ser de 3 a 7 días hábiles una vez despachado el pedido.</p>',
    ],
    [
        'title' => '¿Hay envío gratis?',
        'html' => '<p>' . htmlspecialchars($freeShipText, ENT_QUOTES, 'UTF-8') . ' Para montos menores, el costo se calcula automáticamente en el checkout según tu código postal.</p>',
    ],
    [
        'title' => '¿Puedo cambiar o devolver un producto?',
        'html' => '<p>Sí, dentro de los 10 días de recibida la compra, siempre que la prenda esté sin uso, con etiquetas y en perfecto estado. Escribinos para coordinar el cambio o la devolución.</p>',
    ],
    [
        'title' => '¿Qué medios de pago aceptan?',
        'html' => '<p>Aceptamos Mercado Pago: tarjetas de crédito y débito, dinero en cuenta y otras opciones disponibles según tu perfil. Las cuotas dependen de la promoción vigente al momento de pagar.</p>',
    ],
];

include __DIR__ . '/../partials/info-page-shell.php';
