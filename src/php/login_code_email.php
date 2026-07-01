<?php

declare(strict_types=1);

require_once __DIR__ . '/email.php';

function generateLoginCodeEmail(string $nombre, string $code): string
{
    $content = '
    <p style="color:#2C2A27;font-size:15px;line-height:1.6;">Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ',</p>
    <p style="color:#2C2A27;font-size:15px;line-height:1.6;">Usá este código para iniciar sesión en Yofi:</p>
    <p style="text-align:center;margin:28px 0;">
        <span style="display:inline-block;background:#FAE1C8;color:#2C2A27;padding:14px 28px;border-radius:12px;font-weight:bold;font-size:28px;letter-spacing:8px;">'
        . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</span>
    </p>
    <p style="background:#FAE1C8;border-left:4px solid #FAAF7D;padding:12px;color:#2C2A27;font-size:13px;">
        <strong>Importante:</strong> este código vence en 10 minutos y solo se puede usar una vez.
    </p>
    <p style="color:#7D7D64;font-size:13px;">Si no pediste este código, podés ignorar este email.</p>';

    return getEmailTemplate('Tu código de acceso', $content);
}
