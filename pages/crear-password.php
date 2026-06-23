<?php

require_once __DIR__ . '/../src/php/auth.php';

$page_title = 'Crear contraseña | ' . SITE_NAME;
$meta_description = 'Establecé tu contraseña de acceso a Yofi.';

$error = null;
$token = trim((string) ($_GET['token'] ?? ''));
$tokenData = null;

if ($token !== '') {
    $tokenData = validatePasswordToken($token);
    if (!$tokenData) {
        $error = 'El enlace es inválido o expiró. Solicitá uno nuevo desde el login.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validatePublicCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'La sesión expiró. Recargá la página e intentá de nuevo.';
    } else {
        $postToken = trim((string) ($_POST['token'] ?? $token));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');

        if ($password === '' || strlen($password) < 8) {
            $error = 'La contraseña debe tener al menos 8 caracteres';
        } elseif ($password !== $confirm) {
            $error = 'Las contraseñas no coinciden';
        } elseif ($postToken === '') {
            $error = 'Enlace inválido';
        } else {
            $result = setPasswordFromToken($postToken, $password);
            if ($result['success']) {
                header('Location: ' . page_path('mi-cuenta') . '&tab=perfil&password_set=1');
                exit;
            }
            $error = $result['message'] ?? 'No se pudo guardar la contraseña';
        }
    }
}

?>
<section class="w-full px-6 md:px-8 py-10 md:py-16 max-w-md mx-auto">
    <?php if ($error && !$tokenData): ?>
    <h1 class="text-2xl font-bold text-dark mb-4">Enlace inválido</h1>
    <p class="text-earth mb-6"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <a href="<?php echo page_path('login'); ?>&forgot=1" class="inline-block bg-accent text-white font-bold px-6 py-3 rounded-full">Solicitar nuevo enlace</a>
    <?php else: ?>
    <h1 class="text-2xl md:text-3xl font-bold text-dark mb-2">Nueva contraseña</h1>
    <p class="text-earth text-sm mb-8">Hola <?php echo htmlspecialchars(trim(($tokenData['nombre'] ?? '') . ' ' . ($tokenData['apellido'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>, elegí una contraseña segura.</p>

    <?php if ($error): ?>
    <div class="mb-6 rounded-lg border border-accent/30 bg-accent/10 text-sm px-4 py-3" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
        <?php echo public_csrf_field(); ?>
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
        <div>
            <label for="password" class="block text-sm font-semibold mb-1">Contraseña nueva</label>
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password" class="w-full border border-cream rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
            <label for="confirm_password" class="block text-sm font-semibold mb-1">Confirmar contraseña</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password" class="w-full border border-cream rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <button type="submit" class="w-full bg-accent text-white font-bold py-3 rounded-full hover:opacity-90 transition-opacity">Guardar contraseña</button>
    </form>
    <?php endif; ?>
</section>
