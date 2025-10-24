<?php
/**
 * Procesar Cambio de Contraseña (Estudiante)
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

// Verificar autenticación
AuthMiddleware::requireStudent('../public/login.php');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil.php');
    exit();
}

// Obtener datos
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$userId = AuthMiddleware::id();

// Validaciones básicas
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    AuthMiddleware::setFlash('error', 'Todos los campos son obligatorios');
    header('Location: perfil.php');
    exit();
}

if (strlen($newPassword) < 6) {
    AuthMiddleware::setFlash('error', 'La nueva contraseña debe tener al menos 6 caracteres');
    header('Location: perfil.php');
    exit();
}

if ($newPassword !== $confirmPassword) {
    AuthMiddleware::setFlash('error', 'Las contraseñas nuevas no coinciden');
    header('Location: perfil.php');
    exit();
}

// Cambiar contraseña
$authController = new AuthController($conexion);
$result = $authController->changePassword($userId, $currentPassword, $newPassword, $confirmPassword);

if ($result['success']) {
    AuthMiddleware::setFlash('success', '¡Contraseña actualizada exitosamente!');
} else {
    AuthMiddleware::setFlash('error', $result['message']);
}

header('Location: perfil.php');
exit();