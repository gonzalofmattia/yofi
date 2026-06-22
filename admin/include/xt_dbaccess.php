<?php
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASSWORD') || !defined('DB_DATABASE')) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_DATABASE', 'yofi');
}

$host = DB_HOST;
$user = DB_USER;
$password = DB_PASSWORD;
$db = DB_DATABASE;

$con = new mysqli($host, $user, $password, $db);
if ($con->connect_error) {
    die('Error de conexión: ' . $con->connect_error);
}
$con->set_charset(CHARSET);
