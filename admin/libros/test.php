<?php
/**
 * Archivo de prueba para diagnosticar problemas
 */

// Mostrar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test de Diagnóstico</h1>";

// Test 1: Rutas
echo "<h2>1. Verificación de Rutas</h2>";
echo "<p>__DIR__: " . __DIR__ . "</p>";
echo "<p>__FILE__: " . __FILE__ . "</p>";

// Test 2: Archivos de configuración
echo "<h2>2. Archivos de Configuración</h2>";
$configApp = __DIR__ . '/../../config/app.php';
$configDB = __DIR__ . '/../../config/database.php';

echo "<p>config/app.php existe: " . (file_exists($configApp) ? '✓ SÍ' : '✗ NO') . "</p>";
echo "<p>config/database.php existe: " . (file_exists($configDB) ? '✓ SÍ' : '✗ NO') . "</p>";

// Test 3: Cargar configuración
echo "<h2>3. Cargando Configuración...</h2>";
try {
    require_once $configApp;
    echo "<p>✓ config/app.php cargado correctamente</p>";
    echo "<p>APP_NAME: " . (defined('APP_NAME') ? APP_NAME : 'NO DEFINIDO') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error al cargar app.php: " . $e->getMessage() . "</p>";
}

try {
    require_once $configDB;
    echo "<p>✓ config/database.php cargado correctamente</p>";
    echo "<p>Conexión BD: " . (isset($conexion) ? '✓ OK' : '✗ FALLO') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error al cargar database.php: " . $e->getMessage() . "</p>";
}

// Test 4: Middleware
echo "<h2>4. Middleware</h2>";
$middleware = __DIR__ . '/../../app/Middleware/AuthMiddleware.php';
echo "<p>AuthMiddleware existe: " . (file_exists($middleware) ? '✓ SÍ' : '✗ NO') . "</p>";

try {
    require_once $middleware;
    echo "<p>✓ AuthMiddleware cargado</p>";
    
    // Intentar iniciar sesión
    AuthMiddleware::startSession();
    echo "<p>✓ Sesión iniciada</p>";
    echo "<p>Usuario autenticado: " . (AuthMiddleware::check() ? '✓ SÍ' : '✗ NO') . "</p>";
    
    if (AuthMiddleware::check()) {
        echo "<p>Username: " . AuthMiddleware::username() . "</p>";
        echo "<p>Role: " . AuthMiddleware::role() . "</p>";
        echo "<p>Es Admin: " . (AuthMiddleware::isAdmin() ? '✓ SÍ' : '✗ NO') . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test 5: Modelo Book
echo "<h2>5. Modelo Book</h2>";
$modelBook = __DIR__ . '/../../app/Models/Book.php';
echo "<p>Book.php existe: " . (file_exists($modelBook) ? '✓ SÍ' : '✗ NO') . "</p>";

try {
    require_once $modelBook;
    echo "<p>✓ Book.php cargado</p>";
    
    if (isset($conexion)) {
        $bookModel = new Book($conexion);
        echo "<p>✓ Modelo Book instanciado</p>";
        
        $books = $bookModel->all();
        echo "<p>Total de libros: " . count($books) . "</p>";
        
        if (count($books) > 0) {
            echo "<h3>Primeros 3 libros:</h3>";
            echo "<ul>";
            foreach (array_slice($books, 0, 3) as $book) {
                echo "<li>" . htmlspecialchars($book['titulo']) . " - " . htmlspecialchars($book['autor']) . "</li>";
            }
            echo "</ul>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test 6: CSS
echo "<h2>6. Assets</h2>";
$cssPath = __DIR__ . '/../../public/assets/css/bookary.css';
echo "<p>CSS existe: " . (file_exists($cssPath) ? '✓ SÍ' : '✗ NO') . "</p>";
echo "<p>Ruta CSS: $cssPath</p>";

echo "<hr>";
echo "<h2>✓ Test Completo</h2>";
echo "<p><a href='index.php'>Volver a index.php</a></p>";
?>