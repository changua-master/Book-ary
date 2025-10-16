<?php
/**
 * Middleware de Autenticación
 * Verifica que el usuario esté autenticado y tenga los permisos necesarios
 */

class AuthMiddleware {
    
    /**
     * Iniciar sesión si no está iniciada
     */
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionName = defined('SESSION_NAME') ? SESSION_NAME : 'bookary_session';
            session_name($sessionName);
            session_start();
            
            // Regenerar ID de sesión periódicamente (seguridad)
            if (!isset($_SESSION['last_regeneration'])) {
                $_SESSION['last_regeneration'] = time();
            } else if (time() - $_SESSION['last_regeneration'] > 1800) { // cada 30 min
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            }
        }
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public static function check() {
        self::startSession();
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }
    
    /**
     * Obtener el ID del usuario autenticado
     */
    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Obtener el nombre de usuario
     */
    public static function username() {
        return $_SESSION['username'] ?? null;
    }
    
    /**
     * Obtener el rol del usuario
     */
    public static function role() {
        return $_SESSION['role'] ?? null;
    }
    
    /**
     * Verificar si el usuario es administrador
     */
    public static function isAdmin() {
        return self::check() && strtolower(self::role()) === 'administrador';
    }
    
    /**
     * Verificar si el usuario es estudiante
     */
    public static function isStudent() {
        return self::check() && strtolower(self::role()) === 'estudiante';
    }
    
    /**
     * Requerir autenticación (redirige si no está autenticado)
     */
    public static function requireAuth($redirectTo = '../public/login.php') {
        self::startSession();
        if (!self::check()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . $redirectTo);
            exit();
        }
    }
    
    /**
     * Requerir rol de administrador
     */
    public static function requireAdmin($redirectTo = '../public/index.php') {
        self::requireAuth();
        if (!self::isAdmin()) {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta sección.';
            header('Location: ' . $redirectTo);
            exit();
        }
    }
    
    /**
     * Requerir rol de estudiante
     */
    public static function requireStudent($redirectTo = '../public/index.php') {
        self::requireAuth();
        if (!self::isStudent()) {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta sección.';
            header('Location: ' . $redirectTo);
            exit();
        }
    }
    
    /**
     * Verificar si el usuario es el propietario del recurso
     */
    public static function owns($resourceUserId) {
        return self::check() && self::id() == $resourceUserId;
    }
    
    /**
     * Cerrar sesión
     */
    public static function logout() {
        self::startSession();
        
        // Limpiar todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la cookie de sesión
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destruir la sesión
        session_destroy();
    }
    
    /**
     * Establecer mensaje flash
     */
    public static function setFlash($key, $message) {
        self::startSession();
        $_SESSION['flash'][$key] = $message;
    }
    
    /**
     * Obtener y eliminar mensaje flash
     */
    public static function getFlash($key) {
        self::startSession();
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }
    
    /**
     * Verificar si hay un mensaje flash
     */
    public static function hasFlash($key) {
        self::startSession();
        return isset($_SESSION['flash'][$key]);
    }
}