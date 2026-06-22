<?php
require_once __DIR__ . '/../src/php/content.php';

$page_title = 'Contacto | ' . SITE_NAME;
$meta_description = 'Contactá a Yofi por WhatsApp, email o teléfono. Estamos para ayudarte con tu compra.';

$empresa = empresa_config_get_all();
$whatsapp = trim($empresa['whatsapp'] ?? '');
$email = trim($empresa['email_contacto'] ?? '');
$telefono = trim($empresa['telefono'] ?? '');
$direccion = trim($empresa['direccion'] ?? '');
$horario = trim($empresa['horario_atencion'] ?? '');
$waHref = whatsapp_href($whatsapp);

$contactHtml = '<ul class="space-y-3">';
if ($waHref !== '') {
    $contactHtml .= '<li><strong>WhatsApp:</strong> <a class="text-accent underline" href="' . htmlspecialchars($waHref, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener">' . htmlspecialchars($whatsapp, ENT_QUOTES, 'UTF-8') . '</a></li>';
}
if ($email !== '') {
    $contactHtml .= '<li><strong>Email:</strong> <a class="text-accent underline" href="mailto:' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</a></li>';
}
if ($telefono !== '') {
    $telHref = 'tel:' . preg_replace('/[^\d+]/', '', $telefono);
    $contactHtml .= '<li><strong>Teléfono:</strong> <a class="text-accent underline" href="' . htmlspecialchars($telHref, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') . '</a></li>';
}
if ($direccion !== '') {
    $contactHtml .= '<li><strong>Dirección:</strong> ' . htmlspecialchars($direccion, ENT_QUOTES, 'UTF-8') . '</li>';
}
if ($horario !== '') {
    $contactHtml .= '<li><strong>Horario:</strong> ' . nl2br(htmlspecialchars($horario, ENT_QUOTES, 'UTF-8')) . '</li>';
}
if ($contactHtml === '<ul class="space-y-3">') {
    $contactHtml .= '<li>Todavía no cargamos datos de contacto. Escribinos desde el formulario de la tienda o volvé pronto.</li>';
}
$contactHtml .= '</ul>';

$info_page_title = 'Contacto';
$info_page_intro = 'Estamos para ayudarte con talles, envíos, cambios o cualquier duda sobre tu pedido.';
$info_page_sections = [
    [
        'title' => 'Datos de contacto',
        'html' => $contactHtml,
    ],
    [
        'title' => 'Tiempo de respuesta',
        'html' => '<p>Respondemos consultas por WhatsApp y email de lunes a viernes en horario comercial. Los fines de semana y feriados puede demorar un poco más, pero siempre te respondemos.</p>',
    ],
    [
        'title' => 'Antes de escribirnos',
        'html' => '<p>Si tu consulta es sobre envíos, cambios o talles, revisá nuestras <a class="text-accent underline" href="' . htmlspecialchars(page_path('envios-devoluciones'), ENT_QUOTES, 'UTF-8') . '">políticas de envío</a> y la <a class="text-accent underline" href="' . htmlspecialchars(page_path('guia-talles'), ENT_QUOTES, 'UTF-8') . '">guía de talles</a>. Muchas veces la respuesta ya está ahí.</p>',
    ],
];

include __DIR__ . '/../partials/info-page-shell.php';
