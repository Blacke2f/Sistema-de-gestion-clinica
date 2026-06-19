// =====================================================
// SISGECLIN - JavaScript Principal (Laravel Edition)
// =====================================================

// Variables globales
let CSRF_TOKEN = '';
let MODULE = '';

// =====================================================
// INICIALIZACIÓN
// =====================================================
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.CSRF_TOKEN !== 'undefined') {
        CSRF_TOKEN = window.CSRF_TOKEN;
    }
    if (typeof window.MODULE !== 'undefined') {
        MODULE = window.MODULE;
    }

    initDarkMode();
    initSidebar();
    initForms(); // vacío — cada vista Blade maneja sus propios formularios

    // Cerrar modales con Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAllModals();
    });

    // Cerrar modales clickeando fuera
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            closeAllModals();
        }
    });

    initProfileMenu();
});

// =====================================================
// MODO OSCURO
// =====================================================
function initDarkMode() {
    const switchMode = document.querySelector('.switch-mode');
    const savedMode  = localStorage.getItem('darkMode');

    const updateTooltip = (isDark) => {
        if (switchMode) {
            switchMode.title = isDark
                ? 'Modo oscuro activo — clic para modo claro'
                : 'Modo claro activo — clic para modo oscuro';
        }
    };

    if (document.body.classList.contains('dark-mode')) {
        if (switchMode) switchMode.classList.add('active');
        updateTooltip(true);
    } else if (savedMode === 'true') {
        document.body.classList.add('dark-mode');
        if (switchMode) switchMode.classList.add('active');
        updateTooltip(true);
    } else {
        updateTooltip(false);
    }

    if (switchMode) {
        switchMode.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            switchMode.classList.toggle('active');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark);
            updateTooltip(isDark);
        });
    }
}

// =====================================================
// SIDEBAR
// =====================================================
function initSidebar() {
    const btn     = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    if (!btn || !sidebar) return;

    const isMobile = () => window.innerWidth <= 768;

    // Overlay — se crea una sola vez
    let overlay = document.getElementById('sidebar-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id        = 'sidebar-overlay';
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

    function openSidebar() {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        document.body.classList.add('sidebar-open');
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.classList.remove('sidebar-open');
    }

    // Botón hamburguesa — click y touchend
    function onMenuToggle(e) {
        e.preventDefault();
        e.stopPropagation();
        if (isMobile()) {
            sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
        } else {
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed',
                document.body.classList.contains('sidebar-collapsed'));
        }
    }

    btn.addEventListener('click',    onMenuToggle);
    btn.addEventListener('touchend', onMenuToggle, { passive: false });

    // Overlay cierra el sidebar
    overlay.addEventListener('click',    closeSidebar);
    overlay.addEventListener('touchend', closeSidebar, { passive: true });

    // Links del sidebar navegan y cierran el drawer
    sidebar.querySelectorAll('.side-menu a').forEach(function(link) {
        link.addEventListener('click', function() {
            if (isMobile()) closeSidebar();
        });
    });

    // Logout cierra el drawer
    const logoutForm = document.getElementById('logout-form');
    if (logoutForm) {
        logoutForm.addEventListener('submit', function() {
            if (isMobile()) closeSidebar();
        });
    }

    // Escape cierra el drawer
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSidebar();
    });

    // Al rotar: cerrar si ya no es móvil
    window.addEventListener('resize', function() {
        if (!isMobile()) closeSidebar();
    });

    // Restaurar estado colapsado (solo desktop)
    if (!isMobile()) {
        if (document.documentElement.classList.contains('sidebar-collapsed-init')) {
            document.body.classList.add('sidebar-collapsed');
        }
    }
    document.documentElement.classList.remove('sidebar-collapsed-init');

    // Restaurar dark mode
    if (document.documentElement.classList.contains('dark-mode-init')) {
        document.body.classList.add('dark-mode');
        document.documentElement.classList.remove('dark-mode-init');
        const sw = document.querySelector('.switch-mode');
        if (sw) sw.classList.add('active');
    }

    // Quitar transición inicial para evitar flash
    requestAnimationFrame(function() {
        requestAnimationFrame(function() {
            sidebar.style.transition = '';
            const content = document.getElementById('content');
            if (content) content.style.transition = '';
        });
    });
}

// =====================================================
// PROFILE MENU
// =====================================================
function initProfileMenu() {
    const profileWrapper = document.querySelector('.profile-wrapper');
    const profileBtn     = document.querySelector('.profile');
    const profileMenu    = document.querySelector('.profile-menu');

    if (profileBtn && profileMenu) {
        profileBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            profileMenu.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (profileWrapper && !profileWrapper.contains(e.target)) {
                profileMenu.classList.remove('show');
            }
        });

        profileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => profileMenu.classList.remove('show'));
        });
    }
}

// =====================================================
// MODALES
// =====================================================
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

function closeAllModals() {
    document.querySelectorAll('.modal.show').forEach(modal => {
        modal.classList.remove('show');
    });
    document.body.style.overflow = '';
}

// =====================================================
// TOAST NOTIFICATIONS
// =====================================================
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container') || createToastContainer();

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class='bx ${type === 'success' ? 'bx-check-circle' : type === 'error' ? 'bx-x-circle' : 'bx-error'}'></i>
        <span>${message}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'toastSlideIn 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id        = 'toast-container';
    container.className = 'toast-container';
    container.style.pointerEvents = 'none'; // no bloquea toques cuando está vacío
    document.body.appendChild(container);
    return container;
}

// =====================================================
// INICIALIZAR FORMULARIOS
// NOTE: Cada vista Blade registra sus propios listeners.
//       Esta función existe solo para compatibilidad.
// =====================================================
function initForms() {
    // Intencionalmente vacío.
    // Los formularios son manejados en cada vista Blade de Laravel.
}

// =====================================================
// EXPORTAR TABLA (utilidad genérica)
// =====================================================
function exportTable(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const csv  = [];
    const rows = table.querySelectorAll('tr');

    rows.forEach(row => {
        const cols    = row.querySelectorAll('th, td');
        const rowData = [];
        cols.forEach(col => {
            const text = col.innerText.replace(/"/g, '""');
            rowData.push('"' + text + '"');
        });
        csv.push(rowData.join(','));
    });

    const blob = new Blob(['\ufeff' + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href     = URL.createObjectURL(blob);
    link.download = `${filename}_${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();

    showToast('Archivo exportado correctamente');
}
