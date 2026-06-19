@extends('layouts.app')

@section('title', 'Solicitudes de Inventario')
@section('nav-title', 'Solicitudes')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Solicitudes de Reposición</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Solicitudes</a></li>
        </ul>
    </div>
    {{-- Botón visible para TODOS los roles --}}
    <div class="head-actions">
        <button class="btn-primary" onclick="verificarStockYAbrir()">
            <i class='bx bx-plus-circle'></i> Nueva Solicitud
        </button>
    </div>
</div>

{{-- Stat cards --}}
@php
    $pendientes  = $solicitudes->getCollection()->where('estado','pendiente')->count();
    $aprobadas   = $solicitudes->getCollection()->where('estado','aprobada')->count();
    $completadas = $solicitudes->getCollection()->where('estado','completada')->count();
    $rechazadas  = $solicitudes->getCollection()->where('estado','rechazada')->count();
@endphp
<div class="stat-cards-grid">
    <div class="stat-card-item">
        <div class="stat-card-icon blue"><i class='bx bx-package'></i></div>
        <div class="stat-card-body">
            <div class="stat-card-num">{{ $solicitudes->total() }}</div>
            <div class="stat-card-lbl">Total solicitudes</div>
        </div>
    </div>
    <div class="stat-card-item">
        <div class="stat-card-icon yellow"><i class='bx bx-time-five'></i></div>
        <div class="stat-card-body">
            <div class="stat-card-num">{{ $pendientes }}</div>
            <div class="stat-card-lbl">Pendientes</div>
        </div>
    </div>
    <div class="stat-card-item">
        <div class="stat-card-icon green"><i class='bx bx-check-circle'></i></div>
        <div class="stat-card-body">
            <div class="stat-card-num">{{ $completadas }}</div>
            <div class="stat-card-lbl">Completadas</div>
        </div>
    </div>
    <div class="stat-card-item">
        <div class="stat-card-icon red"><i class='bx bx-x-circle'></i></div>
        <div class="stat-card-body">
            <div class="stat-card-num">{{ $rechazadas }}</div>
            <div class="stat-card-lbl">Rechazadas</div>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3><i class='bx bx-package'></i> Solicitudes de Inventario</h3>
        <span class="badge badge-info">{{ $solicitudes->total() }} registros</span>
        @if($search)
            <span class="search-active-badge">
                <i class='bx bx-search'></i> "{{ $search }}"
                <a href="{{ route('solicitudes.index') }}">✕</a>
            </span>
        @endif
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>N° Orden</th>
                    <th>Medicamento</th>
                    <th>Cant.</th>
                    <th>Proveedor</th>
                    <th>Teléfono</th>
                    <th>Solicitado por</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    @if(auth()->user()->isAdmin())<th>Acciones</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $s)
                    @php
                        $estadoBadge = [
                            'pendiente'  => 'badge-yellow',
                            'aprobada'   => 'badge-blue',
                            'rechazada'  => 'badge-red',
                            'completada' => 'badge-green',
                        ][$s->estado] ?? 'badge-gray';
                    @endphp
                    <tr>
                        <td><code>{{ $s->numero_orden ?? 'SOL-' . str_pad($s->id, 4, '0', STR_PAD_LEFT) }}</code></td>
                        <td>
                            <strong>{{ $s->medicamento->nombre_medicamento }}</strong><br>
                            <span class="text-muted">Stock: {{ $s->medicamento->cantidad }} {{ $s->medicamento->unidad }}</span>
                        </td>
                        <td><strong style="font-size:15px;">{{ $s->cantidad_solicitada }}</strong></td>
                        <td>{{ $s->proveedor_sugerido ?? '—' }}</td>
                        <td>
                            @if($s->telefono_proveedor)
                                <a href="tel:{{ $s->telefono_proveedor }}" style="color:var(--primary);display:flex;align-items:center;gap:4px;font-size:13px;">
                                    <i class='bx bx-phone'></i> {{ $s->telefono_proveedor }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="cell-user">
                                <img src="" data-avatar="{{ $s->solicitadoPor->nombre_completo }}" data-bg="#6f42c1" data-size="32" alt="">
                                <div>
                                    <div style="font-size:13px;font-weight:500;">{{ $s->solicitadoPor->nombre_completo }}</div>
                                    <span class="text-muted">{{ $s->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px;color:var(--text-secondary);">{{ $s->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge {{ $estadoBadge }}">{{ ucfirst($s->estado) }}</span>
                            @if($s->atendida_at)
                                <br><span class="text-muted">{{ $s->atendida_at->format('d/m/Y') }}</span>
                            @endif
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="actions">
                            @if($s->estado === 'pendiente')
                                <button class="btn-icon btn-success" onclick="aprobarSolicitud({{ $s->id }}, '{{ addslashes($s->medicamento->nombre_medicamento) }}', {{ $s->cantidad_solicitada }})" title="Aprobar y registrar entrada">
                                    <i class='bx bx-check-double'></i>
                                </button>
                                <button class="btn-icon btn-delete" onclick="atenderSolicitud({{ $s->id }}, 'rechazada')" title="Rechazar">
                                    <i class='bx bx-x'></i>
                                </button>
                            @elseif($s->estado === 'aprobada')
                                <button class="btn-icon btn-warning" onclick="atenderSolicitud({{ $s->id }}, 'completada')" title="Marcar como completada">
                                    <i class='bx bx-package'></i>
                                </button>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="9" class="empty-row">
                        <div class="empty-state">
                            <i class='bx bx-package'></i>
                            <p>No hay solicitudes registradas</p>
                            <button class="btn-primary btn-sm" onclick="verificarStockYAbrir()">
                                <i class='bx bx-plus'></i> Crear Solicitud
                            </button>
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $solicitudes->links('partials.pagination') }}</div>
</div>

{{-- ══════════════════════════════════════════
     MODALES — disponibles para todos los roles
     ══════════════════════════════════════════ --}}

{{-- Modal confirmación genérico (rechazar / completar) --}}
<div class="modal" id="modal-confirm-custom">
    <div class="modal-content" style="max-width:420px;">
        <div class="modal-body" style="padding:32px 28px;text-align:center;">
            <i class='bx bx-question-mark' id="confirm-icon" style="font-size:52px;color:var(--primary);display:block;margin-bottom:16px;"></i>
            <p id="confirm-msg" style="font-size:15px;color:var(--text-primary);line-height:1.6;"></p>
        </div>
        <div class="modal-footer" style="justify-content:center;gap:12px;">
            <button type="button" class="btn-secondary" onclick="closeModal('modal-confirm-custom')">
                <i class='bx bx-x'></i> Cancelar
            </button>
            <button type="button" id="confirm-ok-btn" class="btn-primary">
                <i class='bx bx-check'></i> Aceptar
            </button>
        </div>
    </div>
</div>

{{-- Modal aprobar solicitud (solo admin lo usa) --}}
<div class="modal" id="modal-aprobar">
    <div class="modal-content modal-sm">
        <div class="modal-header" style="background:var(--success);color:#fff;">
            <h2><i class='bx bx-check-double'></i> Aprobar y Registrar Entrada</h2>
            <button class="modal-close" onclick="closeModal('modal-aprobar')" style="color:#fff;">&times;</button>
        </div>
        <form id="form-aprobar" class="modal-form">
            @csrf
            <input type="hidden" id="apr-solicitud-id">
            <div style="background:var(--success-light);border-left:4px solid var(--success);padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;color:var(--text-primary);display:flex;gap:10px;">
                <i class='bx bx-info-circle' style="font-size:18px;color:var(--success);flex-shrink:0;margin-top:1px;"></i>
                <span>Al aprobar, se registrará automáticamente la <strong>entrada al inventario</strong>.</span>
            </div>
            <div class="form-group">
                <label><i class='bx bx-capsule'></i> Medicamento</label>
                <input type="text" id="apr-medicamento" disabled style="opacity:.7;">
            </div>
            <div class="form-group">
                <label><i class='bx bx-package'></i> Cantidad a ingresar *</label>
                <input type="number" id="apr-cantidad" name="cantidad" min="1" required>
            </div>
            <div class="form-group">
                <label><i class='bx bx-receipt'></i> Número de Factura / Referencia</label>
                <input type="text" name="numero_factura" placeholder="Ej: FAC-2026-001">
            </div>
            <div class="form-group">
                <label><i class='bx bx-note'></i> Notas de recepción</label>
                <textarea name="notas_recepcion" rows="2" placeholder="Observaciones al recibir los medicamentos..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-aprobar')">Cancelar</button>
                <button type="submit" class="btn-success"><i class='bx bx-check-double'></i> Aprobar y Registrar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal aviso: stock en buen estado --}}
