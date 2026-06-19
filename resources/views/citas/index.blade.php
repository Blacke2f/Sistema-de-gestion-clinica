@extends('layouts.app')

@section('title', 'Agenda de Citas')
@section('nav-title', 'Agenda de Citas')

@push('styles')
<link href="{{ asset('vendor/fullcalendar/index.global.min.css') }}" rel='stylesheet' />
<style>
/* ── FullCalendar — modo claro y oscuro ── */
.fc .fc-toolbar-title { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); }

/* Botones de navegación */
.fc .fc-button {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #fff !important;
    font-size: 12px;
    font-weight: 600;
    box-shadow: none !important;
}
.fc .fc-button:hover  { background: #0b5ed7 !important; border-color: #0b5ed7 !important; }
.fc .fc-button:focus  { box-shadow: 0 0 0 3px rgba(13,110,253,.3) !important; }
.fc .fc-button-active { background: #0b5ed7 !important; border-color: #0b5ed7 !important; }

/* Cabecera de días */
.fc .fc-col-header-cell-cushion {
    color: var(--text-primary) !important;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .5px;
}

/* Números de día */
.fc .fc-daygrid-day-number {
    color: var(--text-primary) !important;
    font-weight: 600;
    font-size: 13px;
}

/* Bordes de celdas */
.fc-theme-standard td,
.fc-theme-standard th,
.fc-theme-standard .fc-scrollgrid { border-color: var(--border-color) !important; }

/* Fondo del calendario */
.fc .fc-daygrid-body,
.fc .fc-scrollgrid,
.fc table { background: var(--bg-card) !important; }

/* Día de hoy */
.fc .fc-day-today { background: rgba(13,110,253,.08) !important; }
body.dark-mode .fc .fc-day-today { background: rgba(13,110,253,.15) !important; }

/* ── EVENTOS — bloques sólidos y legibles ── */
.fc .fc-daygrid-event,
.fc .fc-daygrid-event.fc-event,
.fc a.fc-event {
    border-radius: 5px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    padding: 3px 7px !important;
    border: none !important;
    color: #fff !important;
    text-shadow: 0 1px 2px rgba(0,0,0,.4) !important;
    margin-bottom: 2px !important;
    min-height: 22px !important;
    display: flex !important;
    align-items: center !important;
    opacity: 1 !important;
}

/* Forzar que el color de fondo del evento se vea */
.fc .fc-daygrid-event .fc-event-main {
    color: #fff !important;
    padding: 0 !important;
}

/* Punto de color — ocultarlo, el bloque ya tiene color */
.fc .fc-daygrid-event-dot { display: none !important; }

/* Título del evento */
.fc .fc-event-title,
.fc .fc-event-title-container {
    color: #fff !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    flex: 1 !important;
}

/* Hora del evento */
.fc .fc-event-time {
    color: rgba(255,255,255,.95) !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    margin-right: 4px !important;
    flex-shrink: 0 !important;
}

/* Hover en eventos */
.fc .fc-daygrid-event:hover {
    opacity: .85 !important;
    cursor: pointer !important;
    box-shadow: 0 3px 10px rgba(0,0,0,.3) !important;
}

/* Asegurar que eventos sin color asignado tengan un color visible */
.fc .fc-daygrid-event:not([style*="background"]) {
    background-color: #0d6efd !important;
}

/* Vista de semana/día — eventos en timeline */
.fc .fc-timegrid-event,
.fc .fc-timegrid-event.fc-event {
    border-radius: 6px !important;
    border: none !important;
    color: #fff !important;
    font-weight: 600 !important;
    padding: 4px 8px !important;
}
.fc .fc-timegrid-event .fc-event-title { color: #fff !important; font-size: 12px !important; }
.fc .fc-timegrid-event .fc-event-time  { color: rgba(255,255,255,.9) !important; font-size: 11px !important; }

/* Modo oscuro — fondo de celdas */
body.dark-mode .fc .fc-daygrid-day { background: var(--bg-card) !important; }
body.dark-mode .fc .fc-daygrid-day:hover { background: rgba(255,255,255,.03) !important; }
body.dark-mode .fc .fc-list-event { background: var(--bg-card); color: var(--text-primary); }
body.dark-mode .fc .fc-list-day-cushion { background: var(--bg-body); color: var(--text-primary); }
body.dark-mode .fc .fc-timegrid-slot { border-color: var(--border-color) !important; }
body.dark-mode .fc .fc-timegrid-axis { color: var(--text-muted) !important; }

/* Más eventos ("+2 más") */
.fc .fc-daygrid-more-link {
    color: var(--primary) !important;
    font-weight: 700 !important;
    font-size: 11px !important;
}

/* Estadísticas de citas */
.stat-cita { background: var(--bg-card); border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.stat-cita i { font-size: 28px; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.stat-cita .val { font-size: 22px; font-weight: 700; color: var(--text-primary); }
.stat-cita .lbl { font-size: 12px; color: var(--text-muted); }

/* Tabs */
.tab-btn { background: none; border: none; padding: 8px 18px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: var(--text-secondary); transition: all .2s; }
.tab-btn.active { background: var(--primary); color: #fff; }
.tab-content { display: none; }
.tab-content.active { display: block; }
</style>
@endpush

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Agenda de Citas</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Citas</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <button class="btn-primary" onclick="openModal('modal-cita')">
            <i class='bx bx-plus'></i> Nueva Cita
        </button>
    </div>
</div>

{{-- Estadísticas --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
    <div class="stat-cita">
        <i class='bx bx-calendar-check' style="background:rgba(13,110,253,.12);color:#0d6efd;"></i>
        <div><div class="val">{{ $stats['hoy'] }}</div><div class="lbl">Citas Hoy</div></div>
    </div>
    <div class="stat-cita">
        <i class='bx bx-time' style="background:rgba(255,193,7,.12);color:#ffc107;"></i>
        <div><div class="val">{{ $stats['pendientes'] }}</div><div class="lbl">Pendientes</div></div>
    </div>
    <div class="stat-cita">
        <i class='bx bx-calendar' style="background:rgba(25,135,84,.12);color:#198754;"></i>
        <div><div class="val">{{ $stats['semana'] }}</div><div class="lbl">Próximos 7 días</div></div>
    </div>
</div>

{{-- Tabs: Calendario / Lista --}}
<div class="table-container">
    <div class="table-header" style="flex-wrap:wrap;gap:10px;">
        <div style="display:flex;gap:6px;">
            <button class="tab-btn active" onclick="switchTab('calendario')"><i class='bx bx-calendar'></i> Calendario</button>
            <button class="tab-btn" onclick="switchTab('lista')"><i class='bx bx-list-ul'></i> Lista</button>
        </div>
        <span class="badge badge-info">{{ $citas->total() }} citas registradas</span>
    </div>

    {{-- TAB CALENDARIO --}}
    <div id="tab-calendario" class="tab-content active" style="padding:16px;">
        <div id="calendar"></div>
    </div>

    {{-- TAB LISTA --}}
    <div id="tab-lista" class="tab-content">
        {{-- Filtros --}}
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;padding:16px 16px 0;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar paciente o motivo..." class="form-control" style="max-width:260px;">
            <select name="estado" class="form-control" style="max-width:160px;">
                <option value="">Todos los estados</option>
                @foreach(['programada','confirmada','en_curso','completada','cancelada','no_asistio'] as $e)
                    <option value="{{ $e }}" {{ $estado === $e ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$e)) }}</option>
                @endforeach
            </select>
            <input type="date" name="fecha" value="{{ $fecha }}" class="form-control" style="max-width:160px;">
            <button type="submit" class="btn-primary btn-sm"><i class='bx bx-search'></i> Filtrar</button>
            <a href="{{ route('citas.index') }}" class="btn-secondary btn-sm">Limpiar</a>
        </form>

        <div class="table-responsive" style="padding:16px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Fecha / Hora</th>
                        <th>Duración</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($citas as $i => $c)
                    <tr>
                        <td>{{ $citas->firstItem() + $i }}</td>
                        <td>
                            <div class="cell-user">
                                <img src="" data-avatar="{{ $c->paciente->nombre_completo }}" data-bg="#0d6efd" data-size="32" alt="">
                                <span>{{ $c->paciente->nombre_completo }}</span>
                            </div>
                        </td>
                        <td>{{ $c->medico_nombre }}</td>
                        <td>
                            <strong>{{ $c->fecha_hora->format('d/m/Y') }}</strong><br>
                            <small>{{ $c->fecha_hora->format('H:i') }}</small>
                        </td>
                        <td>{{ $c->duracion_minutos }} min</td>
                        <td>{{ \Illuminate\Support\Str::limit($c->motivo, 40) }}</td>
                        <td>
                            <span class="badge badge-{{ $c->estado_color }}">{{ $c->estado_texto }}</span>
                        </td>
                        <td class="actions">
                            <button class="btn-icon btn-edit" onclick="editarCita({{ $c->id }})" title="Editar"><i class='bx bx-edit'></i></button>
                            @if(!$c->consulta_id && in_array($c->estado, ['programada','confirmada','en_curso']))
                            <button class="btn-icon btn-success" onclick="convertirCita({{ $c->id }})" title="Convertir en Consulta"><i class='bx bx-transfer'></i></button>
                            @endif
                            <button class="btn-icon btn-delete" onclick="eliminarCita({{ $c->id }})" title="Eliminar"><i class='bx bx-trash'></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="empty-row">
                        <div class="empty-state"><i class='bx bx-calendar-x'></i><p>No hay citas registradas</p>
                        <button class="btn-primary btn-sm" onclick="openModal('modal-cita')">Programar Cita</button></div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $citas->links('partials.pagination') }}</div>
    </div>
</div>

{{-- MODAL NUEVA / EDITAR CITA --}}
<div class="modal" id="modal-cita">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-cita-title"><i class='bx bx-calendar-plus'></i> Nueva Cita</h2>
            <button class="modal-close" onclick="closeModal('modal-cita')">&times;</button>
        </div>
        <form id="form-cita" class="modal-form">
            @csrf
            <input type="hidden" name="_method" id="cita-method" value="POST">
            <input type="hidden" name="id" id="cita-id">

            <div class="form-row">
                <div class="form-group">
                    <label><i class='bx bx-user'></i> Paciente *</label>
                    <select name="paciente_id" id="cita-paciente_id" required>
                        <option value="">Seleccionar paciente...</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre_completo }} — {{ $p->cedula }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label><i class='bx bx-user-check'></i> Médico</label>
                    <select name="empleado_id" id="cita-empleado_id">
                        <option value="">Seleccionar médico...</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre_completo }} — {{ $m->cargo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label><i class='bx bx-calendar'></i> Fecha y Hora *</label>
                    <input type="datetime-local" name="fecha_hora" id="cita-fecha_hora" required>
                </div>
                <div class="form-group">
                    <label><i class='bx bx-time'></i> Duración manual (min)</label>
                    <select name="duracion_minutos" id="cita-duracion_minutos">
                        <option value="15">15 min</option>
                        <option value="30" selected>30 min</option>
                        <option value="45">45 min</option>
                        <option value="60">1 hora</option>
                        <option value="90">1.5 horas</option>
                        <option value="120">2 horas</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label><i class='bx bx-briefcase-alt'></i> Servicio / Tipo de Cita</label>
                    <select name="servicio_id" id="cita-servicio_id" onchange="document.getElementById('cita-duracion_minutos').disabled = !!this.value;">
                        <option value="">Personalizado (usar manual)</option>
                        @foreach($servicios as $s)
                            <option value="{{ $s->id }}">{{ $s->nombre }} ({{ $s->duracion_minutos }} min)</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label><i class='bx bx-edit-alt'></i> Motivo / Nota rápida</label>
                    <input type="text" name="motivo" id="cita-motivo" placeholder="Motivo de la cita">
                </div>
            </div>
            <div class="form-group">
                <label><i class='bx bx-note'></i> Notas adicionales</label>
                <textarea name="notas" id="cita-notas" rows="2" placeholder="Observaciones o instrucciones previas..."></textarea>
            </div>
            <div id="cita-estado-group" class="form-group" style="display:none;">
                <label><i class='bx bx-info-circle'></i> Estado</label>
                <select name="estado" id="cita-estado">
                    <option value="programada">Programada</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="en_curso">En Curso</option>
                    <option value="completada">Completada</option>
                    <option value="cancelada">Cancelada</option>
                    <option value="no_asistio">No Asistió</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-cita')"><i class='bx bx-x'></i> Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-save'></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/fullcalendar/index.global.min.js') }}"></script>
<script>
const ROUTES_CITAS = {
    store:    '{{ route('citas.store') }}',
    show:     '{{ url('citas') }}/',
    update:   '{{ url('citas') }}/',
    destroy:  '{{ url('citas') }}/',
    convertir:'{{ url('citas') }}/',
    calendario: '{{ route('citas.calendario') }}',
};

// ── Calendario FullCalendar ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const calEl = document.getElementById('calendar');
    if (!calEl) return;

    const calendar = new FullCalendar.Calendar(calEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,timeGridDay',
        },
        events: {
            url: ROUTES_CITAS.calendario,
            extraParams: () => ({ _token: window.CSRF_TOKEN }),
        },
        eventClick: function (info) {
            const p = info.event.extendedProps;
            const color = info.event.backgroundColor || '#0d6efd';

            // Popup visual con detalles de la cita
            const popup = document.getElementById('cita-popup');
            if (popup) popup.remove();

            const div = document.createElement('div');
            div.id = 'cita-popup';
            div.style.cssText = `
                position:fixed; z-index:9999;
                background:var(--bg-card);
                border-radius:12px;
                box-shadow:0 8px 32px rgba(0,0,0,.25);
                padding:0;
                min-width:260px;
                max-width:320px;
                overflow:hidden;
                border-top:4px solid ${color};
            `;

            // Posicionar cerca del clic
            const rect = info.el.getBoundingClientRect();
            const top  = Math.min(rect.bottom + 8, window.innerHeight - 220);
            const left = Math.min(rect.left, window.innerWidth - 340);
            div.style.top  = top + 'px';
            div.style.left = left + 'px';

            const estadoColors = {
                programada: '#0d6efd', confirmada: '#0dcaf0',
                en_curso: '#ffc107', completada: '#198754',
                cancelada: '#dc3545', no_asistio: '#6c757d'
            };
            const estadoColor = estadoColors[p.estado] || '#0d6efd';

            div.innerHTML = `
                <div style="padding:14px 16px;border-bottom:1px solid var(--border-color);">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                        <strong style="font-size:14px;color:var(--text-primary);flex:1;">${escHtml(p.paciente)}</strong>
                        <button onclick="document.getElementById('cita-popup').remove()"
                            style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:18px;line-height:1;padding:0;">&times;</button>
                    </div>
                    <span style="display:inline-block;margin-top:4px;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${estadoColor}22;color:${estadoColor};">
                        ${escHtml(p.estadoTexto)}
                    </span>
                </div>
                <div style="padding:12px 16px;">
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px;"><i class='bx bx-user-check'></i> ${escHtml(p.medico)}</div>
                    <div style="font-size:13px;color:var(--text-primary);"><i class='bx bx-edit-alt'></i> ${escHtml(p.motivo)}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:6px;">
                        <i class='bx bx-time'></i>
                        ${info.event.start ? info.event.start.toLocaleTimeString('es-DO',{hour:'2-digit',minute:'2-digit'}) : ''}
                        ${info.event.end   ? ' — ' + info.event.end.toLocaleTimeString('es-DO',{hour:'2-digit',minute:'2-digit'}) : ''}
                    </div>
                </div>
                <div style="padding:8px 16px 12px;display:flex;gap:8px;">
                    <button onclick="editarCita(${info.event.id});document.getElementById('cita-popup').remove();"
                        style="flex:1;padding:6px;border-radius:6px;border:1px solid var(--primary);background:transparent;color:var(--primary);cursor:pointer;font-size:12px;font-weight:600;">
                        <i class='bx bx-edit'></i> Editar
                    </button>
                    <button onclick="convertirCita(${info.event.id});document.getElementById('cita-popup').remove();"
                        style="flex:1;padding:6px;border-radius:6px;border:none;background:var(--primary);color:#fff;cursor:pointer;font-size:12px;font-weight:600;">
                        <i class='bx bx-transfer'></i> Consulta
                    </button>
                </div>
            `;

            document.body.appendChild(div);

            // Cerrar al hacer clic fuera
            setTimeout(() => {
                document.addEventListener('click', function closePop(e) {
                    if (!div.contains(e.target)) {
                        div.remove();
                        document.removeEventListener('click', closePop);
                    }
                });
            }, 100);
        },
        eventDidMount: function (info) {
            const color = info.event.backgroundColor || '#0d6efd';
            const el    = info.el;

            // Forzar estilos inline — FullCalendar los aplica inline y CSS externo no puede sobreescribirlos
            el.style.setProperty('background-color', color, 'important');
            el.style.setProperty('border-color',     color, 'important');
            el.style.setProperty('border',           'none','important');
            el.style.setProperty('border-radius',    '5px', 'important');
            el.style.setProperty('padding',          '3px 7px', 'important');
            el.style.setProperty('font-weight',      '700', 'important');
            el.style.setProperty('font-size',        '12px','important');
            el.style.setProperty('box-shadow',       '0 1px 4px rgba(0,0,0,.25)', 'important');
            el.style.setProperty('opacity',          '1',   'important');
            el.style.setProperty('margin-bottom',    '2px', 'important');

            // Texto blanco en todos los hijos
            el.querySelectorAll('*').forEach(child => {
                child.style.setProperty('color', '#ffffff', 'important');
            });

            el.title = `${info.event.extendedProps.paciente} — ${info.event.extendedProps.motivo}`;
        },
        height: 'auto',
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        
        // ── Drag & Drop / Resize (Reprogramación Profesional) ──
        editable: true,
        eventDurationEditable: true,
        eventDrop: async function(info) {
            await actualizarFechaCita(info);
        },
        eventResize: async function(info) {
            await actualizarFechaCita(info);
        }
    });
    calendar.render();
});

