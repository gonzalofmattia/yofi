<?php

declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

$user = getCurrentUser();

api_json([
    'success' => true,
    'logged_in' => isUserLoggedIn(),
    'user' => $user,
    'csrf_token' => generatePublicCsrfToken(),
]);
