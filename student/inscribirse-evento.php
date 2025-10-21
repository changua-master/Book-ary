<?php
/**
 * Inscribirse a un Evento
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/Event.php';

AuthMiddleware::requireStudent('../public/login.php');

$eventoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$userId = AuthMiddleware::id();

if (!$eventoId) {
    AuthMiddleware::setFlash('error', 'Evento inválido');
    header('Location: eventos.php');
    exit();
}

$eventModel = new Event($conexion);
$result = $eventModel->inscribir($eventoId, $userId);

if ($result['success']) {
    AuthMiddleware::setFlash('success', '¡Inscripción exitosa! Te esperamos en el evento.');
} else {
    AuthMiddleware::setFlash('error', $result['message']);
}

header('Location: eventos.php');
exit();