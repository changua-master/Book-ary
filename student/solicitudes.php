<?php
/**
 * Manejador de Solicitudes de Préstamo (Estudiante)
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/LoanRequest.php';

// Verificar autenticación
AuthMiddleware::requireStudent('../public/login.php');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: catalogo.php');
    exit();
}

// Obtener datos
$bookId = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
$notes = trim($_POST['notes'] ?? '');
$userId = AuthMiddleware::id();

// Validar
if (!$bookId) {
    AuthMiddleware::setFlash('error', 'Libro inválido');
    header('Location: catalogo.php');
    exit();
}

// Crear solicitud
$requestModel = new LoanRequest($conexion);
$result = $requestModel->create($bookId, $userId, $notes ?: null);

if ($result['success']) {
    AuthMiddleware::setFlash('success', '¡Solicitud enviada! El administrador la revisará pronto.');
    header('Location: solicitudes.php');
} else {
    AuthMiddleware::setFlash('error', $result['message']);
    header('Location: catalogo.php');
}
exit();