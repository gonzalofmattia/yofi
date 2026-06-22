<?php
if (!function_exists('detectEnvironment')) {
    function detectEnvironment(): string
    {
        $hostname = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
        $serverName = isset($_SERVER['SERVER_NAME']) ? strtolower($_SERVER['SERVER_NAME']) : '';
        $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        $isDemo = (
            strpos($scriptName, '/demo/') !== false ||
            strpos($requestUri, '/demo/') !== false ||
            strpos($hostname, 'demo') !== false
        );

        $isLocal = (
            $hostname === 'localhost' ||
            $hostname === '127.0.0.1' ||
            strpos($hostname, '.local') !== false ||
            strpos($hostname, '.test') !== false ||
            strpos($hostname, 'localhost:') !== false ||
            strpos($serverName, 'localhost') !== false ||
            strpos($serverName, '127.0.0.1') !== false
        );

        if ($isLocal) {
            return 'local';
        }
        if ($isDemo) {
            return 'demo';
        }

        return 'production';
    }
}

if (!function_exists('admin_define')) {
    function admin_define(string $name, mixed $value): void
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }
}

$ENVIRONMENT = detectEnvironment();

if ($ENVIRONMENT === 'local') {
    admin_define('DB_HOST', 'localhost');
    admin_define('DB_USER', 'root');
    admin_define('DB_PASSWORD', '');
    admin_define('DB_DATABASE', 'yofi');
    admin_define('SITE_URL', 'http://localhost/yofi');
    admin_define('IS_LOCAL', true);
    admin_define('IS_PRODUCTION', false);
    admin_define('IS_DEMO', false);
} elseif ($ENVIRONMENT === 'demo') {
    admin_define('DB_HOST', 'localhost');
    admin_define('DB_USER', 'root');
    admin_define('DB_PASSWORD', '');
    admin_define('DB_DATABASE', 'yofi');
    admin_define('SITE_URL', 'http://localhost/yofi/demo');
    admin_define('IS_LOCAL', false);
    admin_define('IS_PRODUCTION', false);
    admin_define('IS_DEMO', true);
} else {
    admin_define('DB_HOST', 'localhost');
    admin_define('DB_USER', 'root');
    admin_define('DB_PASSWORD', '');
    admin_define('DB_DATABASE', 'yofi');
    admin_define('SITE_URL', 'https://yofi.com.ar');
    admin_define('IS_LOCAL', false);
    admin_define('IS_PRODUCTION', true);
    admin_define('IS_DEMO', false);
}

admin_define('SITE_NAME', 'Yofi');
admin_define('TBL_ADMIN', 'tbl_admin');
admin_define('TBL_USUARIOS', 'tbl_usuarios');
admin_define('TBL_PRODUCTOS', 'tbl_productos');
admin_define('TBL_CATEGORIAS', 'tbl_categorias');
admin_define('TBL_ORDENES', 'tbl_ordenes');
admin_define('CHARSET', 'utf8mb4');
admin_define('TIMEZONE', 'America/Argentina/Buenos_Aires');

date_default_timezone_set(TIMEZONE);

$conexion = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if ($conexion->connect_error) {
    die('Error de conexión: ' . $conexion->connect_error);
}
$conexion->set_charset(CHARSET);
