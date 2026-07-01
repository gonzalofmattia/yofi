<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_json(['success' => false, 'message' => 'Método no permitido'], 405);
}

api_require_csrf();

$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '', true);
$email = trim((string)(is_array($data) ? ($data['email'] ?? '') : ($_POST['email'] ?? '')));
$code = trim((string)(is_array($data) ? ($data['code'] ?? '') : ($_POST['code'] ?? '')));

$result = verifyLoginCode($email, $code);

api_json($result, $result['success'] ? 200 : 400);
