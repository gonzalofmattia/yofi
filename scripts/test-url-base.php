<?php

declare(strict_types=1);

$_SERVER['DOCUMENT_ROOT'] = 'C:/laragon/www';
$_SERVER['HTTP_HOST'] = 'decline-purging-hatching.ngrok-free.dev';
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
$_SERVER['SCRIPT_NAME'] = '/yofi/scripts/mp-config-check.php';

define('YOFI_PROJECT_ROOT', dirname(__DIR__));
require_once dirname(__DIR__) . '/src/php/url_helpers.php';

echo 'base=' . yofi_app_base_path() . PHP_EOL;
echo 'site=' . yofi_site_url() . PHP_EOL;
echo 'notify=' . yofi_site_url() . '/webhooks/mp-notification.php' . PHP_EOL;
