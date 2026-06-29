<?php

declare(strict_types=1);

/**
 * Descarga error_log.txt del servidor vía FTPS.
 * Uso: php deploy/fetch-error-log.php
 */

$config = require dirname(__DIR__) . '/config/deploy.local.php';
$ftp = $config['ftp'];
$host = trim((string)$ftp['host']);
$user = trim((string)$ftp['user']);
$pass = (string)$ftp['password'];
$base = trim(str_replace('\\', '/', $ftp['remote_path'] ?? 'public_html'), '/');

$conn = ftp_ssl_connect($host, (int)($ftp['port'] ?? 21), 30);
if (!$conn || !ftp_login($conn, $user, $pass)) {
    fwrite(STDERR, "Error FTP\n");
    exit(1);
}
ftp_pasv($conn, true);
if (defined('FTP_USEPASVADDRESS')) {
    @ftp_set_option($conn, FTP_USEPASVADDRESS, false);
}

@ftp_chdir($conn, '/');
if ($base !== '') {
    ftp_chdir($conn, $base);
}

$local = dirname(__DIR__) . '/deploy/remote-error_log.txt';
$files = ['error_log.txt', 'error_log', '../logs/error_log.txt'];

$downloaded = false;
foreach ($files as $remote) {
    if (@ftp_get($conn, $local, $remote, FTP_BINARY)) {
        echo "Descargado: {$remote}\n";
        echo "Guardado en: deploy/remote-error_log.txt\n\n";
        echo file_get_contents($local);
        $downloaded = true;
        break;
    }
}

if (!$downloaded) {
    echo "No se encontró error_log en el servidor.\n";
    echo "Listando archivos en /{$base}/:\n";
    $list = @ftp_nlist($conn, '.');
    if (is_array($list)) {
        foreach ($list as $item) {
            if (stripos($item, 'error') !== false || stripos($item, 'log') !== false) {
                echo "  - {$item}\n";
            }
        }
    }
}

@ftp_close($conn);
