<?php
// Cargar configuración
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

// Si ya está autenticado, redirigir según rol
if (AuthMiddleware::check()) {
    if (AuthMiddleware::isAdmin()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('student/dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="public\assets\css\bookary.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="<?php echo url('public/index.php'); ?>" class="navbar-brand">Book<span>ary</span></a>
                <ul class="navbar-nav">
                    <li><a href="<?php echo url('public/login.php'); ?>" class="btn btn-secondary">Iniciar Sesión</a></li>
                    <li><a href="<?php echo url('public/signup.php'); ?>" class="btn btn-accent">Registrarse</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="container">
            <h1 class="hero-title animate-slide-up">Tu biblioteca personal, en cualquier lugar.</h1>
            <p class="hero-subtitle animate-slide-up">Descubre, organiza y disfruta tus próximas lecturas con Bookary.</p>
            <div class="hero-actions animate-fade-in">
                <a href="<?php echo url('public/signup.php'); ?>" class="btn btn-accent">
                    Comienza ahora <i class="fas fa-arrow-right"></i>
                </a>
                <a href="#features" class="btn btn-secondary">
                    Descubre más
                </a>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="section">
        <div class="container">
            <div class="dashboard-grid">
                <div class="card animate-scale-in">
                    <div class="card-icon"><i class="fas fa-book-open"></i></div>
                    <h3 class="card-title">Catálogo Extenso</h3>
                    <p class="card-text">Explora miles de títulos de todos los géneros y autores.</p>
                </div>
                <div class="card animate-scale-in">
                    <div class="card-icon"><i class="fas fa-tasks"></i></div>
                    <h3 class="card-title">Gestión de Préstamos</h3>
                    <p class="card-text">Lleva un control claro y sencillo de los libros que has prestado.</p>
                </div>
                <div class="card animate-scale-in">
                    <div class="card-icon"><i class="fas fa-history"></i></div>
                    <h3 class="card-title">Historial de Lectura</h3>
                    <p class="card-text">Guarda un registro de todas tus lecturas y redescubre tus favoritos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="<?php echo asset('js/bookary.js'); ?>"></script>
</body>
</html>