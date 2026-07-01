<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/../../src/php/users.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_json(['success' => false, 'message' => 'Método no permitido'], 405);
}

$email = trim((string)($_GET['email'] ?? ''));

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    api_json(['success' => true, 'tiene_cuenta' => false]);
}

$status = checkEmailAccountStatus($email);
$tieneCuenta = $status !== null && $status['exists'] && $status['has_password'];

api_json(['success' => true, 'tiene_cuenta' => $tieneCuenta]);
