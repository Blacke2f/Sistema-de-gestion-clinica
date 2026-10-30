@extends('layouts.app')

@section('title', 'Medicamentos')
@section('nav-title', 'Medicamentos')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Gestión de Medicamentos</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Medicamentos</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <button class="btn-export" onclick="openModal('modal-export-medicina')">
            <i class='bx bx-export'></i> Exportar
        </button>
        <button class="btn-primary" onclick="openModal('modal-medicina')">
            <i class='bx bx-plus'></i> Nuevo Medicamento
        </button>
    </div>
</div>

@include('partials.export-modal', [
    'exportModule' => 'medicina',
    'exportTitle'  => 'Exportar Medicamentos',
    'exportColor'  => '#198754',
    'exportIcon'   => 'bxs-capsule',
    'exportRoutes' => [
        'pdf'   => route('export.medicamentos', ['formato' => 'pdf']),
        'excel' => route('export.medicamentos', ['formato' => 'excel']),
        'csv'   => route('export.medicamentos', ['formato' => 'csv']),
    ],
])

<div class="table-container">
    <div class="table-header">
        <h3><i class='bx bxs-capsule'></i> Inventario de Medicamentos</h3>
        @php
            $allMeds = \App\Models\Medicamento::all();
            $kpiTotalMeds = $allMeds->count();
            $kpiTotalUds  = $allMeds->sum('cantidad');
            $kpiStockBajo = $allMeds->filter(fn($item) => $item->tieneStockBajo())->count();
            $kpiPorVencer = $allMeds->filter(fn($item) => $item->fecha_vencimiento && $item->fecha_vencimiento->lte(now()->addDays(30)))->count();
        @endphp
        <div style="width:100%;display:grid;grid-template-columns:repeat(auto-fit,minmax(185px,1fr));gap:12px;margin:10px 0 4px;">
            <div style="background:linear-gradient(135deg,#0d6efd,#3b82f6);color:#fff;padding:12px 16px;border-radius:10px;display:flex;align-items:center;gap:12px;">
                <i class='bx bxs-capsule' style="font-size:28px;opacity:.85;"></i>
                <div><div style="font-size:20px;font-weight:800;line-height:1;">{{ $kpiTotalMeds }}</div><small style="opacity:.9;font-size:11px;">En Catálogo</small></div>
            </div>
            <div style="background:linear-gradient(135deg,#198754,#10b981);color:#fff;padding:12px 16px;border-radius:10px;display:flex;align-items:center;gap:12px;">
                <i class='bx bx-package' style="font-size:28px;opacity:.85;"></i>
                <div><div style="font-size:20px;font-weight:800;line-height:1;">{{ number_format($kpiTotalUds) }}</div><small style="opacity:.9;font-size:11px;">Unidades en Stock</small></div>
            </div>
            <div style="background:linear-gradient(135deg,#dc3545,#f43f5e);color:#fff;padding:12px 16px;border-radius:10px;display:flex;align-items:center;gap:12px;">
                <i class='bx bx-error-circle' style="font-size:28px;opacity:.85;"></i>
                <div><div style="font-size:20px;font-weight:800;line-height:1;">{{ $kpiStockBajo }}</div><small style="opacity:.9;font-size:11px;">Con Stock Bajo</small></div>
            </div>
            <div style="background:linear-gradient(135deg,#d97706,#f59e0b);color:#fff;padding:12px 16px;border-radius:10px;display:flex;align-items:center;gap:12px;">
                <i class='bx bx-calendar-exclamation' style="font-size:28px;opacity:.85;"></i>
                <div><div style="font-size:20px;font-weight:800;line-height:1;">{{ $kpiPorVencer }}</div><small style="opacity:.9;font-size:11px;">Vencidos / &le;30 días</small></div>
            </div>
        </div>
        <span class="badge badge-info">{{ $medicamentos->total() }} registros</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicamento</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Vencimiento</th>
                    <th>Proveedor</th>
                    <th>Stock Mín.</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicamentos as $i => $m)
                    <tr class="clickable-row" title="Clic para ver detalles" onclick="verMedicamento({{ $m->id }})" data-id="{{ $m->id }}">
                        <td>{{ $medicamentos->firstItem() + $i }}</td>
                        <td><strong>{{ $m->nombre_medicamento }}</strong><br><small class="text-muted">{{ Str::limit($m->descripcion, 30) }}</small></td>
                        <td><span class="{{ $m->tieneStockBajo() ? 'text-danger fw-bold' : '' }}">{{ $m->cantidad }}</span></td>
                        <td>{{ $m->unidad }}</td>
                        <td>
                            @if($m->fecha_vencimiento)
                                @php
                                    $diasVenc = (int) now()->startOfDay()->diffInDays($m->fecha_vencimiento->startOfDay(), false);
                                @endphp
                                <div>{{ $m->fecha_vencimiento->format('d/m/Y') }}</div>
                                @if($diasVenc < 0)
                                    <span style="display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:700;padding:2px 7px;border-radius:12px;background:rgba(220,53,69,.15);color:#dc3545;">
                                        <i class='bx bxs-circle' style="font-size:7px;"></i> Vencido
                                    </span>
                                @elseif($diasVenc <= 30)
                                    <span style="display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:700;padding:2px 7px;border-radius:12px;background:rgba(245,158,11,.18);color:#d97706;">
                                        <i class='bx bxs-circle' style="font-size:7px;"></i> Vence en {{ $diasVenc }} d.
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:600;padding:2px 7px;border-radius:12px;background:rgba(25,135,84,.12);color:#198754;">
                                        <i class='bx bxs-circle' style="font-size:7px;"></i> Vigente
                                    </span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $m->proveedor ?? '-' }}</td>
                        <td>{{ $m->stock_minimo }}</td>
                        <td>
                            @if($m->tieneStockBajo())
                                <span class="badge badge-danger">Stock Bajo</span>
                            @else
                                <span class="badge badge-success">Normal</span>
                            @endif
                        </td>
                        <td class="actions" onclick="event.stopPropagation()">
                            <button class="btn-icon btn-edit" onclick="editarMedicamento({{ $m->id }})" title="Editar"><i class='bx bx-edit'></i></button>
                            <button class="btn-icon btn-delete" onclick="confirmarEliminarMedicamento({{ $m->id }}, '{{ addslashes($m->nombre_medicamento) }}')" title="Eliminar"><i class='bx bx-trash'></i></button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="empty-row">
                        <div class="empty-state"><i class='bx bxs-capsule'></i><p>No hay medicamentos registrados</p></div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $medicamentos->links('partials.pagination') }}</div>
