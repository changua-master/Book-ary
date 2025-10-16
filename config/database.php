<?php
/**
 * Configuración de Base de Datos
 * Lee las variables de entorno y establece la conexión
 */

// Cargar variables de entorno si existe el archivo
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Constantes de conexión
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'bookary');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

// Crear conexión
try {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar conexión
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }
    
    // Establecer charset
    if (!$conexion->set_charset(DB_CHARSET)) {
        throw new Exception("Error al establecer charset: " . $conexion->error);
    }
    
    // Modo de errores (solo en desarrollo)
    if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }
    
} catch (Exception $e) {
    // Log del error
    error_log($e->getMessage());
    
    // En producción, mostrar mensaje genérico
    if (!isset($_ENV['APP_DEBUG']) || $_ENV['APP_DEBUG'] !== 'true') {
        die("Error al conectar con la base de datos. Por favor, contacta al administrador.");
    } else {
        die("Error de Base de Datos: " . $e->getMessage());
    }
}

/**
 * Función helper para cerrar la conexión
 */
function closeConnection() {
    global $conexion;
    if ($conexion) {
        $conexion->close();
    }
}

// Registrar función de cierre al finalizar el script
register_shutdown_function('closeConnection');

return $conexion;