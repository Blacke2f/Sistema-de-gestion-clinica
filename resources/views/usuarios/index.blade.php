@extends('layouts.app')

@section('title', 'Usuarios')
@section('nav-title', 'Usuarios')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Gestión de Usuarios</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Usuarios</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <button class="btn-primary" onclick="openModal('modal-usuario')"><i class='bx bx-plus'></i> Nuevo Usuario</button>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3><i class='bx bxs-user-account'></i> Usuarios del Sistema</h3>
        <span class="badge badge-info">{{ $usuarios->total() }} registros</span>
        @if($search)
            <span class="search-active-badge">
                <i class='bx bx-search'></i> "{{ $search }}"
                <a href="{{ route('usuarios.index') }}">✕</a>
            </span>
        @endif
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último Acceso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $i => $u)
                    <tr class="clickable-row" title="Clic para ver detalles" onclick="verUsuario({{ $u->id }})" data-id="{{ $u->id }}">
                        <td>{{ $usuarios->firstItem() + $i }}</td>
                        <td>
                            <div class="cell-user">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($u->nombre_completo) }}&background=0d6efd&color=fff&size=32" alt="">
                                <strong>{{ $u->username }}</strong>
                            </div>
                        </td>
                        <td>{{ $u->nombre_completo }}</td>
                        <td>{{ $u->email }}</td>
                        <td>
                            <span class="badge badge-{{ $u->rol === 'admin' ? 'danger' : ($u->rol === 'medico' ? 'primary' : 'secondary') }}">
                                {{ ucfirst($u->rol) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $u->activo ? 'success' : 'secondary' }}">
                                {{ $u->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>{{ $u->ultimo_acceso?->format('d/m/Y H:i') ?? 'Nunca' }}</td>
                        <td class="actions" onclick="event.stopPropagation()">
                            <button class="btn-icon btn-edit" onclick="editarUsuario({{ $u->id }})" title="Editar"><i class='bx bx-edit'></i></button>
                            @if($u->id !== auth()->id())
                                @if(!$u->isAdmin())
                                <button class="btn-icon btn-view" onclick="abrirPermisos({{ $u->id }})" title="Gestionar permisos">
                                    <i class='bx bx-shield'></i>
                                </button>
                                @endif
                                <button class="btn-icon {{ $u->activo ? 'btn-warning' : 'btn-success' }}"
                                        onclick="toggleEstado({{ $u->id }}, '{{ $u->username }}', {{ $u->activo ? 'true' : 'false' }})"
                                        title="{{ $u->activo ? 'Desactivar' : 'Activar' }}">
                                    <i class='bx bx-{{ $u->activo ? 'block' : 'check-circle' }}'></i>
                                </button>
                                <button class="btn-icon btn-delete" onclick="confirmarEliminarUsuario({{ $u->id }}, '{{ $u->username }}')" title="Eliminar"><i class='bx bx-trash'></i></button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty-row">
                        <div class="empty-state"><i class='bx bxs-user-account'></i><p>No hay usuarios registrados</p></div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $usuarios->links('partials.pagination') }}</div>
</div>

{{-- MODAL USUARIO --}}
<div class="modal" id="modal-usuario">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-usuario-title"><i class='bx bx-user-plus'></i> Nuevo Usuario</h2>
            <button class="modal-close" onclick="closeModal('modal-usuario')">&times;</button>
        </div>
        <form id="form-usuario" class="modal-form">
            @csrf
            <input type="hidden" name="id" id="usuario-id">
            <div class="form-row">
                <div class="form-group">
                    <label>Usuario *</label>
                    <input type="text" name="username" id="usuario-username" required>
                </div>
                <div class="form-group">
                    <label>Nombre Completo *</label>
                    <input type="text" name="nombre_completo" id="usuario-nombre_completo" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" id="usuario-email" required>
                </div>
                <div class="form-group">
                    <label>Contraseña <span id="password-hint">(requerida)</span></label>
                    <input type="password" name="password" id="usuario-password">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Rol *</label>
                    <select name="rol" id="usuario-rol">
                        <option value="recepcionista">Recepcionista</option>
                        <option value="medico">Médico</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="activo" id="usuario-activo">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-usuario')">Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-save'></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
{{-- MODAL VER USUARIO --}}
<div class="modal" id="modal-ver-usuario">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class='bx bxs-user-account'></i> Detalles del Usuario</h2>
            <button class="modal-close" onclick="closeModal('modal-ver-usuario')">&times;</button>
        </div>
        <div class="modal-body" id="ver-usuario-content" style="padding:20px 24px;"></div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('modal-ver-usuario')">Cerrar</button>
        </div>
    </div>
