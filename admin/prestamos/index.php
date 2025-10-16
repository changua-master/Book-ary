<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../app/Models/Loan.php';

// Función helper para escapar HTML (si no existe)
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// Verificar autenticación
AuthMiddleware::requireAdmin('../../public/login.php');

// Obtener préstamos
$loanModel = new Loan($conexion);
$filter = $_GET['filter'] ?? 'all';

switch ($filter) {
    case 'active':
        $loans = $loanModel->active();
        $title = 'Préstamos Activos';
        break;
    case 'overdue':
        $loans = $loanModel->overdue();
        $title = 'Préstamos Vencidos';
        break;
    default:
        $loans = $loanModel->all();
        $title = 'Todos los Préstamos';
}

// Mensajes flash
$success = AuthMiddleware::getFlash('success');
$error = AuthMiddleware::getFlash('error');

$username = AuthMiddleware::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Préstamos - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../public/assets/css/bookary.css">
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
                <a href="<?php echo url('admin/dashboard.php'); ?>" class="sidebar-link">
                    <i class="fas fa-home"></i> Inicio
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/libros/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-book"></i> Gestión de Libros
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/prestamos/index.php'); ?>" class="sidebar-link active">
                    <i class="fas fa-book-reader"></i> Préstamos
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h1 style="color: var(--color-primary); font-family: 'Playfair Display', serif; margin: 0;">
                        <?php echo $title; ?>
                    </h1>
                    <p style="color: var(--color-secondary); margin: 0.5rem 0 0 0;">
                        Gestiona los préstamos de libros
                    </p>
                </div>
                <a href="<?php echo url('admin/prestamos/crear.php'); ?>" class="btn btn-accent">
                    <i class="fas fa-plus"></i> Nuevo Préstamo
                </a>
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

            <!-- Filtros -->
            <div style="background: white; padding: 1rem; border-radius: 1rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="?filter=all" class="btn <?php echo $filter === 'all' ? 'btn-accent' : 'btn-secondary'; ?>">
                        <i class="fas fa-list"></i> Todos
                    </a>
                    <a href="?filter=active" class="btn <?php echo $filter === 'active' ? 'btn-accent' : 'btn-secondary'; ?>">
                        <i class="fas fa-book-reader"></i> Activos
                    </a>
                    <a href="?filter=overdue" class="btn <?php echo $filter === 'overdue' ? 'btn-accent' : 'btn-secondary'; ?>">
                        <i class="fas fa-exclamation-triangle"></i> Vencidos
                    </a>
                </div>
            </div>

            <!-- Tabla de Préstamos -->
            <div style="background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: var(--color-primary); color: white;">
                        <tr>
                            <th style="padding: 1rem; text-align: left;">ID</th>
                            <th style="padding: 1rem; text-align: left;">Libro</th>
                            <th style="padding: 1rem; text-align: left;">Usuario</th>
                            <th style="padding: 1rem; text-align: center;">Fecha Préstamo</th>
                            <th style="padding: 1rem; text-align: center;">Fecha Devolución</th>
                            <th style="padding: 1rem; text-align: center;">Estado</th>
                            <th style="padding: 1rem; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($loans)): ?>
                            <tr>
                                <td colspan="7" style="padding: 2rem; text-align: center; color: var(--color-secondary);">
                                    <i class="fas fa-book-reader" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 1rem;"></i>
                                    No hay préstamos registrados
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($loans as $loan): 
                                $isOverdue = $loan['estado'] === 'activo' && strtotime($loan['fecha_devolucion']) < time();
                                $statusClass = $loan['estado'] === 'devuelto' ? 'success' : ($isOverdue ? 'error' : 'warning');
                            ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem;"><?php echo $loan['id']; ?></td>
                                    <td style="padding: 1rem;">
                                        <strong><?php echo e($loan['libro_titulo']); ?></strong>
                                        <br><small style="color: var(--color-secondary);"><?php echo e($loan['libro_autor']); ?></small>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <i class="fas fa-user"></i> <?php echo e($loan['usuario_nombre']); ?>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <?php echo date('d/m/Y', strtotime($loan['fecha_prestamo'])); ?>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <?php echo date('d/m/Y', strtotime($loan['fecha_devolucion'])); ?>
                                        <?php if ($isOverdue): ?>
                                            <br><small style="color: #dc3545; font-weight: 600;">¡Vencido!</small>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <span class="message <?php echo $statusClass; ?>" style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.9rem;">
                                            <?php echo ucfirst($loan['estado']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <?php if ($loan['estado'] === 'activo'): ?>
                                            <a href="<?php echo url('admin/prestamos/devolver.php?id=' . $loan['id']); ?>" 
                                               class="btn btn-accent btn-sm"
                                               onclick="return confirm('¿Registrar devolución de este libro?')">
                                                <i class="fas fa-check"></i> Devolver
                                            </a>
                                        <?php else: ?>
                                            <span style="color: var(--color-secondary);">
                                                <i class="fas fa-check-circle"></i> Devuelto
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="../../public/assets/js/sidebar.js"></script>
</body>
</html>