@extends('layouts.app')

@section('title', 'Consultas')
@section('nav-title', 'Consultas')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Gestión de Consultas</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Consultas</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <button class="btn-export" onclick="openModal('modal-export-consultas')">
            <i class='bx bx-export'></i> Exportar
        </button>
        <button class="btn-primary" onclick="openModal('modal-consulta')">
            <i class='bx bx-plus'></i> Nueva Consulta
        </button>
    </div>
</div>

@include('partials.export-modal', [
    'exportModule'    => 'consultas',
    'exportTitle'     => 'Exportar Consultas',
    'exportColor'     => '#0d6efd',
    'exportIcon'      => 'bx-clipboard',
    'exportHasPeriod' => true,
    'exportRoutes'    => [
        'pdf'   => route('export.consultas', ['formato' => 'pdf']),
        'excel' => route('export.consultas', ['formato' => 'excel']),
        'csv'   => route('consultas.exportar'),
    ],
])

<div class="table-container">
    <div class="table-header">
        <h3><i class='bx bx-clipboard'></i> Listado de Consultas</h3>
        @php
            $kpiConsTotal = \App\Models\Consulta::count();
            $kpiConsHoy   = \App\Models\Consulta::whereDate('fecha_hora', today())->count();
            $kpiConsPend  = \App\Models\Consulta::whereIn('estado', ['pendiente', 'en_proceso'])->count();
            $kpiConsComp  = \App\Models\Consulta::where('estado', 'completada')->count();
        @endphp
        <div style="width:100%;display:grid;grid-template-columns:repeat(auto-fit,minmax(185px,1fr));gap:12px;margin:10px 0 8px;">
            <div style="background:linear-gradient(135deg,#0d6efd,#3b82f6);color:#fff;padding:12px 16px;border-radius:10px;display:flex;align-items:center;gap:12px;">
                <i class='bx bx-clipboard' style="font-size:28px;opacity:.85;"></i>
                <div><div style="font-size:20px;font-weight:800;line-height:1;">{{ $kpiConsTotal }}</div><small style="opacity:.9;font-size:11px;">Total Consultas</small></div>
            </div>
            <div style="background:linear-gradient(135deg,#0dcaf0,#06b6d4);color:#fff;padding:12px 16px;border-radius:10px;display:flex;align-items:center;gap:12px;">
                <i class='bx bx-calendar-check' style="font-size:28px;opacity:.85;"></i>
                <div><div style="font-size:20px;font-weight:800;line-height:1;">{{ $kpiConsHoy }}</div><small style="opacity:.9;font-size:11px;">Consultas Hoy</small></div>
            </div>
            <div style="background:linear-gradient(135deg,#d97706,#f59e0b);color:#fff;padding:12px 16px;border-radius:10px;display:flex;align-items:center;gap:12px;">
                <i class='bx bx-time-five' style="font-size:28px;opacity:.85;"></i>
                <div><div style="font-size:20px;font-weight:800;line-height:1;">{{ $kpiConsPend }}</div><small style="opacity:.9;font-size:11px;">Pendientes / En Curso</small></div>
            </div>
            <div style="background:linear-gradient(135deg,#198754,#10b981);color:#fff;padding:12px 16px;border-radius:10px;display:flex;align-items:center;gap:12px;">
                <i class='bx bx-check-shield' style="font-size:28px;opacity:.85;"></i>
                <div><div style="font-size:20px;font-weight:800;line-height:1;">{{ $kpiConsComp }}</div><small style="opacity:.9;font-size:11px;">Completadas</small></div>
            </div>
        </div>
        <div style="width:100%;display:flex;gap:6px;flex-wrap:wrap;margin-bottom:4px;">
            @foreach(['' => 'Todas', 'pendiente' => 'Pendientes', 'en_proceso' => 'En Proceso', 'completada' => 'Completadas', 'cancelada' => 'Canceladas'] as $estKey => $estLabel)
                <a href="{{ route('consultas.index', $estKey ? ['search' => $estKey] : []) }}"
                   style="font-size:11px;font-weight:600;padding:4px 12px;border-radius:20px;text-decoration:none;border:1px solid {{ $search === $estKey ? 'var(--primary)' : 'var(--border-color)' }};background:{{ $search === $estKey ? 'var(--primary)' : 'var(--bg-body)' }};color:{{ $search === $estKey ? '#fff' : 'var(--text-secondary)' }};">
                    {{ $estLabel }}
                </a>
            @endforeach
        </div>
        <span class="badge badge-info">{{ $consultas->total() }} registros</span>
        @if($search)
            <span class="search-active-badge">
                <i class='bx bx-search'></i> "{{ $search }}"
                <a href="{{ route('consultas.index') }}">✕</a>
            </span>
        @endif
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Fecha/Hora</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consultas as $i => $c)
                    <tr class="clickable-row" title="Clic para ver detalles" onclick="verConsulta({{ $c->id }})" data-id="{{ $c->id }}">
                        <td>{{ $consultas->firstItem() + $i }}</td>
                        <td>
                            <div class="cell-user">
                                <img src="" data-avatar="{{ $c->paciente->nombre_completo }}" data-bg="#0d6efd" data-size="32" alt="">
                                <span>{{ $c->paciente->nombre_completo }}</span>
                            </div>
                        </td>
                        <td>{{ $c->medico_nombre }}</td>
                        <td>{{ $c->fecha_hora->format('d/m/Y H:i') }}</td>
                        <td>{{ Str::limit($c->motivo, 40) }}</td>
                        <td><span class="status status-{{ $c->estado }}">{{ $c->estado_texto }}</span></td>
                        <td class="actions" onclick="event.stopPropagation()">
                            <a href="{{ route('expediente.show', $c->paciente_id) }}" class="btn-icon btn-primary" title="Expediente"><i class='bx bx-folder-open'></i></a>
                            <a href="{{ route('recetas.index', ['search' => $c->paciente->cedula ?? $c->paciente->nombre_completo]) }}" class="btn-icon" style="background:rgba(25,135,84,.14);color:#198754;" title="Ver/Crear Recetas del paciente"><i class='bx bx-file-blank'></i></a>
                            <button class="btn-icon btn-edit" onclick="editarConsulta({{ $c->id }})" title="Editar"><i class='bx bx-edit'></i></button>
                            <button class="btn-icon btn-delete" onclick="confirmarEliminarConsulta({{ $c->id }}, '{{ addslashes($c->paciente->nombre_completo) }}')" title="Eliminar"><i class='bx bx-trash'></i></button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-row">
                        <div class="empty-state"><i class='bx bx-clipboard'></i><p>No hay consultas registradas</p></div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $consultas->links('partials.pagination') }}</div>
