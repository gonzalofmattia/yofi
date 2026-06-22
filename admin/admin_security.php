<?php
// Archivo de configuración de seguridad para el panel de administración Casa de Insecticidas
// Este archivo debe ser incluido en todas las páginas del admin

// Configuraciones de seguridad PHP
if (defined('IS_LOCAL') && IS_LOCAL) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'Off');
    ini_set('log_errors', 'On');
}
// Usar ruta local dentro de /admin para no exponer fuera del scope
$adminLogDir = __DIR__ . DIRECTORY_SEPARATOR . 'logs';
if (!is_dir($adminLogDir)) {
    @mkdir($adminLogDir, 0700, true);
}
ini_set('error_log', $adminLogDir . DIRECTORY_SEPARATOR . 'admin_errors.log');

// Headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// Detectar entorno (si no está definido, usar detección automática)
if (!defined('IS_LOCAL') && !defined('IS_PRODUCTION')) {
    $hostname = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
    $isLocal = (
        $hostname === 'localhost' ||
        $hostname === '127.0.0.1' ||
        strpos($hostname, '.local') !== false ||
        strpos($hostname, '.test') !== false ||
        strpos($hostname, 'localhost:') !== false
    );
    define('IS_LOCAL', $isLocal);
    define('IS_PRODUCTION', !$isLocal);
}

// CSP según el entorno (solo si no se han enviado headers todavía)
// Para páginas que ya tienen HTML, no aplicar CSP restrictiva
if (!headers_sent() && !isset($_GET['no_csp'])) {
    if (defined('IS_LOCAL') && IS_LOCAL) {
        // CSP permisiva para desarrollo local (permitir TinyMCE y otros recursos externos)
        header("Content-Security-Policy: default-src * 'unsafe-inline' 'unsafe-eval' data: blob:; style-src * 'unsafe-inline'; font-src * data:; script-src * 'unsafe-inline' 'unsafe-eval'; connect-src *; frame-src *; img-src * data: blob:; worker-src * blob:; object-src 'none'; base-uri 'self'; frame-ancestors 'none'");
    } else {
        // CSP más permisiva para producción para evitar problemas con estilos
        $csp = "default-src 'self' *; " .
               "style-src 'self' 'unsafe-inline' * https://fonts.googleapis.com https://cdn.tiny.cloud https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
               "font-src 'self' data: https://fonts.gstatic.com https://fonts.googleapis.com *; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' * https://cdn.tiny.cloud https://ajax.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
               "connect-src 'self' * https://cdn.tiny.cloud https://api.tiny.cloud; " .
               "frame-src 'self' * https://cdn.tiny.cloud; " .
               "img-src 'self' data: blob: https: *; " .
               "worker-src 'self' blob: *; " .
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "frame-ancestors 'none';";
        header("Content-Security-Policy: " . $csp);
    }
}

// Configuraciones de sesión seguras (solo si la sesión no está iniciada)
// Estas configuraciones deben hacerse ANTES de session_start()
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    // Detectar si HTTPS
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
               (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    ini_set('session.cookie_secure', $isHttps ? 1 : 0);
    ini_set('session.use_strict_mode', 1);
    // Usar 'Lax' en lugar de 'Strict' para permitir redirects después del login
    ini_set('session.cookie_samesite', 'Lax');
}

// Asegurar que la sesión esté activa (pero no iniciarla si ya está iniciada)
// La sesión ya debería estar iniciada desde includes.php
if (session_status() === PHP_SESSION_NONE) {
    session_name('CASAADMINSESSID');
    session_start();
    
    // Generar nonce para scripts y estilos inline (después de iniciar sesión)
    if (!headers_sent() && !isset($_SESSION['csp_nonce'])) {
        $nonce = base64_encode(random_bytes(16));
        $_SESSION['csp_nonce'] = $nonce;
    }
}

// Función para verificar si el usuario está autenticado
function isAdminAuthenticated() {
    return isset($_SESSION['adminValido']) && $_SESSION['adminValido'] === 'si' && 
           isset($_SESSION['idUsuarioAdminSID']) && $_SESSION['idUsuarioAdminSID'] > 0;
}

