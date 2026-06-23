<?php
/**
 * Pone stock = 10 en todos los SKUs activos.
 * Uso: php scripts/run-set-stock-masivo.php
 */
declare(strict_types=1);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=yofi;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (Throwable $e) {
    fwrite(STDERR, "Error de conexión: {$e->getMessage()}\n");
    exit(1);
}

$before = (int)$pdo->query('SELECT SUM(stock) FROM tbl_skus WHERE activo = 1')->fetchColumn();
$count = (int)$pdo->query('SELECT COUNT(*) FROM tbl_skus WHERE activo = 1')->fetchColumn();

$pdo->exec('UPDATE tbl_skus SET stock = 10 WHERE activo = 1');

$after = (int)$pdo->query('SELECT SUM(stock) FROM tbl_skus WHERE activo = 1')->fetchColumn();

echo "SKUs actualizados: {$count}\n";
echo "Stock total antes: {$before} → después: {$after}\n";
