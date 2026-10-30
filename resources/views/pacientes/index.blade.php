@extends('layouts.app')

@section('title', 'Pacientes')
@section('nav-title', 'Pacientes')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Gestión de Pacientes</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Pacientes</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <button class="btn-export" onclick="openModal('modal-export-pacientes')">
            <i class='bx bx-export'></i> Exportar
        </button>
        <button class="btn-primary" onclick="openModal('modal-paciente')">
            <i class='bx bx-plus'></i> Nuevo Paciente
        </button>
    </div>
</div>

@include('partials.export-modal', [
    'exportModule' => 'pacientes',
    'exportTitle'  => 'Exportar Pacientes',
    'exportColor'  => '#005a14',
    'exportIcon'   => 'bxs-user-detail',
    'exportRoutes' => [
        'pdf'   => route('export.pacientes', ['formato' => 'pdf']),
        'excel' => route('export.pacientes', ['formato' => 'excel']),
        'csv'   => route('pacientes.exportar'),
    ],
])

<div class="table-container">
    <div class="table-header">
        <h3><i class='bx bx-user-detail'></i> Listado de Pacientes</h3>
        @php
            $kpiPacTotal    = \App\Models\Paciente::count();
            $kpiPacMes      = \App\Models\Paciente::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
            $kpiPacAlergias = \App\Models\Paciente::whereNotNull('alergias')->where('alergias', '!=', '')->whereNotIn('alergias', ['Ninguna', 'ninguna', 'No', 'no'])->count();
        @endphp
        <div style="width:100%;display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:12px;margin:10px 0 4px;">
            <div style="background:linear-gradient(135deg,#0d6efd,#3b82f6);color:#fff;padding:12px 16px;border-radius:10px;display:flex;align-items:center;gap:12px;">
                <i class='bx bxs-user-detail' style="font-size:28px;opacity:.85;"></i>
                <div><div style="font-size:20px;font-weight:800;line-height:1;">{{ $kpiPacTotal }}</div><small style="opacity:.9;font-size:11px;">Pacientes Registrados</small></div>
            </div>
            <div style="background:linear-gradient(135deg,#198754,#10b981);color:#fff;padding:12px 16px;border-radius:10px;display:flex;align-items:center;gap:12px;">
                <i class='bx bx-user-plus' style="font-size:28px;opacity:.85;"></i>
                <div><div style="font-size:20px;font-weight:800;line-height:1;">{{ $kpiPacMes }}</div><small style="opacity:.9;font-size:11px;">Nuevos este Mes</small></div>
            </div>
            <div style="background:linear-gradient(135deg,#dc3545,#f43f5e);color:#fff;padding:12px 16px;border-radius:10px;display:flex;align-items:center;gap:12px;">
                <i class='bx bx-error-alt' style="font-size:28px;opacity:.85;"></i>
                <div><div style="font-size:20px;font-weight:800;line-height:1;">{{ $kpiPacAlergias }}</div><small style="opacity:.9;font-size:11px;">Con Alergias Reportadas</small></div>
            </div>
        </div>
        <span class="badge badge-info"><i class='bx bx-user'></i> {{ $pacientes->total() }} registros</span>
    </div>
    <div class="table-responsive">
        <table class="data-table" id="pacientes-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre Completo</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Género</th>
                    <th>Tipo Sangre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pacientes as $i => $p)
                    <tr class="clickable-row" title="Clic para ver detalles" onclick="verPaciente({{ $p->id }})" data-id="{{ $p->id }}">
                        <td><span class="cell-num">{{ $pacientes->firstItem() + $i }}</span></td>
                        <td>
                            <div class="cell-user">
                                <img src="" data-avatar="{{ $p->nombre_completo }}" data-bg="#005a14" data-size="32" alt="">
                                <div>
                                    <span>{{ $p->nombre_completo }}</span>
                                    @if($p->alergias && !in_array(strtolower(trim($p->alergias)), ['ninguna', 'no', '-']))
                                        <span title="Alergias: {{ $p->alergias }}" style="display:inline-block;margin-left:4px;font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;background:rgba(220,53,69,.12);color:#dc3545;">⚠️ Alergia</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><code>{{ $p->cedula }}</code></td>
                        <td>{{ $p->telefono ?? '-' }}</td>
                        <td>{{ $p->email ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $p->genero === 'M' ? 'primary' : ($p->genero === 'F' ? 'pink' : 'secondary') }}">
                                {{ $p->genero_texto }}
                            </span>
                        </td>
                        <td><span class="blood-type">{{ $p->tipo_sangre ?? '-' }}</span></td>
                        <td class="actions" onclick="event.stopPropagation()">
                            <a href="{{ route('expediente.show', $p->id) }}" class="btn-icon btn-primary" title="Expediente Clínico">
                                <i class='bx bx-folder-open'></i>
                            </a>
                            <button class="btn-icon btn-edit" onclick="editarPaciente({{ $p->id }})" title="Editar">
                                <i class='bx bx-edit'></i>
                            </button>
                            <button class="btn-icon btn-delete" onclick="confirmarEliminar({{ $p->id }}, '{{ addslashes($p->nombre_completo) }}')" title="Eliminar">
                                <i class='bx bx-trash'></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty-row">
                        <div class="empty-state">
                            <i class='bx bx-user-x'></i>
                            <p>No hay pacientes registrados</p>
                            <button class="btn-primary btn-sm" onclick="openModal('modal-paciente')">Agregar Paciente</button>
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="pagination-wrapper">{{ $pacientes->links('partials.pagination') }}</div>
</div>

