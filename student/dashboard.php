<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/Loan.php';

// Verificar autenticación y rol de estudiante
AuthMiddleware::requireStudent('../public/login.php');

// Obtener información del usuario
$userId = AuthMiddleware::id();
$username = AuthMiddleware::username();

// Obtener préstamos del usuario
$loanModel = new Loan($conexion);
$activeLoans = $loanModel->byUser($userId, 'activo');
$loanHistory = $loanModel->history($userId, 5); // Últimos 5 préstamos

$activeLoanCount = count($activeLoans);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Biblioteca - <?php echo APP_NAME; ?></title>
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
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($username); ?>
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
            
            <!-- Bienvenida -->
            <div class="welcome-section">
                <h1 class="welcome-title">¡Hola, <?php echo htmlspecialchars($username); ?>!</h1>
                <p class="welcome-subtitle">Bienvenido a tu biblioteca personal</p>
            </div>

            <!-- Resumen de Préstamos -->
            <div class="dashboard-grid" style="margin-bottom: 3rem;">
                <div class="card student-card animate-scale-in">
                    <div class="card-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; margin: 0.5rem 0;"><?php echo $activeLoanCount; ?></h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Préstamos Activos</p>
                </div>

                <div class="card student-card animate-scale-in">
                    <div class="card-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; margin: 0.5rem 0;"><?php echo count($loanHistory); ?></h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Libros Leídos</p>
                </div>

                <div class="card student-card animate-scale-in">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; margin: 0.5rem 0;">
                        <?php echo (MAX_ACTIVE_LOANS - $activeLoanCount); ?>
                    </h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Préstamos Disponibles</p>
                </div>
            </div>

            <!-- Mis Préstamos Activos -->
            <?php if (!empty($activeLoans)): ?>
            <h2 style="color: var(--color-primary); margin-bottom: 1.5rem; font-family: 'Playfair Display', serif;">
                Mis Préstamos Activos
            </h2>
            <div style="background: white; border-radius: 1rem; padding: 1.5rem; margin-bottom: 3rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <?php foreach ($activeLoans as $loan): 
                    $daysLeft = (strtotime($loan['fecha_devolucion']) - time()) / (60 * 60 * 24);
                    $isOverdue = $daysLeft < 0;
                    $statusClass = $isOverdue ? 'error' : ($daysLeft < 3 ? 'warning' : 'success');
                ?>
                <div class="dashboard-item">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4 style="margin: 0 0 0.5rem 0; color: var(--color-primary);">
                                <i class="fas fa-book"></i> <?php echo htmlspecialchars($loan['libro_titulo']); ?>
                            </h4>
                            <p style="margin: 0; color: var(--color-secondary);">
                                <i class="fas fa-user-edit"></i> <?php echo htmlspecialchars($loan['libro_autor']); ?>
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <p style="margin: 0 0 0.5rem 0;">
                                <strong>Devolver antes de:</strong> <?php echo date('d/m/Y', strtotime($loan['fecha_devolucion'])); ?>
                            </p>
                            <span class="message <?php echo $statusClass; ?>" style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.9rem;">
                                <?php if ($isOverdue): ?>
                                    <i class="fas fa-exclamation-triangle"></i> ¡Vencido!
                                <?php elseif ($daysLeft < 3): ?>
                                    <i class="fas fa-clock"></i> Vence pronto (<?php echo round($daysLeft); ?> días)
                                <?php else: ?>
                                    <i class="fas fa-check"></i> <?php echo round($daysLeft); ?> días restantes
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="message" style="background: #f8f9fa; margin-bottom: 3rem;">
                <i class="fas fa-info-circle"></i>
                No tienes préstamos activos en este momento. 
                <a href="<?php echo url('student/catalogo.php'); ?>" style="color: var(--color-accent); font-weight: 600;">Explora el catálogo</a>
            </div>
            <?php endif; ?>

            <!-- Acciones Rápidas -->
            <h2 style="color: var(--color-primary); margin-bottom: 1.5rem; font-family: 'Playfair Display', serif;">
                ¿Qué quieres hacer hoy?
            </h2>
            <div class="dashboard-grid">
                <a href="<?php echo url('student/catalogo.php'); ?>" class="dashboard-card student-card">
                    <div class="card-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3>Explorar Catálogo</h3>
                    <p>Descubre nuevos libros para leer</p>
                </a>

                <a href="<?php echo url('student/mis-prestamos.php'); ?>" class="dashboard-card student-card">
                    <div class="card-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3>Mis Préstamos</h3>
                    <p>Gestiona tus libros prestados</p>
                </a>

                <a href="<?php echo url('student/historial.php'); ?>" class="dashboard-card student-card">
                    <div class="card-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3>Historial</h3>
                    <p>Revisa tu actividad en la biblioteca</p>
                </a>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="../public/assets/js/sidebar.js"></script>
    <script src="../public/assets/js/bookary.js"></script>
    <script src="../public/assets/js/dashboard.js"></script>
    <script src="../public/assets/js/student-dashboard.js"></script>
</body>
</html>