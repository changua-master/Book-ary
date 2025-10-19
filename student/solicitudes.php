<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/LoanRequest.php';

// Función helper
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// Verificar autenticación
AuthMiddleware::requireStudent('../public/login.php');

// Obtener información del usuario
$userId = AuthMiddleware::id();
$username = AuthMiddleware::username();

// Obtener solicitudes del usuario
$requestModel = new LoanRequest($conexion);
$pendingRequests = $requestModel->byUser($userId, 'pendiente');
$approvedRequests = $requestModel->byUser($userId, 'aprobada');
$rejectedRequests = $requestModel->byUser($userId, 'rechazada');

// Mensajes
$success = AuthMiddleware::getFlash('success');
$error = AuthMiddleware::getFlash('error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Solicitudes - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo url('public/assets/css/bookary.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="student-layout">
    
    <!-- Sidebar -->
    <div class="sidebar student-sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Mi Biblioteca</h3>
            <button class="sidebar-close" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="<?php echo url('student/dashboard.php'); ?>" class="sidebar-link">
                    <i class="fas fa-home"></i> Inicio
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('student/catalogo.php'); ?>" class="sidebar-link">
                    <i class="fas fa-books"></i> Catálogo
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('student/mis-prestamos.php'); ?>" class="sidebar-link">
                    <i class="fas fa-book-reader"></i> Mis Préstamos
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('student/solicitudes.php'); ?>" class="sidebar-link active">
                    <i class="fas fa-paper-plane"></i> Mis Solicitudes
                    <?php if (count($pendingRequests) > 0): ?>
                        <span style="background: #ffc107; color: #856404; border-radius: 50%; padding: 0.2rem 0.5rem; font-size: 0.75rem; margin-left: 0.5rem;">
                            <?php echo count($pendingRequests); ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('student/historial.php'); ?>" class="sidebar-link">
                    <i class="fas fa-history"></i> Historial
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Navbar -->
    <nav class="navbar student-navbar">
        <div class="container">
            <div class="navbar-content">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="<?php echo url('student/dashboard.php'); ?>" class="navbar-brand">Book<span>ary</span></a>
                <ul class="navbar-nav">
                    <li>
                        <span style="color: var(--color-white); margin-right: 1rem;">
                            <i class="fas fa-user-circle"></i> <?php echo e($username); ?>
                        </span>
                    </li>
                    <li>
                        <a href="<?php echo url('public/logout.php'); ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="main-content" id="mainContent">
        <div class="container section">
            
            <!-- Header -->
            <div style="margin-bottom: 2rem;">
                <h1 style="color: var(--color-primary); font-family: 'Playfair Display', serif; margin: 0 0 0.5rem 0;">
                    Mis Solicitudes de Préstamo
                </h1>
                <p style="color: var(--color-secondary); margin: 0;">
                    Revisa el estado de tus solicitudes
                </p>
            </div>

            <!-- Mensajes -->
            <?php if ($success): ?>
                <div class="message success" style="margin-bottom: 2rem;">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="message error" style="margin-bottom: 2rem;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Solicitudes Pendientes -->
            <div style="margin-bottom: 3rem;">
                <h2 style="color: var(--color-primary); margin-bottom: 1.5rem; font-size: 1.5rem;">
                    <i class="fas fa-clock"></i> Pendientes (<?php echo count($pendingRequests); ?>)
                </h2>

                <?php if (empty($pendingRequests)): ?>
                    <div class="card" style="text-align: center; padding: 2rem; background: #f8f9fa;">
                        <i class="fas fa-inbox" style="font-size: 3rem; color: var(--color-secondary); opacity: 0.3; margin-bottom: 0.5rem;"></i>
                        <p style="color: var(--color-secondary); margin: 0;">No tienes solicitudes pendientes</p>
                    </div>
                <?php else: ?>
                    <div style="display: grid; gap: 1.5rem;">
                        <?php foreach ($pendingRequests as $request): ?>
                            <div class="card" style="padding: 1.5rem; border-left: 4px solid #ffc107;">
                                <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                                    <div style="flex: 1;">
                                        <h3 style="color: var(--color-primary); margin: 0 0 0.5rem 0; font-size: 1.2rem;">
                                            <i class="fas fa-book"></i> <?php echo e($request['libro_titulo']); ?>
                                        </h3>
                                        <p style="color: var(--color-secondary); margin: 0 0 0.5rem 0;">
                                            <i class="fas fa-user-edit"></i> <?php echo e($request['libro_autor']); ?>
                                        </p>
                                        <p style="color: var(--color-secondary); margin: 0; font-size: 0.9rem;">
                                            <i class="fas fa-calendar"></i> Solicitado: <?php echo date('d/m/Y H:i', strtotime($request['fecha_solicitud'])); ?>
                                        </p>
                                        <?php if ($request['notas_usuario']): ?>
                                            <div style="margin-top: 0.5rem; padding: 0.5rem; background: #f8f9fa; border-radius: 0.5rem;">
                                                <small style="color: var(--color-secondary);">
                                                    <strong>Tus notas:</strong> <?php echo e($request['notas_usuario']); ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div style="text-align: right;">
                                        <span class="message warning" style="display: inline-block; padding: 0.5rem 1rem; margin-bottom: 0.5rem;">
                                            <i class="fas fa-clock"></i> En Revisión
                                        </span>
                                        <form action="<?php echo url('student/cancelar-solicitud.php'); ?>" method="POST" onsubmit="return confirm('¿Seguro que deseas cancelar esta solicitud?')">
                                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                            <button type="submit" class="btn btn-secondary btn-sm" style="background: #dc3545; width: 100%;">
                                                <i class="fas fa-times"></i> Cancelar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Solicitudes Aprobadas -->
            <?php if (!empty($approvedRequests)): ?>
            <div style="margin-bottom: 3rem;">
                <h2 style="color: var(--color-primary); margin-bottom: 1.5rem; font-size: 1.5rem;">
                    <i class="fas fa-check-circle"></i> Aprobadas (<?php echo count($approvedRequests); ?>)
                </h2>
                <div style="display: grid; gap: 1.5rem;">
                    <?php foreach ($approvedRequests as $request): ?>
                        <div class="card" style="padding: 1.5rem; border-left: 4px solid #28a745;">
                            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                                <div style="flex: 1;">
                                    <h3 style="color: var(--color-primary); margin: 0 0 0.5rem 0; font-size: 1.2rem;">
                                        <i class="fas fa-book"></i> <?php echo e($request['libro_titulo']); ?>
                                    </h3>
                                    <p style="color: var(--color-secondary); margin: 0 0 0.5rem 0;">
                                        <i class="fas fa-user-edit"></i> <?php echo e($request['libro_autor']); ?>
                                    </p>
                                    <p style="color: var(--color-secondary); margin: 0; font-size: 0.9rem;">
                                        <i class="fas fa-calendar-check"></i> Aprobada: <?php echo date('d/m/Y H:i', strtotime($request['fecha_respuesta'])); ?>
                                    </p>
                                    <?php if ($request['notas_admin']): ?>
                                        <div style="margin-top: 0.5rem; padding: 0.5rem; background: #d4edda; border-radius: 0.5rem;">
                                            <small style="color: #155724;">
                                                <strong>Mensaje del administrador:</strong> <?php echo e($request['notas_admin']); ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <span class="message success" style="display: inline-block; padding: 0.5rem 1rem;">
                                    <i class="fas fa-check-circle"></i> Aprobada
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Solicitudes Rechazadas -->
            <?php if (!empty($rejectedRequests)): ?>
            <div style="margin-bottom: 3rem;">
                <h2 style="color: var(--color-primary); margin-bottom: 1.5rem; font-size: 1.5rem;">
                    <i class="fas fa-times-circle"></i> Rechazadas (<?php echo count($rejectedRequests); ?>)
                </h2>
                <div style="display: grid; gap: 1.5rem;">
                    <?php foreach ($rejectedRequests as $request): ?>
                        <div class="card" style="padding: 1.5rem; border-left: 4px solid #dc3545;">
                            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                                <div style="flex: 1;">
                                    <h3 style="color: var(--color-primary); margin: 0 0 0.5rem 0; font-size: 1.2rem;">
                                        <i class="fas fa-book"></i> <?php echo e($request['libro_titulo']); ?>
                                    </h3>
                                    <p style="color: var(--color-secondary); margin: 0 0 0.5rem 0;">
                                        <i class="fas fa-user-edit"></i> <?php echo e($request['libro_autor']); ?>
                                    </p>
                                    <p style="color: var(--color-secondary); margin: 0; font-size: 0.9rem;">
                                        <i class="fas fa-calendar-times"></i> Rechazada: <?php echo date('d/m/Y H:i', strtotime($request['fecha_respuesta'])); ?>
                                    </p>
                                    <?php if ($request['notas_admin']): ?>
                                        <div style="margin-top: 0.5rem; padding: 0.5rem; background: #f8d7da; border-radius: 0.5rem;">
                                            <small style="color: #721c24;">
                                                <strong>Motivo:</strong> <?php echo e($request['notas_admin']); ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <span class="message error" style="display: inline-block; padding: 0.5rem 1rem;">
                                    <i class="fas fa-times-circle"></i> Rechazada
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- CTA si no hay solicitudes -->
            <?php if (empty($pendingRequests) && empty($approvedRequests) && empty($rejectedRequests)): ?>
                <div class="card" style="text-align: center; padding: 3rem;">
                    <i class="fas fa-paper-plane" style="font-size: 4rem; color: var(--color-secondary); opacity: 0.3; margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--color-primary); margin-bottom: 1rem;">No tienes solicitudes aún</h3>
                    <p style="color: var(--color-secondary); margin-bottom: 2rem;">
                        Explora nuestro catálogo y solicita tu primer libro
                    </p>
                    <a href="<?php echo url('student/catalogo.php'); ?>" class="btn btn-accent">
                        <i class="fas fa-books"></i> Ver Catálogo
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Scripts -->
    <script src="<?php echo url('public/assets/js/sidebar.js'); ?>"></script>
</body>
</html>