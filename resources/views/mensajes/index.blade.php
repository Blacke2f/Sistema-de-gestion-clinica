@extends('layouts.app')

@section('title', 'Mensajes')
@section('nav-title', 'Mensajes')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Mensajes</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Mensajes</a></li>
        </ul>
    </div>
    <div class="head-actions">
        @if(auth()->user()->isAdmin())
            <button class="btn-warning" onclick="openModal('modal-masivo')"><i class='bx bx-broadcast'></i> Mensaje Masivo</button>
        @endif
        <button class="btn-primary" onclick="openModal('modal-mensaje')"><i class='bx bx-send'></i> Nuevo Mensaje</button>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;" class="mensajes-grid">
    {{-- Recibidos --}}
    <div class="table-container">
        <div class="table-header">
            <h3><i class='bx bx-inbox'></i> Recibidos</h3>
            <span class="badge badge-info">{{ $mensajesRecibidos->count() }}</span>
        </div>
        <div class="table-responsive">
            {{-- Vista de tarjetas para móvil, tabla para desktop --}}
            <div class="mensajes-lista">
                @forelse($mensajesRecibidos as $m)
                    <div class="mensaje-card {{ !$m->leido ? 'mensaje-nuevo' : '' }}"
                         onclick="verMensaje({{ $m->id }})"
                         style="cursor:pointer;">
                        <div class="mensaje-card-avatar">
                            <img src="" data-avatar="{{ $m->remitente?->nombre_completo ?? 'Sistema' }}" data-bg="#0d6efd" data-size="40" alt="">
                        </div>
                        <div class="mensaje-card-body">
                            <div class="mensaje-card-top">
                                <span class="mensaje-card-de">{{ $m->remitente?->nombre_completo ?? 'Sistema' }}</span>
                                <span class="mensaje-card-fecha">{{ $m->created_at->format('d/m H:i') }}</span>
                            </div>
                            <div class="mensaje-card-asunto">
                                @if(!$m->leido)
                                    <span class="badge badge-primary" style="font-size:9px;margin-right:4px;">Nuevo</span>
                                @endif
                                {{ Str::limit($m->asunto, 40) }}
                            </div>
                        </div>
                        <div class="mensaje-card-actions" onclick="event.stopPropagation()">
                            <button class="btn-icon btn-delete" onclick="eliminarMensaje({{ $m->id }})" title="Eliminar">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-row">
                        <div class="empty-state"><i class='bx bx-inbox'></i><p>Sin mensajes recibidos</p></div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Enviados --}}
    <div class="table-container">
        <div class="table-header">
            <h3><i class='bx bx-send'></i> Enviados</h3>
            <span class="badge badge-info">{{ $mensajesEnviados->count() }}</span>
        </div>
        <div class="table-responsive">
            <div class="mensajes-lista">
                @forelse($mensajesEnviados as $m)
                    <div class="mensaje-card"
                         onclick="verMensajeEnviado({{ $m->id }})"
                         style="cursor:pointer;">
                        <div class="mensaje-card-avatar">
                            <img src="" data-avatar="{{ $m->destinatario?->nombre_completo ?? 'Todos' }}" data-bg="#198754" data-size="40" alt="">
                        </div>
                        <div class="mensaje-card-body">
                            <div class="mensaje-card-top">
                                <span class="mensaje-card-de">Para: {{ $m->destinatario?->nombre_completo ?? ($m->es_masivo ? 'Mensaje Masivo' : 'Todos') }}</span>
                                <span class="mensaje-card-fecha">{{ $m->created_at->format('d/m H:i') }}</span>
                            </div>
                            <div class="mensaje-card-asunto">{{ Str::limit($m->asunto, 40) }}</div>
                        </div>
                        <div class="mensaje-card-actions" onclick="event.stopPropagation()">
                            <button class="btn-icon btn-delete" onclick="eliminarMensaje({{ $m->id }})" title="Eliminar">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-row">
                        <div class="empty-state"><i class='bx bx-send'></i><p>Sin mensajes enviados</p></div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- MODAL NUEVO MENSAJE --}}
