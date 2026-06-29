<?php

declare(strict_types=1);

$config = require dirname(__DIR__) . '/config/deploy.local.php';
$f = $config['ftp'];
$root = dirname(__DIR__);

$conn = ftp_ssl_connect(trim($f['host']), 21, 30);
if (!$conn || !ftp_login($conn, trim($f['user']), $f['password'])) {
    fwrite(STDERR, "Error FTP\n");
    exit(1);
}
ftp_pasv($conn, true);
if (defined('FTP_USEPASVADDRESS')) {
    @ftp_set_option($conn, FTP_USEPASVADDRESS, false);
}
ftp_chdir($conn, '/');
ftp_chdir($conn, trim($f['remote_path']));

if (!ftp_put($conn, 'db-probe.php', $root . '/db-probe.php', FTP_BINARY)) {
    fwrite(STDERR, "No se pudo subir db-probe.php\n");
    exit(1);
}
echo "db-probe.php subido.\n";

$url = 'https://yofi.com.ar/db-probe.php?token=yofi-db-test';
echo "Ejecutá en el navegador (o esperá descarga FTP):\n{$url}\n\n";

// Intentar HTTP
$body = @file_get_contents($url, false, stream_context_create([
    'http' => ['timeout' => 30],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
]));

$localResult = dirname(__DIR__) . '/deploy/db-probe-result.txt';
if (@ftp_get($conn, $localResult, 'db-probe-result.txt', FTP_BINARY)) {
    echo "=== Resultado (db-probe-result.txt) ===\n\n";
    echo file_get_contents($localResult);
} elseif ($body !== false && !str_contains($body, '<html')) {
    echo "=== Resultado (HTTP) ===\n\n";
    echo $body;
} else {
    echo "Abrí la URL en el navegador y pegá el resultado, o corré de nuevo este script.\n";
    if ($body !== false && str_contains($body, '<html')) {
        echo "(HTTP devolvió HTML — probá en el navegador)\n";
    }
}

@ftp_close($conn);