</div>

{{-- MODAL CONSULTA --}}
<div class="modal" id="modal-consulta">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-consulta-title"><i class='bx bx-plus-circle'></i> Nueva Consulta</h2>
            <button class="modal-close" onclick="closeModal('modal-consulta')">&times;</button>
        </div>
        <form id="form-consulta" class="modal-form">
            @csrf
            <input type="hidden" name="id" id="consulta-id">

            <div class="form-row">
                <div class="form-group">
                    <label>Paciente *
                        <button type="button" onclick="abrirRegistroRapidoPaciente()"
                            style="margin-left:8px;background:rgba(13,110,253,.1);border:1px solid rgba(13,110,253,.3);color:var(--primary);border-radius:6px;padding:2px 8px;font-size:11px;font-weight:600;cursor:pointer;vertical-align:middle;">
                            <i class='bx bx-user-plus'></i> Nuevo paciente
                        </button>
                    </label>
                    <select name="paciente_id" id="consulta-paciente_id" required>
                        <option value="">Seleccionar paciente</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}" data-alergias="{{ $p->alergias ?? '' }}" data-sangre="{{ $p->tipo_sangre ?? '' }}" data-edad="{{ $p->edad ?? '' }}">{{ $p->nombre_completo }} - {{ $p->cedula }}</option>
                        @endforeach
                    </select>
                    <small style="color:var(--text-muted);font-size:11px;">¿No está en la lista? Haz clic en "Nuevo paciente"</small>
                    <div id="consulta-alergia-banner" style="display:none;margin-top:8px;padding:8px 12px;border-radius:8px;font-size:12px;"></div>
                </div>
                <div class="form-group">
                    <label>Médico *</label>
                    <select name="empleado_id" id="consulta-empleado_id" required>
                        <option value="">Seleccionar médico</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre_completo }} - {{ $m->cargo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Fecha y Hora *</label>
                    <input type="datetime-local" name="fecha_hora" id="consulta-fecha_hora" required>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" id="consulta-estado">
                        <option value="pendiente">Pendiente</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Motivo *</label>
                <textarea name="motivo" id="consulta-motivo" rows="2" required placeholder="Motivo de la consulta"></textarea>
            </div>
            <div class="form-group">
                <label>Diagnóstico</label>
                <textarea name="diagnostico" id="consulta-diagnostico" rows="2" placeholder="Diagnóstico médico"></textarea>
            </div>
            <div class="form-group">
                <label>Tratamiento</label>
                <textarea name="tratamiento" id="consulta-tratamiento" rows="2" placeholder="Tratamiento indicado"></textarea>
            </div>

            {{-- Signos Vitales (solo al editar/ver consulta activa) --}}
            <div id="sv-wrapper" style="display:none;">
                @include('partials.signos-vitales-form', ['consultaId' => '', 'pacienteId' => ''])
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-consulta')">Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-save'></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL VER CONSULTA --}}
<div class="modal" id="modal-ver-consulta">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class='bx bx-clipboard'></i> Detalles de la Consulta</h2>
            <button class="modal-close" onclick="closeModal('modal-ver-consulta')">&times;</button>
        </div>
        <div class="modal-body" id="ver-consulta-content"></div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('modal-ver-consulta')">Cerrar</button>
        </div>
    </div>
