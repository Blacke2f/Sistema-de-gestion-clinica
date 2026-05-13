@extends('layouts.app')

@section('title', 'Nómina')
@section('nav-title', 'Nómina')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Gestión de Nómina</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Nómina</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <button class="btn-export" onclick="openModal('modal-export-nomina')">
            <i class='bx bx-export'></i> Exportar
        </button>
        <button class="btn-primary" onclick="openModal('modal-empleado')"><i class='bx bx-plus'></i> Nuevo Empleado</button>
    </div>
</div>

@include('partials.export-modal', [
    'exportModule' => 'nomina',
    'exportTitle'  => 'Exportar Nómina',
    'exportColor'  => '#6f42c1',
    'exportIcon'   => 'bxs-group',
    'exportRoutes' => [
        'pdf'   => route('export.nomina', ['formato' => 'pdf']),
        'excel' => route('export.nomina', ['formato' => 'excel']),
        'csv'   => route('nomina.exportar'),
    ],
])

<div class="table-container">
    <div class="table-header">
        <h3><i class='bx bxs-group'></i> Personal</h3>
        <span class="badge badge-info">{{ $empleados->total() }} registros</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Cargo</th>
                    <th>Departamento</th>
                    <th>Salario</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($empleados as $i => $e)
                    <tr class="clickable-row" title="Clic para ver detalles" onclick="verEmpleado({{ $e->id }})" data-id="{{ $e->id }}">
                        <td>{{ $empleados->firstItem() + $i }}</td>
                        <td>
                            <div class="cell-user">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($e->nombre_completo) }}&background=764ba2&color=fff&size=32" alt="">
                                <span>{{ $e->nombre_completo }}</span>
                            </div>
                        </td>
                        <td><code>{{ $e->cedula }}</code></td>
                        <td>{{ $e->cargo }}</td>
                        <td>{{ $e->departamento }}</td>
                        <td>RD$ {{ number_format($e->salario, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $e->estado === 'activo' ? 'success' : 'secondary' }}">
                                {{ ucfirst($e->estado) }}
                            </span>
                        </td>
                        <td class="actions" onclick="event.stopPropagation()">
                            <button class="btn-icon btn-edit" onclick="editarEmpleado({{ $e->id }})" title="Editar"><i class='bx bx-edit'></i></button>
                            <button class="btn-icon btn-delete" onclick="confirmarEliminarEmpleado({{ $e->id }}, '{{ addslashes($e->nombre_completo) }}')" title="Eliminar"><i class='bx bx-trash'></i></button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty-row">
                        <div class="empty-state"><i class='bx bxs-group'></i><p>No hay empleados registrados</p></div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $empleados->links('partials.pagination') }}</div>
</div>

{{-- MODAL EMPLEADO --}}
<div class="modal" id="modal-empleado">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-empleado-title"><i class='bx bx-user-plus'></i> Nuevo Empleado</h2>
            <button class="modal-close" onclick="closeModal('modal-empleado')">&times;</button>
        </div>
        <form id="form-empleado" class="modal-form">
            @csrf
            <input type="hidden" name="id" id="empleado-id">
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" id="empleado-nombre" required>
                </div>
                <div class="form-group">
                    <label>Apellido *</label>
                    <input type="text" name="apellido" id="empleado-apellido" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Cédula *</label>
                    <input type="text" name="cedula" id="empleado-cedula" required>
                </div>
                <div class="form-group">
                    <label>Cargo *</label>
                    <input type="text" name="cargo" id="empleado-cargo" required placeholder="Ej: Médico General">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Departamento *</label>
                    <input type="text" name="departamento" id="empleado-departamento" required placeholder="Ej: Consultas">
                </div>
                <div class="form-group">
                    <label>Salario *</label>
                    <input type="number" name="salario" id="empleado-salario" min="0" step="0.01" required value="0">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Fecha Ingreso *</label>
                    <input type="date" name="fecha_ingreso" id="empleado-fecha_ingreso" required>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" id="empleado-estado">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" name="telefono" id="empleado-telefono">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="empleado-email">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-empleado')">Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-save'></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
{{-- MODAL VER EMPLEADO --}}
<div class="modal" id="modal-ver-empleado">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class='bx bxs-group'></i> Detalles del Empleado</h2>
            <button class="modal-close" onclick="closeModal('modal-ver-empleado')">&times;</button>
        </div>
        <div class="modal-body" id="ver-empleado-content" style="padding:20px 24px;"></div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('modal-ver-empleado')">Cerrar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const NOMINA_URL = '{{ url('nomina') }}';

