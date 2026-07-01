<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function getPublicSessionCookiePath(): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($script === '' || $script[0] !== '/') {
        return '/';
    }
    $parts = array_values(array_filter(explode('/', trim($script, '/')), static fn($p) => $p !== ''));
    if ($parts === []) {
        return '/';
    }
    $first = $parts[0];
    if ($first === 'index.php' || (strlen($first) > 4 && substr($first, -4) === '.php')) {
        return '/';
    }

    return rtrim('/' . $first, '/') . '/';
}

function isHttpsRequest(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    if (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https') {
        return true;
    }
    if (strtolower((string) ($_SERVER['HTTP_FRONT_END_HTTPS'] ?? '')) === 'on') {
        return true;
    }

    return false;
}

function isSessionCookieSecure(): bool
{
    return isHttpsRequest();
}

function getPublicSessionName(): string
{
    return 'YOFIPUBLICSESSID';
}

function emitPublicSessionCookie(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }
    $name = session_name();
    $sid = session_id();
    if ($name === '' || $sid === '') {
        return;
    }
    $path = getPublicSessionCookiePath();
    $secure = isSessionCookieSecure();

    if (PHP_VERSION_ID >= 70300) {
        setcookie($name, $sid, [
            'expires' => 0,
            'path' => $path,
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        setcookie($name, $sid, 0, $path, '', $secure, true);
    }
}

function publicSessionStart(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    static $handlerRegistered = false;
    if (!$handlerRegistered) {
        require_once __DIR__ . '/DbSessionHandler.php';
        DbSessionHandler::register();
        $handlerRegistered = true;
    }

    session_name(getPublicSessionName());
    $path = getPublicSessionCookiePath();
    $secure = isSessionCookieSecure();

    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => $path,
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        session_set_cookie_params(0, $path, '', $secure, true);
    }

    session_start();
}

