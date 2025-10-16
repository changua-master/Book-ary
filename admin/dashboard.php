<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/Book.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Loan.php';

// Verificar autenticación y rol de administrador
AuthMiddleware::requireAdmin('../public/login.php');

// Obtener estadísticas
$bookModel = new Book($conexion);
$userModel = new User($conexion);
$loanModel = new Loan($conexion);

$totalBooks = $bookModel->count();
$totalCopies = $bookModel->countCopies();
$totalStudents = $userModel->countByRole('estudiante');
$activeLoans = $loanModel->countActive();
$overdueLoans = $loanModel->countOverdue();

$username = AuthMiddleware::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../public/assets/css/bookary.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-layout">
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Panel Administrativo</h3>
            <button class="sidebar-close" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="<?php echo url('admin/dashboard.php'); ?>" class="sidebar-link active">
                    <i class="fas fa-home"></i> Inicio
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/libros/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-book"></i> Gestión de Libros
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/prestamos/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-book-reader"></i> Préstamos
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/usuarios/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-users"></i> Usuarios
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/reportes/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="<?php echo url('admin/dashboard.php'); ?>" class="navbar-brand">Book<span>ary</span></a>
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
                <h1 class="welcome-title">Bienvenido, <?php echo htmlspecialchars($username); ?></h1>
                <p class="welcome-subtitle">Panel de Administración - <?php echo APP_NAME; ?></p>
            </div>

            <!-- Estadísticas -->
            <div class="dashboard-grid" style="margin-bottom: 3rem;">
                <div class="card animate-scale-in">
                    <div class="card-icon" style="color: #3498db;">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; margin: 0.5rem 0;"><?php echo $totalBooks; ?></h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Títulos en Catálogo</p>
                </div>

                <div class="card animate-scale-in">
                    <div class="card-icon" style="color: #9b59b6;">
                        <i class="fas fa-copy"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; margin: 0.5rem 0;"><?php echo $totalCopies; ?></h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Ejemplares Totales</p>
                </div>

                <div class="card animate-scale-in">
                    <div class="card-icon" style="color: #e74c3c;">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; margin: 0.5rem 0;"><?php echo $activeLoans; ?></h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Préstamos Activos</p>
                </div>

                <div class="card animate-scale-in">
                    <div class="card-icon" style="color: #f39c12;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; margin: 0.5rem 0;"><?php echo $totalStudents; ?></h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Estudiantes Registrados</p>
                </div>
            </div>

            <?php if ($overdueLoans > 0): ?>
            <div class="message error" style="margin-bottom: 2rem;">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Atención:</strong> Hay <?php echo $overdueLoans; ?> préstamo(s) vencido(s).
                <a href="<?php echo url('admin/prestamos/index.php?filter=overdue'); ?>" style="color: inherit; text-decoration: underline;">Ver detalles</a>
            </div>
            <?php endif; ?>

            <!-- Acciones Rápidas -->
            <h2 style="color: var(--color-primary); margin-bottom: 1.5rem; font-family: 'Playfair Display', serif;">
                Acciones Rápidas
            </h2>
            <div class="dashboard-grid">
                <a href="<?php echo url('admin/libros/index.php'); ?>" class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3>Gestión de Libros</h3>
                    <p>Administra el catálogo de libros</p>
                </a>

                <a href="<?php echo url('admin/prestamos/index.php'); ?>" class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3>Préstamos</h3>
                    <p>Gestiona los préstamos activos</p>
                </a>

                <a href="<?php echo url('admin/usuarios/index.php'); ?>" class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Usuarios</h3>
                    <p>Administra los usuarios del sistema</p>
                </a>

                <a href="<?php echo url('admin/reportes/index.php'); ?>" class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Reportes</h3>
                    <p>Visualiza estadísticas y reportes</p>
                </a>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="../public/assets/js/sidebar.js"></script>
    <script src="../public/assets/js/bookary.js"></script>
    <script src="../public/assets/js/dashboard.js"></script>
    <script src="../public/assets/js/admin-dashboard.js"></script>
</body>
</html>