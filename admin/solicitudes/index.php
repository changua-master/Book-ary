<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../app/Models/LoanRequest.php';

// Verificar autenticación
AuthMiddleware::requireAdmin('../../public/login.php');

// Obtener solicitudes pendientes
$requestModel = new LoanRequest($conexion);
$pendingRequests = $requestModel->getPending();

// Mensajes
$success = AuthMiddleware::getFlash('success');
$error = AuthMiddleware::getFlash('error');

$username = AuthMiddleware::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Préstamo - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../public/assets/css/bookary.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-layout">
    
<div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Panel Admin</h3>
            <button class="sidebar-close" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="<?php echo url('admin/dashboard.php'); ?>" class="sidebar-link active">
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
                    <?php if ($pendingRequests > 0): ?>
                        <span style="background: #dc3545; color: white; border-radius: 50%; padding: 0.2rem 0.5rem; font-size: 0.75rem; margin-left: 0.5rem;">
                            <?php echo $pendingRequests; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/prestamos/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-book-reader"></i> Préstamos
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/eventos/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-calendar-alt"></i> Eventos
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/usuarios/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-users"></i> Usuarios
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo url('admin/reportes/index.php'); ?>" class="sidebar-link">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
            </li>
        </ul>
        
        <!-- Perfil y Logout en Sidebar -->
        <!-- Perfil y Logout en Sidebar -->
<div class="sidebar-user">
    <div class="sidebar-user-info">
        <div class="sidebar-user-avatar">
            <?php echo $userInitial; ?>
        </div>
        <div class="sidebar-user-details">
            <h4><?php echo htmlspecialchars($username); ?></h4>
            <p>Administrador</p>
        </div>
    </div>
    <a href="<?php echo url('admin/perfil.php'); ?>" class="btn btn-secondary btn-sm" style="width: 100%; margin-bottom: 0.5rem; background: var(--color-primary);">
        <i class="fas fa-user-cog"></i> Configurar Cuenta
    </a>
    <a href="<?php echo url('public/logout.php'); ?>" class="sidebar-logout">
        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
    </a>
