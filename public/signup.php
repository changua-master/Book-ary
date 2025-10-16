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
$error = AuthMiddleware::getFlash('error');
$success = AuthMiddleware::getFlash('success');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="..\public\assets\css\bookary.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="auth-page">
        <div class="auth-container">
            <div class="form-header">
                <a href="<?php echo url('public/index.php'); ?>" class="navbar-brand">Book<span>ary</span></a>
                <h1 class="auth-title">Crear Cuenta</h1>
                <p class="form-subtitle">Únete a nuestra comunidad literaria</p>
            </div>

            <?php if ($error): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="message success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo url('public/signup_handler.php'); ?>" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username" class="form-label">Nombre de Usuario</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input" 
                        placeholder="Ingresa tu nombre de usuario" 
                        required
                        minlength="3"
                        autocomplete="username"
                        autofocus
                    >
                    <small class="form-help">Mínimo 3 caracteres</small>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Crea una contraseña segura" 
                        required
                        minlength="6"
                        autocomplete="new-password"
                    >
                    <small class="form-help">Mínimo 6 caracteres</small>
                </div>

                <div class="form-group">
                    <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        class="form-input" 
                        placeholder="Confirma tu contraseña" 
                        required
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="btn btn-full">
                    <i class="fas fa-user-plus"></i> Crear Cuenta
                </button>
            </form>

            <div class="auth-links">
                <p>¿Ya tienes cuenta? <a href="<?php echo url('public/login.php'); ?>">Inicia sesión</a></p>
            </div>
        </div>
    </div>

    <script src="<?php echo asset('js/bookary.js'); ?>"></script>
    <script>
        // Validación de contraseñas en tiempo real
        document.getElementById('password_confirm').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            
            if (confirm && password !== confirm) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>