<div class="modal" id="modal-stock-ok">
    <div class="modal-content" style="max-width:420px;">
        <div class="modal-header" style="background:linear-gradient(135deg,#198754,#20c997);color:#fff;">
            <h2><i class='bx bxs-check-circle'></i> Stock en Buen Estado</h2>
            <button class="modal-close" onclick="closeModal('modal-stock-ok')" style="color:#fff;">&times;</button>
        </div>
        <div class="modal-body" style="padding:28px 24px;text-align:center;">
            <i class='bx bxs-check-circle' style="font-size:54px;color:#198754;display:block;margin-bottom:14px;"></i>
            <p style="font-size:15px;font-weight:600;color:var(--text-primary);margin-bottom:10px;">
                El inventario aún posee medicamentos suficientes.
            </p>
            <p style="font-size:13px;color:var(--text-secondary);line-height:1.7;">
                Ningún medicamento tiene stock bajo en este momento.<br>
                ¿Deseas realizar una solicitud de todas formas?
            </p>
        </div>
        <div class="modal-footer" style="justify-content:center;gap:12px;">
            <button type="button" class="btn-secondary" onclick="closeModal('modal-stock-ok')">
                <i class='bx bx-x'></i> No
            </button>
            <button type="button" class="btn-primary" onclick="closeModal('modal-stock-ok'); openModal('modal-solicitud');">
                <i class='bx bx-send'></i> Sí, crear solicitud
            </button>
        </div>
    </div>