</div>

{{-- MODAL PERMISOS --}}
<div class="modal" id="modal-permisos">
    <div class="modal-content" style="max-width:700px;">
        <div class="modal-header" style="background:linear-gradient(135deg,#1e293b,#1e3a8a);color:#fff;">
            <h2><i class='bx bx-shield'></i> Gestionar Permisos — <span id="permisos-nombre-usuario"></span></h2>
            <button class="modal-close" onclick="closeModal('modal-permisos')" style="color:#fff;">&times;</button>
        </div>
        <div class="modal-body" style="padding:20px 24px;">
            <div style="background:rgba(13,110,253,.08);border-left:4px solid var(--primary);border-radius:8px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:var(--text-primary);display:flex;gap:10px;">
                <i class='bx bx-info-circle' style="font-size:18px;color:var(--primary);flex-shrink:0;margin-top:1px;"></i>
                <span>Define exactamente qué puede hacer este usuario en cada módulo del sistema. Los cambios se aplican de inmediato.</span>
            </div>

            <div id="permisos-loading" style="text-align:center;padding:30px;color:var(--text-muted);">
                <i class='bx bx-loader-alt bx-spin' style="font-size:28px;"></i>
                <p style="margin-top:8px;font-size:13px;">Cargando permisos...</p>
            </div>

            <form id="form-permisos" style="display:none;">
                <input type="hidden" id="permisos-usuario-id">

                {{-- Acciones rápidas --}}
                <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
                    <button type="button" onclick="seleccionarTodos(true)"
                        style="padding:6px 14px;background:rgba(25,135,84,.12);color:#198754;border:1px solid rgba(25,135,84,.3);border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">
                        <i class='bx bx-check-double'></i> Seleccionar todo
                    </button>
                    <button type="button" onclick="seleccionarTodos(false)"
                        style="padding:6px 14px;background:rgba(220,53,69,.1);color:#dc3545;border:1px solid rgba(220,53,69,.25);border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">
                        <i class='bx bx-x'></i> Quitar todo
                    </button>
                    <button type="button" onclick="soloLectura()"
                        style="padding:6px 14px;background:rgba(13,110,253,.1);color:#0d6efd;border:1px solid rgba(13,110,253,.25);border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">
                        <i class='bx bx-show'></i> Solo lectura
                    </button>
                </div>

                {{-- Tabla de permisos --}}
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="background:var(--bg-body);">
                                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid var(--border-color);">Módulo</th>
                                <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:700;color:#0d6efd;text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid var(--border-color);">Ver</th>
                                <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:700;color:#198754;text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid var(--border-color);">Crear</th>
                                <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:700;color:#f59e0b;text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid var(--border-color);">Editar</th>
                                <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:700;color:#dc3545;text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid var(--border-color);">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody id="permisos-tbody">
                            {{-- Se llena dinámicamente con JS --}}
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('modal-permisos')">Cancelar</button>
            <button type="button" class="btn-primary" id="btn-guardar-permisos" onclick="guardarPermisos()" style="display:none;">
                <i class='bx bx-save'></i> Guardar Permisos
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const USUARIOS_URL = '{{ url('usuarios') }}';

let _usuarioSubmitting = false;
document.getElementById('form-usuario').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_usuarioSubmitting) return;
    _usuarioSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Guardando...'; }

    const id  = document.getElementById('usuario-id').value;
    const url = id ? `${USUARIOS_URL}/${id}` : USUARIOS_URL;
    const fd  = new FormData(this);
    if (id) fd.set('_method', 'PUT');

    try {
        const res  = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }, body: fd });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeModal('modal-usuario');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'Error.', 'error');
        }
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        _usuarioSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Guardar'; }
    }
});

