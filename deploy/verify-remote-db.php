<?php

declare(strict_types=1);

$config = require dirname(__DIR__) . '/config/deploy.local.php';
$f = $config['ftp'];
$conn = ftp_ssl_connect(trim($f['host']), 21, 30);
ftp_login($conn, trim($f['user']), $f['password']);
ftp_pasv($conn, true);
if (defined('FTP_USEPASVADDRESS')) {
    @ftp_set_option($conn, FTP_USEPASVADDRESS, false);
}
ftp_chdir($conn, '/');
ftp_chdir($conn, trim($f['remote_path']));
ftp_chdir($conn, 'config');

$local = dirname(__DIR__) . '/deploy/remote-db.local.php';
if (!@ftp_get($conn, $local, 'db.local.php', FTP_BINARY)) {
    echo "No se pudo descargar config/db.local.php del servidor\n";
    exit(1);
}
@ftp_close($conn);

$remote = file_get_contents($local);
$localProd = file_get_contents(dirname(__DIR__) . '/config/db.production.php');

echo "Remoto config/db.local.php: " . filesize($local) . " bytes\n";
echo "Local db.production.php:    " . strlen($localProd) . " bytes\n";
echo "¿Contenido idéntico? " . (trim($remote) === trim($localProd) ? 'SÍ' : 'NO') . "\n\n";

foreach (['DB_HOST', 'DB_USER', 'DB_DATABASE'] as $const) {
    if (preg_match("/define\('{$const}',\s*'([^']*)'\)/", $remote, $m)) {
        echo "{$const} (remoto): {$m[1]}\n";
    }
}
if (preg_match("/define\('DB_PASSWORD',\s*'([^']*)'\)/", $remote, $m)) {
    $p = $m[1];
    echo 'DB_PASSWORD (remoto): longitud ' . strlen($p) . ", empieza con '{$p[0]}'\n";
}
if (preg_match("/define\('DB_PASSWORD',\s*'([^']*)'\)/", $localProd, $m)) {
    $p = $m[1];
    echo 'DB_PASSWORD (local):  longitud ' . strlen($p) . ", empieza con '{$p[0]}'\n";
}
