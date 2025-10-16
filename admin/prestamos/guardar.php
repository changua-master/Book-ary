<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../app/Models/Loan.php';

// Verificar autenticación
AuthMiddleware::requireAdmin('../../public/login.php');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Obtener y validar datos
$bookId = filter_input(INPUT_POST, 'id_libro', FILTER_VALIDATE_INT);
$userId = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
$returnDate = $_POST['fecha_devolucion'] ?? '';

// Validaciones
if (!$bookId || !$userId || empty($returnDate)) {
    AuthMiddleware::setFlash('error', 'Todos los campos son obligatorios');
    header('Location: crear.php');
    exit();
}

// Validar fecha
if (strtotime($returnDate) <= time()) {
    AuthMiddleware::setFlash('error', 'La fecha de devolución debe ser futura');
    header('Location: crear.php');
    exit();
}

// Crear préstamo
$loanModel = new Loan($conexion);
$result = $loanModel->create($bookId, $userId, $returnDate);

if ($result['success']) {
    AuthMiddleware::setFlash('success', '¡Préstamo registrado exitosamente!');
    header('Location: index.php');
} else {
    AuthMiddleware::setFlash('error', $result['message']);
    header('Location: crear.php');
}
exit();