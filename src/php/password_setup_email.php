<?php

declare(strict_types=1);

require_once __DIR__ . '/email.php';

function generatePasswordSetupEmail(array $data): string
{
    $nombre = $data['nombre'] ?? 'Cliente';
    $magicLink = $data['magic_link'] ?? '';
    $flow = $data['flow'] ?? 'create';
    $isReset = $flow === 'reset';

    $headline = $isReset ? 'Restablecé tu contraseña' : 'Creá tu contraseña';
    $ctaText = $isReset ? 'Restablecer contraseña' : 'Crear contraseña';
    $intro = $isReset
        ? 'Recibimos una solicitud para restablecer la contraseña de tu cuenta en Yofi.'
        : 'Creá tu contraseña para acceder a tu cuenta Yofi.';
    $ignoreText = $isReset
        ? 'Si no solicitaste restablecer tu contraseña, podés ignorar este email.'
        : 'Si no solicitaste este enlace, podés ignorar este email.';

    $content = '
    <p style="color:#2C2A27;font-size:15px;line-height:1.6;">Hola ' . htmlspecialchars((string) $nombre, ENT_QUOTES, 'UTF-8') . ',</p>
    <p style="color:#2C2A27;font-size:15px;line-height:1.6;">' . htmlspecialchars($intro, ENT_QUOTES, 'UTF-8') . '</p>
    <p style="text-align:center;margin:28px 0;">
        <a href="' . htmlspecialchars($magicLink, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;background:#E1644B;color:#fff;padding:14px 28px;text-decoration:none;border-radius:999px;font-weight:bold;">'
        . htmlspecialchars($ctaText, ENT_QUOTES, 'UTF-8') . '</a>
    </p>
    <p style="color:#7D7D64;font-size:13px;line-height:1.5;">O copiá este enlace:<br>
    <a href="' . htmlspecialchars($magicLink, ENT_QUOTES, 'UTF-8') . '" style="color:#E1644B;word-break:break-all;">' . htmlspecialchars($magicLink, ENT_QUOTES, 'UTF-8') . '</a></p>
    <p style="background:#FAE1C8;border-left:4px solid #FAAF7D;padding:12px;color:#2C2A27;font-size:13px;">
        <strong>Importante:</strong> este enlace es válido por 1 hora y solo se puede usar una vez.
    </p>
    <p style="color:#7D7D64;font-size:13px;">' . htmlspecialchars($ignoreText, ENT_QUOTES, 'UTF-8') . '</p>';

    return getEmailTemplate($headline, $content);
}
