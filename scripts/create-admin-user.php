<?php

/**
 * Crear o resetear usuario admin de la tienda.
 *
 * Uso local (Laragon):
 *   php scripts/create-admin-user.php
 *   php scripts/create-admin-user.php --user=admin --email=admin@yofi.com.ar
 *
 * Solo generar SQL para phpMyAdmin (producción):
 *   php scripts/create-admin-user.php --print-sql
 */

declare(strict_types=1);

$options = getopt('', ['user:', 'email:', 'password:', 'print-sql', 'apply-local']);

$user = trim((string)($options['user'] ?? 'admin'));
$email = trim((string)($options['email'] ?? 'admin@yofi.com.ar'));
$printSql = isset($options['print-sql']);
$applyLocal = isset($options['apply-local']);

if ($user === '') {
    fwrite(STDERR, "Error: --user no puede estar vacío.\n");
    exit(1);
}

$password = isset($options['password']) ? (string) $options['password'] : generateAdminPassword();
$hash = password_hash($password, PASSWORD_DEFAULT);

if ($hash === false) {
    fwrite(STDERR, "Error al generar hash de contraseña.\n");
    exit(1);
}

$sql = buildAdminSql($user, $hash, $email);

if ($printSql || !$applyLocal) {
    echo "=== Credenciales admin Yofi ===\n\n";
    echo "URL:      https://yofi.com.ar/admin/\n";
    echo "Usuario:  {$user}\n";
    echo "Password: {$password}\n\n";
    echo "=== SQL — ejecutar en phpMyAdmin (base a0141154_yofi) ===\n\n";
    echo $sql . "\n";
    echo "\nGuardá la contraseña en un lugar seguro. No se vuelve a mostrar.\n";
    exit(0);
}

// --apply-local: insertar/actualizar en BD local Laragon
$host = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'yofi';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec($sql);
    echo "OK — usuario '{$user}' creado/actualizado en BD local (yofi).\n";
    echo "Usuario:  {$user}\n";
    echo "Password: {$password}\n";
} catch (PDOException $e) {
    fwrite(STDERR, 'Error DB local: ' . $e->getMessage() . "\n");
    exit(1);
}

function generateAdminPassword(int $length = 14): string
{
    $lower = 'abcdefghjkmnpqrstuvwxyz';
    $upper = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    $digits = '23456789';
    $all = $lower . $upper . $digits;

    $password = $lower[random_int(0, strlen($lower) - 1)]
        . $upper[random_int(0, strlen($upper) - 1)]
        . $digits[random_int(0, strlen($digits) - 1)];

    for ($i = 3; $i < $length; $i++) {
        $password .= $all[random_int(0, strlen($all) - 1)];
    }

    return str_shuffle($password);
}

function buildAdminSql(string $user, string $hash, string $email): string
{
    $userEsc = addslashes($user);
    $hashEsc = addslashes($hash);
    $emailEsc = addslashes($email);

    return <<<SQL
-- Usuario admin tienda Yofi (contraseña hasheada bcrypt)
INSERT INTO `tbl_admin` (`usuadmin`, `clave`, `publicado`, `username`, `password`, `email`)
VALUES ('{$userEsc}', '{$hashEsc}', 1, '{$userEsc}', '{$hashEsc}', '{$emailEsc}')
ON DUPLICATE KEY UPDATE
  `clave` = VALUES(`clave`),
  `password` = VALUES(`password`),
  `email` = VALUES(`email`),
  `publicado` = 1;
SQL;
}
