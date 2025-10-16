<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';

// Verificar autenticación
AuthMiddleware::requireAdmin('../../public/login.php');

// Obtener libros disponibles y usuarios
$libros_result = $conexion->query("SELECT id, titulo, autor, ejemplares FROM libros WHERE ejemplares > 0 ORDER BY titulo ASC");
$usuarios_result = $conexion->query("SELECT id, username FROM users WHERE role = 'estudiante' ORDER BY username ASC");

// Fecha por defecto de devolución (15 días)
$defaultReturnDate = date('Y-m-d', strtotime('+' . (DEFAULT_LOAN_DAYS ?? 15) . ' days'));

$username = AuthMiddleware::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Préstamo - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="..\public\assets\css\bookary.css">
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
            
            <div style="max-width: 600px; margin: 0 auto;">
                <!-- Header -->
                <div style="margin-bottom: 2rem;">
                    <h1 style="color: var(--color-primary); font-family: 'Playfair Display', serif; margin: 0 0 0.5rem 0;">
                        Crear Nuevo Préstamo
                    </h1>
                    <p style="color: var(--color-secondary); margin: 0;">
                        Registra un préstamo de libro a un estudiante
                    </p>
                </div>

                <!-- Formulario -->
                <form action="<?php echo url('admin/prestamos/guardar.php'); ?>" method="POST" style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <div class="form-group">
                        <label for="id_libro" class="form-label">
                            <i class="fas fa-book"></i> Libro *
                        </label>
                        <select id="id_libro" name="id_libro" class="form-input" required>
                            <option value="">Selecciona un libro disponible</option>
                            <?php while ($libro = $libros_result->fetch_assoc()): ?>
                                <option value="<?php echo $libro['id']; ?>">
                                    <?php echo e($libro['titulo']); ?> - <?php echo e($libro['autor']); ?> 
                                    (<?php echo $libro['ejemplares']; ?> disponibles)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_usuario" class="form-label">
                            <i class="fas fa-user"></i> Estudiante *
                        </label>
                        <select id="id_usuario" name="id_usuario" class="form-input" required>
                            <option value="">Selecciona un estudiante</option>
                            <?php while ($usuario = $usuarios_result->fetch_assoc()): ?>
                                <option value="<?php echo $usuario['id']; ?>">
                                    <?php echo e($usuario['username']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fecha_devolucion" class="form-label">
                            <i class="fas fa-calendar-alt"></i> Fecha de Devolución *
                        </label>
                        <input 
                            type="date" 
                            id="fecha_devolucion" 
                            name="fecha_devolucion" 
                            class="form-input" 
                            value="<?php echo $defaultReturnDate; ?>"
                            min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                            required
                        >
                        <small style="color: var(--color-secondary); font-size: 0.85rem;">
                            Por defecto: <?php echo DEFAULT_LOAN_DAYS ?? 15; ?> días de préstamo
                        </small>
                    </div>

                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee; display: flex; gap: 1rem; justify-content: flex-end;">
                        <a href="<?php echo url('admin/prestamos/index.php'); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-accent">
                            <i class="fas fa-save"></i> Registrar Préstamo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="<?php echo asset('js/sidebar.js'); ?>"></script>
</body>
</html>