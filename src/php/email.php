<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Envío de emails vía PHPMailer + SMTP (DonWeb).
 */
function sendEmail(string $to, string $subject, string $body, bool $isHTML = true, ?string $fromEmail = null, ?string $fromName = null): bool
{
    $phpmailerPath = __DIR__ . '/../../lib/class.phpmailer.php';
    $smtpPath = __DIR__ . '/../../lib/class.smtp.php';

    if (!file_exists($phpmailerPath) || !file_exists($smtpPath)) {
        error_log('PHPMailer no encontrado en lib/');

        return sendEmailNative($to, $subject, $body, $isHTML, $fromEmail, $fromName);
    }

    require_once $phpmailerPath;
    require_once $smtpPath;

    if ($fromEmail === null) {
        $fromEmail = defined('MAIL_FROM') ? MAIL_FROM : 'no-reply@yofi.com.ar';
    }
    if ($fromName === null) {
        $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : (defined('SITE_NAME') ? SITE_NAME : 'Yofi');
    }

    if (!defined('SMTP_HOST') || SMTP_HOST === '' || !defined('SMTP_USER') || SMTP_USER === '') {
        error_log('SMTP no configurado — completá config/smtp.local.php');

        return sendEmailNative($to, $subject, $body, $isHTML, $fromEmail, $fromName);
    }

    try {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Port = defined('SMTP_PORT') ? (int) SMTP_PORT : 587;
        $mail->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : 'tls';
        $mail->IsHTML($isHTML);
        $mail->CharSet = 'utf-8';
        $mail->Host = SMTP_HOST;
        $mail->Username = SMTP_USER;
        $mail->Password = defined('SMTP_PASS') ? SMTP_PASS : '';

        if (defined('ENV') && ENV === 'dev') {
            $mail->SMTPDebug = 0;
        }

        $mail->From = $fromEmail;
        $mail->FromName = $fromName;
        $mail->AddReplyTo($fromEmail, $fromName);
        $mail->AddAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;
        if (!$isHTML) {
            $mail->AltBody = strip_tags($body);
        }

        $result = $mail->Send();
        if (!$result) {
            error_log('PHPMailer error: ' . $mail->ErrorInfo);

            return false;
        }

        return true;
    } catch (Throwable $e) {
        error_log('sendEmail exception: ' . $e->getMessage());

        return sendEmailNative($to, $subject, $body, $isHTML, $fromEmail, $fromName);
    }
}

function sendEmailNative(string $to, string $subject, string $body, bool $isHTML = true, ?string $fromEmail = null, ?string $fromName = null): bool
{
    if ($fromEmail === null) {
        $fromEmail = defined('MAIL_FROM') ? MAIL_FROM : 'no-reply@yofi.com.ar';
    }
    if ($fromName === null) {
        $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Yofi';
    }

    $headers = [
        'MIME-Version: 1.0',
        'From: ' . $fromName . ' <' . $fromEmail . '>',
        'Reply-To: ' . $fromEmail,
        $isHTML ? 'Content-Type: text/html; charset=UTF-8' : 'Content-Type: text/plain; charset=UTF-8',
        'X-Mailer: PHP/' . phpversion(),
    ];

    return @mail($to, $subject, $body, implode("\r\n", $headers));
}

function getEmailTemplate(string $title, string $content, ?string $footerText = null): string
{
    if ($footerText === null) {
        $footerText = '© ' . date('Y') . ' Yofi. Todos los derechos reservados.';
    }
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Yofi';

    return '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>'
        . htmlspecialchars($title, ENT_QUOTES, 'UTF-8')
        . '</title></head><body style="margin:0;padding:0;font-family:Nunito,Arial,sans-serif;background:#FAE1C8;">'
        . '<table width="100%" cellpadding="0" cellspacing="0" style="padding:24px;"><tr><td align="center">'
        . '<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;">'
        . '<tr><td style="background:#FAAF7D;padding:28px;text-align:center;">'
        . '<h1 style="margin:0;color:#2C2A27;font-size:22px;">' . htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') . '</h1></td></tr>'
        . '<tr><td style="padding:28px;"><h2 style="margin:0 0 16px;color:#2C2A27;font-size:18px;">'
        . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2>' . $content . '</td></tr>'
        . '<tr><td style="background:#f6f3ef;padding:16px;text-align:center;font-size:12px;color:#7D7D64;">'
        . htmlspecialchars($footerText, ENT_QUOTES, 'UTF-8') . '</td></tr>'
        . '</table></td></tr></table></body></html>';
}
