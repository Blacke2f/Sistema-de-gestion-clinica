@extends('layouts.app')

@section('title', 'Movimientos de Inventario')
@section('nav-title', 'Movimientos')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Movimientos de Inventario</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a href="{{ route('medicina.index') }}">Medicamentos</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Movimientos</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <a href="{{ route('inventario.auditoria') }}?fecha_inicio={{ now()->subDays(30)->format('Y-m-d') }}&fecha_fin={{ now()->format('Y-m-d') }}"
           class="btn-export" target="_blank" title="Reporte de auditoría — verifica que no se perdió ningún medicamento">
            <i class='bx bx-shield-check'></i> Auditoría
        </a>
        <button class="btn-export" onclick="openModal('modal-exportar-opciones')">
            <i class='bx bx-export'></i> Exportar
        </button>
        <button class="btn-success" onclick="openModal('modal-entrada')">
            <i class='bx bx-plus-circle'></i> Registrar Entrada
        </button>
        <button class="btn-danger" onclick="openModal('modal-salida')">
            <i class='bx bx-minus-circle'></i> Registrar Salida
        </button>
    </div>
</div>

{{-- ── Tarjetas de estadísticas ── --}}
@php
    $totalEntradas = $estadisticas->where('tipo_movimiento', 'entrada')->sum('total_cantidad');
    $totalSalidas  = $estadisticas->where('tipo_movimiento', 'salida')->sum('total_cantidad');
    $periodoLabel  = $periodo === 'semanal' ? 'Semana' : 'Mes';
@endphp

<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin-bottom:24px;">

    <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); color:#fff; padding:24px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.15);">
        <div style="display:flex; align-items:center; gap:16px;">
            <i class='bx bx-trending-up' style="font-size:48px; opacity:.85;"></i>
            <div>
                <h3 style="margin:0; font-size:32px; font-weight:700;">{{ $totalEntradas }}</h3>
                <p style="margin:4px 0 0; opacity:.9;">Total Entradas ({{ $periodoLabel }})</p>
            </div>
        </div>
    </div>

    <div style="background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%); color:#fff; padding:24px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.15);">
        <div style="display:flex; align-items:center; gap:16px;">
            <i class='bx bx-trending-down' style="font-size:48px; opacity:.85;"></i>
            <div>
                <h3 style="margin:0; font-size:32px; font-weight:700;">{{ $totalSalidas }}</h3>
                <p style="margin:4px 0 0; opacity:.9;">Total Salidas ({{ $periodoLabel }})</p>
            </div>
        </div>
    </div>

    <div style="background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%); color:#fff; padding:24px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.15);">
        <div style="display:flex; align-items:center; gap:16px;">
            <i class='bx bx-transfer' style="font-size:48px; opacity:.85;"></i>
            <div>
                <h3 style="margin:0; font-size:32px; font-weight:700;">{{ $movimientos->total() }}</h3>
                <p style="margin:4px 0 0; opacity:.9;">Movimientos Recientes</p>
            </div>
        </div>
    </div>

</div>

{{-- ── Alerta de stock bajo ── --}}
@php $stockBajo = \App\Models\Medicamento::stockBajo()->get(); @endphp
@if($stockBajo->count() > 0)
<div style="background:var(--danger-light);border:1px solid var(--danger);border-radius:10px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:flex-start;gap:14px;">
    <i class='bx bx-error' style="font-size:24px;color:var(--danger);flex-shrink:0;margin-top:2px;"></i>
    <div style="flex:1;">
        <strong style="color:var(--danger);font-size:14px;">{{ $stockBajo->count() }} medicamento(s) con stock bajo</strong>
        <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:8px;">
            @foreach($stockBajo as $med)
            <span style="background:var(--danger);color:#fff;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                {{ $med->nombre_medicamento }} ({{ $med->cantidad }}/{{ $med->stock_minimo }})
            </span>
            @endforeach
        </div>
        <button onclick="openModal('modal-solicitud')" style="margin-top:10px;background:var(--danger);color:#fff;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;">
            <i class='bx bx-send'></i> Crear Solicitud de Reposición
        </button>
    </div>
</div>
@endif

