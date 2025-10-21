<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

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
    <link rel="stylesheet" href="../public/assets/css/bookary.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar" style="background: rgba(94,48,35,0.95); backdrop-filter: blur(10px);">
        <div class="container">
            <div class="navbar-content">
                <a href="<?php echo url('public/index.php'); ?>" class="navbar-brand">Book<span>ary</span></a>
                <ul class="navbar-nav">
                    <li><a href="<?php echo url('public/login.php'); ?>" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </a></li>
                    <li><a href="<?php echo url('public/signup.php'); ?>" class="btn btn-accent">
                        <i class="fas fa-user-plus"></i> Registrarse
                    </a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section Mejorado -->
    <header class="hero">
        <!-- Decoraciones flotantes -->
        <div class="hero-decoration"><i class="fas fa-book-open"></i></div>
        <div class="hero-decoration"><i class="fas fa-bookmark"></i></div>
        <div class="hero-decoration"><i class="fas fa-feather-alt"></i></div>
        <div class="hero-decoration"><i class="fas fa-glasses"></i></div>
        
        <div class="container" style="position: relative; z-index: 2;">
            <h1 class="hero-title">Tu biblioteca personal,<br>en cualquier lugar</h1>
            <p class="hero-subtitle">
                Descubre, organiza y disfruta tus próximas lecturas con Bookary.<br>
                Miles de libros esperan por ti.
            </p>
            <div class="hero-actions">
                <a href="<?php echo url('public/signup.php'); ?>" class="btn btn-accent">
                    <i class="fas fa-rocket"></i> Comienza Ahora
                </a>
                <a href="#features" class="btn btn-secondary">
                    <i class="fas fa-arrow-down"></i> Descubre Más
                </a>
            </div>
        </div>
    </header>

    <!-- Sección de Estadísticas -->
    <section class="section" style="background: white; padding: 4rem 0;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 3rem; text-align: center;">
                <div class="animate-scale-in">
                    <div style="font-size: 3.5rem; color: var(--color-accent); margin-bottom: 0.5rem;">
                        <i class="fas fa-books"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; color: var(--color-primary); margin: 0.5rem 0;">500+</h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Libros Disponibles</p>
                </div>
                <div class="animate-scale-in" style="animation-delay: 0.2s;">
                    <div style="font-size: 3.5rem; color: var(--color-secondary); margin-bottom: 0.5rem;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; color: var(--color-primary); margin: 0.5rem 0;">200+</h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Lectores Activos</p>
                </div>
                <div class="animate-scale-in" style="animation-delay: 0.4s;">
                    <div style="font-size: 3.5rem; color: var(--color-primary); margin-bottom: 0.5rem;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; color: var(--color-primary); margin: 0.5rem 0;">50+</h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Eventos Realizados</p>
                </div>
                <div class="animate-scale-in" style="animation-delay: 0.6s;">
                    <div style="font-size: 3.5rem; color: var(--color-accent); margin-bottom: 0.5rem;">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 style="font-size: 2.5rem; color: var(--color-primary); margin: 0.5rem 0;">4.8</h3>
                    <p style="color: var(--color-secondary); font-weight: 600;">Calificación Promedio</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section Mejorado -->
    <section id="features" class="section" style="background: var(--color-light); padding: 5rem 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 4rem;">
                <h2 style="font-family: 'Playfair Display', serif; font-size: 3rem; color: var(--color-primary); margin-bottom: 1rem;">
                    ¿Por qué elegir Bookary?
                </h2>
                <p style="font-size: 1.2rem; color: var(--color-secondary); max-width: 600px; margin: 0 auto;">
                    Una plataforma completa diseñada para amantes de la lectura
                </p>
            </div>

            <div class="dashboard-grid">
                <div class="card animate-scale-in" style="text-align: center; padding: 2.5rem;">
                    <div style="font-size: 4rem; color: var(--color-accent); margin-bottom: 1.5rem;">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="card-title" style="font-size: 1.5rem;">Catálogo Extenso</h3>
                    <p class="card-text">
                        Explora miles de títulos de todos los géneros. Desde clásicos hasta las últimas novedades.
                    </p>
                </div>

                <div class="card animate-scale-in" style="text-align: center; padding: 2.5rem; animation-delay: 0.2s;">
                    <div style="font-size: 4rem; color: var(--color-secondary); margin-bottom: 1.5rem;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3 class="card-title" style="font-size: 1.5rem;">Gestión Inteligente</h3>
                    <p class="card-text">
                        Lleva un control claro de tus préstamos, solicitudes y fechas de devolución.
                    </p>
                </div>

                <div class="card animate-scale-in" style="text-align: center; padding: 2.5rem; animation-delay: 0.4s;">
                    <div style="font-size: 4rem; color: var(--color-primary); margin-bottom: 1.5rem;">
                        <i class="fas fa-calendar-star"></i>
                    </div>
                    <h3 class="card-title" style="font-size: 1.5rem;">Eventos Literarios</h3>
                    <p class="card-text">
                        Participa en clubs de lectura, talleres y encuentros con autores.
                    </p>
                </div>

                <div class="card animate-scale-in" style="text-align: center; padding: 2.5rem; animation-delay: 0.6s;">
                    <div style="font-size: 4rem; color: var(--color-accent); margin-bottom: 1.5rem;">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="card-title" style="font-size: 1.5rem;">Historial Completo</h3>
                    <p class="card-text">
                        Guarda un registro de todas tus lecturas y redescubre tus favoritos.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Cómo Funciona -->
    <section class="section" style="background: white; padding: 5rem 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 4rem;">
                <h2 style="font-family: 'Playfair Display', serif; font-size: 3rem; color: var(--color-primary); margin-bottom: 1rem;">
                    Cómo Funciona
                </h2>
                <p style="font-size: 1.2rem; color: var(--color-secondary);">
                    Comienza tu aventura literaria en 3 simples pasos
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 3rem;">
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: var(--color-accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: bold; box-shadow: 0 8px 20px rgba(182,69,48,0.3);">
                        1
                    </div>
                    <h3 style="color: var(--color-primary); margin-bottom: 1rem; font-size: 1.5rem;">Regístrate</h3>
                    <p style="color: var(--color-secondary);">
                        Crea tu cuenta en segundos y accede a nuestra biblioteca completa
                    </p>
                </div>

                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: var(--color-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: bold; box-shadow: 0 8px 20px rgba(107,142,78,0.3);">
                        2
                    </div>
                    <h3 style="color: var(--color-primary); margin-bottom: 1rem; font-size: 1.5rem;">Explora</h3>
                    <p style="color: var(--color-secondary);">
                        Busca y descubre libros por categoría, autor o título
                    </p>
                </div>

                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: var(--color-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: bold; box-shadow: 0 8px 20px rgba(94,48,35,0.3);">
                        3
                    </div>
                    <h3 style="color: var(--color-primary); margin-bottom: 1rem; font-size: 1.5rem;">Disfruta</h3>
                    <p style="color: var(--color-secondary);">
                        Solicita préstamos y sumérgete en tus lecturas favoritas
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="section" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%); padding: 5rem 0; color: white; text-align: center;">
        <div class="container">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 3rem; margin-bottom: 1.5rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                ¿Listo para comenzar?
            </h2>
            <p style="font-size: 1.3rem; margin-bottom: 2.5rem; opacity: 0.95;">
                Únete a nuestra comunidad de lectores hoy mismo
            </p>
            <a href="<?php echo url('public/signup.php'); ?>" class="btn" style="background: white; color: var(--color-primary); font-size: 1.2rem; padding: 1.2rem 3rem; box-shadow: 0 8px 20px rgba(0,0,0,0.3);">
                <i class="fas fa-rocket"></i> Crear Cuenta Gratis
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem; margin-bottom: 2rem;">
                <div>
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--color-secondary); margin-bottom: 1rem;">
                        Book<span style="color: var(--color-accent);">ary</span>
                    </h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">
                        Tu biblioteca digital favorita. Descubre, lee y comparte tu pasión por los libros.
                    </p>
                </div>
                <div>
                    <h4 style="color: var(--color-secondary); margin-bottom: 1rem;">Enlaces Rápidos</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;">
                            <a href="<?php echo url('public/login.php'); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none;">
                                <i class="fas fa-chevron-right"></i> Iniciar Sesión
                            </a>
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="<?php echo url('public/signup.php'); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none;">
                                <i class="fas fa-chevron-right"></i> Registrarse
                            </a>
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="#features" style="color: rgba(255,255,255,0.8); text-decoration: none;">
                                <i class="fas fa-chevron-right"></i> Características
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 style="color: var(--color-secondary); margin-bottom: 1rem;">Contacto</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem; color: rgba(255,255,255,0.8);">
                            <i class="fas fa-envelope"></i> info@bookary.com
                        </li>
                        <li style="margin-bottom: 0.5rem; color: rgba(255,255,255,0.8);">
                            <i class="fas fa-phone"></i> +57 300 123 4567
                        </li>
                        <li style="margin-bottom: 0.5rem; color: rgba(255,255,255,0.8);">
                            <i class="fas fa-map-marker-alt"></i> Medellín, Colombia
                        </li>
                    </ul>
                </div>
            </div>
            <div style="text-align: center; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
                <p style="color: rgba(255,255,255,0.7); margin: 0;">
                    &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos los derechos reservados.
                </p>
                <div style="margin-top: 1rem;">
                    <a href="#" style="color: rgba(255,255,255,0.8); margin: 0 0.5rem; font-size: 1.5rem;">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" style="color: rgba(255,255,255,0.8); margin: 0 0.5rem; font-size: 1.5rem;">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" style="color: rgba(255,255,255,0.8); margin: 0 0.5rem; font-size: 1.5rem;">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?php echo asset('js/bookary.js'); ?>"></script>
    <script>
        // Smooth scroll para enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animación al hacer scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-scale-in').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            observer.observe(el);
        });
    </script>
</body>
</html>