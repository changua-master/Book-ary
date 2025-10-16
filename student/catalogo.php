<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/Book.php';

// Verificar autenticación
AuthMiddleware::requireStudent('../public/login.php');

// Obtener libros disponibles
$bookModel = new Book($conexion);
$searchTerm = $_GET['search'] ?? '';
$categoryId = $_GET['category'] ?? null;

if ($searchTerm) {
    $books = $bookModel->searchByTitle($searchTerm);
} elseif ($categoryId) {
    $books = $bookModel->byCategory($categoryId);
} else {
    $books = $bookModel->available();
}

// Obtener categorías para el filtro
$categorias_result = $conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");

$username = AuthMiddleware::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Libros - <?php echo APP_NAME; ?></title>
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
                <a href="<?php echo url('student/catalogo.php'); ?>" class="sidebar-link active">
                    <i class="fas fa-books"></i> Catálogo
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
                    Catálogo de Libros
                </h1>
                <p style="color: var(--color-secondary); margin: 0;">
                    Explora nuestra colección de libros disponibles
                </p>
            </div>

            <!-- Buscador y Filtros -->
            <div style="background: white; padding: 1.5rem; border-radius: 1rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Buscar por título o autor..." 
                        value="<?php echo e($searchTerm); ?>"
                        style="flex: 1; min-width: 250px; padding: 0.75rem 1rem; border: 2px solid #ddd; border-radius: 0.5rem; font-size: 1rem;"
                    >
                    <select name="category" style="padding: 0.75rem 1rem; border: 2px solid #ddd; border-radius: 0.5rem; font-size: 1rem;">
                        <option value="">Todas las categorías</option>
                        <?php while ($categoria = $categorias_result->fetch_assoc()): ?>
                            <option value="<?php echo $categoria['id']; ?>" <?php echo $categoryId == $categoria['id'] ? 'selected' : ''; ?>>
                                <?php echo e($categoria['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" class="btn btn-accent">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <?php if ($searchTerm || $categoryId): ?>
                        <a href="catalogo.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Grid de Libros -->
            <?php if (empty($books)): ?>
                <div class="card" style="text-align: center; padding: 3rem;">
                    <i class="fas fa-book-open" style="font-size: 4rem; color: var(--color-secondary); opacity: 0.3; margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--color-primary);">No se encontraron libros</h3>
                    <p style="color: var(--color-secondary);">
                        <?php if ($searchTerm): ?>
                            Intenta con otros términos de búsqueda
                        <?php else: ?>
                            No hay libros disponibles en este momento
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
                    <?php foreach ($books as $book): ?>
                        <div class="card" style="padding: 1.5rem; transition: all 0.3s;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                <div style="flex: 1;">
                                    <h3 style="color: var(--color-primary); margin: 0 0 0.5rem 0; font-size: 1.25rem;">
                                        <?php echo e($book['titulo']); ?>
                                    </h3>
                                    <p style="color: var(--color-secondary); margin: 0; font-size: 0.95rem;">
                                        <i class="fas fa-user-edit"></i> <?php echo e($book['autor']); ?>
                                    </p>
                                </div>
                                <span style="background: <?php echo $book['ejemplares'] > 0 ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $book['ejemplares'] > 0 ? '#155724' : '#721c24'; ?>; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.9rem;">
                                    <?php echo $book['ejemplares']; ?>
                                </span>
                            </div>

                            <div style="margin: 1rem 0; padding: 1rem 0; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
                                <?php if ($book['editorial']): ?>
                                    <p style="margin: 0.25rem 0; font-size: 0.9rem;">
                                        <i class="fas fa-building"></i> <?php echo e($book['editorial']); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($book['ano_publicacion']): ?>
                                    <p style="margin: 0.25rem 0; font-size: 0.9rem;">
                                        <i class="fas fa-calendar"></i> <?php echo $book['ano_publicacion']; ?>
                                    </p>
                                <?php endif; ?>
                                <p style="margin: 0.25rem 0; font-size: 0.9rem;">
                                    <i class="fas fa-tags"></i> <?php echo e($book['categoria_nombre'] ?? 'Sin categoría'); ?>
                                </p>
                                <?php if ($book['ubicacion']): ?>
                                    <p style="margin: 0.25rem 0; font-size: 0.9rem;">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo e($book['ubicacion']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div style="text-align: center;">
                                <?php if ($book['ejemplares'] > 0): ?>
                                    <p style="color: #28a745; font-weight: 600; margin-bottom: 0.5rem;">
                                        <i class="fas fa-check-circle"></i> Disponible para préstamo
                                    </p>
                                    <small style="color: var(--color-secondary); display: block;">
                                        Solicítalo con el administrador
                                    </small>
                                <?php else: ?>
                                    <p style="color: #dc3545; font-weight: 600;">
                                        <i class="fas fa-times-circle"></i> No disponible
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Scripts -->
    <script src="js/sidebar.js"></script>
</body>
</html>