// Función para procesar el drag & drop / resize
async function actualizarFechaCita(info) {
    const id = info.event.id;
    const start = info.event.start;
    const end = info.event.end || new Date(start.getTime() + 30*60000); // 30 min por defecto
    
    // Ajustar a timezone local para input datetime-local formating
    const tzoffset = start.getTimezoneOffset() * 60000;
    const localStart = new Date(start.getTime() - tzoffset).toISOString().slice(0, 16).replace('T', ' ');
    
    // Calcular duracion
    const diffMs = end - start;
    const duracion_minutos = Math.round(diffMs / 60000);

    const fd = new FormData();
    fd.append('_method', 'PUT');
    fd.append('paciente_id', info.event.extendedProps.paciente_id);
    if(info.event.extendedProps.empleado_id) fd.append('empleado_id', info.event.extendedProps.empleado_id);
    if(info.event.extendedProps.medico_id) fd.append('medico_id', info.event.extendedProps.medico_id);
    if(info.event.extendedProps.servicio_id) fd.append('servicio_id', info.event.extendedProps.servicio_id);
    fd.append('fecha_hora', localStart);
    fd.append('duracion_minutos', duracion_minutos);
    fd.append('motivo', info.event.extendedProps.motivo || 'Cita médica');
    fd.append('estado', info.event.extendedProps.estado);

    try {
        const res = await fetch(ROUTES_CITAS.update + id, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
            body: fd
        });
        const data = await res.json();
        
        if(data.success) {
            showToast('Cita reprogramada exitosamente.', 'success');
        } else {
            showToast(data.message || 'Error al reprogramar.', 'error');
            info.revert(); // Revertir visualmente si hay conflicto
        }
    } catch (e) {
        showToast('Error de conexión al servidor.', 'error');
        info.revert();
    }
}