</div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Navbar con decoraciones -->
    <nav class="navbar" style="position: relative;">
        <div class="navbar-content">
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
            <a href="<?php echo url('admin/dashboard.php'); ?>" class="navbar-brand">Book<span>ary</span></a>
        </div>
        
        <!-- Iconos decorativos dispersos con levitación -->
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h1 style="color: var(--color-primary); font-family: 'Playfair Display', serif; margin: 0;">
                        Solicitudes de Préstamo
                    </h1>
                    <p style="color: var(--color-secondary); margin: 0.5rem 0 0 0;">
                        <?php echo count($pendingRequests); ?> solicitud(es) pendiente(s)
                    </p>
                </div>
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

            <!-- Lista de Solicitudes -->
            <?php if (empty($pendingRequests)): ?>
                <div class="card" style="text-align: center; padding: 3rem;">
                    <i class="fas fa-inbox" style="font-size: 4rem; color: var(--color-secondary); opacity: 0.3; margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--color-primary);">No hay solicitudes pendientes</h3>
                    <p style="color: var(--color-secondary);">
                        Las nuevas solicitudes aparecerán aquí
                    </p>
                </div>
            <?php else: ?>
                <div style="display: grid; gap: 1.5rem;">
                    <?php foreach ($pendingRequests as $request): ?>
                        <div class="card" style="padding: 1.5rem; border-left: 4px solid #ffc107;">
                            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                                <!-- Información -->
                                <div style="flex: 1; min-width: 300px;">
                                    <h3 style="color: var(--color-primary); margin: 0 0 0.5rem 0; font-size: 1.3rem;">
                                        <i class="fas fa-book"></i> <?php echo htmlspecialchars($request['libro_titulo']); ?>
                                    </h3>
                                    <p style="color: var(--color-secondary); margin: 0 0 0.5rem 0;">
                                        <i class="fas fa-user-edit"></i> Autor: <?php echo htmlspecialchars($request['libro_autor']); ?>
                                    </p>
                                    <p style="color: var(--color-secondary); margin: 0 0 0.5rem 0;">
                                        <i class="fas fa-user"></i> Solicitado por: <strong><?php echo htmlspecialchars($request['usuario_nombre']); ?></strong>
                                    </p>
                                    <p style="color: var(--color-secondary); margin: 0 0 0.5rem 0; font-size: 0.9rem;">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($request['fecha_solicitud'])); ?>
                                    </p>
                                    <p style="margin: 0; font-size: 0.9rem;">
                                        <i class="fas fa-copy"></i> Disponibles: 
                                        <span style="color: <?php echo $request['libro_ejemplares'] > 0 ? '#28a745' : '#dc3545'; ?>; font-weight: 600;">
                                            <?php echo $request['libro_ejemplares']; ?>
                                        </span>
                                    </p>
                                    
                                    <?php if ($request['notas_usuario']): ?>
                                        <div style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 0.5rem;">
                                            <strong style="color: var(--color-primary);">Notas del estudiante:</strong>
                                            <p style="margin: 0.5rem 0 0 0;"><?php echo htmlspecialchars($request['notas_usuario']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Acciones -->
                                <div style="text-align: right; min-width: 200px;">
                                    <button onclick="openApproveModal(<?php echo $request['id']; ?>, '<?php echo htmlspecialchars($request['libro_titulo'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($request['usuario_nombre'], ENT_QUOTES); ?>')" 
                                            class="btn btn-accent" 
                                            style="width: 100%; margin-bottom: 0.5rem; background: #28a745;"
                                            <?php echo $request['libro_ejemplares'] <= 0 ? 'disabled' : ''; ?>>
                                        <i class="fas fa-check"></i> Aprobar
                                    </button>
                                    <button onclick="openRejectModal(<?php echo $request['id']; ?>, '<?php echo htmlspecialchars($request['libro_titulo'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($request['usuario_nombre'], ENT_QUOTES); ?>')" 
                                            class="btn btn-secondary" 
                                            style="width: 100%; background: #dc3545;">
                                        <i class="fas fa-times"></i> Rechazar
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal Aprobar -->
    <div id="approveModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 1rem; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <h2 style="color: var(--color-primary); margin: 0 0 1rem 0;">Aprobar Solicitud</h2>
            <p style="margin-bottom: 1rem;">
                <strong>Libro:</strong> <span id="approveBookTitle"></span><br>
                <strong>Estudiante:</strong> <span id="approveUserName"></span>
            </p>
            
            <form action="<?php echo url('admin/solicitudes/aprobar.php'); ?>" method="POST">
                <input type="hidden" name="request_id" id="approveRequestId">
                
                <div class="form-group">
                    <label for="approve_notes" class="form-label">Notas para el estudiante (opcional)</label>
                    <textarea 
                        id="approve_notes" 
                        name="notes" 
                        class="form-input" 
                        rows="3"
                        placeholder="Mensaje para el estudiante..."
                    ></textarea>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <button type="button" onclick="closeApproveModal()" class="btn btn-secondary" style="flex: 1;">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-accent" style="flex: 1; background: #28a745;">
                        <i class="fas fa-check"></i> Aprobar y Crear Préstamo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Rechazar -->
    <div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 1rem; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <h2 style="color: var(--color-primary); margin: 0 0 1rem 0;">Rechazar Solicitud</h2>
            <p style="margin-bottom: 1rem;">
                <strong>Libro:</strong> <span id="rejectBookTitle"></span><br>
                <strong>Estudiante:</strong> <span id="rejectUserName"></span>
            </p>
            
            <form action="<?php echo url('admin/solicitudes/rechazar.php'); ?>" method="POST">
                <input type="hidden" name="request_id" id="rejectRequestId">
                
                <div class="form-group">
                    <label for="reject_notes" class="form-label">Motivo del rechazo *</label>
                    <textarea 
                        id="reject_notes" 
                        name="notes" 
                        class="form-input" 
                        rows="3"
                        placeholder="Explica el motivo del rechazo..."
                        required
                    ></textarea>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <button type="button" onclick="closeRejectModal()" class="btn btn-secondary" style="flex: 1;">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-accent" style="flex: 1; background: #dc3545;">
                        <i class="fas fa-times"></i> Rechazar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../public/assets/js/sidebar.js"></script>
    <script>
        // Modal Aprobar
        function openApproveModal(requestId, bookTitle, userName) {
            document.getElementById('approveRequestId').value = requestId;
            document.getElementById('approveBookTitle').textContent = bookTitle;
            document.getElementById('approveUserName').textContent = userName;
            document.getElementById('approveModal').style.display = 'flex';
        }

        function closeApproveModal() {
            document.getElementById('approveModal').style.display = 'none';
        }

        // Modal Rechazar
        function openRejectModal(requestId, bookTitle, userName) {
            document.getElementById('rejectRequestId').value = requestId;
            document.getElementById('rejectBookTitle').textContent = bookTitle;
            document.getElementById('rejectUserName').textContent = userName;
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }

        // Cerrar modales con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeApproveModal();
                closeRejectModal();
            }
        });

        // Cerrar al hacer clic fuera
        document.getElementById('approveModal').addEventListener('click', function(e) {
            if (e.target === this) closeApproveModal();
        });

        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) closeRejectModal();
        });
    </script>
</body>
</html>