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

    if (document.body.classList.contains('dark-mode')) {
        if (switchMode) switchMode.classList.add('active');
    } else if (savedMode === 'true') {
        document.body.classList.add('dark-mode');
        if (switchMode) switchMode.classList.add('active');
    }

    if (switchMode) {
        switchMode.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            switchMode.classList.toggle('active');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        });
    }
}

// =====================================================
// SIDEBAR
// =====================================================
function initSidebar() {
    const menuBar = document.querySelector('#content nav .bx-menu');
    const sidebar  = document.getElementById('sidebar');
    const isMobile = () => window.innerWidth <= 768;

    if (document.documentElement.classList.contains('sidebar-collapsed-init')) {
        document.body.classList.add('sidebar-collapsed');
        document.documentElement.classList.remove('sidebar-collapsed-init');
    }
    if (document.documentElement.classList.contains('dark-mode-init')) {
        document.body.classList.add('dark-mode');
        document.documentElement.classList.remove('dark-mode-init');
        const switchMode = document.querySelector('.switch-mode');
        if (switchMode) switchMode.classList.add('active');
    }

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            if (sidebar) sidebar.style.transition = '';
            const content = document.getElementById('content');
            if (content) content.style.transition = '';
        });
    });

    if (menuBar && sidebar) {
        menuBar.addEventListener('click', (e) => {
            e.stopPropagation();
            if (isMobile()) {
                sidebar.classList.toggle('show');
                document.body.classList.toggle('sidebar-open');
            } else {
                document.body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed',
                    document.body.classList.contains('sidebar-collapsed'));
            }
        });
    }

    document.addEventListener('click', (e) => {
        if (isMobile() && sidebar && sidebar.classList.contains('show')) {
            if (!sidebar.contains(e.target) && !e.target.closest('.bx-menu')) {
                sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        }
    });

    const sidebarLinks = document.querySelectorAll('#sidebar .side-menu a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (isMobile() && sidebar) {
                sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
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
