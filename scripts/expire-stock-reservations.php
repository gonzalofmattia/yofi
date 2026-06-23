#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Libera reservas de stock vencidas (cron cada 5–10 min).
 * Ejecutar: php scripts/expire-stock-reservations.php
 */

if (PHP_SAPI === 'cli') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
    $_SERVER['HTTP_HOST'] = 'localhost';
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/php/db.php';
require_once __DIR__ . '/../src/php/stock_reservation.php';

$pdo = db_rw();
$released = stock_expire_pending_reservations($pdo);

echo date('Y-m-d H:i:s') . " — reservas expiradas liberadas: {$released}\n";
