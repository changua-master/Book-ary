<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../app/Models/Event.php';

AuthMiddleware::requireAdmin('../../public/login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Recolectar y validar datos
$data = [
    'titulo' => trim($_POST['titulo'] ?? ''),
    'descripcion' => trim($_POST['descripcion'] ?? ''),
    'fecha_evento' => $_POST['fecha_evento'] ?? '',
    'hora_inicio' => $_POST['hora_inicio'] ?? '',
    'hora_fin' => trim($_POST['hora_fin'] ?? '') ?: null,
    'ubicacion' => trim($_POST['ubicacion'] ?? '') ?: null,
    'cupo_maximo' => filter_input(INPUT_POST, 'cupo_maximo', FILTER_VALIDATE_INT) ?: null,
    'id_admin_creador' => AuthMiddleware::id()
];

// Validaciones básicas
if (empty($data['titulo']) || empty($data['fecha_evento']) || empty($data['hora_inicio'])) {
    AuthMiddleware::setFlash('error', 'El título, fecha y hora de inicio son obligatorios');
    header('Location: crear.php');
    exit();
}

// Validar que la fecha no sea pasada
if (strtotime($data['fecha_evento']) < strtotime('today')) {
    AuthMiddleware::setFlash('error', 'La fecha del evento no puede ser anterior a hoy');
    header('Location: crear.php');
    exit();
}

// Crear evento
$eventModel = new Event($conexion);
$result = $eventModel->create($data);

if ($result['success']) {
    AuthMiddleware::setFlash('success', '¡Evento creado exitosamente!');
    header('Location: index.php');
} else {
    AuthMiddleware::setFlash('error', $result['message']);
    header('Location: crear.php');
}
exit();