<?php

require_once __DIR__ . '/../src/php/auth.php';

function account_safe_redirect(string $url): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    }
    $safe = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta http-equiv="refresh" content="0;url=' . $safe . '"></head><body><script>location.replace(' . json_encode($url) . ');</script></body></html>';
    exit;
}

function account_sanitize_redirect(string $raw): string
{
    $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($raw));
    if ($slug === '') {
        return 'mi-cuenta';
    }
    $allowed = ['mi-cuenta', 'catalogo', 'home'];
    if (!in_array($slug, $allowed, true)) {
        return 'mi-cuenta';
    }

    return $slug;
}

if (isUserLoggedIn()) {
    $cu = getCurrentUser();
    $uid = is_numeric($cu['id'] ?? null) ? (int) $cu['id'] : 0;
    if ($uid > 0 && getUserData($uid)) {
        account_safe_redirect(page_path('mi-cuenta'));
    }
    logoutUser();
}

$page_title = 'Iniciar sesión | ' . SITE_NAME;
$meta_description = 'Ingresá a tu cuenta Yofi para ver pedidos, direcciones y favoritos.';

$error = null;
$success = null;

if (isset($_GET['success']) && $_GET['success'] === 'password_created') {
    $success = 'Contraseña creada. Ya podés iniciar sesión.';
}
if (isset($_GET['logout'])) {
    $success = 'Sesión cerrada correctamente.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validatePublicCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'La sesión expiró. Recargá la página e intentá de nuevo.';
    } else {
        $action = $_POST['action'] ?? 'login';
        if ($action === 'forgot') {
            $result = requestPasswordReset(trim((string) ($_POST['email'] ?? '')));
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        } else {
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Ingresá un email válido';
            } else {
                $result = loginUser($email, $password);
                if ($result['success']) {
                    if (function_exists('syncWishlistOnLogin')) {
                        // JS sync on next page load
                    }
                    $redirect = account_sanitize_redirect((string) ($_POST['redirect'] ?? $_GET['redirect'] ?? 'mi-cuenta'));
                    account_safe_redirect(page_path($redirect));
                } else {
                    $error = $result['message'] ?? 'Email o contraseña incorrectos';
                    if (($result['code'] ?? '') === 'guest_needs_registration') {
                        $error .= ' <a class="text-accent underline font-semibold" href="' . htmlspecialchars(page_path('registro'), ENT_QUOTES, 'UTF-8') . '">Completar registro</a>';
                    }
                }
            }
        }
    }
}

$redirectParam = isset($_GET['redirect']) ? htmlspecialchars((string) $_GET['redirect'], ENT_QUOTES, 'UTF-8') : '';
$showForgot = isset($_GET['forgot']) || (($_POST['action'] ?? '') === 'forgot');
?>
<section class="w-full px-6 md:px-8 py-10 md:py-16 max-w-md mx-auto">
    <h1 class="text-2xl md:text-3xl font-bold text-dark mb-2"><?php echo $showForgot ? 'Recuperar contraseña' : 'Iniciar sesión'; ?></h1>
    <p class="text-earth text-sm mb-8"><?php echo $showForgot ? 'Te enviamos un enlace por email para restablecer tu contraseña.' : 'Accedé a tus pedidos, direcciones y lista de deseos.'; ?></p>

    <?php if ($error): ?>
    <div class="mb-6 rounded-lg border border-accent/30 bg-accent/10 text-dark text-sm px-4 py-3" role="alert"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="mb-6 rounded-lg border border-secondary/40 bg-secondary/10 text-dark text-sm px-4 py-3" role="status"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if ($showForgot): ?>
    <form method="post" action="<?php echo htmlspecialchars(page_path('login') . '&forgot=1', ENT_QUOTES, 'UTF-8'); ?>" class="space-y-4">
        <?php echo public_csrf_field(); ?>
        <input type="hidden" name="action" value="forgot">
        <div>
            <label for="email" class="block text-sm font-semibold mb-1">Email</label>
            <input type="email" id="email" name="email" required class="w-full border border-cream rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary" value="<?php echo htmlspecialchars((string) ($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <button type="submit" class="w-full bg-accent text-white font-bold py-3 rounded-full hover:opacity-90 transition-opacity">Enviar enlace</button>
        <p class="text-center text-sm"><a href="<?php echo page_path('login'); ?>" class="text-accent underline">Volver al login</a></p>
    </form>
    <?php else: ?>
    <form method="post" action="<?php echo htmlspecialchars(page_path('login') . ($redirectParam !== '' ? '&redirect=' . urlencode($_GET['redirect'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>" class="space-y-4">
        <?php echo public_csrf_field(); ?>
        <?php if ($redirectParam !== ''): ?>
        <input type="hidden" name="redirect" value="<?php echo $redirectParam; ?>">
        <?php endif; ?>
        <div>
            <label for="email" class="block text-sm font-semibold mb-1">Email</label>
            <input type="email" id="email" name="email" required autocomplete="email" class="w-full border border-cream rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary" value="<?php echo htmlspecialchars((string) ($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div>
            <label for="password" class="block text-sm font-semibold mb-1">Contraseña</label>
            <input type="password" id="password" name="password" required autocomplete="current-password" class="w-full border border-cream rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="text-right">
            <a href="<?php echo page_path('login'); ?>&forgot=1" class="text-sm text-accent underline">¿Olvidaste tu contraseña?</a>
        </div>
        <button type="submit" class="w-full bg-accent text-white font-bold py-3 rounded-full hover:opacity-90 transition-opacity">Ingresar</button>
    </form>
    <p class="text-center text-sm text-earth mt-6">¿No tenés cuenta? <a href="<?php echo page_path('registro'); ?>" class="text-accent font-semibold underline">Crear cuenta</a></p>
    <?php endif; ?>
</section>
