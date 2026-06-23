<?php

declare(strict_types=1);

/**
 * Aplica migrate-checkout-stock-reserva.sql de forma idempotente.
 * Ejecutar: php scripts/run-migrate-checkout-stock-reserva.php
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

if (!migrate_column_exists($pdo, 'tbl_skus', 'stock_reservado')) {
    $pdo->exec('ALTER TABLE `tbl_skus` ADD COLUMN `stock_reservado` int NOT NULL DEFAULT 0 AFTER `stock`');
    echo "OK tbl_skus.stock_reservado\n";
} else {
    echo "SKIP tbl_skus.stock_reservado\n";
}

if (!migrate_column_exists($pdo, 'tbl_ordenes', 'reserva_expira_at')) {
    $pdo->exec('ALTER TABLE `tbl_ordenes` ADD COLUMN `reserva_expira_at` datetime DEFAULT NULL AFTER `fecha_actualizacion`');
    echo "OK tbl_ordenes.reserva_expira_at\n";
} else {
    echo "SKIP tbl_ordenes.reserva_expira_at\n";
}

if (!migrate_column_exists($pdo, 'tbl_ordenes', 'reserva_activa')) {
    $pdo->exec("ALTER TABLE `tbl_ordenes` ADD COLUMN `reserva_activa` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=stock reservado pendiente de pago' AFTER `reserva_expira_at`");
    echo "OK tbl_ordenes.reserva_activa\n";
} else {
    echo "SKIP tbl_ordenes.reserva_activa\n";
}

$pdo->exec("ALTER TABLE `tbl_stock_log`
    MODIFY COLUMN `motivo` enum(
        'venta',
        'ajuste_manual',
        'carga_inicial',
        'carga_csv',
        'devolucion',
        'reserva',
        'liberacion_reserva'
    ) NOT NULL");
echo "OK tbl_stock_log.motivo enum\n";

try {
    $pdo->exec('CREATE INDEX idx_ordenes_reserva_expira ON tbl_ordenes (estado, reserva_activa, reserva_expira_at)');
    echo "OK idx_ordenes_reserva_expira\n";
} catch (Throwable $e) {
    echo "SKIP idx_ordenes_reserva_expira (" . $e->getMessage() . ")\n";
}

echo "Migración checkout stock reserva completada.\n";
