<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/Loan.php';
require_once __DIR__ . '/../app/Models/LoanRequest.php';

AuthMiddleware::requireStudent('../public/login.php');

$userId = AuthMiddleware::id();
$username = AuthMiddleware::username();
$userInitial = strtoupper(substr($username, 0, 1));

$loanModel = new Loan($conexion);
$requestModel = new LoanRequest($conexion);

$activeLoans = $loanModel->byUser($userId, 'activo');
$loanHistory = $loanModel->history($userId, 5);
$pendingRequests = $requestModel->byUser($userId, 'pendiente');

$activeLoanCount = count($activeLoans);
$pendingRequestCount = count($pendingRequests);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Biblioteca - <?php echo APP_NAME; ?></title>
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
            <a href="<?php echo url('public/logout.php'); ?>" class="sidebar-logout" style="background: var(--color-secondary);">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Navbar con decoraciones -->
    <nav class="navbar student-navbar" style="position: relative;">
        <div class="navbar-content">
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
            <a href="<?php echo url('student/dashboard.php'); ?>" class="navbar-brand">Book<span>ary</span></a>
        </div>
        
        <!-- Iconos decorativos dispersos con levitación -->
        <div class="navbar-decorations">
            <i class="fas fa-feather-alt navbar-icon" title="Escritura"></i>
            <i class="fas fa-glasses navbar-icon" title="Lectura"></i>
            <i class="fas fa-lightbulb navbar-icon" title="Ideas"></i>
            <i class="fas fa-magic navbar-icon" title="Inspiración"></i>
            <i class="fas fa-leaf navbar-icon" title="Conocimiento"></i>
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

            <!-- Alertas -->
            <?php if ($pendingRequestCount > 0): ?>
            <div class="message warning" style="margin-bottom: 2rem;">
                <i class="fas fa-clock"></i>
                Tienes <?php echo $pendingRequestCount; ?> solicitud(es) pendiente(s) de aprobación.
                <a href="<?php echo url('student/solicitudes.php'); ?>" style="color: inherit; text-decoration: underline; font-weight: 600;">Ver solicitudes</a>
            </div>
            <?php endif; ?>

            <!-- Resumen de Préstamos -->
            <div class="dashboard-grid" style="margin-bottom: 3rem;">
                <div class="card student-card animate-scale-in">
                    <div class="card-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; margin: 0.5rem 0;"><?php echo $activeLoanCount; ?></h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Préstamos Activos</p>
                </div>

                <div class="card student-card animate-scale-in" style="<?php echo $pendingRequestCount > 0 ? 'border-left: 4px solid #ffc107;' : ''; ?>">
                    <div class="card-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; margin: 0.5rem 0;"><?php echo $pendingRequestCount; ?></h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Solicitudes Pendientes</p>
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
                        <i class="fas fa-check-circle"></i>
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
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h4 style="margin: 0 0 0.5rem 0; color: var(--color-primary);">
                                <i class="fas fa-book"></i> <?php echo htmlspecialchars($loan['libro_titulo']); ?>
                            </h4>
                            <p style="margin: 0; color: var(--color-secondary);">
                                <i class="fas fa-user-edit"></i> <?php echo htmlspecialchars($loan['libro_autor']); ?>
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem;">
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

                <a href="<?php echo url('student/solicitudes.php'); ?>" class="dashboard-card student-card" style="<?php echo $pendingRequestCount > 0 ? 'border: 2px solid #ffc107;' : ''; ?>">
                    <div class="card-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <h3>Mis Solicitudes <?php echo $pendingRequestCount > 0 ? "($pendingRequestCount)" : ''; ?></h3>
                    <p>Revisa el estado de tus solicitudes</p>
                    <?php if ($pendingRequestCount > 0): ?>
                        <span style="display: inline-block; margin-top: 0.5rem; padding: 0.25rem 0.75rem; background: #ffc107; color: #856404; border-radius: 0.5rem; font-size: 0.9rem; font-weight: 600;">
                            En revisión
                        </span>
                    <?php endif; ?>
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