</div>

{{-- MODAL MEDICAMENTO --}}
<div class="modal" id="modal-medicina">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-medicina-title"><i class='bx bx-plus-circle'></i> Nuevo Medicamento</h2>
            <button class="modal-close" onclick="closeModal('modal-medicina')">&times;</button>
        </div>
        <form id="form-medicina" class="modal-form">
            @csrf
            <input type="hidden" name="id" id="medicina-id">

            <div class="form-row">
                <div class="form-group">
                    <label>Nombre del Medicamento *</label>
                    <input type="text" name="nombre_medicamento" id="medicina-nombre" required placeholder="Ej: Paracetamol 500mg">
                </div>
                <div class="form-group">
                    <label>Proveedor</label>
                    <input type="text" name="proveedor" id="medicina-proveedor" placeholder="Nombre del proveedor">
                </div>
            </div>
            <div class="form-group">
                <label>Teléfono del Proveedor</label>
                <input type="tel" name="telefono_proveedor" id="medicina-telefono_proveedor" placeholder="809-000-0000">
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" id="medicina-descripcion" rows="2" placeholder="Descripción del medicamento"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Cantidad *</label>
                    <input type="number" name="cantidad" id="medicina-cantidad" min="0" required value="0">
                </div>
                <div class="form-group">
                    <label>Unidad *</label>
                    <select name="unidad" id="medicina-unidad">
                        <option value="tabletas">Tabletas</option>
                        <option value="cápsulas">Cápsulas</option>
                        <option value="inhaladores">Inhaladores</option>
                        <option value="ampollas">Ampollas</option>
                        <option value="frascos">Frascos</option>
                        <option value="unidades">Unidades</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Stock Mínimo *</label>
                    <input type="number" name="stock_minimo" id="medicina-stock_minimo" min="0" required value="10">
                </div>
                <div class="form-group">
                    <label>Fecha de Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" id="medicina-fecha_vencimiento">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-medicina')">Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-save'></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
{{-- MODAL VER MEDICAMENTO --}}
<div class="modal" id="modal-ver-medicina">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class='bx bxs-capsule'></i> Detalles del Medicamento</h2>
            <button class="modal-close" onclick="closeModal('modal-ver-medicina')">&times;</button>
        </div>
        <div class="modal-body" id="ver-medicina-content" style="padding:20px 24px;"></div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('modal-ver-medicina')">Cerrar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const MEDICINA_URL = '{{ url('medicina') }}';