</div>

{{-- MODAL REGISTRO RÁPIDO DE PACIENTE --}}
<div class="modal" id="modal-paciente-rapido">
    <div class="modal-content" style="max-width:600px;">
        <div class="modal-header" style="background:linear-gradient(135deg,#0d6efd,#6610f2);color:#fff;">
            <h2><i class='bx bx-user-plus'></i> Registrar Nuevo Paciente</h2>
            <button class="modal-close" onclick="cerrarRegistroRapido()" style="color:#fff;">&times;</button>
        </div>
        <div style="padding:12px 20px;background:rgba(13,110,253,.08);border-bottom:1px solid var(--border-color);font-size:13px;color:var(--text-primary);">
            <i class='bx bx-info-circle' style="color:var(--primary);"></i>
            El paciente se registrará y se seleccionará automáticamente en la consulta.
        </div>
        <form id="form-paciente-rapido" class="modal-form">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" id="rp-nombre" placeholder="Nombre" required>
                </div>
                <div class="form-group">
                    <label>Apellido *</label>
                    <input type="text" name="apellido" id="rp-apellido" placeholder="Apellido" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Cédula *</label>
                    <input type="text" name="cedula" id="rp-cedula" placeholder="000-0000000-0" required>
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" name="telefono" id="rp-telefono" placeholder="809-000-0000">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Género *</label>
                    <select name="genero" id="rp-genero" required>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="rp-fecha_nacimiento">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Tipo de Sangre</label>
                    <select name="tipo_sangre" id="rp-tipo_sangre">
                        <option value="">Seleccionar</option>
                        @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $ts)
                            <option value="{{ $ts }}">{{ $ts }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Alergias</label>
                    <input type="text" name="alergias" id="rp-alergias" placeholder="Alergias conocidas...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarRegistroRapido()">
                    <i class='bx bx-x'></i> Cancelar
                </button>
                <button type="submit" class="btn-primary" id="btn-guardar-paciente-rapido">
                    <i class='bx bx-save'></i> Registrar y Seleccionar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CONSULTAS_URL = '{{ url('consultas') }}';

let _consultaSubmitting = false;
document.getElementById('form-consulta').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_consultaSubmitting) return;
    _consultaSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Guardando...'; }

    const id  = document.getElementById('consulta-id').value;
    const url = id ? `${CONSULTAS_URL}/${id}` : CONSULTAS_URL;
    const fd  = new FormData(this);
    if (id) fd.set('_method', 'PUT');

    try {
        const res  = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }, body: fd });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeModal('modal-consulta');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'Error al guardar.', 'error');
        }
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        _consultaSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Guardar'; }
    }
});

