/**
 * Sidebar Navigation
 * Maneja la apertura/cierre del menú lateral
 */

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const closeBtn = document.getElementById('closeSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');

    // Abrir sidebar
    function openSidebar() {
        if (sidebar) {
            sidebar.classList.add('active');
            if (overlay) overlay.classList.add('active');
            if (mainContent) mainContent.classList.add('sidebar-active');
            document.body.style.overflow = 'hidden'; // Prevenir scroll
        }
    }

    // Cerrar sidebar
    function closeSidebar() {
        if (sidebar) {
            sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            if (mainContent) mainContent.classList.remove('sidebar-active');
            document.body.style.overflow = ''; // Restaurar scroll
        }
    }

    // Toggle sidebar
    function toggleSidebar() {
        if (sidebar && sidebar.classList.contains('active')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    // Event listeners
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            closeSidebar();
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Cerrar con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });

    // Prevenir que clicks dentro del sidebar lo cierren
    if (sidebar) {
        sidebar.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Marcar link activo
    const currentPath = window.location.pathname;
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    
    sidebarLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname;
        if (currentPath === linkPath || currentPath.includes(linkPath)) {
            link.classList.add('active');
        }
    });

    // Animación de hover en los items
    sidebarLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Responsive: cerrar automáticamente en pantallas grandes
    function handleResize() {
        if (window.innerWidth > 768 && sidebar && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    }

    window.addEventListener('resize', handleResize);
});