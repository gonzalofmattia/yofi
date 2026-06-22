<?php
require_once __DIR__ . '/../src/php/content.php';

$page_title = 'Envíos y devoluciones | ' . SITE_NAME;
$meta_description = 'Información sobre envíos a todo el país, plazos de entrega, costos y política de cambios en Yofi.';

$threshold = free_shipping_threshold();
$freeShipBlock = $threshold > 0
    ? '<p>¡Envío gratis en compras superiores a <strong>' . htmlspecialchars(format_money_ars($threshold), ENT_QUOTES, 'UTF-8') . '</strong>! Para montos menores, el costo se cotiza en el checkout según tu código postal.</p>'
    : '<p>El costo de envío se calcula automáticamente en el checkout según tu código postal y la opción elegida.</p>';

$info_page_title = 'Envíos y devoluciones';
$info_page_intro = 'Enviamos a todo el país con seguimiento. Queremos que recibas tu pedido rápido y, si hace falta, cambiarlo sin vueltas.';
$info_page_sections = [
    [
        'title' => 'Zonas de entrega',
        'html' => '<p>Realizamos envíos a todo el territorio argentino a través de Zipnova y operadores logísticos asociados. También podés consultarnos por retiro en local cuando esté disponible.</p>',
    ],
    [
        'title' => 'Costos y envío gratis',
        'html' => $freeShipBlock,
    ],
    [
        'title' => 'Plazos de entrega',
        'html' => '<p>Una vez confirmado el pago, preparamos tu pedido en 24 a 48 hs hábiles. Los plazos de traslado dependen de la zona:</p><ul class="list-disc pl-5 space-y-1"><li><strong>CABA y GBA:</strong> 1 a 3 días hábiles.</li><li><strong>Interior:</strong> 3 a 7 días hábiles.</li></ul><p>Te enviamos el código de seguimiento por email cuando el paquete salga de nuestro depósito.</p>',
    ],
    [
        'title' => 'Cambios',
        'html' => '<p>Podés solicitar un cambio de talle dentro de los 10 días de recibida la compra. La prenda debe estar sin uso, con etiquetas y en condiciones originales. Los costos de envío del cambio pueden variar según el caso; te los confirmamos antes de gestionarlo.</p>',
    ],
    [
        'title' => 'Devoluciones',
        'html' => '<p>Si recibiste un producto defectuoso o incorrecto, escribinos dentro de las 48 hs con fotos del artículo y del packaging. Para arrepentimiento de compra, tenés 10 días desde la recepción para iniciar la devolución, según la normativa vigente.</p>',
    ],
];

include __DIR__ . '/../partials/info-page-shell.php';
