<?php
/**
 * Aprobar Solicitud de Préstamo (Administrador)
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../app/Models/LoanRequest.php';

// Verificar autenticación
AuthMiddleware::requireAdmin('../../public/login.php');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$requestId = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
$notes = trim($_POST['notes'] ?? '');
$adminId = AuthMiddleware::id();

if (!$requestId) {
    AuthMiddleware::setFlash('error', 'Solicitud inválida');
    header('Location: index.php');
    exit();
}

// Aprobar solicitud
$requestModel = new LoanRequest($conexion);
$result = $requestModel->approve($requestId, $adminId, $notes ?: null);

if ($result['success']) {
    AuthMiddleware::setFlash('success', 'Solicitud aprobada y préstamo creado exitosamente');
} else {
    AuthMiddleware::setFlash('error', $result['message']);
}

header('Location: index.php');
exit();