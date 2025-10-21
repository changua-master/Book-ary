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

$error = AuthMiddleware::getFlash('error');
$success = AuthMiddleware::getFlash('success');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - <?php echo APP_NAME; ?></title>
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
                <h1 class="auth-title">¡Únete a Nosotros!</h1>
                <p class="form-subtitle">Crea tu cuenta y descubre un mundo de lectura</p>
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

            <form action="<?php echo url('public/signup_handler.php'); ?>" method="POST" class="auth-form" id="signupForm">
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Nombre de Usuario
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input" 
                        placeholder="Elige un nombre de usuario único" 
                        required
                        minlength="3"
                        autocomplete="username"
                        autofocus
                    >
                    <small class="form-help">
                        <i class="fas fa-info-circle"></i> Mínimo 3 caracteres
                    </small>
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
                            placeholder="Crea una contraseña segura" 
                            required
                            minlength="6"
                            autocomplete="new-password"
                            style="padding-right: 3rem;"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password', 'toggleIcon1')"
                            style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-secondary); cursor: pointer; font-size: 1.2rem;"
                        >
                            <i class="fas fa-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                    <div id="passwordStrength" style="margin-top: 0.5rem; height: 4px; background: #e0e0e0; border-radius: 2px; overflow: hidden;">
                        <div id="strengthBar" style="height: 100%; width: 0%; transition: all 0.3s; background: #dc3545;"></div>
                    </div>
                    <small class="form-help" id="strengthText">
                        <i class="fas fa-shield-alt"></i> Mínimo 6 caracteres
                    </small>
                </div>

                <div class="form-group">
                    <label for="password_confirm" class="form-label">
                        <i class="fas fa-lock"></i> Confirmar Contraseña
                    </label>
                    <div style="position: relative;">
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            class="form-input" 
                            placeholder="Confirma tu contraseña" 
                            required
                            autocomplete="new-password"
                            style="padding-right: 3rem;"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password_confirm', 'toggleIcon2')"
                            style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-secondary); cursor: pointer; font-size: 1.2rem;"
                        >
                            <i class="fas fa-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                    <small class="form-help" id="matchText"></small>
                </div>

                <button type="submit" class="btn btn-full" id="submitBtn">
                    <i class="fas fa-user-plus"></i> Crear Cuenta
                </button>
            </form>

            <div class="auth-links">
                <p style="margin-bottom: 1rem;">
                    ¿Ya tienes cuenta? <a href="<?php echo url('public/login.php'); ?>">Inicia sesión</a>
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
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
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

        // Verificar fortaleza de contraseña
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let text = '';
            let color = '';

            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            const width = (strength / 5) * 100;
            strengthBar.style.width = width + '%';

            if (strength <= 1) {
                color = '#dc3545';
                text = '<i class="fas fa-times-circle"></i> Contraseña débil';
            } else if (strength <= 3) {
                color = '#ffc107';
                text = '<i class="fas fa-exclamation-circle"></i> Contraseña media';
            } else {
                color = '#28a745';
                text = '<i class="fas fa-check-circle"></i> Contraseña fuerte';
            }

            strengthBar.style.background = color;
            strengthText.innerHTML = text;
        });

        // Validación de contraseñas coincidentes
        const confirmInput = document.getElementById('password_confirm');
        const matchText = document.getElementById('matchText');

        confirmInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirm = this.value;
            
            if (confirm.length > 0) {
                if (password === confirm) {
                    matchText.innerHTML = '<i class="fas fa-check-circle" style="color: #28a745;"></i> Las contraseñas coinciden';
                    this.style.borderColor = '#28a745';
                    this.setCustomValidity('');
                } else {
                    matchText.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i> Las contraseñas no coinciden';
                    this.style.borderColor = '#dc3545';
                    this.setCustomValidity('Las contraseñas no coinciden');
                }
            } else {
                matchText.innerHTML = '';
                this.style.borderColor = '';
            }
        });

        // Validar al enviar
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            
            if (password !== confirm) {
                e.preventDefault();
                matchText.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i> Las contraseñas no coinciden';
                confirmInput.focus();
            }
        });

        // Animación del contenedor
        document.querySelector('.auth-container').style.animation = 'containerAppear 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
    </script>
</body>
</html>