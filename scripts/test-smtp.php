<?php

declare(strict_types=1);

/**
 * Prueba de envío SMTP (sin credenciales en el repo).
 * Ejecutar desde Laragon terminal:
 *   php scripts/test-smtp.php tu@email.com
 *
 * Requiere config/smtp.local.php completado.
 */

if ($argc < 2) {
    fwrite(STDERR, "Uso: php scripts/test-smtp.php destino@email.com\n");
    exit(1);
}

$to = trim($argv[1]);
if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Email destino inválido.\n");
    exit(1);
}

$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/php/email.php';
require_once __DIR__ . '/../src/php/password_setup_email.php';

if (!defined('SMTP_HOST') || SMTP_HOST === '') {
    fwrite(STDERR, "SMTP_HOST vacío. Creá config/smtp.local.php desde el .example\n");
    exit(1);
}

echo "SMTP host: " . SMTP_HOST . "\n";
echo "SMTP port: " . (defined('SMTP_PORT') ? SMTP_PORT : 587) . "\n";
echo "SMTP user: " . (defined('SMTP_USER') ? SMTP_USER : '(sin definir)') . "\n";
echo "From: " . (defined('MAIL_FROM') ? MAIL_FROM : '') . "\n";
echo "Enviando prueba a: {$to}\n";

$body = generatePasswordSetupEmail([
    'nombre' => 'Prueba SMTP Yofi',
    'email' => $to,
    'magic_link' => (defined('SITE_URL') ? SITE_URL : 'http://localhost/yofi') . '/index.php?p=crear-password&token=test',
    'flow' => 'reset',
]);

$ok = sendEmail($to, 'Prueba SMTP — Yofi (recuperación de contraseña)', $body, true);

if ($ok) {
    echo "[OK] Email enviado. Revisá bandeja de entrada y spam.\n";
    exit(0);
}

echo "[FAIL] No se pudo enviar. Revisá error_log.txt (buscá PHPMailer).\n";
exit(1);