// ── Tabs ─────────────────────────────────────────────────────────────────────
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    event.target.closest('.tab-btn').classList.add('active');
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Formulario ────────────────────────────────────────────────────────────────
let _citaSubmitting = false;
document.getElementById('form-cita').addEventListener('submit', async function (e) {
    e.preventDefault();
    if (_citaSubmitting) return;
    _citaSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Guardando...'; }

    const id  = document.getElementById('cita-id').value;
    const url = id ? ROUTES_CITAS.update + id : ROUTES_CITAS.store;
    const fd  = new FormData(this);
    if (id) fd.set('_method', 'PUT');

    try {
        const res  = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }, body: fd });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeModal('modal-cita');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'Error al guardar.', 'error');
        }
    } catch (err) {
        showToast('Error de conexión.', 'error');
    } finally {
        _citaSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Guardar'; }
    }
});

async function editarCita(id) {
    const res  = await fetch(ROUTES_CITAS.show + id, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
    const data = await res.json();
    if (data.success) {
        const c = data.data;
        document.getElementById('cita-id').value             = c.id;
        document.getElementById('cita-paciente_id').value    = c.paciente_id;
        document.getElementById('cita-empleado_id').value    = c.empleado_id || c.medico_id || '';
        document.getElementById('cita-servicio_id').value    = c.servicio_id || '';
        document.getElementById('cita-fecha_hora').value     = c.fecha_hora ? c.fecha_hora.substring(0, 16) : '';
        document.getElementById('cita-duracion_minutos').value = c.duracion_minutos;
        document.getElementById('cita-duracion_minutos').disabled = !!c.servicio_id;
        document.getElementById('cita-motivo').value         = c.motivo || '';
        document.getElementById('cita-notas').value          = c.notas || '';
        document.getElementById('cita-estado').value         = c.estado;
        document.getElementById('cita-estado-group').style.display = 'block';
        document.getElementById('modal-cita-title').innerHTML = "<i class='bx bx-edit'></i> Editar Cita";
        openModal('modal-cita');
    }
}

async function convertirCita(id) {
    // Usar el modal de confirmación personalizado del sistema
    document.getElementById('delete-item-name').textContent = 'La cita se convertirá en una consulta médica activa.';
    document.querySelector('#modal-confirm-delete .modal-header').className = 'modal-header modal-header-primary';
    document.querySelector('#modal-confirm-delete h2').innerHTML = "<i class='bx bx-transfer'></i> Convertir en Consulta";
    document.getElementById('btn-confirm-delete').className = 'btn-primary';
    document.getElementById('btn-confirm-delete').innerHTML = "<i class='bx bx-transfer'></i> Convertir";
    document.getElementById('btn-confirm-delete').onclick = async () => {
        const res  = await fetch(ROUTES_CITAS.convertir + id + '/convertir', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await res.json();
        closeModal('modal-confirm-delete');
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message, 'error');
        }
    };
    openModal('modal-confirm-delete');
}

