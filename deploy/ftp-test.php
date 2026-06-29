<?php

declare(strict_types=1);

$config = require dirname(__DIR__) . '/config/deploy.local.php';
$ftp = $config['ftp'];

$host = trim((string)$ftp['host']);
$port = (int)($ftp['port'] ?? 21);
$user = trim((string)$ftp['user']);
$pass = (string)$ftp['password'];
$useSsl = (bool)($ftp['ssl'] ?? true);

$mode = $useSsl ? 'FTPS' : 'FTP';
echo "Probando {$mode} a {$host}:{$port}...\n";

if ($useSsl) {
    if (!function_exists('ftp_ssl_connect')) {
        echo "RESULTADO: se necesita ftp_ssl_connect (FTPS). Revisá php.ini.\n";
        exit(1);
    }
    $conn = @ftp_ssl_connect($host, $port, 15);
} else {
    $conn = @ftp_connect($host, $port, 15);
}

if ($conn === false) {
    echo "RESULTADO: no se pudo conectar al host/puerto.\n";
    exit(1);
}

echo "Conexión TCP: OK\n";

if (!@ftp_login($conn, $user, $pass)) {
    echo "RESULTADO: autenticación fallida.\n";
    if (!$useSsl) {
        echo "Tip: DonWeb/Ferozo suele requerir 'ssl' => true en deploy.local.php\n";
    }
    ftp_close($conn);
    exit(1);
}

echo "Login: OK\n";

if (!empty($ftp['passive'])) {
    ftp_pasv($conn, true);
}
if (defined('FTP_USEPASVADDRESS') && function_exists('ftp_set_option')) {
    @ftp_set_option($conn, FTP_USEPASVADDRESS, false);
}

$pwd = @ftp_pwd($conn);
echo "Directorio inicial: " . ($pwd !== false ? $pwd : '(desconocido)') . "\n";

$remotePath = trim((string)($ftp['remote_path'] ?? ''));
if ($remotePath !== '' && @ftp_chdir($conn, $remotePath)) {
    echo "remote_path '{$remotePath}': OK\n";
} elseif ($remotePath !== '') {
    echo "remote_path '{$remotePath}': NO existe o sin permiso\n";
    echo "Listá el directorio raíz en FileZilla y ajustá remote_path en deploy.local.php\n";
}

ftp_close($conn);
echo "RESULTADO: FTP configurado correctamente.\n";
