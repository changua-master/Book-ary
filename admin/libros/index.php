<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../app/Models/Book.php';

// Verificar autenticación y rol de administrador
AuthMiddleware::requireAdmin('../../public/login.php');

// Función helper para escapar HTML (si no existe)
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// Obtener libros
$bookModel = new Book($conexion);
$books = $bookModel->all();

// Verificar mensajes flash
$success = AuthMiddleware::getFlash('success');
$error = AuthMiddleware::getFlash('error');

$username = AuthMiddleware::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Libros - <?php echo APP_NAME; ?></title>
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
                <a href="<?php echo url('admin/libros/index.php'); ?>" class="sidebar-link active">
                    <i class="fas fa-book"></i> Gestión de Libros
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/prestamos/index.php'); ?>" class="sidebar-link">
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
                        Gestión de Libros
                    </h1>
                    <p style="color: var(--color-secondary); margin: 0.5rem 0 0 0;">
                        Administra el catálogo de la biblioteca
                    </p>
                </div>
                <a href="<?php echo url('admin/libros/crear.php'); ?>" class="btn btn-accent">
                    <i class="fas fa-plus"></i> Agregar Libro
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

            <!-- Buscador -->
            <div style="background: white; padding: 1.5rem; border-radius: 1rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; gap: 1rem;">
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="Buscar por título o autor..." 
                        style="flex: 1; padding: 0.75rem 1rem; border: 2px solid #ddd; border-radius: 0.5rem; font-size: 1rem;"
                    >
                    <button onclick="searchBooks()" class="btn btn-secondary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>

            <!-- Tabla de Libros -->
            <div style="background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: var(--color-primary); color: white;">
                        <tr>
                            <th style="padding: 1rem; text-align: left;">ID</th>
                            <th style="padding: 1rem; text-align: left;">Título</th>
                            <th style="padding: 1rem; text-align: left;">Autor</th>
                            <th style="padding: 1rem; text-align: left;">Categoría</th>
                            <th style="padding: 1rem; text-align: center;">Ejemplares</th>
                            <th style="padding: 1rem; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="booksTableBody">
                        <?php if (empty($books)): ?>
                            <tr>
                                <td colspan="6" style="padding: 2rem; text-align: center; color: var(--color-secondary);">
                                    <i class="fas fa-book-open" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 1rem;"></i>
                                    No hay libros registrados. <a href="<?php echo url('admin/libros/crear.php'); ?>" style="color: var(--color-accent);">Agrega el primero</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($books as $book): ?>
                                <tr class="book-row" style="border-bottom: 1px solid #eee;" data-title="<?php echo e($book['titulo']); ?>" data-author="<?php echo e($book['autor']); ?>">
                                    <td style="padding: 1rem;"><?php echo $book['id']; ?></td>
                                    <td style="padding: 1rem;">
                                        <strong><?php echo e($book['titulo']); ?></strong>
                                        <?php if ($book['ano_publicacion']): ?>
                                            <br><small style="color: var(--color-secondary);">(<?php echo $book['ano_publicacion']; ?>)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 1rem;"><?php echo e($book['autor']); ?></td>
                                    <td style="padding: 1rem;">
                                        <span style="background: var(--color-secondary-light); padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.9rem;">
                                            <?php echo e($book['categoria_nombre'] ?? 'Sin categoría'); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <span style="background: <?php echo $book['ejemplares'] > 0 ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $book['ejemplares'] > 0 ? '#155724' : '#721c24'; ?>; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-weight: 600;">
                                            <?php echo $book['ejemplares']; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <a href="<?php echo url('admin/libros/editar.php?id=' . $book['id']); ?>" 
                                           class="btn btn-secondary btn-sm" 
                                           style="margin: 0 0.25rem;"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmDelete(<?php echo $book['id']; ?>, '<?php echo e($book['titulo']); ?>')" 
                                                class="btn btn-accent btn-sm" 
                                                style="margin: 0 0.25rem; background: #dc3545;"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
    <script>
        // Búsqueda en tiempo real
        function searchBooks() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.book-row');
            
            rows.forEach(row => {
                const title = row.dataset.title.toLowerCase();
                const author = row.dataset.author.toLowerCase();
                
                if (title.includes(searchTerm) || author.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Búsqueda en tiempo real mientras escribe
        document.getElementById('searchInput')?.addEventListener('input', searchBooks);

        // Confirmar eliminación
        function confirmDelete(id, title) {
            if (confirm(`¿Estás seguro de eliminar el libro "${title}"?\n\nEsta acción no se puede deshacer.`)) {
                window.location.href = `<?php echo url('admin/libros/eliminar.php'); ?>?id=${id}`;
            }
        }
    </script>
</body>
</html>