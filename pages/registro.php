<?php

require_once __DIR__ . '/../src/php/auth.php';
require_once __DIR__ . '/../src/php/users.php';

if (isUserLoggedIn()) {
    header('Location: ' . page_path('mi-cuenta'));
    exit;
}

$page_title = 'Crear cuenta | ' . SITE_NAME;
$meta_description = 'Registrate en Yofi para guardar tus favoritos, direcciones y ver tus pedidos.';

$error = null;
$success = null;
$isGuestComplete = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validatePublicCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'La sesión expiró. Recargá la página e intentá de nuevo.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $status = checkEmailAccountStatus($email);
        if ($status && $status['exists'] && $status['is_guest'] && !$status['has_password']) {
            $isGuestComplete = true;
        }

        $result = registerUser(
            $email,
            (string) ($_POST['password'] ?? ''),
            trim((string) ($_POST['nombre'] ?? '')),
            trim((string) ($_POST['apellido'] ?? '')),
            trim((string) ($_POST['telefono'] ?? ''))
        );

        if ($result['success']) {
            header('Location: ' . page_path('mi-cuenta') . '&welcome=1');
            exit;
        }
        $error = $result['message'] ?? 'No se pudo crear la cuenta';
        if (($result['code'] ?? '') === 'email_exists') {
            $error .= ' <a class="text-accent underline" href="' . htmlspecialchars(page_path('login'), ENT_QUOTES, 'UTF-8') . '">Iniciá sesión</a> o <a class="text-accent underline" href="' . htmlspecialchars(page_path('login') . '&forgot=1', ENT_QUOTES, 'UTF-8') . '">recuperá tu contraseña</a>.';
        }
    }
} elseif (isset($_GET['email'])) {
    $preEmail = trim((string) $_GET['email']);
    $st = checkEmailAccountStatus($preEmail);
    if ($st && $st['exists'] && $st['is_guest'] && !$st['has_password']) {
        $isGuestComplete = true;
    }
}

?>
<section class="w-full px-6 md:px-8 py-10 md:py-16 max-w-md mx-auto">
    <h1 class="text-2xl md:text-3xl font-bold text-dark mb-2"><?php echo $isGuestComplete ? 'Completar registro' : 'Crear cuenta'; ?></h1>
    <p class="text-earth text-sm mb-8"><?php echo $isGuestComplete ? 'Ya compraste con este email. Creá tu contraseña para acceder a tu cuenta.' : 'Registrate para guardar favoritos, direcciones y ver tus pedidos.'; ?></p>

    <?php if ($error): ?>
    <div class="mb-6 rounded-lg border border-accent/30 bg-accent/10 text-dark text-sm px-4 py-3" role="alert"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo page_path('registro'); ?>" class="space-y-4">
        <?php echo public_csrf_field(); ?>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="nombre" class="block text-sm font-semibold mb-1">Nombre</label>
                <input type="text" id="nombre" name="nombre" required class="w-full border border-cream rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary" value="<?php echo htmlspecialchars((string) ($_POST['nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div>
                <label for="apellido" class="block text-sm font-semibold mb-1">Apellido</label>
                <input type="text" id="apellido" name="apellido" required class="w-full border border-cream rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary" value="<?php echo htmlspecialchars((string) ($_POST['apellido'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>
        <div>
            <label for="email" class="block text-sm font-semibold mb-1">Email</label>
            <input type="email" id="email" name="email" required autocomplete="email" class="w-full border border-cream rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary" value="<?php echo htmlspecialchars((string) ($_POST['email'] ?? $_GET['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div>
            <label for="telefono" class="block text-sm font-semibold mb-1">Teléfono <span class="text-earth font-normal">(opcional)</span></label>
            <input type="tel" id="telefono" name="telefono" class="w-full border border-cream rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary" value="<?php echo htmlspecialchars((string) ($_POST['telefono'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div>
            <label for="password" class="block text-sm font-semibold mb-1">Contraseña</label>
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password" class="w-full border border-cream rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <p class="text-xs text-earth mt-1">Mínimo 8 caracteres</p>
        </div>
        <button type="submit" class="w-full bg-accent text-white font-bold py-3 rounded-full hover:opacity-90 transition-opacity"><?php echo $isGuestComplete ? 'Completar registro' : 'Crear cuenta'; ?></button>
    </form>
    <p class="text-center text-sm text-earth mt-6">¿Ya tenés cuenta? <a href="<?php echo page_path('login'); ?>" class="text-accent font-semibold underline">Iniciar sesión</a></p>
</section>
