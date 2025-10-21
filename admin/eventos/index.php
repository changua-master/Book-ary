<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../app/Models/Event.php';

if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

AuthMiddleware::requireAdmin('../../public/login.php');

$eventModel = new Event($conexion);
$eventos = $eventModel->all();

$success = AuthMiddleware::getFlash('success');
$error = AuthMiddleware::getFlash('error');

$username = AuthMiddleware::username();
$userInitial = strtoupper(substr($username, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Eventos - <?php echo APP_NAME; ?></title>
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
                <a href="<?php echo url('admin/libros/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-book"></i> Gestión de Libros
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/solicitudes/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-paper-plane"></i> Solicitudes
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/prestamos/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-book-reader"></i> Préstamos
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/eventos/index.php'); ?>" class="sidebar-link active">
                    <i class="fas fa-calendar-alt"></i> Eventos
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/usuarios/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-users"></i> Usuarios
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
            <i class="fas fa-book navbar-icon" title="Biblioteca"></i>
            <i class="fas fa-star navbar-icon" title="Destacado"></i>
            <i class="fas fa-heart navbar-icon" title="Favoritos"></i>
            <i class="fas fa-bookmark navbar-icon" title="Marcadores"></i>
            <i class="fas fa-crown navbar-icon" title="Premium"></i>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="main-content" id="mainContent">
        <div class="container section">
            
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1 style="color: var(--color-primary); font-family: 'Playfair Display', serif; margin: 0;">
                        Gestión de Eventos
                    </h1>
                    <p style="color: var(--color-secondary); margin: 0.5rem 0 0 0;">
                        Administra los eventos de la biblioteca
                    </p>
                </div>
                <a href="<?php echo url('admin/eventos/crear.php'); ?>" class="btn btn-accent">
                    <i class="fas fa-plus"></i> Crear Evento
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

            <!-- Filtros -->
            <div style="background: white; padding: 1rem; border-radius: 1rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="?estado=activo" class="btn <?php echo ($_GET['estado'] ?? '') === 'activo' ? 'btn-accent' : 'btn-secondary'; ?>">
                        <i class="fas fa-check-circle"></i> Activos
                    </a>
                    <a href="?estado=finalizado" class="btn <?php echo ($_GET['estado'] ?? '') === 'finalizado' ? 'btn-accent' : 'btn-secondary'; ?>">
                        <i class="fas fa-flag-checkered"></i> Finalizados
                    </a>
                    <a href="?estado=cancelado" class="btn <?php echo ($_GET['estado'] ?? '') === 'cancelado' ? 'btn-accent' : 'btn-secondary'; ?>">
                        <i class="fas fa-ban"></i> Cancelados
                    </a>
                    <a href="<?php echo url('admin/eventos/index.php'); ?>" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Todos
                    </a>
                </div>
            </div>

            <!-- Grid de Eventos -->
            <?php if (empty($eventos)): ?>
                <div class="card" style="text-align: center; padding: 3rem;">
                    <i class="fas fa-calendar-alt" style="font-size: 4rem; color: var(--color-secondary); opacity: 0.3; margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--color-primary);">No hay eventos registrados</h3>
                    <p style="color: var(--color-secondary); margin-bottom: 2rem;">
                        Crea el primer evento para tu biblioteca
                    </p>
                    <a href="<?php echo url('admin/eventos/crear.php'); ?>" class="btn btn-accent">
                        <i class="fas fa-plus"></i> Crear Primer Evento
                    </a>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
                    <?php foreach ($eventos as $evento): 
                        $esPasado = strtotime($evento['fecha_evento']) < strtotime('today');
                        $esHoy = strtotime($evento['fecha_evento']) == strtotime('today');
                        $tieneCupo = !$evento['cupo_maximo'] || $evento['inscritos'] < $evento['cupo_maximo'];
                    ?>
                        <div class="card" style="padding: 0; overflow: hidden; <?php echo $evento['estado'] !== 'activo' ? 'opacity: 0.7;' : ''; ?>">
                            <!-- Header del Evento -->
                            <div style="padding: 1.5rem; background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%); color: white;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                    <h3 style="margin: 0; font-size: 1.3rem;">
                                        <?php echo e($evento['titulo']); ?>
                                    </h3>
                                    <?php if ($evento['estado'] === 'activo'): ?>
                                        <span class="badge success" style="background: rgba(255,255,255,0.2); color: white;">
                                            <i class="fas fa-check-circle"></i> Activo
                                        </span>
                                    <?php elseif ($evento['estado'] === 'cancelado'): ?>
                                        <span class="badge error" style="background: rgba(220,53,69,0.9); color: white;">
                                            <i class="fas fa-ban"></i> Cancelado
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(108,117,125,0.9); color: white;">
                                            <i class="fas fa-flag-checkered"></i> Finalizado
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div style="display: flex; gap: 1.5rem; font-size: 0.9rem; margin-top: 1rem;">
                                    <span>
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($evento['hora_inicio'])); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Contenido del Evento -->
                            <div style="padding: 1.5rem;">
                                <p style="color: var(--color-neutral); margin-bottom: 1rem; min-height: 60px;">
                                    <?php echo e(substr($evento['descripcion'], 0, 120)); ?><?php echo strlen($evento['descripcion']) > 120 ? '...' : ''; ?>
                                </p>

                                <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.9rem; color: var(--color-secondary);">
                                    <?php if ($evento['ubicacion']): ?>
                                        <div class="info-row">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?php echo e($evento['ubicacion']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($evento['cupo_maximo']): ?>
                                        <div class="info-row">
                                            <i class="fas fa-users"></i>
                                            <span><?php echo $evento['inscritos']; ?> / <?php echo $evento['cupo_maximo']; ?> inscritos</span>
                                        </div>
                                        <div style="background: #f0f0f0; border-radius: 0.5rem; height: 8px; overflow: hidden;">
                                            <div style="background: var(--color-accent); height: 100%; width: <?php echo ($evento['cupo_maximo'] > 0) ? ($evento['inscritos'] / $evento['cupo_maximo'] * 100) : 0; ?>%; transition: width 0.3s;"></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="info-row">
                                            <i class="fas fa-users"></i>
                                            <span><?php echo $evento['inscritos']; ?> inscritos (sin límite)</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Acciones -->
                                <div style="display: flex; gap: 0.5rem; border-top: 1px solid #eee; padding-top: 1rem;">
                                    <a href="<?php echo url('admin/eventos/editar.php?id=' . $evento['id']); ?>" 
                                       class="btn btn-secondary btn-sm" 
                                       style="flex: 1;">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <button onclick="confirmDelete(<?php echo $evento['id']; ?>, '<?php echo e($evento['titulo']); ?>')" 
                                            class="btn btn-sm" 
                                            style="background: #dc3545; color: white;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Scripts -->
    <script src="../../public/assets/js/sidebar.js"></script>
    <script src="../../public/assets/js/bookary.js"></script>
    <script>
        function confirmDelete(id, titulo) {
            if (confirm(`¿Estás seguro de eliminar el evento "${titulo}"?\n\nEsta acción no se puede deshacer.`)) {
                window.location.href = `<?php echo url('admin/eventos/eliminar.php'); ?>?id=${id}`;
            }
        }
    </script>
</body>
</html>