</div>

{{-- Modal Nueva Solicitud (multi-medicamento) --}}
<div class="modal" id="modal-solicitud">
    <div class="modal-content modal-lg">
        <div class="modal-header" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;">
            <h2><i class='bx bx-send'></i> Nueva Solicitud de Reposición</h2>
            <button class="modal-close" onclick="closeModal('modal-solicitud')" style="color:#fff;">&times;</button>
        </div>
        <form id="form-solicitud" class="modal-form">
            @csrf
            <div class="solicitud-alert-info" style="margin-bottom:16px;display:flex;gap:10px;padding:12px 16px;background:rgba(13,202,240,.1);border-radius:8px;border-left:4px solid var(--info);font-size:13px;color:var(--text-primary);">
                <i class='bx bx-info-circle' style="font-size:18px;flex-shrink:0;color:var(--info);margin-top:1px;"></i>
                <span>Puedes agregar varios medicamentos en una sola solicitud. Haz clic en <strong>+ Agregar medicamento</strong> para añadir más.</span>
            </div>

            <div id="solicitud-items">
                <div class="solicitud-item" style="border:1px solid var(--border-color);border-radius:10px;padding:16px;margin-bottom:12px;position:relative;background:var(--bg-body);">
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
                                        data-tel="{{ $med->telefono_proveedor ?? '' }}">
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

            <button type="button" onclick="agregarSolicitudItem()"
                style="background:none;border:2px dashed var(--border-color);width:100%;padding:12px;border-radius:10px;cursor:pointer;color:var(--text-secondary);font-size:13px;margin-bottom:16px;transition:all .2s;"
                onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                onmouseout="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'">
                <i class='bx bx-plus-circle'></i> Agregar otro medicamento
            </button>

            <div class="form-group">
                <label><i class='bx bx-note'></i> Notas generales</label>
                <textarea name="notas" rows="2" placeholder="Urgencia, observaciones generales..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-solicitud')">Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-send'></i> Enviar Solicitud</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const SOL_URL         = '{{ url('solicitudes') }}';
