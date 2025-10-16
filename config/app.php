<?php
/**
 * Configuración General de la Aplicación
 */

// Cargar variables de entorno
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Configuración de la aplicación
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Bookary');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', ($_ENV['APP_DEBUG'] ?? 'false') === 'true');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');

// Configuración de zona horaria
date_default_timezone_set($_ENV['TIMEZONE'] ?? 'America/Bogota');

// Configuración de sesión
define('SESSION_LIFETIME', (int)($_ENV['SESSION_LIFETIME'] ?? 7200));
define('SESSION_NAME', $_ENV['SESSION_NAME'] ?? 'bookary_session');

// Configuración de errores
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../storage/logs/error.log');
}

// Rutas de la aplicación
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEWS_PATH', APP_PATH . '/Views');

// URLs base
define('BASE_URL', rtrim(APP_URL, '/'));
define('ASSETS_URL', BASE_URL . '/assets');

// Roles de usuario
define('ROLE_ADMIN', 'administrador');
define('ROLE_STUDENT', 'estudiante');

// Configuración de archivos
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

// Configuración de préstamos
define('DEFAULT_LOAN_DAYS', 15);
define('MAX_ACTIVE_LOANS', 3);

/**
 * Función helper para obtener URL completa
 */
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Función helper para obtener URL de assets
 */
function asset($path = '') {
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Función helper para redireccionar
 */
function redirect($path = '', $statusCode = 302) {
    header('Location: ' . url($path), true, $statusCode);
    exit();
}

/**
 * Función helper para sanitizar input
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}