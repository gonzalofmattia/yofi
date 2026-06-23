<?php

declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_json(['success' => false, 'message' => 'Método no permitido'], 405);
}

api_require_csrf();
logoutUser();

api_json(['success' => true, 'message' => 'Sesión cerrada']);