<div class="modal" id="modal-mensaje">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <h2><i class='bx bx-send'></i> Nuevo Mensaje</h2>
            <button class="modal-close" onclick="closeModal('modal-mensaje')">&times;</button>
        </div>
        <form id="form-mensaje" class="modal-form">
            @csrf
            <div class="form-group">
                <label>Destinatario *</label>
                <select name="destinatario_id" required>
                    <option value="">Seleccionar usuario</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->nombre_completo }} ({{ ucfirst($u->rol) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Asunto *</label>
                <input type="text" name="asunto" required placeholder="Asunto del mensaje">
            </div>
            <div class="form-group">
                <label>Mensaje *</label>
                <textarea name="contenido" rows="4" required placeholder="Escriba su mensaje..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-mensaje')">Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-send'></i> Enviar</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL MASIVO (solo admin) --}}
@if(auth()->user()->isAdmin())
<div class="modal" id="modal-masivo">
    <div class="modal-content modal-sm">
        <div class="modal-header modal-header-warning">
            <h2><i class='bx bx-broadcast'></i> Mensaje Masivo</h2>
            <button class="modal-close" onclick="closeModal('modal-masivo')">&times;</button>
        </div>
        <form id="form-masivo" class="modal-form">
            @csrf
            <div class="form-group">
                <label>Enviar a</label>
                <select name="destinatarios">
                    <option value="todos">Todos los usuarios</option>
                    <option value="medicos">Solo médicos</option>
                    <option value="recepcionistas">Solo recepcionistas</option>
                    <option value="admins">Solo administradores</option>
                </select>
            </div>
            <div class="form-group">
                <label>Asunto *</label>
                <input type="text" name="asunto" required placeholder="Asunto del mensaje">
            </div>
            <div class="form-group">
                <label>Mensaje *</label>
                <textarea name="contenido" rows="4" required placeholder="Escriba su mensaje..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-masivo')">Cancelar</button>
                <button type="submit" class="btn-warning"><i class='bx bx-broadcast'></i> Enviar a Todos</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- MODAL VER MENSAJE --}}
<div class="modal" id="modal-ver-mensaje">
    <div class="modal-content" style="max-width:560px; width:95%;">
        <div class="modal-header">
            <h2><i class='bx bx-envelope-open'></i> <span id="ver-mensaje-asunto">Mensaje</span></h2>
            <button class="modal-close" onclick="closeModal('modal-ver-mensaje')">&times;</button>
        </div>
        <div id="ver-mensaje-content" style="padding:20px 24px; overflow-y:auto; max-height:60vh;">
            {{-- Estructura fija que el JS viejo necesita --}}
            <div id="ver-mensaje-remitente" style="display:none;"></div>
            <div id="ver-mensaje-fecha" style="display:none;"></div>
            <div id="ver-mensaje-contenido" style="display:none;"></div>
            <img id="ver-mensaje-avatar" src="" alt="" style="display:none;">
            {{-- Contenido visual real --}}
            <div id="ver-mensaje-visual"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('modal-ver-mensaje')">Cerrar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@push('styles')
<style>
/* ── Tarjetas de mensajes ── */
.mensajes-lista { padding: 8px 0; }

.mensaje-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border-color, #f0f0f0);
    transition: background .15s;
    -webkit-tap-highlight-color: rgba(13,110,253,.08);
}
.mensaje-card:last-child { border-bottom: none; }
.mensaje-card:hover,
.mensaje-card:active { background: var(--bg-body); }

.mensaje-card.mensaje-nuevo { background: rgba(13,110,253,.06); border-left: 3px solid var(--primary); }
.mensaje-card.mensaje-nuevo:hover { background: rgba(13,110,253,.1); }

.mensaje-card-avatar img {
    width: 42px; height: 42px;
    border-radius: 50%;
    flex-shrink: 0;
    display: block;
}

.mensaje-card-body {
    flex: 1;
    min-width: 0;
}

.mensaje-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}

.mensaje-card-de {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mensaje-card-fecha {
    font-size: 11px;
    color: var(--text-muted);
    flex-shrink: 0;
}

.mensaje-card-asunto {
    font-size: 13px;
    color: var(--text-secondary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Mensaje nuevo — asunto más visible */
.mensaje-card.mensaje-nuevo .mensaje-card-de { color: var(--text-primary); }
.mensaje-card.mensaje-nuevo .mensaje-card-asunto { color: var(--text-primary); font-weight: 500; }

.mensaje-card-actions {
    flex-shrink: 0;
    padding-left: 8px;
}

/* ── Modal ver mensaje — respeta modo oscuro ── */
.msg-detail-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: var(--bg-body);
    border-radius: 8px;
    border-left: 4px solid var(--primary);
    margin-bottom: 16px;
}
.msg-detail-etiqueta {
    font-size: 11px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .4px;
}
.msg-detail-nombre {
    font-weight: 600;
    font-size: 14px;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.msg-detail-fecha { font-size: 12px; color: var(--text-muted); }
.msg-detail-asunto-label {
    font-size: 11px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    padding: 4px 0 0;
}
.msg-detail-asunto {
    font-size: 15px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 4px 0 0;
}
.msg-detail-divider {
    border: none;
    border-top: 1px solid var(--border-color);
    margin: 12px 0 14px;
}
.msg-detail-body {
    font-size: 14px;
    color: var(--text-primary);
    line-height: 1.7;
    white-space: pre-wrap;
    word-break: break-word;
}

/* Mensajes responsive */
.mensajes-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
@media (max-width: 768px) {
    .mensajes-grid { grid-template-columns: 1fr !important; }

    /* Modal bottom sheet en móvil */
    #modal-ver-mensaje .modal-content {
        width: 100% !important;
        max-width: 100% !important;
        border-radius: 16px 16px 0 0 !important;
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        max-height: 85vh !important;
        margin: 0 !important;
    }
    #modal-ver-mensaje {
        align-items: flex-end !important;
        padding: 0 !important;
    }
    #ver-mensaje-content { max-height: 55vh !important; }
}
</style>
@endpush
@push('scripts')
<script>
const MENSAJES_URL = '{{ url('mensajes') }}';

let _mensajeSubmitting = false;
document.getElementById('form-mensaje').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_mensajeSubmitting) return;
    _mensajeSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Enviando...'; }
    try {
        const res  = await fetch(MENSAJES_URL, { method: 'POST', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }, body: new FormData(this) });
        const data = await res.json();
        if (data.success) { showToast(data.message, 'success'); closeModal('modal-mensaje'); setTimeout(() => location.reload(), 800); }
        else showToast(data.message || 'Error al enviar.', 'error');
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        _mensajeSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-send"></i> Enviar'; }
    }
});