{{-- ── Filtros ── --}}
<div class="filters-section" style="background:var(--bg-card); border-radius:10px; padding:16px 20px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
    <form method="GET" action="{{ route('inventario.index') }}" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
        <div class="flt-group">
            <label class="flt-label"><i class='bx bx-calendar'></i> Período</label>
            <select name="periodo" class="flt-select" onchange="this.form.submit()">
                <option value="semanal" {{ $periodo === 'semanal' ? 'selected' : '' }}>Última Semana</option>
                <option value="mensual" {{ $periodo === 'mensual' ? 'selected' : '' }}>Último Mes</option>
            </select>
        </div>
        <div class="flt-group">
            <label class="flt-label"><i class='bx bx-transfer'></i> Tipo</label>
            <select name="tipo" class="flt-select" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="entrada" {{ $tipo === 'entrada' ? 'selected' : '' }}>Entradas</option>
                <option value="salida"  {{ $tipo === 'salida'  ? 'selected' : '' }}>Salidas</option>
            </select>
        </div>
        <div class="flt-group">
            <label class="flt-label" style="visibility:hidden;">‎</label>
            <a href="{{ route('inventario.index') }}" class="flt-btn-reset">
                <i class='bx bx-refresh'></i> Resetear
            </a>
        </div>
    </form>
</div>

{{-- ── Tabla ── --}}
<div class="table-container">
    <div class="table-header">
        <h3><i class='bx bx-history'></i> Historial de Movimientos</h3>
        <span class="badge badge-info"><i class='bx bx-transfer'></i> {{ $movimientos->total() }} movimientos</span>
        @if($search)
            <span class="search-active-badge">
                <i class='bx bx-search'></i> "{{ $search }}"
                <a href="{{ route('inventario.index') }}">✕</a>
            </span>
        @endif
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha/Hora</th>
                    <th>Medicamento</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Stock Ant.</th>
                    <th>Stock Nuevo</th>
                    <th>Paciente / Motivo</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimientos as $i => $m)
                    <tr>
                        <td><span class="cell-num">{{ $movimientos->firstItem() + $i }}</span></td>
                        <td>
                            <div class="cell-date">
                                <i class='bx bx-calendar'></i> {{ $m->created_at->format('d/m/Y') }}<br>
                                <small><i class='bx bx-time-five'></i> {{ $m->created_at->format('H:i') }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="cell-medicine">
                                <i class='bx bx-capsule'></i>
                                <span>{{ $m->medicamento->nombre_medicamento }}</span>
                            </div>
                        </td>
                        <td>
                            @if($m->tipo_movimiento === 'entrada')
                                <span class="badge badge-success"><i class='bx bx-up-arrow-alt'></i> Entrada</span>
                            @else
                                <span class="badge badge-danger"><i class='bx bx-down-arrow-alt'></i> Salida</span>
                            @endif
                        </td>
                        <td>
                            <strong class="{{ $m->tipo_movimiento === 'entrada' ? 'text-success' : 'text-danger' }}">
                                {{ $m->tipo_movimiento === 'entrada' ? '+' : '-' }}{{ $m->cantidad }}
                            </strong>
                        </td>
                        <td>{{ $m->cantidad_anterior }}</td>
                        <td><strong>{{ $m->cantidad_nueva }}</strong></td>
                        <td>
                            @if($m->tipo_movimiento === 'salida' && $m->paciente)
                                <div class="cell-user">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($m->paciente->nombre_completo) }}&background=dc3545&color=fff&size=28" alt="">
                                    <span style="font-size:12px;">{{ $m->paciente->nombre_completo }}</span>
                                </div>
                            @else
                                <span style="font-size:12px;color:var(--text-muted);">{{ $m->motivo ?? '-' }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="cell-user">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($m->usuario->nombre_completo) }}&background=0d6efd&color=fff&size=28" alt="">
                                <span style="font-size:12px;">{{ $m->usuario->nombre_completo }}</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="empty-row">
                        <div class="empty-state"><i class='bx bx-package'></i><p>No hay movimientos registrados</p></div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $movimientos->links('partials.pagination') }}</div>
</div>

{{-- ── Modal Entrada ── --}}
<div class="modal" id="modal-entrada">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class='bx bx-plus-circle'></i> Registrar Entrada de Inventario</h2>
            <button class="modal-close" onclick="closeModal('modal-entrada')">&times;</button>
        </div>
        <form id="form-entrada" class="modal-form">
            @csrf
            <div class="form-group">
                <label><i class='bx bx-capsule'></i> Medicamento *</label>
                <select name="medicamento_id" required>
                    <option value="">Seleccionar medicamento</option>
                    @foreach(\App\Models\Medicamento::orderBy('nombre_medicamento')->get() as $med)
                        <option value="{{ $med->id }}">{{ $med->nombre_medicamento }} (Stock: {{ $med->cantidad }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label><i class='bx bx-package'></i> Cantidad *</label>
                <input type="number" name="cantidad" min="1" value="1" required>
            </div>
            <div class="form-group">
                <label><i class='bx bx-detail'></i> Motivo</label>
                <textarea name="motivo" rows="3" placeholder="Compra, donación, etc.">Entrada de inventario</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-entrada')">Cancelar</button>
                <button type="submit" class="btn-success"><i class='bx bx-save'></i> Registrar Entrada</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal Salida ── --}}