{{-- MODAL CREAR / EDITAR PACIENTE --}}
<div class="modal" id="modal-paciente">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-paciente-title"><i class='bx bx-user-plus'></i> Nuevo Paciente</h2>
            <button class="modal-close" onclick="closeModal('modal-paciente')">&times;</button>
        </div>
        <form id="form-paciente" class="modal-form">
            @csrf
            <input type="hidden" name="_method" id="paciente-method" value="POST">
            <input type="hidden" name="id" id="paciente-id">

            <div class="form-row">
                <div class="form-group">
                    <label><i class='bx bx-user'></i> Nombre *</label>
                    <input type="text" name="nombre" id="paciente-nombre" placeholder="Nombre" required>
                </div>
                <div class="form-group">
                    <label><i class='bx bx-user'></i> Apellido *</label>
                    <input type="text" name="apellido" id="paciente-apellido" placeholder="Apellido" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label><i class='bx bx-id-card'></i> Cédula *</label>
                    <input type="text" name="cedula" id="paciente-cedula" placeholder="000-0000000-0" required>
                </div>
                <div class="form-group">
                    <label><i class='bx bx-phone'></i> Teléfono</label>
                    <input type="tel" name="telefono" id="paciente-telefono" placeholder="809-000-0000">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label><i class='bx bx-envelope'></i> Email</label>
                    <input type="email" name="email" id="paciente-email" placeholder="correo@ejemplo.com">
                </div>
                <div class="form-group">
                    <label><i class='bx bx-calendar'></i> Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="paciente-fecha_nacimiento">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label><i class='bx bx-male-female'></i> Género</label>
                    <select name="genero" id="paciente-genero">
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class='bx bx-droplet'></i> Tipo Sangre</label>
                    <select name="tipo_sangre" id="paciente-tipo_sangre">
                        <option value="">Seleccionar</option>
                        @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $ts)
                            <option value="{{ $ts }}">{{ $ts }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label><i class='bx bx-map'></i> Dirección</label>
                <textarea name="direccion" id="paciente-direccion" rows="2" placeholder="Dirección del paciente"></textarea>
            </div>
            <div class="form-group">
                <label><i class='bx bx-error'></i> Alergias</label>
                <textarea name="alergias" id="paciente-alergias" rows="2" placeholder="Indique alergias conocidas"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-paciente')">
                    <i class='bx bx-x'></i> Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class='bx bx-save'></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL VER PACIENTE --}}
<div class="modal" id="modal-ver-paciente">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class='bx bx-user-detail'></i> Detalles del Paciente</h2>
            <button class="modal-close" onclick="closeModal('modal-ver-paciente')">&times;</button>
        </div>
        <div class="modal-body" id="ver-paciente-content"></div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('modal-ver-paciente')">Cerrar</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.data-table tbody tr.clickable-row { cursor: pointer; }
