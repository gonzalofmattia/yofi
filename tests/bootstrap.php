<?php

declare(strict_types=1);

$_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ?? 'C:\\laragon\\www\\yofi';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/php/db.php';