@if(auth()->user()->isAdmin())
let _masivoSubmitting = false;
document.getElementById('form-masivo').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_masivoSubmitting) return;
    _masivoSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Enviando...'; }
    try {
        const res  = await fetch(`${MENSAJES_URL}/masivo`, { method: 'POST', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }, body: new FormData(this) });
        const data = await res.json();
        if (data.success) { showToast(data.message, 'success'); closeModal('modal-masivo'); setTimeout(() => location.reload(), 800); }
        else showToast(data.message || 'Error al enviar.', 'error');
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        _masivoSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-broadcast"></i> Enviar a Todos'; }
    }
});
@endif

async function verMensaje(id, esEnviado = false) {
    try {
        const res  = await fetch(`${MENSAJES_URL}/${id}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
        });
        const data = await res.json();

        if (data.success) {
            const m = data.data;
            const nombreMostrar = esEnviado
                ? (m.destinatario?.nombre_completo || m.remitente?.nombre_completo || '-')
                : (m.remitente?.nombre_completo || '-');
            const colorAvatar = esEnviado ? '198754' : '0d6efd';
            const colorBorde  = esEnviado ? '#198754' : '#0d6efd';
            const etiqueta    = esEnviado ? 'Para' : 'De';

            // Compatibilidad con script.js viejo
            const elAsunto    = document.getElementById('ver-mensaje-asunto');
            const elRemitente = document.getElementById('ver-mensaje-remitente');
            const elFecha     = document.getElementById('ver-mensaje-fecha');
            const elContenido = document.getElementById('ver-mensaje-contenido');
            const elAvatar    = document.getElementById('ver-mensaje-avatar');
            if (elAsunto)    elAsunto.textContent   = m.asunto || 'Mensaje';
            if (elRemitente) elRemitente.textContent = nombreMostrar;
            if (elFecha)     elFecha.textContent     = m.created_at || '';
            if (elContenido) elContenido.textContent = m.contenido || '';
            if (elAvatar)    elAvatar.src = window.generarAvatar ? window.generarAvatar(nombreMostrar, '#' + colorAvatar, 80) : '';

            // Contenido visual
            const visual = document.getElementById('ver-mensaje-visual');
            if (visual) {
                const nombre    = escHtml(nombreMostrar);
                const asunto    = escHtml(m.asunto || '');
                const contenido = escHtml(m.contenido || '');
                const fecha     = m.created_at ? new Date(m.created_at).toLocaleString('es-DO') : '';
                const avatarUrl = window.generarAvatar ? window.generarAvatar(nombreMostrar, '#' + colorAvatar, 80) : '';

                visual.innerHTML = `
                    <div class="msg-detail-header" style="border-left-color:${colorBorde};">
                        <img src="${avatarUrl}" style="width:42px;height:42px;border-radius:50%;flex-shrink:0;" alt="">
                        <div style="min-width:0;">
                            <div class="msg-detail-etiqueta">${etiqueta}</div>
                            <div class="msg-detail-nombre">${nombre}</div>
                            <div class="msg-detail-fecha">${fecha}</div>
                        </div>
                    </div>
                    <div class="msg-detail-asunto-label">Asunto</div>
                    <div class="msg-detail-asunto">${asunto}</div>
                    <hr class="msg-detail-divider">
                    <div class="msg-detail-body">${contenido}</div>`;
            }

            openModal('modal-ver-mensaje');
        } else {
            showToast('No se pudo cargar el mensaje.', 'error');
        }
    } catch(err) {
        showToast('Error al cargar el mensaje.', 'error');
    }
}

function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}

async function verMensajeEnviado(id) {
    // Reutiliza la misma función pero con etiqueta "Para:"
    await verMensaje(id, true);
}

async function eliminarMensaje(id) {
    document.getElementById('delete-item-name').textContent = 'Este mensaje será eliminado permanentemente.';
    document.getElementById('btn-confirm-delete').onclick = async () => {
        const res  = await fetch(`${MENSAJES_URL}/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: '_method=DELETE'
        });
        const data = await res.json();
        closeModal('modal-confirm-delete');
        if (data.success) { showToast(data.message, 'success'); setTimeout(() => location.reload(), 800); }
    };
    openModal('modal-confirm-delete');
}
</script>
@endpush