<div class="modal" id="modal-salida">
    <div class="modal-content">
        <div class="modal-header" style="background:linear-gradient(135deg,#dc3545,#f43f5e);color:#fff;">
            <h2><i class='bx bx-minus-circle'></i> Registrar Salida de Inventario</h2>
            <button class="modal-close" onclick="closeModal('modal-salida')" style="color:#fff;">&times;</button>
        </div>
        <form id="form-salida" class="modal-form">
            @csrf
            <div class="form-group">
                <label><i class='bx bx-capsule'></i> Medicamento *</label>
                <select name="medicamento_id" required>
                    <option value="">Seleccionar medicamento</option>
                    @foreach(\App\Models\Medicamento::orderBy('nombre_medicamento')->get() as $med)
                        <option value="{{ $med->id }}">{{ $med->nombre_medicamento }} (Stock: {{ $med->cantidad }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label><i class='bx bx-package'></i> Cantidad *</label>
                <input type="number" name="cantidad" min="1" value="1" required>
            </div>
            <div class="form-group">
                <label><i class='bx bxs-user-detail'></i> Paciente (opcional)</label>
                <select name="paciente_id">
                    <option value="">Sin paciente asignado</option>
                    @foreach(\App\Models\Paciente::orderBy('nombre')->get() as $pac)
                        <option value="{{ $pac->id }}">{{ $pac->nombre_completo }} — {{ $pac->cedula }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label><i class='bx bx-detail'></i> Motivo</label>
                <textarea name="motivo" rows="2" placeholder="Dispensación, vencimiento, etc.">Salida de inventario</textarea>
            </div>

            {{-- ── Sección de firma de entrega ── --}}
            <div style="background:rgba(13,110,253,.06);border:1px solid rgba(13,110,253,.2);border-radius:10px;padding:14px 16px;margin-top:4px;">
                <div style="font-size:12px;font-weight:700;color:var(--text-primary);margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                    <i class='bx bx-pen' style="color:var(--primary);font-size:16px;"></i>
                    Comprobante de Entrega
                    <span style="font-size:10px;font-weight:400;color:var(--text-muted);">(para auditoría)</span>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class='bx bx-user-check'></i> Entregado por</label>
                        <input type="text" name="entregado_por_nombre"
                            value="{{ auth()->user()->nombre_completo }}"
                            placeholder="Nombre de quien entrega">
                    </div>
                    <div class="form-group">
                        <label><i class='bx bx-user'></i> Recibido por *</label>
                        <input type="text" name="recibido_por_nombre"
                            placeholder="Nombre completo de quien recibe" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class='bx bx-id-card'></i> Cédula del receptor</label>
                        <input type="text" name="recibido_por_cedula"
                            placeholder="000-0000000-0">
                    </div>
                    <div class="form-group">
                        <label><i class='bx bx-note'></i> Observaciones</label>
                        <input type="text" name="observaciones_entrega"
                            placeholder="Observaciones adicionales">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-salida')">Cancelar</button>
                <button type="submit" class="btn-danger"><i class='bx bx-save'></i> Registrar Salida</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal Exportar Opciones ── --}}
<div class="modal" id="modal-exportar-opciones">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class='bx bx-export'></i> Exportar Movimientos</h2>
            <button class="modal-close" onclick="closeModal('modal-exportar-opciones')">&times;</button>
        </div>
        <div class="modal-form" style="padding:20px 24px;">
            {{-- Período --}}
            <div class="form-group">
                <label><i class='bx bx-calendar'></i> Período</label>
                <select id="exp-periodo" onchange="toggleFechasExport()">
                    <option value="semanal">Última Semana</option>
                    <option value="mensual">Último Mes</option>
                    <option value="rango">Rango de Fechas</option>
                    <option value="todos">Todos los registros</option>
                </select>
            </div>
            <div id="exp-fechas" style="display:none;">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class='bx bx-calendar'></i> Fecha Inicio</label>
                        <input type="date" id="exp-fecha-inicio">
                    </div>
                    <div class="form-group">
                        <label><i class='bx bx-calendar-check'></i> Fecha Fin</label>
                        <input type="date" id="exp-fecha-fin">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label><i class='bx bx-filter'></i> Tipo de Movimiento</label>
                <select id="exp-tipo">
                    <option value="">Todos</option>
                    <option value="entrada">Solo Entradas</option>
                    <option value="salida">Solo Salidas</option>
                </select>
            </div>
            {{-- Botones de formato --}}
            <p style="font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:12px;">Selecciona el formato:</p>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <button onclick="exportarMovimientos('pdf')" class="btn-export" style="flex-direction:column;padding:16px;text-align:center;gap:6px;height:auto;">
                    <i class='bx bxs-file-pdf' style="font-size:28px;color:#dc3545;"></i>
                    <span style="font-size:13px;font-weight:600;">PDF</span>
                    <small style="font-size:11px;color:var(--text-muted);">Con estadísticas</small>
                </button>
                <button onclick="exportarMovimientos('excel')" class="btn-export" style="flex-direction:column;padding:16px;text-align:center;gap:6px;height:auto;">
                    <i class='bx bxs-file' style="font-size:28px;color:#198754;"></i>
                    <span style="font-size:13px;font-weight:600;">Excel</span>
                    <small style="font-size:11px;color:var(--text-muted);">.xlsx</small>
                </button>
                <button onclick="exportarMovimientos('csv')" class="btn-export" style="flex-direction:column;padding:16px;text-align:center;gap:6px;height:auto;">
                    <i class='bx bx-spreadsheet' style="font-size:28px;color:#0d6efd;"></i>
                    <span style="font-size:13px;font-weight:600;">CSV</span>
                    <small style="font-size:11px;color:var(--text-muted);">Texto plano</small>
                </button>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('modal-exportar-opciones')">Cerrar</button>
        </div>
    </div>
</div>

{{-- ── Modal Solicitud de Reposición ── --}}
<div class="modal" id="modal-solicitud">
    <div class="modal-content modal-lg">
        <div class="modal-header" style="background:var(--warning);color:#212529;">
            <h2><i class='bx bx-send'></i> Solicitud de Reposición de Inventario</h2>
            <button class="modal-close" onclick="closeModal('modal-solicitud')">&times;</button>
        </div>
        <form id="form-solicitud" class="modal-form">
            @csrf
            <div class="solicitud-alert-info" style="margin-bottom:16px;display:flex;gap:10px;padding:12px 16px;background:rgba(13,202,240,.1);border-radius:8px;border-left:4px solid var(--info);font-size:13px;color:var(--text-primary);">
                <i class='bx bx-info-circle' style="font-size:18px;flex-shrink:0;color:var(--info);"></i>
                <span>Puedes agregar varios medicamentos en una sola solicitud. Haz clic en <strong>+ Agregar Medicamento</strong> para añadir más.</span>
            </div>

            {{-- Lista dinámica de items --}}
            <div id="solicitud-items">
                <div class="solicitud-item" style="border:1px solid var(--border-color);border-radius:10px;padding:16px;margin-bottom:12px;position:relative;">
                    <div style="position:absolute;top:10px;right:10px;display:none;" class="btn-remove-item">
                        <button type="button" onclick="removeSolicitudItem(this)" style="background:var(--danger-light);color:var(--danger);border:none;border-radius:6px;width:28px;height:28px;cursor:pointer;font-size:16px;">×</button>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex:2;">
                            <label><i class='bx bx-capsule'></i> Medicamento *</label>
                            <select name="items[0][medicamento_id]" class="sol-med-select" required onchange="autocompletarProveedor(this)">
                                <option value="">Seleccionar medicamento</option>
                                @foreach(\App\Models\Medicamento::orderBy('nombre_medicamento')->get() as $med)
                                    <option value="{{ $med->id }}"
                                        data-stock="{{ $med->cantidad }}"
                                        data-min="{{ $med->stock_minimo }}"
                                        data-proveedor="{{ $med->proveedor }}"
                                        data-tel="{{ $med->telefono_proveedor }}"
                                        {{ $med->tieneStockBajo() ? 'style=font-weight:bold;color:var(--danger)' : '' }}>
                                        {{ $med->nombre_medicamento }} — Stock: {{ $med->cantidad }}{{ $med->tieneStockBajo() ? ' ⚠️' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label><i class='bx bx-package'></i> Cantidad *</label>
                            <input type="number" name="items[0][cantidad]" class="sol-cantidad" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class='bx bx-building'></i> Proveedor</label>
                            <input type="text" name="items[0][proveedor]" class="sol-proveedor" placeholder="Nombre del proveedor">
                        </div>
                        <div class="form-group">
                            <label><i class='bx bx-phone'></i> Teléfono Proveedor</label>
                            <input type="tel" name="items[0][telefono]" class="sol-telefono" placeholder="809-000-0000">
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" onclick="agregarSolicitudItem()" style="background:none;border:2px dashed var(--border-color);width:100%;padding:12px;border-radius:10px;cursor:pointer;color:var(--text-secondary);font-size:13px;margin-bottom:16px;transition:all .2s;" onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'">
                <i class='bx bx-plus-circle'></i> Agregar otro medicamento
            </button>

            <div class="form-group">
                <label><i class='bx bx-note'></i> Notas generales de la solicitud</label>
                <textarea name="notas" rows="2" placeholder="Urgencia, observaciones generales..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-solicitud')">Cancelar</button>
                <button type="submit" class="btn-warning"><i class='bx bx-send'></i> Enviar Solicitud</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const BASE = '{{ url('/') }}';

// ── Exportar con opciones ──
function toggleFechasExport() {
    const p = document.getElementById('exp-periodo').value;
    document.getElementById('exp-fechas').style.display = p === 'rango' ? 'block' : 'none';
}

function exportarMovimientos(formato) {
    const periodo = document.getElementById('exp-periodo').value;
    const tipo    = document.getElementById('exp-tipo').value;
    let url;

    if (formato === 'csv') {
        url = `${BASE}/inventario/exportar/csv?periodo=${periodo}&tipo=${tipo}`;
        if (periodo === 'rango') {
            url += `&fecha_inicio=${document.getElementById('exp-fecha-inicio').value}`;
            url += `&fecha_fin=${document.getElementById('exp-fecha-fin').value}`;
        }
    } else {
        url = `${BASE}/export/movimientos?formato=${formato}&periodo=${periodo}&tipo=${tipo}`;
        if (periodo === 'rango') {
            url += `&fecha_inicio=${document.getElementById('exp-fecha-inicio').value}`;
            url += `&fecha_fin=${document.getElementById('exp-fecha-fin').value}`;
        }
    }
    window.location.href = url;
    closeModal('modal-exportar-opciones');
}

// Fechas por defecto
document.addEventListener('DOMContentLoaded', function () {
    const hoy    = new Date().toISOString().split('T')[0];
    const hace30 = new Date(Date.now() - 30*24*60*60*1000).toISOString().split('T')[0];
    const fi = document.getElementById('exp-fecha-inicio');
    const ff = document.getElementById('exp-fecha-fin');
    if (fi) { fi.value = hace30; fi.max = hoy; }
    if (ff) { ff.value = hoy;    ff.max = hoy; }
});

// ── Solicitud multi-medicamento ──
let itemCount = 1;
let medicamentosData = [];

// Cargar medicamentos via AJAX para evitar problemas de encoding
fetch('{{ url('medicina') }}?all=1', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } })
    .then(r => r.json())
    .catch(() => ({ data: [] }))
    .then(resp => {
        // Si falla el AJAX, usar los datos del primer select como fallback
        const opts = document.querySelectorAll('#solicitud-items .sol-med-select option');
        opts.forEach(o => {
            if (o.value) medicamentosData.push({
                id: o.value,
                nombre_medicamento: o.textContent.trim(),
                cantidad: o.dataset.stock || 0,
                stock_minimo: o.dataset.min || 10,
                proveedor: o.dataset.proveedor || '',
                telefono_proveedor: o.dataset.tel || ''
            });
        });
    });

function autocompletarProveedor(select) {
    const opt = select.options[select.selectedIndex];
    const item = select.closest('.solicitud-item');
    if (!item) return;
    item.querySelector('.sol-proveedor').value = opt.dataset.proveedor || '';
    item.querySelector('.sol-telefono').value  = opt.dataset.tel || '';
    const stock = parseInt(opt.dataset.stock || 0);
    const min   = parseInt(opt.dataset.min || 10);
    if (stock < min) {
        item.querySelector('.sol-cantidad').value = Math.max(min - stock + min, 1);
    }
}

function agregarSolicitudItem() {
    const idx = itemCount++;
    // Clonar las opciones del primer select
    const primerSelect = document.querySelector('#solicitud-items .sol-med-select');
    const opciones = primerSelect ? primerSelect.innerHTML : '<option value="">Seleccionar medicamento</option>';

    const div = document.createElement('div');
    div.className = 'solicitud-item';
    div.style.cssText = 'border:1px solid var(--border-color);border-radius:10px;padding:16px;margin-bottom:12px;position:relative;';
    div.innerHTML = `
        <div style="position:absolute;top:10px;right:10px;" class="btn-remove-item">
            <button type="button" onclick="removeSolicitudItem(this)" style="background:var(--danger-light);color:var(--danger);border:none;border-radius:6px;width:28px;height:28px;cursor:pointer;font-size:16px;">×</button>
        </div>
        <div class="form-row">
            <div class="form-group" style="flex:2;">
                <label><i class='bx bx-capsule'></i> Medicamento *</label>
                <select name="items[${idx}][medicamento_id]" class="sol-med-select" required onchange="autocompletarProveedor(this)">
                    ${opciones}
                </select>
            </div>
            <div class="form-group" style="flex:1;">
                <label><i class='bx bx-package'></i> Cantidad *</label>
                <input type="number" name="items[${idx}][cantidad]" class="sol-cantidad" min="1" value="1" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label><i class='bx bx-building'></i> Proveedor</label>
                <input type="text" name="items[${idx}][proveedor]" class="sol-proveedor" placeholder="Nombre del proveedor">
            </div>
            <div class="form-group">
                <label><i class='bx bx-phone'></i> Teléfono Proveedor</label>
                <input type="tel" name="items[${idx}][telefono]" class="sol-telefono" placeholder="809-000-0000">
            </div>
        </div>`;
    document.getElementById('solicitud-items').appendChild(div);
    // Resetear el nuevo select
    div.querySelector('.sol-med-select').value = '';
    // Mostrar botones de eliminar en todos los items
    document.querySelectorAll('.btn-remove-item').forEach(b => b.style.display = 'block');
}

function removeSolicitudItem(btn) {
    const items = document.querySelectorAll('.solicitud-item');
    if (items.length <= 1) return;
    btn.closest('.solicitud-item').remove();
    if (document.querySelectorAll('.solicitud-item').length === 1) {
        document.querySelector('.btn-remove-item').style.display = 'none';
    }
}

document.getElementById('form-solicitud')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('[type=submit]');
    if (btn && btn.disabled) return;
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Enviando...'; }
    try {
        const res  = await fetch('{{ route('solicitudes.store') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
            body: new FormData(this)
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeModal('modal-solicitud');
            this.reset();
            const items = document.querySelectorAll('.solicitud-item');
            items.forEach((item, i) => { if (i > 0) item.remove(); });
            itemCount = 1;
        } else {
            showToast(data.message || 'Error al enviar solicitud.', 'error');
        }
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-send"></i> Enviar Solicitud'; }
    }
});

