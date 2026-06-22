<?php
/**
 * SecurityManager - Gestión centralizada de seguridad
 * Maneja autenticación, validación y protección contra ataques
 */

class SecurityManager {
    private $db;
    private $maxLoginAttempts = 5;
    private $lockoutTime = 900; // 15 minutos
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Autenticar usuario con verificación de contraseña hasheada
     */
    public function authenticate($username, $password) {
        // Verificar intentos de login
        if ($this->isAccountLocked($username)) {
            $this->logSuspiciousActivity("Account locked - too many attempts", $username);
            return ['success' => false, 'message' => 'Cuenta bloqueada temporalmente'];
        }
        
        // Buscar usuario
        $user = $this->getUserByUsername($username);
        if (!$user) {
            $this->recordFailedAttempt($username);
            return ['success' => false, 'message' => 'Credenciales inválidas'];
        }
        
        // Verificar contraseña - usar 'password' si es hash válido, sino usar 'clave'
        $storedPassword = null;
        $usePassword = false;
        
        // Priorizar 'password' si es un hash bcrypt/argon2 Y si la verificación funciona
        if (isset($user['password']) && !empty($user['password']) && 
            (strpos($user['password'], '$2y$') === 0 || strpos($user['password'], '$argon2') === 0)) {
            // Verificar primero si el hash de password funciona
            if (password_verify($password, $user['password'])) {
                $storedPassword = $user['password'];
                $usePassword = true;
            }
        }
        
        // Si password no funcionó o no existe, usar 'clave'
        if (!$usePassword && isset($user['clave']) && !empty($user['clave'])) {
            $storedPassword = $user['clave'];
        }
        
        if (!$storedPassword) {
            $this->recordFailedAttempt($username);
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
        
        if (!$this->verifyPassword($password, $storedPassword, $username)) {
            $this->recordFailedAttempt($username);
            // Debug: loguear qué contraseña se intentó y qué se encontró
            error_log("Login fallido para usuario: $username. Password recibido: " . substr($password, 0, 3) . "...");
            return ['success' => false, 'message' => 'Credenciales inválidas'];
        }
        
        // Si el login fue exitoso y usamos 'clave' en texto plano, actualizar AMBOS campos con hash
        // Esto asegura que la contraseña quede hasheada de forma segura para futuros logins
        if (!$usePassword && isset($user['clave']) && !empty($user['clave'])) {
            // Verificar si 'clave' está en texto plano (no es hash)
            $claveEsTextoPlano = (strpos($user['clave'], '$2y$') !== 0 && strpos($user['clave'], '$argon2') !== 0);
            
            // Si 'clave' es texto plano y coincide con la contraseña ingresada, hashear ambos campos
            if ($claveEsTextoPlano && $password === $user['clave']) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                // Actualizar AMBOS campos con el hash para máxima seguridad
                $sql = "UPDATE tbl_admin SET clave = ?, password = ? WHERE usuadmin = ?";
                $stmt = mysqli_prepare($this->db, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'sss', $hash, $hash, $username);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            } 
            // Si 'clave' ya está hasheada pero 'password' no funciona o está vacío, asegurar que 'password' también esté hasheado
            elseif (!$claveEsTextoPlano && (!isset($user['password']) || empty($user['password']) || 
                    (strpos($user['password'], '$2y$') !== 0 && strpos($user['password'], '$argon2') !== 0))) {
                // Usar el hash de 'clave' para 'password' también
                $sql = "UPDATE tbl_admin SET password = ? WHERE usuadmin = ?";
                $stmt = mysqli_prepare($this->db, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'ss', $user['clave'], $username);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
        
        // Login exitoso
        $this->clearFailedAttempts($username);
        $this->createSecureSession($user);
        $this->logSuspiciousActivity("Successful login", $username);
        
        return ['success' => true, 'user' => $user];
    }
    
    /**
     * Verificar contraseña (compatible con texto plano y bcrypt)
     */
    private function verifyPassword($password, $storedHash, $username = null) {
        // Si es bcrypt/argon2, usar password_verify
        if (strpos($storedHash, '$2y$') === 0 || strpos($storedHash, '$argon2') === 0) {
            return password_verify($password, $storedHash);
        }
        
        // Si es texto plano (migración), verificar y actualizar
        if ($password === $storedHash) {
            if ($username) {
                $this->upgradePassword($username, $password);
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Actualizar contraseña de texto plano a bcrypt
     */
    private function upgradePassword($username, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE tbl_admin SET clave = ? WHERE usuadmin = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ss', $hash, $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    
    /**
     * Crear sesión segura
     */
    private function createSecureSession($user) {
        // Asegurar que hay una sesión activa antes de regenerar
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        // Regenerar ID de sesión
        session_regenerate_id(true);
        
        // Establecer datos de sesión
        $_SESSION['idUsuarioAdminSUser'] = $user['usuadmin']; // Nombre de usuario para mostrar
        $_SESSION['idUsuarioAdminSID'] = $user['id']; // ID para compatibilidad
        $_SESSION['adminValido'] = 'si';
        $_SESSION['admin_login_time'] = time();
        $_SESSION['admin_ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['admin_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        
        // Generar token CSRF
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
        
        // En PHP 8.2, NO debemos cerrar la sesión aquí
        // session_commit() guarda los datos pero mantiene la sesión abierta
        // Esto es necesario para que la cookie de sesión se envíe correctamente
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Forzar guardado de datos sin cerrar la sesión
            if (function_exists('session_commit')) {
                session_commit();
            }
        }
    }
    
    /**
     * Verificar si la sesión es válida
     */
    public function isSessionValid() {
        if (!isset($_SESSION['adminValido']) || $_SESSION['adminValido'] !== 'si') {
            return false;
        }
        
        // Verificar IP
        if (isset($_SESSION['admin_ip']) && $_SESSION['admin_ip'] !== $_SERVER['REMOTE_ADDR']) {
            $this->logSuspiciousActivity("IP address changed - possible session hijacking");
            return false;
        }
        
        // Verificar User-Agent
        if (isset($_SESSION['admin_user_agent']) && $_SESSION['admin_user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            $this->logSuspiciousActivity("User-Agent changed - possible session hijacking");
            return false;
        }
        
        // Verificar timeout (8 horas)
        if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > 28800) {
            $this->logSuspiciousActivity("Session expired");
            return false;
        }
        
        return true;
    }
    
    /**
     * Obtener usuario por nombre
     */
    private function getUserByUsername($username) {
        // Obtener ambos campos (clave y password) para tener todas las opciones
        // Primero intentar con publicado = 1, si no encuentra, buscar sin restricción
        $sql = "SELECT id, usuadmin, clave, password, email, publicado FROM tbl_admin WHERE usuadmin = ? AND publicado = 1";
        $stmt = mysqli_prepare($this->db, $sql);
        
        if (!$stmt) {
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        // Si no se encontró con publicado = 1, buscar sin esa restricción
        if (!$user) {
            $sql = "SELECT id, usuadmin, clave, password, email, publicado FROM tbl_admin WHERE usuadmin = ?";
            $stmt = mysqli_prepare($this->db, $sql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);
            }
        }
        
        return $user;
    }
    
    /**
     * Verificar si la cuenta está bloqueada
     * Usa sesión en lugar de base de datos para evitar dependencia de tabla
     */
    private function isAccountLocked($username) {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Inicializar array de intentos si no existe
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }
        
        $now = time();
        $usernameKey = md5($username . $_SERVER['REMOTE_ADDR']);
        
        // Limpiar intentos antiguos (más de lockoutTime segundos)
        if (isset($_SESSION['login_attempts'][$usernameKey])) {
            $attempts = $_SESSION['login_attempts'][$usernameKey];
            $lockoutTime = $this->lockoutTime;
            $attempts = array_filter($attempts, function($timestamp) use ($now, $lockoutTime) {
                return ($now - $timestamp) < $lockoutTime;
            });
            $_SESSION['login_attempts'][$usernameKey] = array_values($attempts);
            
            // Verificar si excede el máximo
            return count($_SESSION['login_attempts'][$usernameKey]) >= $this->maxLoginAttempts;
        }
        
        return false;
    }
    
    /**
     * Registrar intento fallido
     * Usa sesión en lugar de base de datos
     */
    private function recordFailedAttempt($username) {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Inicializar array de intentos si no existe
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }
        
        $usernameKey = md5($username . $_SERVER['REMOTE_ADDR']);
        $now = time();
        
        // Agregar intento
        if (!isset($_SESSION['login_attempts'][$usernameKey])) {
            $_SESSION['login_attempts'][$usernameKey] = [];
        }
        
        $_SESSION['login_attempts'][$usernameKey][] = $now;
        
        // Limpiar intentos antiguos
        $lockoutTime = $this->lockoutTime;
        $_SESSION['login_attempts'][$usernameKey] = array_filter(
            $_SESSION['login_attempts'][$usernameKey],
            function($timestamp) use ($now, $lockoutTime) {
                return ($now - $timestamp) < $lockoutTime;
            }
        );
        $_SESSION['login_attempts'][$usernameKey] = array_values($_SESSION['login_attempts'][$usernameKey]);
    }
    
    /**
     * Limpiar intentos fallidos
     * Usa sesión en lugar de base de datos
     */
    private function clearFailedAttempts($username) {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['login_attempts'])) {
            $usernameKey = md5($username . $_SERVER['REMOTE_ADDR']);
            if (isset($_SESSION['login_attempts'][$usernameKey])) {
                unset($_SESSION['login_attempts'][$usernameKey]);
            }
        }
    }
    
    /**
     * Log de actividades sospechosas
     */
    private function logSuspiciousActivity($activity, $username = null) {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0700, true);
        }
        
        $logFile = $logDir . '/security.log';
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user = $username ?? ($_SESSION['idUsuarioAdminSUser'] ?? 'anonymous');
        
        $logEntry = "[$timestamp] IP: $ip | User: $user | Activity: $activity\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Generar token CSRF
     */
    public function generateCSRFToken() {
        if (!isset($_SESSION['admin_csrf_token'])) {
            $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['admin_csrf_token'];
    }
    
    /**
     * Validar token CSRF
     */
    public function validateCSRFToken($token) {
        return isset($_SESSION['admin_csrf_token']) && hash_equals($_SESSION['admin_csrf_token'], $token);
    }
    
    /**
     * Cerrar sesión de forma segura
     */
    public function logout() {
        $this->logSuspiciousActivity("User logout");
        
        // Limpiar sesión
        $_SESSION = array();
        
        // Destruir cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruir sesión
        session_destroy();
    }
}
?>
