<?php
/**
 * Manejador de Login
 * Procesa el formulario de inicio de sesión
 */

// Cargar bootstrap (incluye config, database, middleware)
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

// Obtener datos del formulario
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validar que no estén vacíos
if (empty($username) || empty($password)) {
    AuthMiddleware::setFlash('error', 'Por favor completa todos los campos');
    header('Location: login.php');
    exit();
}

// Crear instancia del controlador
$authController = new AuthController($conexion);

// Intentar login
$result = $authController->login($username, $password);

// Procesar resultado
if ($result['success']) {
    // Login exitoso - redirigir según el resultado
    header('Location: ' . $result['redirect']);
    exit();
} else {
    // Login fallido - mostrar error
    AuthMiddleware::setFlash('error', $result['message']);
    header('Location: login.php');
    exit();
}