async function verConsulta(id) {
    const res  = await fetch(`${CONSULTAS_URL}/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
    const data = await res.json();
    if (data.success) {
        const c = data.data;
        document.getElementById('ver-consulta-content').innerHTML = `
            <div class="detail-grid">
                <div class="detail-item"><strong>Paciente:</strong> ${c.paciente?.nombre} ${c.paciente?.apellido}</div>
                <div class="detail-item"><strong>Médico:</strong> ${c.empleado ? c.empleado.nombre + ' ' + c.empleado.apellido : '-'}</div>
                <div class="detail-item"><strong>Fecha/Hora:</strong> ${c.fecha_hora}</div>
                <div class="detail-item"><strong>Estado:</strong> ${c.estado}</div>
                <div class="detail-item" style="grid-column:1/-1"><strong>Motivo:</strong> ${c.motivo}</div>
                <div class="detail-item" style="grid-column:1/-1"><strong>Diagnóstico:</strong> ${c.diagnostico || '-'}</div>
                <div class="detail-item" style="grid-column:1/-1"><strong>Tratamiento:</strong> ${c.tratamiento || '-'}</div>
            </div>`;
        openModal('modal-ver-consulta');
    }
}

async function editarConsulta(id) {
    const res  = await fetch(`${CONSULTAS_URL}/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
    const data = await res.json();
    if (data.success) {
        const c = data.data;
        document.getElementById('consulta-id').value          = c.id;
        document.getElementById('consulta-paciente_id').value = c.paciente_id;
        document.getElementById('consulta-empleado_id').value = c.empleado_id || '';
        document.getElementById('consulta-fecha_hora').value  = c.fecha_hora?.replace(' ', 'T').substring(0, 16);
        document.getElementById('consulta-estado').value      = c.estado;
        document.getElementById('consulta-motivo').value      = c.motivo;
        document.getElementById('consulta-diagnostico').value = c.diagnostico || '';
        document.getElementById('consulta-tratamiento').value = c.tratamiento || '';
        document.getElementById('modal-consulta-title').innerHTML = "<i class='bx bx-edit'></i> Editar Consulta";
        // Mostrar sección de signos vitales y actualizar IDs ocultos
        document.getElementById('sv-wrapper').style.display = 'block';
        document.getElementById('sv-consulta-id').value = c.id;
        document.getElementById('sv-paciente-id').value = c.paciente_id;
        // Cargar signos vitales existentes si los hay
        cargarSignosVitales(c.id);
        openModal('modal-consulta');
    }
}

async function cargarSignosVitales(consultaId) {
    try {
        const res  = await fetch(`{{ url('signos-vitales/consulta') }}/${consultaId}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
        });
        const data = await res.json();
        if (data.success && data.data) {
            const sv = data.data;
            const set = (id, val) => { const el = document.getElementById(id); if (el && val != null) el.value = val; };
            set('sv-presion-sistolica',       sv.presion_sistolica);
            set('sv-presion-diastolica',      sv.presion_diastolica);
            set('sv-frecuencia-cardiaca',     sv.frecuencia_cardiaca);
            set('sv-frecuencia-respiratoria', sv.frecuencia_respiratoria);
            set('sv-temperatura',             sv.temperatura);
            set('sv-saturacion-oxigeno',      sv.saturacion_oxigeno);
            set('sv-peso',                    sv.peso);
            set('sv-talla',                   sv.talla);
            set('sv-imc',                     sv.imc);
            set('sv-glucosa',                 sv.glucosa);
            set('sv-observaciones',           sv.observaciones);
        }
    } catch(e) { /* no crítico */ }
}

function confirmarEliminarConsulta(id, nombre) {
    document.getElementById('delete-item-name').textContent = nombre;
    document.getElementById('btn-confirm-delete').onclick = async () => {
        const res  = await fetch(`${CONSULTAS_URL}/${id}`, {
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

// ── Registro rápido de paciente desde modal de consulta ───────────────────────

/**
 * Abre el modal de registro rápido de paciente.
 * Cierra el modal de consulta temporalmente para evitar superposición.
 */
function abrirRegistroRapidoPaciente() {
    // Guardar estado del modal de consulta para restaurarlo después
    openModal('modal-paciente-rapido');
}

/**
 * Cierra el modal de registro rápido y vuelve al modal de consulta.
 */
function cerrarRegistroRapido() {
    closeModal('modal-paciente-rapido');
    document.getElementById('form-paciente-rapido').reset();
}

/**
 * Envía el formulario de registro rápido.
 * Al guardar, agrega el paciente al selector y lo selecciona automáticamente.
 */
let _rpSubmitting = false;
document.getElementById('form-paciente-rapido').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_rpSubmitting) return;
    _rpSubmitting = true;

    const btn = document.getElementById('btn-guardar-paciente-rapido');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Registrando...'; }

    try {
        const res  = await fetch('{{ route('pacientes.store') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
            body: new FormData(this)
        });
        const data = await res.json();

        if (data.success) {
            // Construir el nombre completo del nuevo paciente
            const nombre   = document.getElementById('rp-nombre').value.trim();
            const apellido = document.getElementById('rp-apellido').value.trim();
            const cedula   = document.getElementById('rp-cedula').value.trim();
            const nombreCompleto = `${nombre} ${apellido}`;

            // Agregar la nueva opción al selector de pacientes de la consulta
            const selector = document.getElementById('consulta-paciente_id');
            const option   = document.createElement('option');
            option.value       = data.id;
            option.textContent = `${nombreCompleto} - ${cedula}`;
            option.selected    = true; // seleccionar automáticamente
            selector.appendChild(option);

            // Mostrar confirmación visual
            showToast(`✓ Paciente "${nombreCompleto}" registrado y seleccionado.`, 'success');

            // Cerrar modal de registro rápido
            cerrarRegistroRapido();

            // Resaltar el selector para que el usuario note la selección
            selector.style.borderColor = '#198754';
            selector.style.boxShadow   = '0 0 0 3px rgba(25,135,84,.2)';
            setTimeout(() => {
                selector.style.borderColor = '';
                selector.style.boxShadow   = '';
            }, 2500);

        } else {
            // Mostrar errores de validación
            const msg = data.message || 'Error al registrar el paciente.';
            showToast(msg, 'error');
        }
    } catch(err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        _rpSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Registrar y Seleccionar'; }
    }
});

// Alerta clínica automática de alergias y tipo de sangre al elegir paciente
document.addEventListener('DOMContentLoaded', function() {
    const selPac = document.getElementById('consulta-paciente_id');
    if (selPac) {
        selPac.addEventListener('change', function() {
            const banner = document.getElementById('consulta-alergia-banner');
            if (!banner) return;
            const opt = this.options[this.selectedIndex];
            if (!opt || !opt.value) { banner.style.display = 'none'; return; }
            const alergias = (opt.dataset.alergias || '').trim();
            const sangre   = (opt.dataset.sangre || '').trim();
            const edad     = (opt.dataset.edad || '').trim();
            let chips = [];
            if (edad)   chips.push('<strong>Edad:</strong> ' + edad + ' años');
            if (sangre) chips.push('<strong>Sangre:</strong> ' + sangre);
            if (alergias && alergias.toLowerCase() !== 'ninguna' && alergias.toLowerCase() !== 'no') {
                banner.style.display = 'block';
                banner.style.background = 'rgba(220,53,69,.1)';
                banner.style.border = '1px solid rgba(220,53,69,.35)';
                banner.style.color = 'var(--text-primary)';
                banner.innerHTML = '<div style="color:#dc3545;font-weight:700;"><i class="bx bx-error"></i> ⚠️ Alergias registradas: ' + alergias + '</div>'
                    + (chips.length ? '<div style="margin-top:3px;font-size:11px;color:var(--text-secondary);">' + chips.join(' · ') + '</div>' : '');
            } else if (chips.length) {
                banner.style.display = 'block';
                banner.style.background = 'rgba(13,110,253,.07)';
                banner.style.border = '1px solid rgba(13,110,253,.2)';
                banner.style.color = 'var(--text-secondary)';
                banner.innerHTML = '<i class="bx bx-user-check" style="color:var(--primary);"></i> ' + chips.join(' · ') + ' · Sin alergias reportadas';
            } else {
                banner.style.display = 'none';
            }
        });
    }
});
</script>
@endpush
