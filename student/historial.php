<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/Loan.php';

// Verificar autenticación
AuthMiddleware::requireStudent('../public/login.php');

// Obtener información del usuario
$userId = AuthMiddleware::id();
$username = AuthMiddleware::username();

// Obtener historial completo
$loanModel = new Loan($conexion);
$loanHistory = $loanModel->history($userId);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Historial - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="..\public\assets\css\bookary.css">
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
                <a href="<?php echo url('student/historial.php'); ?>" class="sidebar-link active">
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
                    Mi Historial de Lectura
                </h1>
                <p style="color: var(--color-secondary); margin: 0;">
                    Todos los libros que has leído
                </p>
            </div>

            <?php if (empty($loanHistory)): ?>
                <!-- Sin historial -->
                <div class="card" style="text-align: center; padding: 3rem;">
                    <i class="fas fa-history" style="font-size: 4rem; color: var(--color-secondary); opacity: 0.3; margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--color-primary); margin-bottom: 1rem;">Aún no tienes historial</h3>
                    <p style="color: var(--color-secondary); margin-bottom: 2rem;">
                        Comienza tu aventura literaria explorando nuestro catálogo
                    </p>
                    <a href="<?php echo url('student/catalogo.php'); ?>" class="btn btn-accent">
                        <i class="fas fa-books"></i> Explorar Catálogo
                    </a>
                </div>
            <?php else: ?>
                <!-- Estadísticas -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div class="card student-card" style="text-align: center; padding: 1.5rem;">
                        <i class="fas fa-book" style="font-size: 2rem; color: var(--color-secondary); margin-bottom: 0.5rem;"></i>
                        <h3 style="font-size: 2rem; margin: 0.5rem 0; color: var(--color-primary);">
                            <?php echo count($loanHistory); ?>
                        </h3>
                        <p style="color: var(--color-secondary); margin: 0;">Libros Leídos</p>
                    </div>

                    <div class="card student-card" style="text-align: center; padding: 1.5rem;">
                        <i class="fas fa-calendar-check" style="font-size: 2rem; color: var(--color-secondary); margin-bottom: 0.5rem;"></i>
                        <h3 style="font-size: 2rem; margin: 0.5rem 0; color: var(--color-primary);">
                            <?php echo count(array_filter($loanHistory, fn($l) => $l['estado'] === 'devuelto')); ?>
                        </h3>
                        <p style="color: var(--color-secondary); margin: 0;">Devueltos a Tiempo</p>
                    </div>

                    <div class="card student-card" style="text-align: center; padding: 1.5rem;">
                        <i class="fas fa-book-reader" style="font-size: 2rem; color: var(--color-secondary); margin-bottom: 0.5rem;"></i>
                        <h3 style="font-size: 2rem; margin: 0.5rem 0; color: var(--color-primary);">
                            <?php echo count(array_filter($loanHistory, fn($l) => $l['estado'] === 'activo')); ?>
                        </h3>
                        <p style="color: var(--color-secondary); margin: 0;">Actualmente Leyendo</p>
                    </div>
                </div>

                <!-- Lista de Historial -->
                <div style="background: white; border-radius: 1rem; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h2 style="color: var(--color-primary); margin: 0 0 1.5rem 0; font-size: 1.5rem;">
                        <i class="fas fa-list"></i> Historial Completo
                    </h2>

                    <?php foreach ($loanHistory as $index => $loan): 
                        $isActive = $loan['estado'] === 'activo';
                        $wasLate = !$isActive && isset($loan['fecha_devuelto']) && 
                                   strtotime($loan['fecha_devuelto']) > strtotime($loan['fecha_devolucion']);
                    ?>
                        <div style="border-bottom: <?php echo $index < count($loanHistory) - 1 ? '1px solid #eee' : 'none'; ?>; padding: 1.5rem 0;">
                            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                                <!-- Información del Libro -->
                                <div style="flex: 1; min-width: 250px;">
                                    <h4 style="color: var(--color-primary); margin: 0 0 0.5rem 0; font-size: 1.1rem;">
                                        <i class="fas fa-book"></i> <?php echo e($loan['libro_titulo']); ?>
                                    </h4>
                                    <p style="color: var(--color-secondary); margin: 0 0 0.5rem 0;">
                                        <i class="fas fa-user-edit"></i> <?php echo e($loan['libro_autor']); ?>
                                    </p>
                                    
                                    <!-- Fechas -->
                                    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 0.5rem; font-size: 0.9rem;">
                                        <span style="color: var(--color-secondary);">
                                            <i class="fas fa-calendar-check"></i> Prestado: 
                                            <strong><?php echo date('d/m/Y', strtotime($loan['fecha_prestamo'])); ?></strong>
                                        </span>
                                        <?php if (!$isActive && $loan['fecha_devuelto']): ?>
                                            <span style="color: var(--color-secondary);">
                                                <i class="fas fa-calendar-times"></i> Devuelto: 
                                                <strong><?php echo date('d/m/Y', strtotime($loan['fecha_devuelto'])); ?></strong>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Estado -->
                                <div style="text-align: right;">
                                    <?php if ($isActive): ?>
                                        <span class="message warning" style="display: inline-block; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600;">
                                            <i class="fas fa-book-reader"></i> Leyendo
                                        </span>
                                    <?php elseif ($wasLate): ?>
                                        <span class="message error" style="display: inline-block; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600;">
                                            <i class="fas fa-exclamation-triangle"></i> Devuelto Tarde
                                        </span>
                                    <?php else: ?>
                                        <span class="message success" style="display: inline-block; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600;">
                                            <i class="fas fa-check-circle"></i> Devuelto a Tiempo
                                        </span>
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
    <script src="<?php echo asset('js/sidebar.js'); ?>"></script>
</body>
</html>