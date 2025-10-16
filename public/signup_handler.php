<?php
/**
 * Manejador de Registro
 * Procesa el formulario de registro de nuevos usuarios
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.php');
    exit();
}

// Obtener datos del formulario
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

// Validar que no estén vacíos
if (empty($username) || empty($password) || empty($passwordConfirm)) {
    AuthMiddleware::setFlash('error', 'Por favor completa todos los campos');
    header('Location: signup.php');
    exit();
}

// Crear instancia del controlador
$authController = new AuthController($conexion);

// Intentar registro
$result = $authController->register($username, $password, $passwordConfirm);

// Procesar resultado
if ($result['success']) {
    // Registro exitoso - redirigir a login con mensaje
    AuthMiddleware::setFlash('success', $result['message']);
    header('Location: login.php');
    exit();
} else {
    // Registro fallido - mostrar error
    AuthMiddleware::setFlash('error', $result['message']);
    header('Location: signup.php');
    exit();
}