.data-table tbody tr.clickable-row:hover { background: rgba(13,110,253,.06); }
.data-table tbody tr.clickable-row td.actions { cursor: default; }
</style>
@endpush

@push('scripts')
<script>
const ROUTES = {
    pacientes: {
        store:   '{{ route('pacientes.store') }}',
        show:    '{{ url('pacientes') }}/',
        update:  '{{ url('pacientes') }}/',
        destroy: '{{ url('pacientes') }}/',
    }
};

// Formulario crear/editar
let _pacienteSubmitting = false;
document.getElementById('form-paciente').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_pacienteSubmitting) return;
    _pacienteSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Guardando...'; }

    const id     = document.getElementById('paciente-id').value;
    const method = id ? 'PUT' : 'POST';
    const url    = id ? ROUTES.pacientes.update + id : ROUTES.pacientes.store;

    const formData = new FormData(this);
    if (id) formData.set('_method', 'PUT');

    try {
        const res  = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }, body: formData });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeModal('modal-paciente');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'Error al guardar.', 'error');
        }
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        _pacienteSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Guardar'; }
    }
});

async function verPaciente(id) {
    const res  = await fetch(ROUTES.pacientes.show + id, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
    const data = await res.json();
    if (data.success) {
        const p = data.data;
        document.getElementById('ver-paciente-content').innerHTML = `
            <div class="detail-grid">
                <div class="detail-item"><strong>Nombre:</strong> ${p.nombre} ${p.apellido}</div>
                <div class="detail-item"><strong>Cédula:</strong> ${p.cedula}</div>
                <div class="detail-item"><strong>Teléfono:</strong> ${p.telefono || '-'}</div>
                <div class="detail-item"><strong>Email:</strong> ${p.email || '-'}</div>
                <div class="detail-item"><strong>Fecha Nacimiento:</strong> ${p.fecha_nacimiento || '-'}</div>
                <div class="detail-item"><strong>Género:</strong> ${p.genero === 'M' ? 'Masculino' : p.genero === 'F' ? 'Femenino' : 'Otro'}</div>
                <div class="detail-item"><strong>Tipo Sangre:</strong> ${p.tipo_sangre || '-'}</div>
                <div class="detail-item"><strong>Dirección:</strong> ${p.direccion || '-'}</div>
                <div class="detail-item"><strong>Alergias:</strong> ${p.alergias || 'Ninguna'}</div>
            </div>`;
        openModal('modal-ver-paciente');
    }
}

async function editarPaciente(id) {
    const res  = await fetch(ROUTES.pacientes.show + id, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
    const data = await res.json();
    if (data.success) {
        const p = data.data;
        document.getElementById('paciente-id').value             = p.id;
        document.getElementById('paciente-nombre').value         = p.nombre;
        document.getElementById('paciente-apellido').value       = p.apellido;
        document.getElementById('paciente-cedula').value         = p.cedula;
        document.getElementById('paciente-telefono').value       = p.telefono || '';
        document.getElementById('paciente-email').value          = p.email || '';
        document.getElementById('paciente-fecha_nacimiento').value = p.fecha_nacimiento || '';
        document.getElementById('paciente-genero').value         = p.genero;
        document.getElementById('paciente-tipo_sangre').value    = p.tipo_sangre || '';
        document.getElementById('paciente-direccion').value      = p.direccion || '';
        document.getElementById('paciente-alergias').value       = p.alergias || '';
        document.getElementById('modal-paciente-title').innerHTML = "<i class='bx bx-edit'></i> Editar Paciente";
        openModal('modal-paciente');
    }
}

function confirmarEliminar(id, nombre) {
    document.getElementById('delete-item-name').textContent = nombre;
    document.getElementById('btn-confirm-delete').onclick = async () => {
        const res  = await fetch(ROUTES.pacientes.destroy + id, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: '_method=DELETE'
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeModal('modal-confirm-delete');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message, 'error');
        }
    };
    openModal('modal-confirm-delete');
}

// Reset modal on close
document.querySelector('#modal-paciente .modal-close').addEventListener('click', () => {
    document.getElementById('form-paciente').reset();
    document.getElementById('paciente-id').value = '';
    document.getElementById('modal-paciente-title').innerHTML = "<i class='bx bx-user-plus'></i> Nuevo Paciente";
});
</script>
@endpush
