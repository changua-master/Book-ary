<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/Loan.php';

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

// Obtener préstamos del usuario
$loanModel = new Loan($conexion);
$activeLoans = $loanModel->byUser($userId, 'activo');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Préstamos - <?php echo APP_NAME; ?></title>
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
                    Mis Préstamos Activos
                </h1>
                <p style="color: var(--color-secondary); margin: 0;">
                    Libros que tienes prestados actualmente
                </p>
            </div>

            <?php if (empty($activeLoans)): ?>
                <!-- No hay préstamos -->
                <div class="card" style="text-align: center; padding: 3rem;">
                    <i class="fas fa-book-reader" style="font-size: 4rem; color: var(--color-secondary); opacity: 0.3; margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--color-primary); margin-bottom: 1rem;">No tienes préstamos activos</h3>
                    <p style="color: var(--color-secondary); margin-bottom: 2rem;">
                        Explora nuestro catálogo y solicita un libro
                    </p>
                    <a href="<?php echo url('student/catalogo.php'); ?>" class="btn btn-accent" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-books"></i> Ver Catálogo
                    </a>
                </div>
            <?php else: ?>
                <!-- Grid de Préstamos -->
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
                    <?php foreach ($activeLoans as $loan): 
                        $daysLeft = (strtotime($loan['fecha_devolucion']) - time()) / (60 * 60 * 24);
                        $isOverdue = $daysLeft < 0;
                        $isUrgent = !$isOverdue && $daysLeft < 3;
                        $statusClass = $isOverdue ? 'error' : ($isUrgent ? 'warning' : 'success');
                    ?>
                        <div class="card student-card" style="padding: 1.5rem;">
                            <!-- Estado -->
                            <div style="text-align: center; margin-bottom: 1rem;">
                                <span class="message <?php echo $statusClass; ?>" style="display: inline-block; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600;">
                                    <?php if ($isOverdue): ?>
                                        <i class="fas fa-exclamation-triangle"></i> ¡Préstamo Vencido!
                                    <?php elseif ($isUrgent): ?>
                                        <i class="fas fa-clock"></i> Vence Pronto
                                    <?php else: ?>
                                        <i class="fas fa-check-circle"></i> Al Día
                                    <?php endif; ?>
                                </span>
                            </div>

                            <!-- Información del Libro -->
                            <div style="text-align: center; margin-bottom: 1.5rem;">
                                <h3 style="color: var(--color-primary); margin: 0 0 0.5rem 0; font-size: 1.25rem;">
                                    <?php echo e($loan['libro_titulo']); ?>
                                </h3>
                                <p style="color: var(--color-secondary); margin: 0;">
                                    <i class="fas fa-user-edit"></i> <?php echo e($loan['libro_autor']); ?>
                                </p>
                            </div>

                            <!-- Fechas -->
                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span style="color: var(--color-secondary);">
                                        <i class="fas fa-calendar-check"></i> Prestado:
                                    </span>
                                    <strong><?php echo date('d/m/Y', strtotime($loan['fecha_prestamo'])); ?></strong>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--color-secondary);">
                                        <i class="fas fa-calendar-times"></i> Devolver antes:
                                    </span>
                                    <strong style="color: <?php echo $isOverdue ? '#dc3545' : ($isUrgent ? '#ffc107' : '#28a745'); ?>">
                                        <?php echo date('d/m/Y', strtotime($loan['fecha_devolucion'])); ?>
                                    </strong>
                                </div>
                            </div>

                            <!-- Días Restantes -->
                            <div style="text-align: center; padding: 1rem; background: <?php echo $isOverdue ? '#f8d7da' : ($isUrgent ? '#fff3cd' : '#d4edda'); ?>; border-radius: 0.5rem;">
                                <?php if ($isOverdue): ?>
                                    <p style="margin: 0; color: #721c24; font-weight: 600;">
                                        <i class="fas fa-exclamation-circle"></i> 
                                        Vencido hace <?php echo abs(round($daysLeft)); ?> día(s)
                                    </p>
                                    <small style="color: #721c24; display: block; margin-top: 0.5rem;">
                                        Por favor, devuelve el libro lo antes posible
                                    </small>
                                <?php elseif ($isUrgent): ?>
                                    <p style="margin: 0; color: #856404; font-weight: 600;">
                                        <i class="fas fa-clock"></i> 
                                        Te quedan <?php echo round($daysLeft); ?> día(s)
                                    </p>
                                    <small style="color: #856404; display: block; margin-top: 0.5rem;">
                                        Recuerda devolver el libro a tiempo
                                    </small>
                                <?php else: ?>
                                    <p style="margin: 0; color: #155724; font-weight: 600;">
                                        <i class="fas fa-calendar-check"></i> 
                                        Te quedan <?php echo round($daysLeft); ?> día(s)
                                    </p>
                                    <small style="color: #155724; display: block; margin-top: 0.5rem;">
                                        Disfruta tu lectura
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Información adicional -->
                <div class="card" style="margin-top: 2rem; background: #e7f3ff; border-left: 4px solid #2196F3;">
                    <h4 style="color: #1565C0; margin: 0 0 1rem 0;">
                        <i class="fas fa-info-circle"></i> Información Importante
                    </h4>
                    <ul style="margin: 0; padding-left: 1.5rem; color: #424242;">
                        <li style="margin-bottom: 0.5rem;">Los libros deben ser devueltos en las fechas indicadas</li>
                        <li style="margin-bottom: 0.5rem;">Puedes tener máximo <?php echo MAX_ACTIVE_LOANS ?? 3; ?> préstamos activos</li>
                        <li style="margin-bottom: 0.5rem;">Cuida los libros durante el período de préstamo</li>
                        <li>Para devolver un libro, acércate al administrador</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Scripts -->
    <script src="<?php echo url('public/assets/js/sidebar.js'); ?>"></script>
    <script>
        // Debug: Verificar carga
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página de préstamos cargada');
            
            // Verificar que los enlaces funcionan
            const catalogLink = document.querySelector('a[href*="catalogo.php"]');
            if (catalogLink) {
                console.log('Link al catálogo encontrado:', catalogLink.href);
                
                // Agregar listener para debug
                catalogLink.addEventListener('click', function(e) {
                    console.log('Click en link de catálogo detectado');
                    console.log('Navegando a:', this.href);
                });
            }
        });
    </script>
</body>
</html>