function generatePublicCsrfToken(): string
{
    publicSessionStart();
    if (!isset($_SESSION['public_csrf_token'])) {
        $_SESSION['public_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['public_csrf_token'];
}

function validatePublicCsrfToken(?string $token): bool
{
    publicSessionStart();
    if ($token === null || $token === '') {
        return false;
    }
    if (!isset($_SESSION['public_csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['public_csrf_token'], $token);
}

function public_csrf_field(): string
{
    $token = htmlspecialchars(generatePublicCsrfToken(), ENT_QUOTES, 'UTF-8');

    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function establishUserSession(array $user, bool $needsPasswordSetup = false): void
{
    publicSessionStart();
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int) $user['id_usuario'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''));
    $_SESSION['user_logged_in'] = true;
    $_SESSION['needs_password_setup'] = $needsPasswordSetup;
    $_SESSION['email_verificado'] = (bool) ($user['email_verificado'] ?? true);

    emitPublicSessionCookie();
}

/**
 * Vincula pedidos de invitado (usuario_id NULL) cuyo email coincide con el de la cuenta.
 */
function syncUserOrdersByEmail(int $userId): int
{
    if ($userId <= 0) {
        return 0;
    }

    try {
        $user = getUserData($userId);
        if (!$user) {
            return 0;
        }

        $email = strtolower(trim((string) ($user['email'] ?? '')));
        if ($email === '') {
            return 0;
        }

        $pdo = db_rw();
        $stmt = $pdo->prepare('
            UPDATE tbl_ordenes
            SET usuario_id = ?
            WHERE usuario_id IS NULL
              AND deleted_at IS NULL
              AND LOWER(TRIM(email)) = ?
        ');
        $stmt->execute([$userId, $email]);

        return $stmt->rowCount();
    } catch (Throwable $e) {
        error_log('syncUserOrdersByEmail: ' . $e->getMessage());

        return 0;
    }
}

function loginUser(string $email, string $password): array
{
    try {
        $pdo = db_ro();
        $stmt = $pdo->prepare('
            SELECT id_usuario, email, nombre, apellido, password_hash, is_guest, email_verificado, activo
            FROM tbl_usuarios
            WHERE email = ? AND activo = 1
            LIMIT 1
        ');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'Email o contraseña incorrectos'];
        }

        $hasPassword = !empty($user['password_hash']);
        $isGuest = (int) ($user['is_guest'] ?? 0) === 1;

        if (!$hasPassword) {
            if ($isGuest) {
                return [
                    'success' => false,
                    'message' => 'Tu email está registrado como invitado. Completá tu registro para crear una contraseña.',
                    'code' => 'guest_needs_registration',
                ];
            }

            return ['success' => false, 'message' => 'Tu cuenta no tiene contraseña. Usá "Olvidé mi contraseña" para crear una.'];
        }

        if ($password === '' || !password_verify($password, (string) $user['password_hash'])) {
            return ['success' => false, 'message' => 'Email o contraseña incorrectos'];
        }

        $pdoRw = db_rw();
        $upd = $pdoRw->prepare('UPDATE tbl_usuarios SET fecha_ultimo_acceso = NOW() WHERE id_usuario = ?');
        $upd->execute([(int) $user['id_usuario']]);

        establishUserSession($user, false);
        syncUserOrdersByEmail((int) $user['id_usuario']);

        return [
            'success' => true,
            'user' => [
                'id' => (int) $user['id_usuario'],
                'email' => $user['email'],
                'name' => trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')),
            ],
            'needs_password_setup' => false,
        ];
    } catch (Throwable $e) {
        error_log('loginUser: ' . $e->getMessage());

        return ['success' => false, 'message' => 'Error al iniciar sesión'];
    }
}

function logoutUser(): void
{
    publicSessionStart();
    $_SESSION = [];

    $path = getPublicSessionCookiePath();
    $secure = isSessionCookieSecure();
    $sn = getPublicSessionName();
    if (isset($_COOKIE[$sn])) {
        if (PHP_VERSION_ID >= 70300) {
            setcookie($sn, '', [
                'expires' => time() - 3600,
                'path' => $path,
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        } else {
            setcookie($sn, '', time() - 3600, $path, '', $secure, true);
        }
    }

    session_destroy();
}

function isUserLoggedIn(): bool
{
    publicSessionStart();

    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

function needsPasswordSetup(): bool
{
    if (!isUserLoggedIn()) {
        return false;
    }
    publicSessionStart();

    return !empty($_SESSION['needs_password_setup']);
}

function getCurrentUser(): ?array
{
    if (!isUserLoggedIn()) {
        return null;
    }
    publicSessionStart();
    $id = $_SESSION['user_id'] ?? null;

    return [
        'id' => is_numeric($id) ? (int) $id : null,
        'email' => $_SESSION['user_email'] ?? null,
        'name' => $_SESSION['user_name'] ?? null,
    ];
}

function getUserData(int $userId): ?array
{
    try {
        $pdo = db_ro();
        $stmt = $pdo->prepare('
            SELECT id_usuario, email, nombre, apellido, telefono, direccion, ciudad, provincia,
                   codigo_postal, dni, email_verificado, activo, is_guest
            FROM tbl_usuarios
            WHERE id_usuario = ?
            LIMIT 1
        ');
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        return [
            'id' => (int) $user['id_usuario'],
            'email' => $user['email'],
            'nombre' => $user['nombre'] ?? '',
            'apellido' => $user['apellido'] ?? '',
            'telefono' => $user['telefono'] ?? '',
            'direccion' => $user['direccion'] ?? '',
            'ciudad' => $user['ciudad'] ?? '',
            'provincia' => $user['provincia'] ?? '',
            'codigo_postal' => $user['codigo_postal'] ?? '',
            'dni' => $user['dni'] ?? '',
            'email_verificado' => (bool) ($user['email_verificado'] ?? false),
            'activo' => (int) ($user['activo'] ?? 1),
            'is_guest' => (bool) ($user['is_guest'] ?? false),
        ];
    } catch (Throwable $e) {
        error_log('getUserData: ' . $e->getMessage());

        return null;
    }
}

function updateUserProfile(int $userId, array $data): array
{
    $nombre = trim((string) ($data['nombre'] ?? ''));
    $apellido = trim((string) ($data['apellido'] ?? ''));
    $email = trim((string) ($data['email'] ?? ''));
    $telefono = trim((string) ($data['telefono'] ?? ''));
    $dni = trim((string) ($data['dni'] ?? ''));

    if ($nombre === '' || $apellido === '') {
        return ['success' => false, 'message' => 'Nombre y apellido son obligatorios'];
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Email inválido'];
    }

    try {
        $pdo = db_rw();
        $chk = $pdo->prepare('SELECT id_usuario FROM tbl_usuarios WHERE email = ? AND id_usuario != ? LIMIT 1');
        $chk->execute([$email, $userId]);
        if ($chk->fetch()) {
            return ['success' => false, 'message' => 'Ese email ya está en uso por otra cuenta'];
        }

        $stmt = $pdo->prepare('
            UPDATE tbl_usuarios
            SET nombre = ?, apellido = ?, email = ?, telefono = ?, dni = ?, fecha_actualizacion = NOW()
            WHERE id_usuario = ?
        ');
        $stmt->execute([$nombre, $apellido, $email, $telefono !== '' ? $telefono : null, $dni !== '' ? $dni : null, $userId]);

        publicSessionStart();
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = trim($nombre . ' ' . $apellido);

        return ['success' => true, 'message' => 'Datos actualizados correctamente'];
    } catch (Throwable $e) {
        error_log('updateUserProfile: ' . $e->getMessage());

        return ['success' => false, 'message' => 'No se pudieron guardar los datos'];
    }
}

function changeUserPassword(int $userId, string $currentPassword, string $newPassword): array
{
    if (strlen($newPassword) < 8) {
        return ['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres'];
    }

    try {
        $pdo = db_ro();
        $stmt = $pdo->prepare('SELECT password_hash FROM tbl_usuarios WHERE id_usuario = ? LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        $hasPassword = !empty($row['password_hash']);
        if ($hasPassword) {
            if ($currentPassword === '' || !password_verify($currentPassword, (string) $row['password_hash'])) {
                return ['success' => false, 'message' => 'La contraseña actual es incorrecta'];
            }
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $pdoRw = db_rw();
        $upd = $pdoRw->prepare('
            UPDATE tbl_usuarios SET password_hash = ?, is_guest = 0, fecha_actualizacion = NOW()
            WHERE id_usuario = ?
        ');
        $upd->execute([$hash, $userId]);

        publicSessionStart();
        $_SESSION['needs_password_setup'] = false;

        return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
    } catch (Throwable $e) {
        error_log('changeUserPassword: ' . $e->getMessage());

        return ['success' => false, 'message' => 'No se pudo cambiar la contraseña'];
    }
}

function generatePasswordToken(int $userId, int $expirationHours = 1): ?string
{
    try {
        $pdo = db_rw();
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + ($expirationHours * 3600));

        $pdo->prepare('UPDATE tbl_password_tokens SET used = 1 WHERE usuario_id = ? AND used = 0')
            ->execute([$userId]);

        $stmt = $pdo->prepare('
            INSERT INTO tbl_password_tokens (usuario_id, token, expires_at, used)
            VALUES (?, ?, ?, 0)
        ');
        $stmt->execute([$userId, $token, $expiresAt]);

        return $token;
    } catch (Throwable $e) {
        error_log('generatePasswordToken: ' . $e->getMessage());

        return null;
    }
}

function validatePasswordToken(string $token): ?array
{
    try {
        $pdo = db_ro();
        $stmt = $pdo->prepare('
            SELECT pt.id_token, pt.usuario_id, pt.expires_at, pt.used,
                   u.email, u.nombre, u.apellido
            FROM tbl_password_tokens pt
            INNER JOIN tbl_usuarios u ON pt.usuario_id = u.id_usuario
            WHERE pt.token = ? AND pt.used = 0
            LIMIT 1
        ');
        $stmt->execute([$token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        if (strtotime((string) $row['expires_at']) < time()) {
            return null;
        }

        return [
            'token_id' => (int) $row['id_token'],
            'user_id' => (int) $row['usuario_id'],
            'email' => $row['email'],
            'nombre' => $row['nombre'],
            'apellido' => $row['apellido'],
            'expires_at' => $row['expires_at'],
        ];
    } catch (Throwable $e) {
        error_log('validatePasswordToken: ' . $e->getMessage());

        return null;
    }
}

function setPasswordFromToken(string $token, string $newPassword): array
{
    $tokenData = validatePasswordToken($token);
    if (!$tokenData) {
        return ['success' => false, 'message' => 'Token inválido o expirado'];
    }
    if (strlen($newPassword) < 8) {
        return ['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres'];
    }

    try {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $userId = (int) $tokenData['user_id'];
        $pdo = db_rw();

        $pdo->prepare('
            UPDATE tbl_usuarios
            SET password_hash = ?, is_guest = 0, email_verificado = 1, fecha_actualizacion = NOW()
            WHERE id_usuario = ?
        ')->execute([$hash, $userId]);

        $pdo->prepare('UPDATE tbl_password_tokens SET used = 1 WHERE id_token = ?')
            ->execute([(int) $tokenData['token_id']]);

        $userRow = getUserData($userId);
        if ($userRow) {
            establishUserSession([
                'id_usuario' => $userId,
                'email' => $userRow['email'],
                'nombre' => $userRow['nombre'],
                'apellido' => $userRow['apellido'],
                'email_verificado' => 1,
            ], false);
        }

        return [
            'success' => true,
            'message' => 'Contraseña establecida correctamente',
            'auto_login' => true,
            'user_id' => $userId,
        ];
    } catch (Throwable $e) {
        error_log('setPasswordFromToken: ' . $e->getMessage());

        return ['success' => false, 'message' => 'Error al establecer la contraseña'];
    }
}

function getUserOrders(int $userId, int $limit = 50): array
{
    try {
        $pdo = db_ro();
        $stmt = $pdo->prepare('
            SELECT id_orden, numero_orden, estado, email, subtotal, envio, total,
                   fecha_creacion, metodo_pago
            FROM tbl_ordenes
            WHERE usuario_id = ? AND deleted_at IS NULL
            ORDER BY fecha_creacion DESC
            LIMIT ?
        ');
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        error_log('getUserOrders: ' . $e->getMessage());

        return [];
    }
}

function getUserOrderDetail(int $userId, int $orderId): ?array
{
    try {
        $pdo = db_ro();
        $stmt = $pdo->prepare('
            SELECT id_orden, numero_orden, estado, nombre, apellido, email, telefono,
                   direccion, ciudad, provincia, codigo_postal, notas, metodo_pago,
                   shipping_method_code, shipping_carrier, shipping_eta, tracking_number,
                   subtotal, envio, total, items, fecha_creacion
            FROM tbl_ordenes
            WHERE id_orden = ? AND usuario_id = ? AND deleted_at IS NULL
            LIMIT 1
        ');
        $stmt->execute([$orderId, $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    } catch (Throwable $e) {
        error_log('getUserOrderDetail: ' . $e->getMessage());

        return null;
    }
}

function buildMagicLink(string $token): string
{
    $base = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';

    return $base . '/index.php?p=crear-password&token=' . urlencode($token);
}

function requestPasswordReset(string $email): array
{
    $email = trim($email);
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Email inválido'];
    }

    try {
        $pdo = db_ro();
        $stmt = $pdo->prepare('SELECT id_usuario, nombre, apellido, email FROM tbl_usuarios WHERE email = ? AND activo = 1 LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Respuesta genérica para no revelar si el email existe
        $genericOk = [
            'success' => true,
            'message' => 'Si el email está registrado, te enviamos un enlace para restablecer tu contraseña.',
        ];

        if (!$user) {
            return $genericOk;
        }

        $token = generatePasswordToken((int) $user['id_usuario'], 1);
        if (!$token) {
            return ['success' => false, 'message' => 'No se pudo generar el enlace. Intentá más tarde.'];
        }

        require_once __DIR__ . '/password_setup_email.php';
        require_once __DIR__ . '/email.php';

        $hasPassword = false;
        $pwStmt = $pdo->prepare('SELECT password_hash FROM tbl_usuarios WHERE id_usuario = ?');
        $pwStmt->execute([(int) $user['id_usuario']]);
        $pwRow = $pwStmt->fetch(PDO::FETCH_ASSOC);
        if ($pwRow && !empty($pwRow['password_hash'])) {
            $hasPassword = true;
        }

        $magicLink = buildMagicLink($token);
        $body = generatePasswordSetupEmail([
            'nombre' => trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')),
            'email' => $user['email'],
            'magic_link' => $magicLink,
            'flow' => $hasPassword ? 'reset' : 'create',
        ]);
        $subject = ($hasPassword ? 'Restablecé tu contraseña' : 'Creá tu contraseña') . ' — Yofi';

        if (!sendEmail($user['email'], $subject, $body, true)) {
            return ['success' => false, 'message' => 'No se pudo enviar el email. Verificá la configuración SMTP.'];
        }

        return $genericOk;
    } catch (Throwable $e) {
        error_log('requestPasswordReset: ' . $e->getMessage());

        return ['success' => false, 'message' => 'Error al procesar la solicitud'];
    }
}

function getLoggedInUserId(): ?int
{
    $user = getCurrentUser();
    $id = $user['id'] ?? null;

    return is_numeric($id) && (int) $id > 0 ? (int) $id : null;
}

/**
 * Genera y envía un código de acceso de un solo uso por email.
 * Respuesta genérica siempre: no revela si el email tiene cuenta o no.
 */
function requestLoginCode(string $email): array
{
    $email = trim($email);
    $genericOk = [
        'success' => true,
        'message' => 'Si el email tiene una cuenta, te enviamos un código de acceso.',
    ];

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Email inválido'];
    }

    try {
        $pdo = db_ro();
        $stmt = $pdo->prepare('SELECT id_usuario, nombre, apellido, email FROM tbl_usuarios WHERE email = ? AND activo = 1 LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return $genericOk;
        }

        $userId = (int) $user['id_usuario'];
        $pdoRw = db_rw();

        $lastStmt = $pdoRw->prepare('
            SELECT fecha_creacion, expires_at FROM tbl_login_otp
            WHERE usuario_id = ? AND consumed_at IS NULL
            ORDER BY id_otp DESC LIMIT 1
        ');
        $lastStmt->execute([$userId]);
        $last = $lastStmt->fetch(PDO::FETCH_ASSOC);
        if (
            $last
            && strtotime((string) $last['expires_at']) >= time()
            && (time() - strtotime((string) $last['fecha_creacion'])) < 60
        ) {
            return $genericOk;
        }

        $code = (string) random_int(100000, 999999);
        $codeHash = password_hash($code, PASSWORD_DEFAULT);
        $now = time();
        $createdAt = date('Y-m-d H:i:s', $now);
        $expiresAt = date('Y-m-d H:i:s', $now + 600);

        $pdoRw->prepare('UPDATE tbl_login_otp SET consumed_at = NOW() WHERE usuario_id = ? AND consumed_at IS NULL')
            ->execute([$userId]);

        $insert = $pdoRw->prepare('
            INSERT INTO tbl_login_otp (usuario_id, code_hash, expires_at, fecha_creacion)
            VALUES (?, ?, ?, ?)
        ');
        $insert->execute([$userId, $codeHash, $expiresAt, $createdAt]);

        require_once __DIR__ . '/login_code_email.php';
        require_once __DIR__ . '/email.php';

        $nombre = trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''));
        $body = generateLoginCodeEmail($nombre !== '' ? $nombre : 'Cliente', $code);
        sendEmail($user['email'], 'Tu código de acceso — Yofi', $body, true);

        return $genericOk;
    } catch (Throwable $e) {
        error_log('requestLoginCode: ' . $e->getMessage());

        return ['success' => false, 'message' => 'Error al procesar la solicitud'];
    }
}

/**
 * Valida un código de acceso y, si es correcto, establece la sesión del usuario.
 */
function verifyLoginCode(string $email, string $code): array
{
    $email = trim($email);
    $code = trim($code);

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $code === '') {
        return ['success' => false, 'message' => 'Datos inválidos'];
    }

    try {
        $pdo = db_rw();
        $stmt = $pdo->prepare('
            SELECT o.id_otp, o.usuario_id, o.code_hash, o.attempts, o.expires_at, u.*
            FROM tbl_login_otp o
            INNER JOIN tbl_usuarios u ON u.id_usuario = o.usuario_id
            WHERE u.email = ? AND o.consumed_at IS NULL
            ORDER BY o.id_otp DESC LIMIT 1
        ');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || strtotime((string) $row['expires_at']) < time()) {
            return ['success' => false, 'message' => 'Código inválido o vencido. Pedí uno nuevo.'];
        }

        if ((int) $row['attempts'] >= 5) {
            $pdo->prepare('UPDATE tbl_login_otp SET consumed_at = NOW() WHERE id_otp = ?')->execute([$row['id_otp']]);

            return ['success' => false, 'message' => 'Demasiados intentos. Pedí un código nuevo.'];
        }

        if (!password_verify($code, (string) $row['code_hash'])) {
            $pdo->prepare('UPDATE tbl_login_otp SET attempts = attempts + 1 WHERE id_otp = ?')->execute([$row['id_otp']]);

            return ['success' => false, 'message' => 'Código incorrecto.'];
        }

        $pdo->prepare('UPDATE tbl_login_otp SET consumed_at = NOW() WHERE id_otp = ?')->execute([$row['id_otp']]);
        $pdo->prepare('UPDATE tbl_usuarios SET fecha_ultimo_acceso = NOW() WHERE id_usuario = ?')->execute([(int) $row['usuario_id']]);

        establishUserSession($row, false);
        syncUserOrdersByEmail((int) $row['usuario_id']);

        return [
            'success' => true,
            'user' => [
                'id' => (int) $row['usuario_id'],
                'email' => $row['email'],
                'name' => trim(($row['nombre'] ?? '') . ' ' . ($row['apellido'] ?? '')),
            ],
        ];
    } catch (Throwable $e) {
        error_log('verifyLoginCode: ' . $e->getMessage());

        return ['success' => false, 'message' => 'Error al procesar la solicitud'];
    }
}
