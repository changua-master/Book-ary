<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/Event.php';

if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

AuthMiddleware::requireStudent('../public/login.php');

$userId = AuthMiddleware::id();
$username = AuthMiddleware::username();
$userInitial = strtoupper(substr($username, 0, 1));

$eventModel = new Event($conexion);
$eventosProximos = $eventModel->upcoming();
$misInscripciones = $eventModel->misInscripciones($userId);

$success = AuthMiddleware::getFlash('success');
$error = AuthMiddleware::getFlash('error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../public/assets/css/bookary.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
                <a href="<?php echo url('student/dashboard.php'); ?>" class="sidebar-link active">
                    <i class="fas fa-home"></i> Inicio
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('student/catalogo.php'); ?>" class="sidebar-link">
                    <i class="fas fa-book"></i> Catálogo
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('student/mis-prestamos.php'); ?>" class="sidebar-link">
                    <i class="fas fa-book-reader"></i> Mis Préstamos
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('student/solicitudes.php'); ?>" class="sidebar-link">
                    <i class="fas fa-paper-plane"></i> Mis Solicitudes
                    <?php if ($pendingRequestCount > 0): ?>
                        <span style="background: #ffc107; color: #856404; border-radius: 50%; padding: 0.2rem 0.5rem; font-size: 0.75rem; margin-left: 0.5rem;">
                            <?php echo $pendingRequestCount; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('student/eventos.php'); ?>" class="sidebar-link">
                    <i class="fas fa-calendar-alt"></i> Eventos
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('student/historial.php'); ?>" class="sidebar-link">
                    <i class="fas fa-history"></i> Historial
                </a>
            </li>
        </ul>
        
        <!-- Perfil y Logout en Sidebar -->
        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <div class="sidebar-user-avatar" style="background: var(--color-secondary);">
                    <?php echo $userInitial; ?>
                </div>
                <div class="sidebar-user-details">
                    <h4><?php echo htmlspecialchars($username); ?></h4>
                    <p>Estudiante</p>
                </div>
            </div>
            <a href="<?php echo url('student/perfil.php'); ?>" class="btn btn-secondary btn-sm" style="width: 100%; margin-bottom: 0.5rem; background: var(--color-primary);">
        <i class="fas fa-user-cog"></i> Configurar Cuenta
    </a>
            <a href="<?php echo url('public/logout.php'); ?>" class="sidebar-logout" style="background: var(--color-secondary);">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>

        </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Navbar -->
    <nav class="navbar student-navbar" style="position: relative;">
        <div class="navbar-content">
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
            <a href="<?php echo url('student/dashboard.php'); ?>" class="navbar-brand">Book<span>ary</span></a>
        </div>
        
        <div class="navbar-decorations">
            <i class="fas fa-feather-alt navbar-icon"></i>
            <i class="fas fa-glasses navbar-icon"></i>
            <i class="fas fa-lightbulb navbar-icon"></i>
            <i class="fas fa-magic navbar-icon"></i>
            <i class="fas fa-leaf navbar-icon"></i>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="main-content" id="mainContent">
        <div class="container section">
            
            <!-- Header -->
            <div style="margin-bottom: 2rem;">
                <h1 style="color: var(--color-primary); font-family: 'Playfair Display', serif; margin: 0 0 0.5rem 0;">
                    <i class="fas fa-calendar-alt"></i> Eventos de la Biblioteca
                </h1>
                <p style="color: var(--color-secondary); margin: 0;">
                    Descubre y participa en nuestras actividades
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

            <!-- Mis Inscripciones -->
            <?php if (!empty($misInscripciones)): ?>
            <h2 style="color: var(--color-primary); margin-bottom: 1.5rem; font-family: 'Playfair Display', serif;">
                <i class="fas fa-ticket-alt"></i> Mis Inscripciones
            </h2>
            <div style="background: white; border-radius: 1rem; padding: 1.5rem; margin-bottom: 3rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <?php foreach ($misInscripciones as $index => $inscripcion): 
                    $esPasado = strtotime($inscripcion['fecha_evento']) < strtotime('today');
                    $esHoy = strtotime($inscripcion['fecha_evento']) == strtotime('today');
                ?>
                <div class="dashboard-item">
                    <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 0.5rem 0; color: var(--color-primary);">
                                <?php echo e($inscripcion['titulo']); ?>
                            </h4>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.9rem; color: var(--color-secondary);">
                                <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($inscripcion['fecha_evento'])); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($inscripcion['hora_inicio'])); ?></span>
                                <?php if ($inscripcion['ubicacion']): ?>
                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo e($inscripcion['ubicacion']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <?php if ($esPasado): ?>
                                <span class="badge" style="background: #e0e0e0; color: #666;">
                                    <i class="fas fa-history"></i> Evento Pasado
                                </span>
                            <?php elseif ($esHoy): ?>
                                <span class="badge warning">
                                    <i class="fas fa-calendar-day"></i> ¡Hoy!
                                </span>
                            <?php else: ?>
                                <span class="badge success">
                                    <i class="fas fa-check-circle"></i> Inscrito
                                </span>
                            <?php endif; ?>
                            <?php if (!$esPasado && $inscripcion['estado'] === 'activo'): ?>
                                <br>
                                <button onclick="cancelarInscripcion(<?php echo $inscripcion['id']; ?>, '<?php echo e($inscripcion['titulo']); ?>')" 
                                        class="btn btn-sm" 
                                        style="background: #dc3545; color: white; margin-top: 0.5rem;">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Próximos Eventos -->
            <h2 style="color: var(--color-primary); margin-bottom: 1.5rem; font-family: 'Playfair Display', serif;">
                <i class="fas fa-star"></i> Próximos Eventos
            </h2>

            <?php if (empty($eventosProximos)): ?>
                <div class="card" style="text-align: center; padding: 3rem;">
                    <i class="fas fa-calendar-times" style="font-size: 4rem; color: var(--color-secondary); opacity: 0.3; margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--color-primary);">No hay eventos programados</h3>
                    <p style="color: var(--color-secondary);">
                        Vuelve pronto para ver las próximas actividades
                    </p>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
                    <?php foreach ($eventosProximos as $evento): 
                        $esHoy = strtotime($evento['fecha_evento']) == strtotime('today');
                        $tieneCupo = !$evento['cupo_maximo'] || $evento['inscritos'] < $evento['cupo_maximo'];
                        $estaInscrito = $eventModel->estaInscrito($evento['id'], $userId);
                    ?>
                        <div class="card" style="padding: 0; overflow: hidden; <?php echo $esHoy ? 'border: 3px solid #ffc107;' : ''; ?>">
                            <!-- Header del Evento -->
                            <div style="padding: 1.5rem; background: linear-gradient(135deg, var(--color-secondary) 0%, #8FB339 100%); color: white;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                    <h3 style="margin: 0; font-size: 1.3rem;">
                                        <?php echo e($evento['titulo']); ?>
                                    </h3>
                                    <?php if ($esHoy): ?>
                                        <span class="badge" style="background: #ffc107; color: #856404;">
                                            <i class="fas fa-calendar-day"></i> ¡Hoy!
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div style="display: flex; gap: 1.5rem; font-size: 0.9rem; margin-top: 1rem;">
                                    <span>
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($evento['hora_inicio'])); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Contenido del Evento -->
                            <div style="padding: 1.5rem;">
                                <p style="color: var(--color-neutral); margin-bottom: 1rem; min-height: 60px;">
                                    <?php echo e(substr($evento['descripcion'], 0, 120)); ?><?php echo strlen($evento['descripcion']) > 120 ? '...' : ''; ?>
                                </p>

                                <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.9rem; color: var(--color-secondary);">
                                    <?php if ($evento['ubicacion']): ?>
                                        <div class="info-row">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?php echo e($evento['ubicacion']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($evento['cupo_maximo']): ?>
                                        <div class="info-row">
                                            <i class="fas fa-users"></i>
                                            <span>
                                                <?php echo $evento['inscritos']; ?> / <?php echo $evento['cupo_maximo']; ?> inscritos
                                                <?php if (!$tieneCupo): ?>
                                                    <span style="color: #dc3545; font-weight: 600;"> - LLENO</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <!-- Barra de progreso -->
                                        <div style="background: #f0f0f0; border-radius: 0.5rem; height: 8px; overflow: hidden; margin-top: 0.25rem;">
                                            <div style="background: <?php echo $tieneCupo ? 'var(--color-secondary)' : '#dc3545'; ?>; height: 100%; width: <?php echo ($evento['cupo_maximo'] > 0) ? ($evento['inscritos'] / $evento['cupo_maximo'] * 100) : 0; ?>%; transition: width 0.3s;"></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="info-row">
                                            <i class="fas fa-users"></i>
                                            <span><?php echo $evento['inscritos']; ?> inscritos (sin límite)</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Botón de Inscripción -->
                                <div style="border-top: 1px solid #eee; padding-top: 1rem;">
                                    <?php if ($estaInscrito): ?>
                                        <span class="badge success" style="width: 100%; justify-content: center; padding: 0.75rem;">
                                            <i class="fas fa-check-circle"></i> Ya estás inscrito
                                        </span>
                                    <?php elseif (!$tieneCupo): ?>
                                        <span class="badge error" style="width: 100%; justify-content: center; padding: 0.75rem;">
                                            <i class="fas fa-times-circle"></i> Sin cupos disponibles
                                        </span>
                                    <?php else: ?>
                                        <button onclick="inscribirseEvento(<?php echo $evento['id']; ?>, '<?php echo e($evento['titulo']); ?>')" 
                                                class="btn btn-accent" 
                                                style="width: 100%;">
                                            <i class="fas fa-ticket-alt"></i> Inscribirme
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Scripts -->
    <script src="../public/assets/js/sidebar.js"></script>
    <script src="../public/assets/js/bookary.js"></script>
    <script>
        function inscribirseEvento(id, titulo) {
            if (confirm(`¿Deseas inscribirte al evento:\n\n"${titulo}"?`)) {
                window.location.href = `<?php echo url('student/inscribirse-evento.php'); ?>?id=${id}`;
            }
        }

        function cancelarInscripcion(id, titulo) {
            if (confirm(`¿Estás seguro de cancelar tu inscripción a:\n\n"${titulo}"?`)) {
                window.location.href = `<?php echo url('student/cancelar-inscripcion.php'); ?>?id=${id}`;
            }
        }
    </script>
</body>
</html>