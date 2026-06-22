<?php
// Archivo de verificación de sesión para el panel de administración
// Incluir este archivo al inicio de todas las páginas del admin

// CRÍTICO: Inicializar sesión ANTES de verificar autenticación
// Esto asegura que la sesión esté disponible para la verificación
if (file_exists("include/session_init.php")) {
    require_once("include/session_init.php");
} else {
    // Fallback si session_init.php no existe
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                   (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        ini_set('session.cookie_secure', $isHttps ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Lax');
        session_name('CASAADMINSESSID');
        session_start();
    }
}

require_once("admin_security.php");

// Verificar autenticación
// Usar output buffering para permitir redirects incluso si hay output previo
if (!ob_get_level()) {
    ob_start();
}

requireAdminAuth();

// En todas las solicitudes POST exigir y validar CSRF, salvo endpoints explícitamente públicos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Excluir cambiopubli.php de validación CSRF temporalmente
    $current_script = basename($_SERVER['SCRIPT_NAME']);
    if ($current_script !== 'cambiopubli.php') {
        $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : (isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : '');
        if (!validateAdminCSRFToken($csrfToken)) {
            logSuspiciousAdminActivity('POST without valid CSRF');
            http_response_code(403);
            exit('CSRF token inválido');
        }
    }
}

// Verificar si la sesión no ha expirado (8 horas)
$session_timeout = 8 * 60 * 60; // 8 horas en segundos
if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > $session_timeout) {
    // Sesión expirada
    logSuspiciousAdminActivity('Session expired');
    adminLogout();
    header('Location: index.php?error=session_expired');
    exit();
}

// Verificar si la IP ha cambiado (protección contra session hijacking)
if (isset($_SESSION['admin_ip']) && $_SESSION['admin_ip'] !== $_SERVER['REMOTE_ADDR']) {
    // IP cambiada, posible session hijacking
    logSuspiciousAdminActivity('IP address changed - possible session hijacking');
    adminLogout();
    header('Location: index.php?error=unauthorized');
    exit();
}

// Renovar tiempo de sesión
$_SESSION['admin_login_time'] = time();

// Obtener información del usuario actual
$current_admin_id = $_SESSION['idUsuarioAdminSID'];
$current_admin_user = $_SESSION['idUsuarioAdminSUser'];

// Función para obtener información del usuario actual
function getCurrentAdminInfo() {
    global $con, $current_admin_id;
    
    $sql = "SELECT id, usuadmin, publicado FROM tbl_admin WHERE id = ? AND publicado = 1";
    $stmt = mysqli_prepare($con, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $current_admin_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && $result->num_rows > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    
    return false;
}

// Verificar que el usuario aún existe y está activo en la base de datos
$admin_info = getCurrentAdminInfo();
if (!$admin_info) {
    // Usuario no existe o no está activo
    logSuspiciousAdminActivity('User account not found or inactive');
    adminLogout();
    header('Location: index.php?error=unauthorized');
    exit();
}
?>
