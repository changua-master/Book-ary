<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../app/Models/Book.php';

// Verificar autenticación
AuthMiddleware::requireAdmin('../../public/login.php');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Obtener ID
$bookId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$bookId) {
    AuthMiddleware::setFlash('error', 'ID de libro inválido');
    header('Location: index.php');
    exit();
}

// Recolectar datos
$data = [
    'titulo' => trim($_POST['titulo'] ?? ''),
    'autor' => trim($_POST['autor'] ?? ''),
    'editorial' => trim($_POST['editorial'] ?? '') ?: null,
    'ano_publicacion' => filter_input(INPUT_POST, 'ano_publicacion', FILTER_VALIDATE_INT) ?: null,
    'isbn' => trim($_POST['isbn'] ?? '') ?: null,
    'ejemplares' => filter_input(INPUT_POST, 'ejemplares', FILTER_VALIDATE_INT) ?? 0,
    'ubicacion' => trim($_POST['ubicacion'] ?? '') ?: null,
    'id_categoria' => filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT)
];

// Validaciones
if (empty($data['titulo']) || empty($data['autor'])) {
    AuthMiddleware::setFlash('error', 'El título y el autor son obligatorios');
    header('Location: editar.php?id=' . $bookId);
    exit();
}

// Actualizar
$bookModel = new Book($conexion);
$result = $bookModel->update($bookId, $data);

if ($result['success']) {
    AuthMiddleware::setFlash('success', '¡Libro actualizado exitosamente!');
    header('Location: index.php');
} else {
    AuthMiddleware::setFlash('error', $result['message']);
    header('Location: editar.php?id=' . $bookId);
}
exit();