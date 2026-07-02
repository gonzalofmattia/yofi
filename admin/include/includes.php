<?php
if (!ob_get_level()) {
    ob_start();
}

// Config admin primero (DB, SITE_URL fijo para /yofi)
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

// Solo dependencias del proyecto que el admin necesita (sin config.php raíz: evita BASE_PATH/SITE_URL duplicados)
$projectRoot = dirname(__DIR__, 2);
$zipnovaConfig = $projectRoot . '/config/zipnova.php';
if (file_exists($zipnovaConfig)) {
    require_once $zipnovaConfig;
}
$mercadopagoConfig = $projectRoot . '/config/mercadopago.php';
if (file_exists($mercadopagoConfig)) {
    require_once $mercadopagoConfig;
}
$appConfig = $projectRoot . '/config/app.php';
if (file_exists($appConfig)) {
    require_once $appConfig;
}
$smtpConfig = $projectRoot . '/config/smtp.php';
if (file_exists($smtpConfig)) {
    require_once $smtpConfig;
}

if (defined('IS_LOCAL') && IS_LOCAL) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'Off');
    ini_set('log_errors', 'On');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

if (file_exists(__DIR__ . '/session_init.php')) {
    require_once __DIR__ . '/session_init.php';
}

if (!isset($_GET['modificado'])) {
    $_GET['modificado'] = '';
}
if (!isset($_POST['palabra'])) {
    $_POST['palabra'] = '';
}

$palabra = '';
$id_subcate = '';
$id_cate = '';
$borrado = '';
$agregada = '';
$exite = '';

require_once __DIR__ . '/xt_dbaccess.php';
require_once __DIR__ . '/xt_variables.php';
require_once __DIR__ . '/xt_httpvars.php';

$HttpVars = new varFunctions();
$HttpVars->Inicializar($_POST, $_GET, $_SESSION);

require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/auth_redirect.php';
