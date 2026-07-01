<?php

declare(strict_types=1);

/**
 * Ejecuta scripts/migrate-login-otp.sql sobre la base configurada en src/php/config.php
 */

$_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__);
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';

require_once __DIR__ . '/../src/php/db.php';

$sqlFile = __DIR__ . '/migrate-login-otp.sql';
if (!is_readable($sqlFile)) {
    fwrite(STDERR, "No se encontró migrate-login-otp.sql\n");
    exit(1);
}

$sql = file_get_contents($sqlFile);
$pdo = db_rw();

$statements = array_filter(
    array_map('trim', preg_split('/;\s*\n/', $sql)),
    static fn(string $s): bool => $s !== '' && !preg_match('/^--/', $s) && !preg_match('/^SET NAMES/', $s)
);

foreach ($statements as $statement) {
    if ($statement === '') {
        continue;
    }
    try {
        $pdo->exec($statement);
        echo "OK: " . substr(str_replace(["\r", "\n"], ' ', $statement), 0, 80) . "...\n";
    } catch (PDOException $e) {
        fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
        fwrite(STDERR, "SQL: " . $statement . "\n");
        exit(1);
    }
}

echo "Migración login OTP completada.\n";
