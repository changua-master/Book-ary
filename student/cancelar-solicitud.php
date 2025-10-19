<?php
/**
 * Cancelar Solicitud de Préstamo
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/LoanRequest.php';

// Verificar autenticación
AuthMiddleware::requireStudent('../public/login.php');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: solicitudes.php');
    exit();
}

$requestId = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
$userId = AuthMiddleware::id();

if (!$requestId) {
    AuthMiddleware::setFlash('error', 'Solicitud inválida');
    header('Location: solicitudes.php');
    exit();
}

// Cancelar solicitud
$requestModel = new LoanRequest($conexion);
$result = $requestModel->cancel($requestId, $userId);

if ($result['success']) {
    AuthMiddleware::setFlash('success', 'Solicitud cancelada correctamente');
} else {
    AuthMiddleware::setFlash('error', $result['message']);
}

header('Location: solicitudes.php');
exit();