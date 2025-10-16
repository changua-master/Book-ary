<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';

// Verificar autenticación y rol de administrador
AuthMiddleware::requireAdmin('../../public/login.php');

// Obtener categorías
$categorias_result = $conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");

$username = AuthMiddleware::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Libro - <?php echo APP_NAME; ?></title>
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
                <a href="<?php echo url('admin/libros/index.php'); ?>" class="sidebar-link active">
                    <i class="fas fa-book"></i> Gestión de Libros
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
            
            <div style="max-width: 900px; margin: 0 auto;">
                <!-- Header -->
                <div style="margin-bottom: 2rem;">
                    <h1 style="color: var(--color-primary); font-family: 'Playfair Display', serif; margin: 0 0 0.5rem 0;">
                        Agregar Nuevo Libro
                    </h1>
                    <p style="color: var(--color-secondary); margin: 0;">
                        Completa los datos para añadir un nuevo libro al catálogo
                    </p>
                </div>

                <!-- Formulario -->
                <form action="<?php echo url('admin/libros/guardar.php'); ?>" method="POST" style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <!-- Columna 1 -->
                        <div>
                            <div class="form-group">
                                <label for="titulo" class="form-label">
                                    <i class="fas fa-book"></i> Título *
                                </label>
                                <input 
                                    type="text" 
                                    id="titulo" 
                                    name="titulo" 
                                    class="form-input" 
                                    placeholder="Ej: Cien Años de Soledad" 
                                    required
                                    autofocus
                                >
                            </div>

                            <div class="form-group">
                                <label for="autor" class="form-label">
                                    <i class="fas fa-user-edit"></i> Autor *
                                </label>
                                <input 
                                    type="text" 
                                    id="autor" 
                                    name="autor" 
                                    class="form-input" 
                                    placeholder="Ej: Gabriel García Márquez" 
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="editorial" class="form-label">
                                    <i class="fas fa-building"></i> Editorial
                                </label>
                                <input 
                                    type="text" 
                                    id="editorial" 
                                    name="editorial" 
                                    class="form-input" 
                                    placeholder="Ej: Sudamericana"
                                >
                            </div>
                            
                            <div class="form-group">
                                <label for="id_categoria" class="form-label">
                                    <i class="fas fa-tags"></i> Categoría *
                                </label>
                                <select id="id_categoria" name="id_categoria" class="form-input" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php while ($categoria = $categorias_result->fetch_assoc()): ?>
                                        <option value="<?php echo $categoria['id']; ?>">
                                            <?php echo e($categoria['nombre']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div>
                            <div class="form-group">
                                <label for="isbn" class="form-label">
                                    <i class="fas fa-barcode"></i> ISBN
                                </label>
                                <input 
                                    type="text" 
                                    id="isbn" 
                                    name="isbn" 
                                    class="form-input" 
                                    placeholder="Ej: 978-0307350444"
                                    pattern="[0-9\-]+"
                                >
                                <small style="color: var(--color-secondary); font-size: 0.85rem;">Formato: 978-0307350444</small>
                            </div>

                            <div class="form-group">
                                <label for="ano_publicacion" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Año de Publicación
                                </label>
                                <input 
                                    type="number" 
                                    id="ano_publicacion" 
                                    name="ano_publicacion" 
                                    class="form-input" 
                                    placeholder="Ej: 1967" 
                                    min="1000" 
                                    max="<?php echo date('Y'); ?>"
                                >
                            </div>

                            <div class="form-group">
                                <label for="ejemplares" class="form-label">
                                    <i class="fas fa-copy"></i> Ejemplares *
                                </label>
                                <input 
                                    type="number" 
                                    id="ejemplares" 
                                    name="ejemplares" 
                                    class="form-input" 
                                    value="1" 
                                    min="0"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="ubicacion" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Ubicación
                                </label>
                                <input 
                                    type="text" 
                                    id="ubicacion" 
                                    name="ubicacion" 
                                    class="form-input" 
                                    placeholder="Ej: Estantería A-3"
                                >
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee; display: flex; gap: 1rem; justify-content: flex-end;">
                        <a href="<?php echo url('admin/libros/index.php'); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-accent">
                            <i class="fas fa-save"></i> Guardar Libro
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