// Función para verificar permisos de administrador
function requireAdminAuth() {
    if (!isAdminAuthenticated()) {
        // Limpiar cualquier output previo
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Limpiar sesión
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = array();
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            }
            session_destroy();
        }
        
        // Redirigir al login
        header('Location: index.php?error=unauthorized');
        exit();
    }
}

// Función para limpiar entrada de usuario
function cleanAdminInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    return $data;
}

// Función para validar token CSRF
function validateAdminCSRFToken($token) {
    if (!isset($_SESSION['admin_csrf_token']) || !hash_equals($_SESSION['admin_csrf_token'], (string)$token)) {
        return false;
    }
    return true;
}

// Función para generar token CSRF
function generateAdminCSRFToken() {
    // Rotar token por sesión o cada 2h si se quiere
    if (!isset($_SESSION['admin_csrf_token'])) {
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['admin_csrf_generated_at'] = time();
    }
    return $_SESSION['admin_csrf_token'];
}

// Función para prevenir inyección SQL
function preventSQLInjection($string) {
    // No mutamos agresivamente; confiamos en consultas preparadas.
    // Esta función solo normaliza espacios.
    return trim($string);
}

// Función para registrar intentos de acceso sospechoso
function logSuspiciousAdminActivity($activity, $ip = null) {
    $ip = $ip ?: $_SERVER['REMOTE_ADDR'];
    $user = isset($_SESSION['idUsuarioAdminSUser']) ? $_SESSION['idUsuarioAdminSUser'] : 'unknown';
    $log = date('Y-m-d H:i:s') . " - IP: $ip - User: $user - Activity: $activity\n";
    $path = __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'admin_security.log';
    error_log($log, 3, $path);
}

// Función para limpiar sesión al logout
function adminLogout() {
    // Limpiar todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destruir la sesión
    session_destroy();
}

// Función para verificar si la solicitud es AJAX
function isAdminAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

// Función para validar archivos subidos
function validateUploadedFile($file, $allowedTypes = array('jpg', 'jpeg', 'png', 'gif'), $maxSize = 5242880) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }
    
    // Verificar tamaño
    if ($file['size'] > $maxSize) {
        return false;
    }
    
    // Verificar tipo de archivo
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);
    
    if (!in_array($extension, $allowedTypes)) {
        return false;
    }
    
    // Verificar contenido del archivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimes = array(
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    );
    
    if (!isset($allowedMimes[$extension]) || $allowedMimes[$extension] !== $mimeType) {
        return false;
    }
    
    return true;
}

// Función para generar contraseña segura
function generateSecurePassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    
    return $password;
}

// Función para verificar fortaleza de contraseña
function isPasswordStrong($password) {
    // Mínimo 8 caracteres
    if (strlen($password) < 8) {
        return false;
    }
    
    // Debe contener al menos una letra mayúscula
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    
    // Debe contener al menos una letra minúscula
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    
    // Debe contener al menos un número
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    
    // Debe contener al menos un carácter especial
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $password)) {
        return false;
    }
    
    return true;
}

// Rate limiting simple por IP y usuario (en memoria de sesión + archivo)
function rateLimitKey($username) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return hash('sha256', strtolower((string)$username) . '|' . $ip);
}

function registerLoginAttempt($username, $success) {
    $key = rateLimitKey($username);
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = array();
    }
    $attempts = $_SESSION['login_attempts'][$key] ?? array('count' => 0, 'last' => 0, 'lock_until' => 0);
    $now = time();
    if ($success) {
        $attempts = array('count' => 0, 'last' => $now, 'lock_until' => 0);
    } else {
        if ($attempts['lock_until'] > $now) {
            // Sigue bloqueado
        } else {
            $attempts['count'] = ($attempts['last'] + 900 < $now) ? 1 : $attempts['count'] + 1; // ventana de 15min
            $attempts['last'] = $now;
            if ($attempts['count'] >= 5) {
                $attempts['lock_until'] = $now + 900; // 15 min de lockout
            }
        }
    }
    $_SESSION['login_attempts'][$key] = $attempts;
}

function isLoginLocked($username) {
    $key = rateLimitKey($username);
    if (!isset($_SESSION['login_attempts'][$key])) return false;
    $now = time();
    return $_SESSION['login_attempts'][$key]['lock_until'] > $now;
}

?>