async function verUsuario(id) {
    const res  = await fetch(`${USUARIOS_URL}/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
    const data = await res.json();
    if (data.success) {
        const u = data.data;
        const rolBadge = { admin: 'badge-danger', medico: 'badge-primary', recepcionista: 'badge-secondary' };
        document.getElementById('ver-usuario-content').innerHTML = `
            <div style="text-align:center;margin-bottom:20px;">
                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(u.nombre_completo)}&background=0d6efd&color=fff&size=80"
                     style="width:80px;height:80px;border-radius:50%;border:3px solid var(--border-color);" alt="">
                <h3 style="margin-top:10px;color:var(--text-primary);">${u.nombre_completo}</h3>
                <span class="badge ${rolBadge[u.rol] || 'badge-secondary'}">${u.rol.charAt(0).toUpperCase() + u.rol.slice(1)}</span>
            </div>
            <div class="detail-grid">
                <div class="detail-item"><strong>Usuario:</strong> ${u.username}</div>
                <div class="detail-item"><strong>Email:</strong> ${u.email}</div>
                <div class="detail-item"><strong>Estado:</strong> ${u.activo ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-secondary">Inactivo</span>'}</div>
                <div class="detail-item"><strong>Último acceso:</strong> ${u.ultimo_acceso ? new Date(u.ultimo_acceso).toLocaleString('es-DO') : 'Nunca'}</div>
            </div>`;
        openModal('modal-ver-usuario');
    }
}

async function editarUsuario(id) {
    const res  = await fetch(`${USUARIOS_URL}/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
    const data = await res.json();
    if (data.success) {
        const u = data.data;
        document.getElementById('usuario-id').value             = u.id;
        document.getElementById('usuario-username').value       = u.username;
        document.getElementById('usuario-nombre_completo').value= u.nombre_completo;
        document.getElementById('usuario-email').value          = u.email;
        document.getElementById('usuario-password').value       = '';
        document.getElementById('usuario-rol').value            = u.rol;
        document.getElementById('usuario-activo').value         = u.activo ? '1' : '0';
        document.getElementById('password-hint').textContent    = '(dejar vacío para no cambiar)';
        document.getElementById('modal-usuario-title').innerHTML = "<i class='bx bx-edit'></i> Editar Usuario";
        openModal('modal-usuario');
    }
}

async function toggleEstado(id, username, activo) {
    document.getElementById('estado-message').textContent = `¿Desea ${activo ? 'desactivar' : 'activar'} al usuario "${username}"?`;
    document.getElementById('btn-confirm-estado').onclick = async () => {
        const res  = await fetch(`${USUARIOS_URL}/${id}/toggle`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: '_method=PATCH'
        });
        const data = await res.json();
        if (data.success) { showToast(data.message, 'success'); closeModal('modal-confirm-estado'); setTimeout(() => location.reload(), 800); }
        else showToast(data.message, 'error');
    };
    openModal('modal-confirm-estado');
}

