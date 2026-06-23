<?php
/**
 * Ejecuta migrate-agrupar-colores.sql y reporta conteos post-migración.
 * Uso: php scripts/run-migrate-agrupar-colores.php
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$sqlFile = __DIR__ . '/migrate-agrupar-colores.sql';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=yofi;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    fwrite(STDERR, "Error de conexión: {$e->getMessage()}\n");
    exit(1);
}

$before = (int)$pdo->query('SELECT COUNT(*) FROM tbl_productos WHERE borrado = 0')->fetchColumn();
$beforePub = (int)$pdo->query('SELECT COUNT(*) FROM tbl_productos WHERE borrado = 0 AND publicado = 1')->fetchColumn();

echo "Antes: {$before} productos ({$beforePub} publicados)\n";

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    fwrite(STDERR, "No se pudo leer {$sqlFile}\n");
    exit(1);
}

// Ejecutar statements (ignorar comentarios sueltos)
$rawStatements = preg_split('/;\s*\n/', $sql);
$statements = [];
foreach ($rawStatements as $chunk) {
    $lines = array_filter(array_map('trim', explode("\n", $chunk)), static fn(string $l): bool => $l !== '' && !preg_match('/^--/', $l));
    if ($lines === []) {
        continue;
    }
    $stmt = implode("\n", $lines);
    if (preg_match('/^SET NAMES/', $stmt)) {
        continue;
    }
    $statements[] = $stmt;
}

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
foreach ($statements as $stmt) {
    if (preg_match('/^SET FOREIGN_KEY_CHECKS/', $stmt)) {
        $pdo->exec($stmt);
        continue;
    }
    $pdo->exec($stmt);
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

$after = (int)$pdo->query('SELECT COUNT(*) FROM tbl_productos WHERE borrado = 0')->fetchColumn();
$afterPub = (int)$pdo->query('SELECT COUNT(*) FROM tbl_productos WHERE borrado = 0 AND publicado = 1')->fetchColumn();

echo "Después: {$after} productos ({$afterPub} publicados)\n\n";

echo "=== Productos WhatsApp agrupados ===\n";
$rows = $pdo->query("
    SELECT p.id_prod, p.codigo, p.nombre, p.slug,
           GROUP_CONCAT(DISTINCT c.nombre ORDER BY c.nombre SEPARATOR ', ') AS colores,
           COUNT(DISTINCT s.id_color) AS num_colores
    FROM tbl_productos p
    LEFT JOIN tbl_skus s ON s.id_prod = p.id_prod AND s.activo = 1
    LEFT JOIN tbl_colores c ON c.id_color = s.id_color
    WHERE p.codigo LIKE 'YF-MINI-%' OR p.codigo LIKE 'YF-REG-%'
    GROUP BY p.id_prod, p.codigo, p.nombre, p.slug
    ORDER BY p.codigo
")->fetchAll();

foreach ($rows as $row) {
    echo "{$row['codigo']} | {$row['nombre']} | {$row['num_colores']} color(es): {$row['colores']}\n";
}

echo "\nMigración completada.\n";
