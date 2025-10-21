<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/Book.php';
require_once __DIR__ . '/../app/Models/LoanRequest.php';

// Función helper para escapar HTML (si no existe)
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

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

// Verificar mensajes
$success = AuthMiddleware::getFlash('success');
$error = AuthMiddleware::getFlash('error');

$username = AuthMiddleware::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Libros - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo url('public/assets/css/bookary.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Fix crítico para botones */
        .book-request-btn {
            position: relative !important;
            z-index: 100 !important;
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        
        .card {
            position: relative;
        }
        
        .card::after,
        .card::before {
            pointer-events: none !important;
        }
    </style>
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
            
            <!-- Header -->
            <div style="margin-bottom: 2rem;">
                <h1 style="color: var(--color-primary); font-family: 'Playfair Display', serif; margin: 0 0 0.5rem 0;">
                    Catálogo de Libros
                </h1>
                <p style="color: var(--color-secondary); margin: 0;">
                    Explora nuestra colección y solicita libros
                </p>
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
                        <a href="<?php echo url('student/catalogo.php'); ?>" class="btn btn-secondary">
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
                                    <!-- CAMBIO CRÍTICO: Usar data-attributes en lugar de onclick -->
                                    <button 
                                        type="button"
                                        class="btn btn-accent book-request-btn" 
                                        data-book-id="<?php echo $book['id']; ?>"
                                        data-book-title="<?php echo e($book['titulo']); ?>"
                                        style="width: 100%;">
                                        <i class="fas fa-paper-plane"></i> Solicitar Préstamo
                                    </button>
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

    <!-- Modal de Solicitud -->
    <div id="requestModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 1rem; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <h2 style="color: var(--color-primary); margin: 0 0 1rem 0;">Solicitar Préstamo</h2>
            <p style="color: var(--color-secondary); margin-bottom: 1.5rem;">
                Libro: <strong id="modalBookTitle"></strong>
            </p>
            
            <form action="<?php echo url('student/solicitar_prestamo.php'); ?>" method="POST">
                <input type="hidden" name="book_id" id="modalBookId">
                
                <div class="form-group">
                    <label for="notes" class="form-label">Notas (opcional)</label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        class="form-input" 
                        rows="3"
                        placeholder="¿Alguna nota sobre tu solicitud?"
                    ></textarea>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <button type="button" id="closeModalBtn" class="btn btn-secondary" style="flex: 1;">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-accent" style="flex: 1;">
                        <i class="fas fa-paper-plane"></i> Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?php echo url('public/assets/js/sidebar.js'); ?>"></script>
    <script>
        // ============================================
        // SOLUCIÓN: Usar addEventListener en lugar de onclick
        // ============================================
        
        console.log('=== CATALOGO.PHP CARGADO ===');
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Cargado');
            
            const modal = document.getElementById('requestModal');
            const modalTitle = document.getElementById('modalBookTitle');
            const modalBookId = document.getElementById('modalBookId');
            const closeBtn = document.getElementById('closeModalBtn');
            
            // Verificar que elementos existen
            console.log('Modal:', modal ? 'OK' : 'ERROR');
            console.log('Modal Title:', modalTitle ? 'OK' : 'ERROR');
            console.log('Modal Book ID:', modalBookId ? 'OK' : 'ERROR');
            
            // Obtener TODOS los botones de solicitud
            const requestButtons = document.querySelectorAll('.book-request-btn');
            console.log('Botones encontrados:', requestButtons.length);
            
            // Agregar evento a cada botón
            requestButtons.forEach((button, index) => {
                console.log(`Configurando botón ${index + 1}`);
                
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const bookId = this.getAttribute('data-book-id');
                    const bookTitle = this.getAttribute('data-book-title');
                    
                    console.log('✅ Click detectado!');
                    console.log('Book ID:', bookId);
                    console.log('Book Title:', bookTitle);
                    
                    // Actualizar modal
                    modalBookId.value = bookId;
                    modalTitle.textContent = bookTitle;
                    
                    // Mostrar modal
                    modal.style.display = 'flex';
                    console.log('Modal abierto');
                });
            });
            
            // Cerrar modal
            function closeModal() {
                console.log('Cerrando modal');
                modal.style.display = 'none';
            }
            
            if (closeBtn) {
                closeBtn.addEventListener('click', closeModal);
            }
            
            // Cerrar al hacer clic fuera
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
            
            // Cerrar con ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    closeModal();
                }
            });
            
            console.log('=== CONFIGURACIÓN COMPLETA ===');
        });
    </script>
</body>
</html>