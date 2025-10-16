<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../app/Models/Loan.php';

// Verificar autenticación
AuthMiddleware::requireAdmin('../../public/login.php');

// Obtener ID del préstamo
$loanId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$loanId) {
    AuthMiddleware::setFlash('error', 'ID de préstamo inválido');
    header('Location: index.php');
    exit();
}

// Registrar devolución
$loanModel = new Loan($conexion);
$result = $loanModel->returnBook($loanId);

if ($result['success']) {
    AuthMiddleware::setFlash('success', '¡Devolución registrada exitosamente!');
} else {
    AuthMiddleware::setFlash('error', $result['message']);
}

header('Location: index.php');
exit();