let _medicinaSubmitting = false;
document.getElementById('form-medicina').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_medicinaSubmitting) return;
    _medicinaSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Guardando...'; }

    const id  = document.getElementById('medicina-id').value;
    const url = id ? `${MEDICINA_URL}/${id}` : MEDICINA_URL;
    const fd  = new FormData(this);
    if (id) fd.set('_method', 'PUT');

    try {
        const res  = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }, body: fd });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeModal('modal-medicina');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'Error.', 'error');
        }
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        _medicinaSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Guardar'; }
    }
});

async function verMedicamento(id) {
    const res  = await fetch(`${MEDICINA_URL}/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
    const data = await res.json();
    if (data.success) {
        const m = data.data;
        document.getElementById('ver-medicina-content').innerHTML = `
            <div class="detail-grid">
                <div class="detail-item"><strong>Medicamento:</strong> ${m.nombre_medicamento}</div>
                <div class="detail-item"><strong>Cantidad:</strong> ${m.cantidad} ${m.unidad}</div>
                <div class="detail-item"><strong>Stock Mínimo:</strong> ${m.stock_minimo}</div>
                <div class="detail-item"><strong>Vencimiento:</strong> ${m.fecha_vencimiento || '-'}</div>
                <div class="detail-item"><strong>Proveedor:</strong> ${m.proveedor || '-'}</div>
                ${m.descripcion ? `<div class="detail-item" style="grid-column:1/-1"><strong>Descripción:</strong> ${m.descripcion}</div>` : ''}
            </div>`;
        openModal('modal-ver-medicina');
    }
}

async function editarMedicamento(id) {
    const res  = await fetch(`${MEDICINA_URL}/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
    const data = await res.json();
    if (data.success) {
        const m = data.data;
        document.getElementById('medicina-id').value              = m.id;
        document.getElementById('medicina-nombre').value          = m.nombre_medicamento;
        document.getElementById('medicina-proveedor').value       = m.proveedor || '';
        document.getElementById('medicina-descripcion').value     = m.descripcion || '';
        document.getElementById('medicina-cantidad').value        = m.cantidad;
        document.getElementById('medicina-unidad').value          = m.unidad;
        document.getElementById('medicina-stock_minimo').value    = m.stock_minimo;
        document.getElementById('medicina-fecha_vencimiento').value = m.fecha_vencimiento || '';
        document.getElementById('modal-medicina-title').innerHTML = "<i class='bx bx-edit'></i> Editar Medicamento";
        openModal('modal-medicina');
    }
}

function confirmarEliminarMedicamento(id, nombre) {
    document.getElementById('delete-item-name').textContent = nombre;
    document.getElementById('btn-confirm-delete').onclick = async () => {
        const res  = await fetch(`${MEDICINA_URL}/${id}`, {
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
