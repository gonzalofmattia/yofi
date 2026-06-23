<?php

declare(strict_types=1);

ob_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../src/php/auth.php';
ob_clean();

header('Content-Type: application/json; charset=utf-8');

function api_json(array $payload, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function api_require_csrf(): void
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!validatePublicCsrfToken(is_string($token) ? $token : null)) {
        api_json(['success' => false, 'message' => 'Token de seguridad inválido'], 403);
    }
}

function api_require_login(): int
{
    $userId = getLoggedInUserId();
    if ($userId === null) {
        api_json(['success' => false, 'message' => 'Debés iniciar sesión'], 401);
    }

    return $userId;
}
