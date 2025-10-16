<?php
/**
 * Bootstrap - Inicialización del sistema
 * Carga todas las configuraciones necesarias
 * Incluir este archivo al inicio de cada script
 */

// Cargar configuración de la aplicación
require_once __DIR__ . '/app.php';

// Cargar base de datos
require_once __DIR__ . '/database.php';

// Cargar Middleware
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

// Cargar Helpers
if (file_exists(__DIR__ . '/../app/Helpers/helpers.php')) {
    require_once __DIR__ . '/../app/Helpers/helpers.php';
}

// Definir constantes si no existen (valores por defecto)
if (!defined('SESSION_NAME')) {
    define('SESSION_NAME', 'bookary_session');
}

if (!defined('SESSION_LIFETIME')) {
    define('SESSION_LIFETIME', 7200);
}

if (!defined('DEFAULT_LOAN_DAYS')) {
    define('DEFAULT_LOAN_DAYS', 15);
}

if (!defined('MAX_ACTIVE_LOANS')) {
    define('MAX_ACTIVE_LOANS', 3);
}

// Iniciar sesión automáticamente si no está iniciada
AuthMiddleware::startSession();