@extends('layouts.app')

@section('title', 'Movimientos de Inventario')
@section('nav-title', 'Movimientos')

@section('content')
@push('styles')
<style>
/* ── Tabla movimientos compacta y profesional ── */
#tabla-movimientos {
    font-size: 12px !important;
    table-layout: fixed;
    width: 100%;
}
#tabla-movimientos th {
    padding: 9px 10px !important;
    font-size: 11px !important;
    white-space: nowrap;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    color: var(--text-secondary);
}
#tabla-movimientos td {
    padding: 8px 10px !important;
    font-size: 12px !important;
    vertical-align: middle !important;
}
/* Anchos fijos de columnas */
#tabla-movimientos col.col-num     { width: 36px; }
#tabla-movimientos col.col-fecha   { width: 80px; }
#tabla-movimientos col.col-med     { width: 160px; }
#tabla-movimientos col.col-tipo    { width: 80px; }
#tabla-movimientos col.col-cant    { width: 60px; }
#tabla-movimientos col.col-stock   { width: 100px; }
#tabla-movimientos col.col-pac     { width: 150px; }
#tabla-movimientos col.col-ref     { width: 130px; }
#tabla-movimientos col.col-usr     { width: 120px; }

/* Badge tipo movimiento */
.badge-tipo {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 3px 8px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.badge-tipo.entrada {
    background: rgba(25,135,84,.12);
    color: #146c43;
    border: 1px solid rgba(25,135,84,.25);
}
.badge-tipo.salida {
    background: rgba(220,53,69,.12);
    color: #b02a37;
    border: 1px solid rgba(220,53,69,.25);
}
body.dark-mode .badge-tipo.entrada { background: rgba(25,135,84,.22); color: #75b798; border-color: rgba(25,135,84,.4); }
body.dark-mode .badge-tipo.salida  { background: rgba(220,53,69,.22); color: #ea868f; border-color: rgba(220,53,69,.4); }

/* Indicador de cantidad */
.qty-pill {
    font-size: 13px;
    font-weight: 700;
    letter-spacing: -.3px;
}

/* Tooltip expandible para detalles de firma */
.det-btn {
    background: none;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 2px 7px;
    font-size: 10px;
    cursor: pointer;
    color: var(--text-secondary);
    transition: all .15s;
    white-space: nowrap;
}
.det-btn:hover { background: var(--bg-body); color: var(--primary); border-color: var(--primary); }

/* Fila de detalle expandible */
.row-detail td {
    padding: 0 !important;
    border-top: none !important;
    background: var(--bg-body) !important;
}
.row-detail .detail-inner {
    padding: 10px 14px;
    font-size: 11px;
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    border-left: 3px solid var(--primary);
    margin: 0 8px 6px;
    border-radius: 0 6px 6px 0;
    background: var(--bg-card);
}
.row-detail .detail-inner span { color: var(--text-muted); }
.row-detail .detail-inner strong { color: var(--text-primary); }

/* Avatar mini */
.av-mini {
    width: 22px; height: 22px;
    border-radius: 50%;
    flex-shrink: 0;
}
/* Texto truncado */
.txt-trunc {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}
</style>
@endpush
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

<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:20px;">

    <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); color:#fff; padding:18px 20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.15);">
        <div style="display:flex; align-items:center; gap:14px;">
            <i class='bx bx-trending-up' style="font-size:36px; opacity:.85;"></i>
            <div>
                <h3 style="margin:0; font-size:26px; font-weight:700; line-height:1;">{{ $totalEntradas }}</h3>
                <p style="margin:4px 0 0; opacity:.9; font-size:13px;">Entradas ({{ $periodoLabel }})</p>
            </div>
        </div>
    </div>

    <div style="background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%); color:#fff; padding:18px 20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.15);">
        <div style="display:flex; align-items:center; gap:14px;">
            <i class='bx bx-trending-down' style="font-size:36px; opacity:.85;"></i>
            <div>
                <h3 style="margin:0; font-size:26px; font-weight:700; line-height:1;">{{ $totalSalidas }}</h3>
                <p style="margin:4px 0 0; opacity:.9; font-size:13px;">Salidas ({{ $periodoLabel }})</p>
            </div>
        </div>
    </div>

    <div style="background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%); color:#fff; padding:18px 20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.15);">
        <div style="display:flex; align-items:center; gap:14px;">
            <i class='bx bx-transfer' style="font-size:36px; opacity:.85;"></i>
            <div>
                <h3 style="margin:0; font-size:26px; font-weight:700; line-height:1;">{{ $movimientos->total() }}</h3>
                <p style="margin:4px 0 0; opacity:.9; font-size:13px;">Movimientos</p>
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
        <table class="data-table" id="tabla-movimientos">
            <colgroup>
                <col class="col-num">
                <col class="col-fecha">
                <col class="col-med">
                <col class="col-tipo">
                <col class="col-cant">
                <col class="col-stock">
                <col class="col-pac">
                <col class="col-ref">
                <col class="col-usr">
            </colgroup>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Medicamento</th>
                    <th>Tipo</th>
                    <th style="text-align:center;">Cant.</th>
                    <th>Stock</th>
                    <th>Paciente / Receptor</th>
                    <th>Ref.</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimientos as $i => $m)
                    @php
                        $hasDetail = $m->entregado_por_nombre || $m->recibido_por_nombre
                                     || $m->observaciones_entrega || $m->motivo;
                        $rowId = 'det-' . $m->id;
                    @endphp
                    <tr class="{{ $m->tipo_movimiento === 'salida' && $m->medicamento && $m->medicamento->tieneStockBajo() ? 'row-warning' : '' }}">
                        {{-- # --}}
                        <td><span class="cell-num" style="font-size:11px;">{{ $movimientos->firstItem() + $i }}</span></td>

                        {{-- Fecha --}}
                        <td>
                            <span style="font-size:12px;white-space:nowrap;">{{ $m->created_at->format('d/m/Y') }}</span>
                            <small style="display:block;color:var(--text-muted);font-size:10px;">{{ $m->created_at->format('H:i') }}</small>
                        </td>

                        {{-- Medicamento --}}
                        <td>
                            <span class="txt-trunc" style="font-weight:600;font-size:12px;max-width:155px;" title="{{ $m->medicamento->nombre_medicamento ?? '-' }}">
                                {{ $m->medicamento->nombre_medicamento ?? '-' }}
                            </span>
                            @if($m->medicamento && $m->medicamento->tieneStockBajo())
                                <small style="color:var(--danger);font-size:10px;"><i class='bx bx-error'></i> {{ $m->medicamento->cantidad }} uds.</small>
                            @endif
                        </td>

                        {{-- Tipo --}}
                        <td>
                            @if($m->tipo_movimiento === 'entrada')
                                <span class="badge-tipo entrada"><i class='bx bx-up-arrow-alt'></i> Entrada</span>
                            @else
                                <span class="badge-tipo salida"><i class='bx bx-down-arrow-alt'></i> Salida</span>
                            @endif
                        </td>

                        {{-- Cantidad --}}
                        <td style="text-align:center;">
                            <span class="qty-pill" style="color:{{ $m->tipo_movimiento === 'entrada' ? 'var(--success)' : 'var(--danger)' }};">
                                {{ $m->tipo_movimiento === 'entrada' ? '+' : '-' }}{{ $m->cantidad }}
                            </span>
                        </td>

                        {{-- Stock anterior → nuevo --}}
                        <td style="white-space:nowrap;font-size:11px;">
                            <span style="color:var(--text-muted);">{{ $m->cantidad_anterior }}</span>
                            <i class='bx bx-right-arrow-alt' style="vertical-align:middle;color:var(--text-muted);font-size:12px;"></i>
                            <strong style="font-size:12px;">{{ $m->cantidad_nueva }}</strong>
                        </td>

                        {{-- Paciente / Receptor --}}
                        <td>
                            @if($m->paciente)
                                <div style="display:flex;align-items:center;gap:5px;">
                                    <img src="" data-avatar="{{ $m->paciente->nombre_completo }}" data-bg="#dc3545" data-size="22" alt="" class="av-mini">
                                    <div style="min-width:0;">
                                        <span class="txt-trunc" style="font-size:11px;font-weight:600;max-width:105px;" title="{{ $m->paciente->nombre_completo }}">{{ $m->paciente->nombre_completo }}</span>
                                        <small style="color:var(--text-muted);font-size:10px;">{{ $m->paciente->cedula }}</small>
                                    </div>
                                </div>
                            @elseif($m->recibido_por_nombre)
                                <span class="txt-trunc" style="font-size:11px;max-width:130px;" title="{{ $m->recibido_por_nombre }}">{{ $m->recibido_por_nombre }}</span>
                            @else
                                <span style="font-size:11px;color:var(--text-muted);">{{ $m->motivo ? \Illuminate\Support\Str::limit($m->motivo, 22) : '—' }}</span>
                            @endif
                        </td>

                        {{-- Ref. Consulta / Receta --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                                @if($m->consulta_id)
                                    <a href="{{ route('consultas.index') }}?search={{ $m->consulta_id }}"
                                       style="font-size:10px;color:var(--primary);white-space:nowrap;text-decoration:none;"
                                       title="Ver consulta #{{ $m->consulta_id }}">
                                        <i class='bx bx-clipboard'></i> #{{ $m->consulta_id }}
                                    </a>
                                @endif
                                @if($m->consulta && $m->consulta->recetas->count() > 0)
                                    @foreach($m->consulta->recetas->take(1) as $receta)
                                        <a href="{{ route('recetas.imprimir', $receta) }}" target="_blank"
                                           style="font-size:10px;color:var(--success);white-space:nowrap;text-decoration:none;"
                                           title="{{ $receta->numero_receta }}">
                                            <i class='bx bx-file-blank'></i> {{ \Illuminate\Support\Str::limit($receta->numero_receta, 14) }}
                                        </a>
                                        @if($m->consulta->recetas->count() > 1)
                                            <small style="font-size:10px;color:var(--text-muted);">+{{ $m->consulta->recetas->count()-1 }}</small>
                                        @endif
                                    @endforeach
                                @elseif(!$m->consulta_id)
                                    @if($hasDetail)
                                        <button class="det-btn" onclick="toggleDetail('{{ $rowId }}')" title="Ver detalles de entrega">
                                            <i class='bx bx-chevron-down' id="ico-{{ $rowId }}"></i> Detalle
                                        </button>
                                    @else
                                        <span style="color:var(--text-muted);font-size:11px;">—</span>
                                    @endif
                                @endif
                                @if($m->consulta_id && $hasDetail)
                                    <button class="det-btn" onclick="toggleDetail('{{ $rowId }}')" title="Ver detalles de entrega" style="margin-top:2px;">
                                        <i class='bx bx-chevron-down' id="ico-{{ $rowId }}"></i>
                                    </button>
                                @endif
                            </div>
                        </td>

                        {{-- Usuario --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:5px;">
                                <img src="" data-avatar="{{ $m->usuario?->nombre_completo ?? 'Sistema' }}" data-bg="#0d6efd" data-size="22" alt="" class="av-mini">
                                <div style="min-width:0;">
                                    <span class="txt-trunc" style="font-size:11px;font-weight:600;max-width:90px;" title="{{ $m->usuario?->nombre_completo ?? 'Sistema' }}">{{ $m->usuario?->nombre_completo ?? 'Sistema' }}</span>
                                    @if($m->usuario)
                                        <small style="display:block;color:var(--text-muted);font-size:10px;white-space:nowrap;">{{ ucfirst($m->usuario->rol) }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>

                    {{-- Fila de detalle (expandible) --}}
                    @if($hasDetail)
                    <tr class="row-detail" id="{{ $rowId }}" style="display:none;">
                        <td colspan="9">
                            <div class="detail-inner">
                                @if($m->motivo)
                                    <div><span>Motivo: </span><strong>{{ $m->motivo }}</strong></div>
                                @endif
                                @if($m->entregado_por_nombre)
                                    <div><span>Entregó: </span><strong>{{ $m->entregado_por_nombre }}</strong></div>
                                @endif
                                @if($m->recibido_por_nombre)
                                    <div>
                                        <span>Recibió: </span><strong>{{ $m->recibido_por_nombre }}</strong>
                                        @if($m->recibido_por_cedula)
                                            <span style="margin-left:4px;color:var(--text-muted);">({{ $m->recibido_por_cedula }})</span>
                                        @endif
                                    </div>
                                @endif
                                @if($m->observaciones_entrega)
                                    <div><span>Obs.: </span><strong>{{ $m->observaciones_entrega }}</strong></div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="9" class="empty-row">
                        <div class="empty-state"><i class='bx bx-package'></i><p>No hay movimientos registrados en este período</p></div>
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

            {{-- Sincronizar desde receta --}}
            <div style="background:rgba(25,135,84,.08);border:1px solid rgba(25,135,84,.25);border-radius:10px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:12px;">
                <i class='bx bx-file-blank' style="font-size:22px;color:var(--success);flex-shrink:0;"></i>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:12px;font-weight:700;color:var(--text-primary);margin-bottom:6px;">
                        Autocompletar desde Receta Médica
                        <span style="font-weight:400;color:var(--text-muted);"> (opcional)</span>
                    </div>
                    <select id="sel-receta-sincronizar" style="width:100%;padding:7px 10px;border:1px solid var(--border-color);border-radius:6px;background:var(--bg-card);color:var(--text-primary);font-size:13px;">
                        <option value="">— Seleccionar receta activa para autocompletar —</option>
                        @foreach(\App\Models\Receta::with(['paciente','items.medicamento'])
                            ->where('estado','activa')
                            ->latest()->limit(50)->get() as $rec)
                            <option value="{{ $rec->id }}"
                                data-paciente-id="{{ $rec->paciente_id }}"
                                data-paciente="{{ htmlspecialchars($rec->paciente?->nombre_completo ?? '', ENT_QUOTES) }}"
                                data-cedula="{{ $rec->paciente?->cedula ?? '' }}"
                                data-items="{{ htmlspecialchars(json_encode($rec->items->map(fn($it)=>['med_id'=>$it->medicamento_id,'cantidad'=>$it->cantidad,'nombre'=>$it->nombre_medicamento])), ENT_QUOTES) }}">
                                {{ $rec->numero_receta }} — {{ $rec->paciente?->nombre_completo ?? 'Sin paciente' }} ({{ $rec->created_at->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" onclick="sincronizarDesdeReceta()"
                    style="flex-shrink:0;background:var(--success);color:#fff;border:none;padding:8px 14px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;">
                    <i class='bx bx-sync'></i> Cargar
                </button>
            </div>

            <div class="form-group">
                <label><i class='bx bxs-capsule'></i> Medicamento *</label>
                <select name="medicamento_id" id="salida-medicamento" required>
                    <option value="">Seleccionar medicamento</option>
                    @foreach(\App\Models\Medicamento::orderBy('nombre_medicamento')->get() as $med)
                        <option value="{{ $med->id }}" data-stock="{{ $med->cantidad }}">
                            {{ $med->nombre_medicamento }} — Stock: {{ $med->cantidad }}
                            {{ $med->tieneStockBajo() ? '⚠️' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label><i class='bx bx-package'></i> Cantidad *</label>
                <input type="number" name="cantidad" id="salida-cantidad" min="1" value="1" required>
                <small id="salida-stock-info" style="color:var(--text-muted);font-size:11px;margin-top:4px;display:block;"></small>
            </div>

            <div class="form-group">
                <label><i class='bx bxs-user-detail'></i> Paciente (opcional)</label>
                <select name="paciente_id" id="salida-paciente">
                    <option value="">Sin paciente asignado</option>
                    @foreach(\App\Models\Paciente::orderBy('nombre')->get() as $pac)
                        <option value="{{ $pac->id }}" data-cedula="{{ $pac->cedula }}">
                            {{ $pac->nombre_completo }} — {{ $pac->cedula }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label><i class='bx bx-detail'></i> Motivo</label>
                <textarea name="motivo" id="salida-motivo" rows="2" placeholder="Dispensación por receta, vencimiento, etc.">Salida de inventario</textarea>
            </div>

            {{-- Comprobante de entrega --}}
            <div style="background:rgba(13,110,253,.06);border:1px solid rgba(13,110,253,.2);border-radius:10px;padding:14px 16px;margin-top:4px;">
                <div style="font-size:12px;font-weight:700;color:var(--text-primary);margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                    <i class='bx bx-pen' style="color:var(--primary);font-size:16px;"></i>
                    Comprobante de Entrega
                    <span style="font-size:10px;font-weight:400;color:var(--text-muted);">(para auditoría mensual)</span>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class='bx bx-user-check'></i> Entregado por</label>
                        <input type="text" name="entregado_por_nombre" id="salida-entregado"
                            value="{{ auth()->user()->nombre_completo }}"
                            placeholder="Nombre de quien entrega">
                    </div>
                    <div class="form-group">
                        <label><i class='bx bx-user'></i> Recibido por</label>
                        <input type="text" name="recibido_por_nombre" id="salida-recibido"
                            placeholder="Nombre completo de quien recibe">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class='bx bx-id-card'></i> Cédula del receptor</label>
                        <input type="text" name="recibido_por_cedula" id="salida-cedula"
                            placeholder="000-0000000-0">
                    </div>
                    <div class="form-group">
                        <label><i class='bx bx-note'></i> Observaciones</label>
                        <input type="text" name="observaciones_entrega" id="salida-observaciones"
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
// ── Toggle fila de detalle (firma/motivo) ──────────────────────────────
function toggleDetail(rowId) {
    const row = document.getElementById(rowId);
    const ico = document.getElementById('ico-' + rowId);
    if (!row) return;
    const visible = row.style.display !== 'none';
    row.style.display = visible ? 'none' : 'table-row';
    if (ico) { ico.className = visible ? 'bx bx-chevron-down' : 'bx bx-chevron-up'; }
}

// ── Formulario Salida ──────────────────────────────────────────────────
const SALIDA_URL = '{{ route('inventario.salida') }}';
let _salidaSubmitting = false;

// Mostrar info de stock al cambiar medicamento en salida
document.getElementById('salida-medicamento').addEventListener('change', function() {
    const opt   = this.options[this.selectedIndex];
    const stock = opt ? parseInt(opt.dataset.stock || 0) : 0;
    const info  = document.getElementById('salida-stock-info');
    if (this.value && opt) {
        if (stock <= 0) {
            info.style.color = 'var(--danger)';
            info.textContent = '\u26a0\ufe0f Sin stock disponible';
        } else if (stock <= 5) {
            info.style.color = '#856404';
            info.textContent = '\u26a0\ufe0f Stock bajo: ' + stock + ' unidades disponibles';
        } else {
            info.style.color = 'var(--success)';
            info.textContent = '\u2713 Stock disponible: ' + stock + ' unidades';
        }
        document.getElementById('salida-cantidad').max = stock;
    } else {
        info.textContent = '';
    }
});

// Al seleccionar paciente: prellenar receptor y cargar sus recetas activas
document.getElementById('salida-paciente').addEventListener('change', async function() {
    const pacienteId = this.value;
    const opt        = this.options[this.selectedIndex];
    const selRec     = document.getElementById('sel-receta-sincronizar');

    if (!pacienteId) {
        selRec.innerHTML = '<option value="">\u2014 Seleccionar receta activa para autocompletar \u2014</option>';
        return;
    }

    // Prellenar receptor con nombre del paciente
    const nombrePac = opt.text.split('\u2014')[0].trim();
    const cedulaPac = opt.dataset ? (opt.dataset.cedula || '') : '';
    document.getElementById('salida-recibido').value = nombrePac;
    document.getElementById('salida-cedula').value   = cedulaPac;

    // Cargar recetas activas del paciente
    selRec.innerHTML = '<option value="">\u23f3 Cargando recetas del paciente...</option>';
    try {
        const url  = window.BASE_URL + '/recetas/paciente/' + pacienteId + '/activas';
        const res  = await fetch(url, { headers: { 'Accept':'application/json','X-CSRF-TOKEN':window.CSRF_TOKEN } });
        const data = await res.json();

        selRec.innerHTML = '<option value="">\u2014 Seleccionar receta activa \u2014</option>';

        if (data.success && data.data && data.data.length > 0) {
            data.data.forEach(function(rec) {
                const items      = rec.items || [];
                const o          = document.createElement('option');
                o.value          = rec.id;
                o.dataset.pacienteId = pacienteId;
                o.dataset.paciente   = nombrePac;
                o.dataset.cedula     = cedulaPac;
                o.dataset.items      = JSON.stringify(items);
                var medList = items.map(function(it){ return it.nombre + ' x' + it.cantidad; }).join(', ');
                o.textContent = rec.numero_receta + ' (' + rec.fecha + ')' + (medList ? ' \u2014 ' + medList : '');
                selRec.appendChild(o);
            });
            // Si solo hay una receta, cargarla automáticamente
            if (data.data.length === 1) {
                selRec.selectedIndex = 1;
                sincronizarDesdeReceta();
            } else {
                showToast(data.data.length + ' recetas activas. Selecciona una y haz clic en Cargar.', 'success');
            }
        } else {
            selRec.innerHTML = '<option value="">\u2014 Sin recetas activas para este paciente \u2014</option>';
        }
    } catch(e) {
        selRec.innerHTML = '<option value="">\u2014 Error al cargar recetas \u2014</option>';
        console.error('Error recetas paciente:', e);
    }
});

// Sincronizar desde receta — autocompletar todos los campos
async function sincronizarDesdeReceta() {
    const sel = document.getElementById('sel-receta-sincronizar');
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) { showToast('Selecciona una receta primero.', 'error'); return; }

    // Feedback visual en el botón
    const btn = document.querySelector('[onclick="sincronizarDesdeReceta()"]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>'; }

    try {
        const pacienteId  = opt.dataset.pacienteId;
        const pacienteNom = opt.dataset.paciente;
        const cedulaPac   = opt.dataset.cedula;
        const numReceta   = opt.text.split('\u2014')[0].trim()  // separar por —
                          || opt.text.split('-')[0].trim()
                          || opt.text.trim();
        let   items       = [];
        try {
            const rawItems = opt.getAttribute('data-items') || '[]';
            items = JSON.parse(rawItems);
        } catch(e) {
            console.warn('Error parsing data-items:', e);
            items = [];
        }

        // ── 1. Autocompletar medicamento ──
        if (items.length >= 1) {
            const selMed = document.getElementById('salida-medicamento');
            let found = false;

            // Buscar por ID primero
            for (let i = 0; i < selMed.options.length; i++) {
                if (String(selMed.options[i].value) === String(items[0].med_id)) {
                    selMed.selectedIndex = i;
                    selMed.dispatchEvent(new Event('change'));
                    found = true;
                    break;
                }
            }

            // Si no encontró por ID, buscar por nombre
            if (!found && items[0].nombre) {
                const nombreBuscar = items[0].nombre.toLowerCase();
                for (let i = 0; i < selMed.options.length; i++) {
                    if (selMed.options[i].text.toLowerCase().includes(nombreBuscar)) {
                        selMed.selectedIndex = i;
                        selMed.dispatchEvent(new Event('change'));
                        found = true;
                        break;
                    }
                }
            }

            document.getElementById('salida-cantidad').value = items[0].cantidad || 1;

            if (items.length > 1) {
                var otros = items.slice(1).map(function(it) { return it.nombre + ' (' + it.cantidad + ')'; }).join(', ');
                showToast('Receta con ' + items.length + ' medicamentos. Se cargo: ' + items[0].nombre + '. Registra tambien: ' + otros, 'error');
            }
        }

        // ── 2. Autocompletar paciente ──
        if (pacienteId) {
            const selPac = document.getElementById('salida-paciente');
            for (let i = 0; i < selPac.options.length; i++) {
                if (String(selPac.options[i].value) === String(pacienteId)) {
                    selPac.selectedIndex = i;
                    break;
                }
            }
        }

        // ── 3. Autocompletar comprobante de entrega ──
        if (pacienteNom) {
            document.getElementById('salida-recibido').value = pacienteNom;
            document.getElementById('salida-cedula').value   = cedulaPac || '';
        }

        // ── 4. Motivo y observaciones ──
        document.getElementById('salida-motivo').value        = 'Dispensación por receta ' + numReceta;
        document.getElementById('salida-observaciones').value = 'Receta: ' + numReceta;

        showToast('Datos cargados desde la receta.', 'success');

    } catch(e) {
        showToast('Error al cargar los datos de la receta.', 'error');
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-sync"></i> Cargar'; }
    }
}

document.getElementById('form-salida').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_salidaSubmitting) return;
    _salidaSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Registrando...'; }

    try {
        const res  = await fetch(SALIDA_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
            body: new FormData(this)
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeModal('modal-salida');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'Error al registrar salida.', 'error');
        }
    } catch(err) {
        showToast('Error de conexión.', 'error');
    } finally {
        _salidaSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Registrar Salida'; }
    }
});

// ── Exportar con opciones ──
const BASE = '{{ url('/') }}';

// ── Formulario Entrada ─────────────────────────────────────────────────
const ENTRADA_URL = '{{ route('inventario.entrada') }}';
let _entradaSubmitting = false;
document.getElementById('form-entrada').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_entradaSubmitting) return;
    _entradaSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Registrando...'; }
    try {
        const res  = await fetch(ENTRADA_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
            body: new FormData(this)
        });
        const data = await res.json();
        if (data.success) { showToast(data.message, 'success'); closeModal('modal-entrada'); setTimeout(() => location.reload(), 800); }
        else showToast(data.message || 'Error al registrar entrada.', 'error');
    } catch(err) { showToast('Error de conexión.', 'error'); }
    finally {
        _entradaSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Registrar Entrada'; }
    }
});
function toggleFechasExport() {
    const p = document.getElementById('exp-periodo').value;
    document.getElementById('exp-fechas').style.display = p === 'rango' ? 'block' : 'none';
}

function exportarMovimientos(formato) {
    var periodo = document.getElementById('exp-periodo').value;
    var tipo    = document.getElementById('exp-tipo').value;
    var url;

    if (formato === 'csv') {
        url = BASE + '/inventario/exportar/csv?periodo=' + periodo + '&tipo=' + tipo;
        if (periodo === 'rango') {
            url += '&fecha_inicio=' + document.getElementById('exp-fecha-inicio').value;
            url += '&fecha_fin='    + document.getElementById('exp-fecha-fin').value;
        }
    } else {
        url = BASE + '/export/movimientos?formato=' + formato + '&periodo=' + periodo + '&tipo=' + tipo;
        if (periodo === 'rango') {
            url += '&fecha_inicio=' + document.getElementById('exp-fecha-inicio').value;
            url += '&fecha_fin='    + document.getElementById('exp-fecha-fin').value;
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
    div.innerHTML =
        '<div style="position:absolute;top:10px;right:10px;" class="btn-remove-item">' +
            '<button type="button" onclick="removeSolicitudItem(this)" style="background:var(--danger-light);color:var(--danger);border:none;border-radius:6px;width:28px;height:28px;cursor:pointer;font-size:16px;">&times;</button>' +
        '</div>' +
        '<div class="form-row">' +
            '<div class="form-group" style="flex:2;">' +
                '<label><i class=\'bx bx-capsule\'></i> Medicamento *</label>' +
                '<select name="items[' + idx + '][medicamento_id]" class="sol-med-select" required onchange="autocompletarProveedor(this)">' +
                    opciones +
                '</select>' +
            '</div>' +
            '<div class="form-group" style="flex:1;">' +
                '<label><i class=\'bx bx-package\'></i> Cantidad *</label>' +
                '<input type="number" name="items[' + idx + '][cantidad]" class="sol-cantidad" min="1" value="1" required>' +
            '</div>' +
        '</div>' +
        '<div class="form-row">' +
            '<div class="form-group">' +
                '<label><i class=\'bx bx-building\'></i> Proveedor</label>' +
                '<input type="text" name="items[' + idx + '][proveedor]" class="sol-proveedor" placeholder="Nombre del proveedor">' +
            '</div>' +
            '<div class="form-group">' +
                '<label><i class=\'bx bx-phone\'></i> Tel\u00e9fono Proveedor</label>' +
                '<input type="tel" name="items[' + idx + '][telefono]" class="sol-telefono" placeholder="809-000-0000">' +
            '</div>' +
        '</div>';
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
</script>
@endpush
