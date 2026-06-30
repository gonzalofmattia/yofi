<?php

declare(strict_types=1);

/**
 * Aplica migrate-slider-imagen-mobile.sql de forma idempotente.
 * Ejecutar: php scripts/run-migrate-slider-imagen-mobile.php
 */

if (PHP_SAPI === 'cli') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
    $_SERVER['HTTP_HOST'] = 'localhost';
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/php/db.php';

function migrate_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->query('SHOW COLUMNS FROM `' . str_replace('`', '', $table) . '` LIKE ' . $pdo->quote($column));

    return $stmt !== false && (bool) $stmt->fetch();
}

$pdo = db_rw();

if (!migrate_column_exists($pdo, 'tbl_slider', 'imagen_mobile')) {
    $pdo->exec(
        'ALTER TABLE `tbl_slider` ADD COLUMN `imagen_mobile` varchar(250) DEFAULT NULL AFTER `imagen`'
    );
    echo "OK tbl_slider.imagen_mobile\n";
} else {
    echo "SKIP tbl_slider.imagen_mobile (ya existe)\n";
}

echo "\nDESCRIBE tbl_slider:\n";
foreach ($pdo->query('DESCRIBE tbl_slider') as $row) {
    printf(
        "%-20s %-20s %-5s %-8s %-20s %s\n",
        $row['Field'],
        $row['Type'],
        $row['Null'],
        $row['Key'] ?: '',
        $row['Default'] ?? 'NULL',
        $row['Extra']
    );
}

echo "\nMigración slider imagen mobile completada.\n";
