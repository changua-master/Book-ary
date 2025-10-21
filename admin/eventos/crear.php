<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';

if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

AuthMiddleware::requireAdmin('../../public/login.php');

$username = AuthMiddleware::username();
$userInitial = strtoupper(substr($username, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Evento - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../public/assets/css/bookary.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-layout">
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Panel Admin</h3>
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
                <a href="<?php echo url('admin/eventos/index.php'); ?>" class="sidebar-link active">
                    <i class="fas fa-calendar-alt"></i> Eventos
                </a>
            </li>
        </ul>
        
        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <div class="sidebar-user-avatar">
                    <?php echo $userInitial; ?>
                </div>
                <div class="sidebar-user-details">
                    <h4><?php echo e($username); ?></h4>
                    <p>Administrador</p>
                </div>
            </div>
            <a href="<?php echo url('public/logout.php'); ?>" class="sidebar-logout">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Navbar -->
    <nav class="navbar" style="position: relative;">
        <div class="navbar-content">
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
            <a href="<?php echo url('admin/dashboard.php'); ?>" class="navbar-brand">Book<span>ary</span></a>
        </div>
        
        <div class="navbar-decorations">
            <i class="fas fa-book navbar-icon"></i>
            <i class="fas fa-star navbar-icon"></i>
            <i class="fas fa-heart navbar-icon"></i>
            <i class="fas fa-bookmark navbar-icon"></i>
            <i class="fas fa-crown navbar-icon"></i>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="main-content" id="mainContent">
        <div class="container section">
            
            <div style="max-width: 900px; margin: 0 auto;">
                <!-- Header -->
                <div style="margin-bottom: 2rem;">
                    <h1 style="color: var(--color-primary); font-family: 'Playfair Display', serif; margin: 0 0 0.5rem 0;">
                        Crear Nuevo Evento
                    </h1>
                    <p style="color: var(--color-secondary); margin: 0;">
                        Programa un evento para tu biblioteca
                    </p>
                </div>

                <!-- Formulario -->
                <form action="<?php echo url('admin/eventos/guardar.php'); ?>" method="POST" style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <!-- Columna 1 -->
                        <div>
                            <div class="form-group">
                                <label for="titulo" class="form-label">
                                    <i class="fas fa-heading"></i> Título del Evento *
                                </label>
                                <input 
                                    type="text" 
                                    id="titulo" 
                                    name="titulo" 
                                    class="form-input" 
                                    placeholder="Ej: Club de Lectura" 
                                    required
                                    autofocus
                                    maxlength="200"
                                >
                            </div>

                            <div class="form-group">
                                <label for="fecha_evento" class="form-label">
                                    <i class="fas fa-calendar"></i> Fecha del Evento *
                                </label>
                                <input 
                                    type="date" 
                                    id="fecha_evento" 
                                    name="fecha_evento" 
                                    class="form-input" 
                                    min="<?php echo date('Y-m-d'); ?>"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="hora_inicio" class="form-label">
                                    <i class="fas fa-clock"></i> Hora de Inicio *
                                </label>
                                <input 
                                    type="time" 
                                    id="hora_inicio" 
                                    name="hora_inicio" 
                                    class="form-input" 
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="hora_fin" class="form-label">
                                    <i class="fas fa-clock"></i> Hora de Fin
                                </label>
                                <input 
                                    type="time" 
                                    id="hora_fin" 
                                    name="hora_fin" 
                                    class="form-input"
                                >
                                <small style="color: var(--color-secondary); font-size: 0.85rem;">Opcional</small>
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div>
                            <div class="form-group">
                                <label for="ubicacion" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Ubicación
                                </label>
                                <input 
                                    type="text" 
                                    id="ubicacion" 
                                    name="ubicacion" 
                                    class="form-input" 
                                    placeholder="Ej: Sala de Lectura Principal"
                                    maxlength="100"
                                >
                            </div>

                            <div class="form-group">
                                <label for="cupo_maximo" class="form-label">
                                    <i class="fas fa-users"></i> Cupo Máximo
                                </label>
                                <input 
                                    type="number" 
                                    id="cupo_maximo" 
                                    name="cupo_maximo" 
                                    class="form-input" 
                                    placeholder="Dejar vacío para sin límite" 
                                    min="1"
                                    max="500"
                                >
                                <small style="color: var(--color-secondary); font-size: 0.85rem;">
                                    Opcional - Dejar vacío si no hay límite de asistentes
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="estado" class="form-label">
                                    <i class="fas fa-toggle-on"></i> Estado
                                </label>
                                <select id="estado" name="estado" class="form-input">
                                    <option value="activo" selected>Activo</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción (ancho completo) -->
                    <div class="form-group">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left"></i> Descripción del Evento
                        </label>
                        <textarea 
                            id="descripcion" 
                            name="descripcion" 
                            class="form-input" 
                            rows="5"
                            placeholder="Describe de qué trata el evento, qué actividades se realizarán, qué deben traer los asistentes, etc."
                        ></textarea>
                        <small style="color: var(--color-secondary); font-size: 0.85rem;">
                            Proporciona detalles que ayuden a los estudiantes a decidir si asistir
                        </small>
                    </div>

                    <!-- Botones -->
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee; display: flex; gap: 1rem; justify-content: flex-end;">
                        <a href="<?php echo url('admin/eventos/index.php'); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-accent">
                            <i class="fas fa-save"></i> Crear Evento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="../../public/assets/js/sidebar.js"></script>
    <script src="../../public/assets/js/bookary.js"></script>
</body>
</html>