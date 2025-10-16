<?php
/**
 * Logout
 * Cierra la sesión del usuario y lo redirige al index
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

// Crear instancia del controlador
$authController = new AuthController($conexion);

// Cerrar sesión
$result = $authController->logout();

// Redirigir
header('Location: ' . $result['redirect']);
exit();