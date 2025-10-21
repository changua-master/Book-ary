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

$error = $_GET['error'] ?? null;
$success = AuthMiddleware::getFlash('success');
$errorMessage = AuthMiddleware::getFlash('error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../public/assets/css/bookary.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="auth-page">
        <div class="auth-container">
            <div class="form-header">
                <a href="<?php echo url('public/index.php'); ?>" class="navbar-brand">
                    Book<span>ary</span>
                </a>
                <h1 class="auth-title">¡Bienvenido de Nuevo!</h1>
                <p class="form-subtitle">Ingresa tus credenciales para acceder</p>
            </div>

            <?php if ($error == '1' || $errorMessage): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $errorMessage ?: 'Credenciales incorrectas. Por favor, intenta de nuevo.'; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="message success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo url('public/login_handler.php'); ?>" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Usuario
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input" 
                        placeholder="Ingresa tu nombre de usuario" 
                        required
                        autocomplete="username"
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Contraseña
                    </label>
                    <div style="position: relative;">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="Ingresa tu contraseña" 
                            required
                            autocomplete="current-password"
                            style="padding-right: 3rem;"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-secondary); cursor: pointer; font-size: 1.2rem;"
                        >
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-full">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>

            <div class="auth-links">
                <p style="margin-bottom: 1rem;">
                    ¿No tienes una cuenta? <a href="<?php echo url('public/signup.php'); ?>">Regístrate aquí</a>
                </p>
                <p>
                    <a href="<?php echo url('public/index.php'); ?>" style="color: var(--color-secondary);">
                        <i class="fas fa-arrow-left"></i> Volver al inicio
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="<?php echo asset('js/bookary.js'); ?>"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Animación del contenedor
        document.querySelector('.auth-container').style.animation = 'containerAppear 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
    </script>
</body>
</html>