let _empleadoSubmitting = false;
document.getElementById('form-empleado').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_empleadoSubmitting) return;
    _empleadoSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Guardando...'; }

    const id  = document.getElementById('empleado-id').value;
    const url = id ? `${NOMINA_URL}/${id}` : NOMINA_URL;
    const fd  = new FormData(this);
    if (id) fd.set('_method', 'PUT');

    try {
        const res  = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }, body: fd });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeModal('modal-empleado');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'Error.', 'error');
        }
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        _empleadoSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Guardar'; }
    }
});

async function verEmpleado(id) {
    const res  = await fetch(`${NOMINA_URL}/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
    const data = await res.json();
    if (data.success) {
        const e = data.data;
        document.getElementById('ver-empleado-content').innerHTML = `
            <div class="detail-grid">
                <div class="detail-item"><strong>Nombre:</strong> ${e.nombre} ${e.apellido}</div>
                <div class="detail-item"><strong>Cédula:</strong> ${e.cedula}</div>
                <div class="detail-item"><strong>Cargo:</strong> ${e.cargo}</div>
                <div class="detail-item"><strong>Departamento:</strong> ${e.departamento}</div>
                <div class="detail-item"><strong>Salario:</strong> RD$ ${parseFloat(e.salario).toLocaleString('es-DO', {minimumFractionDigits:2})}</div>
                <div class="detail-item"><strong>Fecha Ingreso:</strong> ${e.fecha_ingreso || '-'}</div>
                <div class="detail-item"><strong>Teléfono:</strong> ${e.telefono || '-'}</div>
                <div class="detail-item"><strong>Email:</strong> ${e.email || '-'}</div>
                <div class="detail-item"><strong>Estado:</strong> ${e.estado === 'activo' ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-secondary">Inactivo</span>'}</div>
            </div>`;
        openModal('modal-ver-empleado');
    }
}

async function editarEmpleado(id) {
    const res  = await fetch(`${NOMINA_URL}/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
    const data = await res.json();
    if (data.success) {
        const e = data.data;
        document.getElementById('empleado-id').value           = e.id;
        document.getElementById('empleado-nombre').value       = e.nombre;
        document.getElementById('empleado-apellido').value     = e.apellido;
        document.getElementById('empleado-cedula').value       = e.cedula;
        document.getElementById('empleado-cargo').value        = e.cargo;
        document.getElementById('empleado-departamento').value = e.departamento;
        document.getElementById('empleado-salario').value      = e.salario;
        document.getElementById('empleado-fecha_ingreso').value= e.fecha_ingreso;
        document.getElementById('empleado-estado').value       = e.estado;
        document.getElementById('empleado-telefono').value     = e.telefono || '';
        document.getElementById('empleado-email').value        = e.email || '';
        document.getElementById('modal-empleado-title').innerHTML = "<i class='bx bx-edit'></i> Editar Empleado";
        openModal('modal-empleado');
    }
}

function confirmarEliminarEmpleado(id, nombre) {
    document.getElementById('delete-item-name').textContent = nombre;
    document.getElementById('btn-confirm-delete').onclick = async () => {
        const res  = await fetch(`${NOMINA_URL}/${id}`, {
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
</script>
@endpush
