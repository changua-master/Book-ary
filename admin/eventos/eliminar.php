<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../app/Models/Event.php';

AuthMiddleware::requireAdmin('../../public/login.php');

$eventoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$eventoId) {
    AuthMiddleware::setFlash('error', 'ID de evento inválido');
    header('Location: index.php');
    exit();
}

$eventModel = new Event($conexion);
$result = $eventModel->delete($eventoId);

if ($result['success']) {
    AuthMiddleware::setFlash('success', 'Evento eliminado exitosamente');
} else {
    AuthMiddleware::setFlash('error', $result['message']);
}

header('Location: index.php');
exit();