<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

// Si ya está autenticado, redirigir
if (AuthMiddleware::check()) {
    if (AuthMiddleware::isAdmin()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('student/dashboard.php');
    }
}

// Verificar mensajes
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
    <link rel="stylesheet" href="..\public\assets\css\bookary.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <div class="auth-page">
        <div class="auth-container">
            <div class="form-header">
                <a href="<?php echo url('public/index.php'); ?>" class="navbar-brand">Book<span>ary</span></a>
                <h1 class="auth-title">Bienvenido de Nuevo</h1>
                <p class="form-subtitle">Ingresa tus credenciales para acceder a tu cuenta.</p>
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
                    <label for="username" class="form-label">Usuario</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input" 
                        placeholder="ej. juan.perez" 
                        required
                        autocomplete="username"
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="••••••••" 
                        required
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" class="btn btn-full">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>

            <div class="auth-links">
                <p>¿No tienes una cuenta? <a href="<?php echo url('public/signup.php'); ?>">Regístrate aquí</a></p>
            </div>
        </div>
    </div>

    <script src="<?php echo asset('js/bookary.js'); ?>"></script>
</body>
</html>