const INV_ENTRADA_URL = '{{ route('inventario.entrada') }}';
const STOCK_BAJO_COUNT = {{ $stockBajoCount }};

// ── Verificar stock antes de abrir modal de solicitud ──
function verificarStockYAbrir() {
    if (STOCK_BAJO_COUNT === 0) {
        // Todo el stock está bien → mostrar aviso Sí/No
        openModal('modal-stock-ok');
    } else {
        // Hay stock bajo → abrir directamente el modal
        openModal('modal-solicitud');
    }
}

// ── Aprobar solicitud (admin) ──
function aprobarSolicitud(id, medicamento, cantidad) {
    document.getElementById('apr-solicitud-id').value = id;
    document.getElementById('apr-medicamento').value  = medicamento;
    document.getElementById('apr-cantidad').value     = cantidad;
    openModal('modal-aprobar');
}

document.getElementById('form-aprobar').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('[type=submit]');
    if (btn && btn.disabled) return;
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Procesando...'; }
    const id  = document.getElementById('apr-solicitud-id').value;
    const fd  = new FormData(this);
    try {
        const fdApr = new FormData();
        fdApr.append('estado', 'aprobada');
        fdApr.append('numero_factura', fd.get('numero_factura') || '');
        fdApr.append('_method', 'PATCH');
        const r1 = await fetch(SOL_URL + '/' + id + '/atender', { method:'POST', headers:{'X-CSRF-TOKEN':window.CSRF_TOKEN,'Accept':'application/json'}, body:fdApr });
        const d1 = await r1.json();
        if (!d1.success) { showToast(d1.message || 'Error al aprobar.', 'error'); return; }

        const fdEnt = new FormData();
        fdEnt.append('medicamento_id', d1.medicamento_id);
        fdEnt.append('cantidad', document.getElementById('apr-cantidad').value);
        fdEnt.append('motivo', 'Entrada por solicitud ' + (d1.numero_orden || id) + (fd.get('numero_factura') ? ' — Factura: ' + fd.get('numero_factura') : ''));
        const r2 = await fetch(INV_ENTRADA_URL, { method:'POST', headers:{'X-CSRF-TOKEN':window.CSRF_TOKEN,'Accept':'application/json'}, body:fdEnt });
        const d2 = await r2.json();

        if (d2.success) {
            showToast('Solicitud aprobada y entrada registrada correctamente.', 'success');
            closeModal('modal-aprobar');
            setTimeout(() => location.reload(), 900);
        } else {
            showToast('Solicitud aprobada pero error al registrar entrada: ' + d2.message, 'error');
            setTimeout(() => location.reload(), 1500);
        }
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-check-double"></i> Aprobar y Registrar'; }
    }
});

