<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../app/Models/Book.php';

// Verificar autenticación
AuthMiddleware::requireAdmin('../../public/login.php');

// Obtener ID
$bookId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$bookId) {
    AuthMiddleware::setFlash('error', 'ID de libro inválido');
    header('Location: index.php');
    exit();
}

// Eliminar libro
$bookModel = new Book($conexion);
$result = $bookModel->delete($bookId);

if ($result['success']) {
    AuthMiddleware::setFlash('success', 'Libro eliminado exitosamente');
} else {
    AuthMiddleware::setFlash('error', $result['message']);
}

header('Location: index.php');
exit();