// ── Entrada ──
let _entradaSubmitting = false;
document.getElementById('form-entrada').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_entradaSubmitting) return;
    _entradaSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Registrando...'; }
    try {
        const res  = await fetch('{{ route('inventario.entrada') }}', { method:'POST', headers:{'X-CSRF-TOKEN':window.CSRF_TOKEN,'Accept':'application/json'}, body:new FormData(this) });
        const data = await res.json();
        if (data.success) { showToast(data.message,'success'); closeModal('modal-entrada'); setTimeout(()=>location.reload(),800); }
        else showToast(data.message || 'Error al registrar entrada.','error');
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        _entradaSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Registrar Entrada'; }
    }
});

// ── Salida ──
let _salidaSubmitting = false;
document.getElementById('form-salida').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_salidaSubmitting) return;
    _salidaSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Registrando...'; }
    try {
        const res  = await fetch('{{ route('inventario.salida') }}', { method:'POST', headers:{'X-CSRF-TOKEN':window.CSRF_TOKEN,'Accept':'application/json'}, body:new FormData(this) });
        const data = await res.json();
        if (data.success) { showToast(data.message,'success'); closeModal('modal-salida'); setTimeout(()=>location.reload(),800); }
        else showToast(data.message || 'Error al registrar salida.','error');
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        _salidaSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Registrar Salida'; }
    }
});
</script>
@endpush
