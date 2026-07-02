<?php
ob_start();
require_once __DIR__ . '/include/session_init.php';
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/include/SecurityManager.php';

$security = new SecurityManager($con);

if ($security->isSessionValid()) {
    ob_end_clean();
    header('Location: ' . admin_safe_redirect_target($_GET['redirect'] ?? null));
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario === '' || $password === '') {
        $error_message = 'Por favor, complete todos los campos.';
    } else {
        $auth_result = $security->authenticate($usuario, $password);
        if ($auth_result['success'] && isset($_SESSION['adminValido']) && $_SESSION['adminValido'] === 'si') {
            if (function_exists('session_commit')) {
                session_commit();
            }
            ob_end_clean();
            header('Location: ' . admin_safe_redirect_target($_POST['redirect'] ?? null));
            exit();
        }
        $error_message = $auth_result['message'] ?? 'Credenciales inválidas';
    }
}

ob_end_flush();

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'session_expired':
            $error_message = 'Su sesión ha expirado. Por favor, inicie sesión nuevamente.';
            break;
        case 'unauthorized':
            $error_message = 'Acceso no autorizado. Por favor, inicie sesión.';
            break;
        case 'logout':
            $success_message = 'Ha cerrado sesión correctamente.';
            break;
    }
}

$redirectParam = (string) ($_GET['redirect'] ?? $_POST['redirect'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Yofi Admin</title>
    <?php include dirname(__DIR__) . '/partials/favicon-head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #FAAF7D 0%, #e8955a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #FAAF7D 0%, #e8955a 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body { padding: 2rem; }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #FAAF7D;
            box-shadow: 0 0 0 0.2rem rgba(250, 175, 125, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #FAAF7D 0%, #e8955a 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="login-card">
                <div class="login-header">
                    <img src="<?= asset_path('img/logo-yofi.png') ?>" alt="Yofi" height="40" class="mb-3">
                    <h4 class="mb-0">Yofi Admin</h4>
                    <p class="mb-0">Panel de administración</p>
                </div>
                <div class="login-body">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                    <form method="POST" action="index.php">
                        <?php if ($redirectParam !== ''): ?>
                        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectParam, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-login">Iniciar sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