async function eliminarCita(id) {
    document.getElementById('delete-item-name').textContent = 'Esta cita será eliminada permanentemente.';
    document.querySelector('#modal-confirm-delete .modal-header').className = 'modal-header modal-header-danger';
    document.querySelector('#modal-confirm-delete h2').innerHTML = "<i class='bx bx-error-circle'></i> Confirmar Eliminación";
    document.getElementById('btn-confirm-delete').className = 'btn-danger';
    document.getElementById('btn-confirm-delete').innerHTML = "<i class='bx bx-trash'></i> Eliminar";
    document.getElementById('btn-confirm-delete').onclick = async () => {
        const res  = await fetch(ROUTES_CITAS.destroy + id, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: '_method=DELETE'
        });
        const data = await res.json();
        closeModal('modal-confirm-delete');
        if (data.success) { showToast(data.message, 'success'); setTimeout(() => location.reload(), 800); }
        else showToast(data.message, 'error');
    };
    openModal('modal-confirm-delete');
}

// Reset modal
document.querySelector('#modal-cita .modal-close').addEventListener('click', () => {
    document.getElementById('form-cita').reset();
    document.getElementById('cita-id').value = '';
    document.getElementById('cita-duracion_minutos').disabled = false;
    document.getElementById('cita-estado-group').style.display = 'none';
    document.getElementById('modal-cita-title').innerHTML = "<i class='bx bx-calendar-plus'></i> Nueva Cita";
});
</script>
@endpush
