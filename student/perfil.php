<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Loan.php';

if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

AuthMiddleware::requireStudent('../public/login.php');

$userId = AuthMiddleware::id();
$username = AuthMiddleware::username();
$userInitial = strtoupper(substr($username, 0, 1));

$userModel = new User($conexion);
$loanModel = new Loan($conexion);

$userInfo = $userModel->findById($userId);
$totalLoans = count($loanModel->history($userId));
$activeLoans = count($loanModel->byUser($userId, 'activo'));

$success = AuthMiddleware::getFlash('success');
$error = AuthMiddleware::getFlash('error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../public/assets/css/bookary.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('student/eventos.php'); ?>" class="sidebar-link">
                    <i class="fas fa-calendar-alt"></i> Eventos
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('student/historial.php'); ?>" class="sidebar-link">
                    <i class="fas fa-history"></i> Historial
                </a>
            </li>
        </ul>
        
        <!-- Perfil en Sidebar -->
        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <div class="sidebar-user-avatar" style="background: var(--color-secondary);">
                    <?php echo $userInitial; ?>
                </div>
                <div class="sidebar-user-details">
                    <h4><?php echo e($username); ?></h4>
                    <p>Estudiante</p>
                </div>
            </div>
            <a href="<?php echo url('student/perfil.php'); ?>" class="btn btn-secondary btn-sm" style="width: 100%; margin-bottom: 0.5rem; background: var(--color-primary);">
                <i class="fas fa-user-cog"></i> Configurar Cuenta
            </a>
            <a href="<?php echo url('public/logout.php'); ?>" class="sidebar-logout" style="background: var(--color-secondary);">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Navbar -->
    <nav class="navbar student-navbar" style="position: relative;">
        <div class="navbar-content">
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
            <a href="<?php echo url('student/dashboard.php'); ?>" class="navbar-brand">Book<span>ary</span></a>
        </div>
        
        <div class="navbar-decorations">
            <i class="fas fa-feather-alt navbar-icon"></i>
            <i class="fas fa-glasses navbar-icon"></i>
            <i class="fas fa-lightbulb navbar-icon"></i>
            <i class="fas fa-magic navbar-icon"></i>
            <i class="fas fa-leaf navbar-icon"></i>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="main-content" id="mainContent">
        <div class="container section">
            
            <!-- Header -->
            <div style="margin-bottom: 2rem;">
                <h1 style="color: var(--color-primary); font-family: 'Playfair Display', serif; margin: 0 0 0.5rem 0;">
                    <i class="fas fa-user-circle"></i> Mi Perfil
                </h1>
                <p style="color: var(--color-secondary); margin: 0;">
                    Gestiona tu información y configuración
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

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
                
                <!-- Columna Izquierda - Información del Usuario -->
                <div>
                    <!-- Tarjeta de Perfil -->
                    <div class="card" style="text-align: center; padding: 2rem; margin-bottom: 2rem;">
                        <div style="width: 120px; height: 120px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, var(--color-secondary), #8FB339); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: white; font-weight: bold; box-shadow: 0 8px 20px rgba(107,142,78,0.3);">
                            <?php echo $userInitial; ?>
                        </div>
                        <h2 style="color: var(--color-primary); margin: 0 0 0.5rem 0; font-size: 1.8rem;">
                            <?php echo e($username); ?>
                        </h2>
                        <p style="color: var(--color-secondary); margin: 0 0 1.5rem 0; font-size: 1.1rem;">
                            <i class="fas fa-graduation-cap"></i> Estudiante
                        </p>
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <span class="badge success">
                                <i class="fas fa-check-circle"></i> Cuenta Activa
                            </span>
                        </div>
                    </div>

                    <!-- Estadísticas Rápidas -->
                    <div class="card" style="padding: 1.5rem;">
                        <h3 style="color: var(--color-primary); margin: 0 0 1rem 0; font-size: 1.2rem;">
                            <i class="fas fa-chart-line"></i> Mis Estadísticas
                        </h3>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f8f9fa; border-radius: 0.5rem;">
                                <span style="color: var(--color-secondary);">
                                    <i class="fas fa-book-reader"></i> Préstamos Activos
                                </span>
                                <strong style="color: var(--color-primary); font-size: 1.2rem;">
                                    <?php echo $activeLoans; ?>
                                </strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f8f9fa; border-radius: 0.5rem;">
                                <span style="color: var(--color-secondary);">
                                    <i class="fas fa-history"></i> Total Leídos
                                </span>
                                <strong style="color: var(--color-primary); font-size: 1.2rem;">
                                    <?php echo $totalLoans; ?>
                                </strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f8f9fa; border-radius: 0.5rem;">
                                <span style="color: var(--color-secondary);">
                                    <i class="fas fa-check-circle"></i> Disponibles
                                </span>
                                <strong style="color: var(--color-secondary); font-size: 1.2rem;">
                                    <?php echo (MAX_ACTIVE_LOANS - $activeLoans); ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Configuración -->
                <div>
                    <!-- Cambiar Contraseña -->
                    <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
                        <h3 style="color: var(--color-primary); margin: 0 0 1.5rem 0; font-size: 1.4rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-lock"></i> Cambiar Contraseña
                        </h3>
                        <form action="<?php echo url('student/actualizar-password.php'); ?>" method="POST" id="passwordForm">
                            <div class="form-group">
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-key"></i> Contraseña Actual *
                                </label>
                                <div style="position: relative;">
                                    <input 
                                        type="password" 
                                        id="current_password" 
                                        name="current_password" 
                                        class="form-input" 
                                        placeholder="Ingresa tu contraseña actual" 
                                        required
                                        style="padding-right: 3rem;"
                                    >
                                    <button 
                                        type="button" 
                                        onclick="togglePasswordVisibility('current_password', 'toggleIcon1')"
                                        style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-secondary); cursor: pointer; font-size: 1.2rem;"
                                    >
                                        <i class="fas fa-eye" id="toggleIcon1"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-lock"></i> Nueva Contraseña *
                                </label>
                                <div style="position: relative;">
                                    <input 
                                        type="password" 
                                        id="new_password" 
                                        name="new_password" 
                                        class="form-input" 
                                        placeholder="Mínimo 6 caracteres" 
                                        required
                                        minlength="6"
                                        style="padding-right: 3rem;"
                                    >
                                    <button 
                                        type="button" 
                                        onclick="togglePasswordVisibility('new_password', 'toggleIcon2')"
                                        style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-secondary); cursor: pointer; font-size: 1.2rem;"
                                    >
                                        <i class="fas fa-eye" id="toggleIcon2"></i>
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
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i> Confirmar Nueva Contraseña *
                                </label>
                                <div style="position: relative;">
                                    <input 
                                        type="password" 
                                        id="confirm_password" 
                                        name="confirm_password" 
                                        class="form-input" 
                                        placeholder="Confirma la nueva contraseña" 
                                        required
                                        style="padding-right: 3rem;"
                                    >
                                    <button 
                                        type="button" 
                                        onclick="togglePasswordVisibility('confirm_password', 'toggleIcon3')"
                                        style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-secondary); cursor: pointer; font-size: 1.2rem;"
                                    >
                                        <i class="fas fa-eye" id="toggleIcon3"></i>
                                    </button>
                                </div>
                                <small class="form-help" id="matchText"></small>
                            </div>

                            <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Limpiar
                                </button>
                                <button type="submit" class="btn btn-accent">
                                    <i class="fas fa-save"></i> Cambiar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Información de la Cuenta -->
                    <div class="card" style="padding: 2rem;">
                        <h3 style="color: var(--color-primary); margin: 0 0 1.5rem 0; font-size: 1.4rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-info-circle"></i> Información de la Cuenta
                        </h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="padding: 1rem; background: #f8f9fa; border-radius: 0.5rem; border-left: 4px solid var(--color-secondary);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <strong style="color: var(--color-primary);">
                                        <i class="fas fa-user"></i> Nombre de Usuario
                                    </strong>
                                </div>
                                <p style="margin: 0; color: var(--color-secondary); font-size: 1.1rem;">
                                    <?php echo e($username); ?>
                                </p>
                            </div>

                            <div style="padding: 1rem; background: #f8f9fa; border-radius: 0.5rem; border-left: 4px solid var(--color-secondary);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <strong style="color: var(--color-primary);">
                                        <i class="fas fa-user-tag"></i> Rol
                                    </strong>
                                </div>
                                <p style="margin: 0; color: var(--color-secondary); font-size: 1.1rem;">
                                    Estudiante
                                </p>
                            </div>

                            <div style="padding: 1rem; background: #f8f9fa; border-radius: 0.5rem; border-left: 4px solid var(--color-secondary);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <strong style="color: var(--color-primary);">
                                        <i class="fas fa-book-reader"></i> Límite de Préstamos
                                    </strong>
                                </div>
                                <p style="margin: 0; color: var(--color-secondary); font-size: 1.1rem;">
                                    <?php echo MAX_ACTIVE_LOANS ?? 3; ?> préstamos simultáneos
                                </p>
                            </div>

                            <div style="padding: 1rem; background: #f8f9fa; border-radius: 0.5rem; border-left: 4px solid var(--color-secondary);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <strong style="color: var(--color-primary);">
                                        <i class="fas fa-calendar-check"></i> Período de Préstamo
                                    </strong>
                                </div>
                                <p style="margin: 0; color: var(--color-secondary); font-size: 1.1rem;">
                                    <?php echo DEFAULT_LOAN_DAYS ?? 15; ?> días
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="../public/assets/js/sidebar.js"></script>
    <script src="../public/assets/js/bookary.js"></script>
    <script>
        // Toggle visibilidad de contraseña
        function togglePasswordVisibility(inputId, iconId) {
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
        const newPasswordInput = document.getElementById('new_password');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');

        newPasswordInput.addEventListener('input', function() {
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
        const confirmInput = document.getElementById('confirm_password');
        const matchText = document.getElementById('matchText');

        confirmInput.addEventListener('input', function() {
            const password = newPasswordInput.value;
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
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const password = newPasswordInput.value;
            const confirm = confirmInput.value;
            
            if (password !== confirm) {
                e.preventDefault();
                matchText.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i> Las contraseñas no coinciden';
                confirmInput.focus();
            }
        });
    </script>
</body>
</html>