// ── Atender (rechazar / completar) ──
async function atenderSolicitud(id, estado) {
    const labels  = { completada:'marcar como completada', rechazada:'rechazar' };
    const iconos  = { completada:'bx-package', rechazada:'bx-x-circle' };
    const colores = { completada:'var(--success)', rechazada:'var(--danger)' };
    document.getElementById('confirm-msg').textContent  = '¿Desea ' + labels[estado] + ' esta solicitud?';
    document.getElementById('confirm-icon').className   = 'bx ' + iconos[estado];
    document.getElementById('confirm-icon').style.color = colores[estado];
    document.getElementById('confirm-ok-btn').className = estado === 'rechazada' ? 'btn-danger' : 'btn-success';
    openModal('modal-confirm-custom');
    document.getElementById('confirm-ok-btn').onclick = async () => {
        closeModal('modal-confirm-custom');
        const fd = new FormData();
        fd.append('estado', estado);
        fd.append('_method', 'PATCH');
        const res  = await fetch(SOL_URL + '/' + id + '/atender', { method:'POST', headers:{'X-CSRF-TOKEN':window.CSRF_TOKEN,'Accept':'application/json'}, body:fd });
        const data = await res.json();
        if (data.success) { showToast(data.message, 'success'); setTimeout(() => location.reload(), 800); }
        else showToast(data.message, 'error');
    };
}

// ── Nueva solicitud multi-medicamento ──
let itemCount = 1;

function autocompletarProveedor(select) {
    const opt  = select.options[select.selectedIndex];
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
    const idx          = itemCount++;
    const primerSelect = document.querySelector('#solicitud-items .sol-med-select');
    const opciones     = primerSelect ? primerSelect.innerHTML : '<option value="">Seleccionar medicamento</option>';
    const div = document.createElement('div');
    div.className = 'solicitud-item';
    div.style.cssText = 'border:1px solid var(--border-color);border-radius:10px;padding:16px;margin-bottom:12px;position:relative;background:var(--bg-body);';
    div.innerHTML =
        '<div style="position:absolute;top:10px;right:10px;">'
        + '<button type="button" onclick="removeSolicitudItem(this)" style="background:var(--danger-light);color:var(--danger);border:none;border-radius:6px;width:28px;height:28px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;">×</button>'
        + '</div>'
        + '<div class="form-row">'
        + '<div class="form-group" style="flex:2;"><label><i class="bx bx-capsule"></i> Medicamento *</label>'
        + '<select name="items[' + idx + '][medicamento_id]" class="sol-med-select" required onchange="autocompletarProveedor(this)">' + opciones + '</select></div>'
        + '<div class="form-group" style="flex:1;"><label><i class="bx bx-package"></i> Cantidad *</label>'
        + '<input type="number" name="items[' + idx + '][cantidad]" class="sol-cantidad" min="1" value="1" required></div>'
        + '</div>'
        + '<div class="form-row">'
        + '<div class="form-group"><label><i class="bx bx-building"></i> Proveedor</label>'
        + '<input type="text" name="items[' + idx + '][proveedor]" class="sol-proveedor" placeholder="Nombre del proveedor"></div>'
        + '<div class="form-group"><label><i class="bx bx-phone"></i> Teléfono Proveedor</label>'
        + '<input type="tel" name="items[' + idx + '][telefono]" class="sol-telefono" placeholder="809-000-0000"></div>'
        + '</div>';
    document.getElementById('solicitud-items').appendChild(div);
    div.querySelector('.sol-med-select').value = '';
}

function removeSolicitudItem(btn) {
    if (document.querySelectorAll('.solicitud-item').length <= 1) return;
    btn.closest('.solicitud-item').remove();
}

let _solSubmitting = false;
document.getElementById('form-solicitud').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_solSubmitting) return;
    _solSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Enviando...'; }
    try {
        const res  = await fetch('{{ route('solicitudes.store') }}', { method:'POST', headers:{'X-CSRF-TOKEN':window.CSRF_TOKEN,'Accept':'application/json'}, body:new FormData(this) });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeModal('modal-solicitud');
            this.reset();
            document.querySelectorAll('.solicitud-item').forEach((item, i) => { if (i > 0) item.remove(); });
            itemCount = 1;
            setTimeout(() => location.reload(), 900);
        } else {
            showToast(data.message || 'Error al enviar solicitud.', 'error');
        }
    } catch (err) {
        showToast('Error de conexión al servidor.', 'error');
    } finally {
        _solSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-send"></i> Enviar Solicitud'; }
    }
});
</script>
@endpush
