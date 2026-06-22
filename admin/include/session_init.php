<?php
/**
 * Inicialización centralizada de sesión
 * Este archivo debe ser incluido ANTES de cualquier otro include
 * para asegurar que la sesión se configure correctamente
 */

if (!function_exists('initAdminSession')) {
    function initAdminSession() {
        // Solo configurar si la sesión no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            // Configurar parámetros de cookie de sesión ANTES de iniciar
            ini_set('session.cookie_httponly', 1);
            
            // Detectar HTTPS
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                       (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
            ini_set('session.cookie_secure', $isHttps ? 1 : 0);
            
            // Usar 'Lax' para permitir redirects después del login
            ini_set('session.cookie_samesite', 'Lax');
            
            ini_set('session.use_strict_mode', 1);
            
            // Iniciar sesión con el nombre correcto
            session_name('CASAADMINSESSID');
            session_start();
        }
    }
}

// Inicializar sesión automáticamente cuando se incluye este archivo
initAdminSession();
?>