function confirmarEliminarUsuario(id, username) {
    document.getElementById('delete-item-name').textContent = username;
    document.getElementById('btn-confirm-delete').onclick = async () => {
        const res  = await fetch(`${USUARIOS_URL}/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: '_method=DELETE'
        });
        const data = await res.json();
        if (data.success) { showToast(data.message, 'success'); closeModal('modal-confirm-delete'); setTimeout(() => location.reload(), 800); }
        else showToast(data.message, 'error');
    };
    openModal('modal-confirm-delete');
}

// ── Gestión de permisos ───────────────────────────────────────────────────────

// Colores de las acciones para los checkboxes
const ACCION_COLORES = {
    ver:      { bg: 'rgba(13,110,253,.1)',  color: '#0d6efd' },
    crear:    { bg: 'rgba(25,135,84,.1)',   color: '#198754' },
    editar:   { bg: 'rgba(245,158,11,.1)',  color: '#d97706' },
    eliminar: { bg: 'rgba(220,53,69,.1)',   color: '#dc3545' },
};

/**
 * Abre el modal de permisos y carga los permisos actuales del usuario via AJAX.
 */
async function abrirPermisos(id) {
    // Mostrar el modal con estado de carga
    document.getElementById('permisos-loading').style.display = 'block';
    document.getElementById('form-permisos').style.display    = 'none';
    document.getElementById('btn-guardar-permisos').style.display = 'none';
    document.getElementById('permisos-nombre-usuario').textContent = 'Cargando...';
    openModal('modal-permisos');

    try {
        const res  = await fetch(`${USUARIOS_URL}/${id}/permisos`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
        });
        const data = await res.json();

        if (!data.success) {
            showToast(data.message, 'error');
            closeModal('modal-permisos');
            return;
        }

        // Actualizar el título del modal
        document.getElementById('permisos-nombre-usuario').textContent = data.usuario.nombre;
        document.getElementById('permisos-usuario-id').value = data.usuario.id;

        // Construir la tabla de permisos dinámicamente
        const tbody = document.getElementById('permisos-tbody');
        tbody.innerHTML = '';

        Object.entries(data.permisos).forEach(([modulo, info]) => {
            const tr = document.createElement('tr');
            tr.style.cssText = 'border-bottom:1px solid var(--border-color);transition:background .15s;';
            tr.onmouseover = () => tr.style.background = 'var(--bg-body)';
            tr.onmouseout  = () => tr.style.background = '';

            // Columna del módulo con ícono y color
            const tdModulo = `
                <td style="padding:12px 14px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:${info.color}18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class='bx ${info.icon}' style="font-size:16px;color:${info.color};"></i>
                        </div>
                        <span style="font-weight:600;color:var(--text-primary);font-size:13px;">${info.label}</span>
                    </div>
                </td>`;

            // Columnas de checkboxes para cada acción
            const acciones = ['ver', 'crear', 'editar', 'eliminar'];
            const tdAcciones = acciones.map(accion => {
                const checked  = info['puede_' + accion] ? 'checked' : '';
                const col      = ACCION_COLORES[accion];
                return `
                    <td style="padding:12px 14px;text-align:center;">
                        <label style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;cursor:pointer;transition:background .15s;"
                               onmouseover="this.style.background='${col.bg}'" onmouseout="this.style.background=''">
                            <input type="checkbox"
                                   name="permisos[${modulo}][${accion}]"
                                   value="1"
                                   ${checked}
                                   style="width:18px;height:18px;accent-color:${col.color};cursor:pointer;">
                        </label>
                    </td>`;
            }).join('');

            tr.innerHTML = tdModulo + tdAcciones;
            tbody.appendChild(tr);
        });

        // Mostrar el formulario y ocultar el loading
        document.getElementById('permisos-loading').style.display = 'none';
        document.getElementById('form-permisos').style.display    = 'block';
        document.getElementById('btn-guardar-permisos').style.display = 'inline-flex';

    } catch (err) {
        showToast('Error al cargar los permisos.', 'error');
        closeModal('modal-permisos');
    }
}

/**
 * Guarda los permisos del formulario via AJAX.
 */
async function guardarPermisos() {
    const id  = document.getElementById('permisos-usuario-id').value;
    const btn = document.getElementById('btn-guardar-permisos');
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Guardando...';

    // Recopilar todos los checkboxes del formulario
    const form     = document.getElementById('form-permisos');
    const formData = new FormData(form);

    // Construir el objeto de permisos
    const permisos = {};
    for (const [key, val] of formData.entries()) {
        // key tiene formato: permisos[modulo][accion]
        const match = key.match(/permisos\[(\w+)\]\[(\w+)\]/);
        if (match) {
            const [, modulo, accion] = match;
            if (!permisos[modulo]) permisos[modulo] = {};
            permisos[modulo][accion] = val;
        }
    }

    try {
        const res  = await fetch(`${USUARIOS_URL}/${id}/permisos`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.CSRF_TOKEN,
                'Accept':       'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ permisos }),
        });
        const data = await res.json();

        if (data.success) {
            showToast(data.message, 'success');
            closeModal('modal-permisos');
        } else {
            showToast(data.message || 'Error al guardar.', 'error');
        }
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-save"></i> Guardar Permisos';
    }
}

/**
 * Selecciona o deselecciona todos los checkboxes de la tabla de permisos.
 * @param {boolean} valor true = marcar todos, false = desmarcar todos
 */
function seleccionarTodos(valor) {
    document.querySelectorAll('#permisos-tbody input[type="checkbox"]')
        .forEach(cb => cb.checked = valor);
}

/**
 * Marca solo los checkboxes de "Ver" y desmarca el resto.
 * Útil para dar acceso de solo lectura a todos los módulos.
 */
function soloLectura() {
    document.querySelectorAll('#permisos-tbody input[type="checkbox"]').forEach(cb => {
        // Marcar solo los que tienen [ver] en el nombre
        cb.checked = cb.name.includes('[ver]');
    });
}
</script>
@endpush
