@extends('layouts.app')

@section('title', 'Gastos Médicos')
@section('nav-title', 'Gastos Médicos')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Gastos Médicos</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Gastos Médicos</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <button class="btn-primary" onclick="openModal('modal-nuevo-gasto')">
            <i class='bx bx-plus'></i> Nuevo Gasto
        </button>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3><i class='bx bx-wallet-alt'></i> Listado de Gastos Médicos</h3>
        <span class="badge badge-info">{{ $gastos->total() }} registros</span>
        @if($search)
            <span class="search-active-badge">
                <i class='bx bx-search'></i> "{{ $search }}"
                <a href="{{ route('gastos-medicos.index') }}">✕</a>
            </span>
        @endif
    </div>

    {{-- Buscador --}}
    <div style="padding:12px 16px;border-bottom:1px solid var(--border-color);">
        <form method="GET" style="display:flex;gap:8px;align-items:center;">
            <div style="position:relative;flex:1;max-width:360px;">
                <i class='bx bx-search' style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por paciente o número..."
                    style="width:100%;padding:8px 10px 8px 32px;border:1px solid var(--border-color);border-radius:8px;background:var(--bg-body);color:var(--text-primary);font-size:13px;">
            </div>
            <button type="submit" class="btn-primary" style="font-size:13px;padding:8px 14px;">Buscar</button>
            @if($search)
                <a href="{{ route('gastos-medicos.index') }}" class="btn-secondary" style="font-size:13px;padding:8px 14px;">Limpiar</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>N° Documento</th>
                    <th>Paciente</th>
                    <th>Fecha</th>
                    <th>Empresa</th>
                    <th>Ítems</th>
                    <th>Total (Bs.)</th>
                    <th>Médico</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gastos as $i => $gasto)
                <tr>
                    <td>{{ $gastos->firstItem() + $i }}</td>
                    <td><span style="font-weight:700;color:var(--primary);">{{ $gasto->numero_gasto }}</span></td>
                    <td>
                        <div class="cell-user">
                            <img src="" data-avatar="{{ $gasto->paciente->nombre_completo }}" data-bg="#0d6efd" data-size="32" alt="">
                            <div>
                                <span>{{ $gasto->paciente->nombre_completo }}</span><br>
                                <small style="color:var(--text-muted);">{{ $gasto->paciente->cedula }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $gasto->fecha->format('d/m/Y') }}</td>
                    <td style="font-size:12px;">{{ $gasto->nombre_empresa ?? '—' }}</td>
                    <td>
                        <span class="badge badge-secondary">{{ $gasto->items->count() }} ítem(s)</span>
                    </td>
                    <td>
                        <span style="font-weight:700;color:var(--success);">
                            {{ number_format($gasto->total, 2) }}
                        </span>
                    </td>
                    <td style="font-size:12px;">{{ $gasto->medico_nombre }}</td>
                    <td class="actions">
                        <a href="{{ route('expediente.show', $gasto->paciente_id) }}"
                           class="btn-icon btn-primary" title="Ver Expediente">
                            <i class='bx bx-folder-open'></i>
                        </a>
                        <a href="{{ route('gastos-medicos.imprimir', $gasto->id) }}" target="_blank"
                           class="btn-icon btn-edit" title="Imprimir PDF">
                            <i class='bx bx-printer'></i>
                        </a>
                        <button class="btn-icon btn-delete"
                                onclick="confirmarEliminarGasto({{ $gasto->id }}, '{{ addslashes($gasto->numero_gasto) }}')"
                                title="Eliminar">
                            <i class='bx bx-trash'></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="empty-row">
                        <div class="empty-state">
                            <i class='bx bx-wallet-alt'></i>
                            <p>No hay gastos médicos registrados</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">{{ $gastos->links('partials.pagination') }}</div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     MODAL: NUEVO GASTO MÉDICO
════════════════════════════════════════════════════════════════════ --}}
<div class="modal" id="modal-nuevo-gasto">
    <div class="modal-content" style="max-width:660px;">
        <div class="modal-header">
            <h2><i class='bx bx-wallet-alt'></i> Nuevo Gasto Médico</h2>
            <button class="modal-close" onclick="closeModal('modal-nuevo-gasto')">&times;</button>
        </div>
        <form id="form-nuevo-gasto" class="modal-form" enctype="multipart/form-data">
            @csrf

            {{-- ── Selector tipo ─────────────────────────────────────── --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
                <label id="tab-gasto-digital"
                    onclick="switchTipoGasto('digital')"
                    style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px 16px;
                           border:2px solid var(--primary);border-radius:10px;font-weight:600;
                           background:rgba(13,110,253,.08);transition:all .2s;">
                    <input type="radio" name="tipo_documento" value="digital" checked style="display:none;">
                    <span style="width:34px;height:34px;border-radius:8px;background:var(--primary);
                                 display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;">
                        <i class='bx bx-list-ul'></i>
                    </span>
                    <div>
                        <div style="font-size:13px;">Digital</div>
                        <div style="font-size:11px;font-weight:400;color:var(--text-muted);">Llenar ítems</div>
                    </div>
                </label>
                <label id="tab-gasto-manuscrita"
                    onclick="switchTipoGasto('manuscrita')"
                    style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px 16px;
                           border:2px solid var(--border-color);border-radius:10px;font-weight:600;
                           background:transparent;transition:all .2s;">
                    <input type="radio" name="tipo_documento" value="manuscrita" style="display:none;">
                    <span style="width:34px;height:34px;border-radius:8px;background:var(--border-color);
                                 display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:16px;">
                        <i class='bx bx-scan'></i>
                    </span>
                    <div>
                        <div style="font-size:13px;">Manuscrita</div>
                        <div style="font-size:11px;font-weight:400;color:var(--text-muted);">Subir escaneo</div>
                    </div>
                </label>
            </div>

            {{-- Campos comunes --}}
            <div class="form-row">
                <div class="form-group">
                    <label>Paciente *</label>
                    <select name="paciente_id" required>
                        <option value="">Seleccionar paciente</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre_completo }} — {{ $p->cedula }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Médico / Responsable</label>
                    <select name="empleado_id">
                        <option value="">Seleccionar médico</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre_completo }} — {{ $m->cargo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre de la Empresa</label>
                    <input type="text" name="nombre_empresa" placeholder="Empresa del trabajador">
                </div>
                <div class="form-group">
                    <label>Fecha *</label>
                    <input type="date" name="fecha" required value="{{ date('Y-m-d') }}">
                </div>
            </div>

            {{-- Sección manuscrita --}}
            <div id="sect-gasto-manuscrita" style="display:none;">
                <div class="form-group">
                    <label>Archivo Escaneado * <small style="color:var(--text-muted);font-weight:400;">(JPG, PNG, WEBP o PDF — máx. 5MB)</small></label>
                    <div style="border:2px dashed var(--primary);border-radius:10px;padding:24px;text-align:center;
                                background:rgba(13,110,253,.03);cursor:pointer;"
                         onclick="document.getElementById('file-gasto').click()">
                        <i class='bx bx-cloud-upload' style="font-size:32px;color:var(--primary);display:block;margin-bottom:6px;"></i>
                        <div style="font-size:13px;font-weight:600;color:var(--primary);">Haz clic o arrastra el archivo aquí</div>
                        <div id="file-gasto-name" style="font-size:12px;color:var(--text-muted);margin-top:4px;">Ningún archivo seleccionado</div>
                        <input type="file" id="file-gasto" name="imagen_escaneo"
                               accept="image/*,.pdf" style="display:none;"
                               onchange="mostrarNombreArchivo(this,'file-gasto-name')">
                    </div>
                </div>
            </div>

            {{-- Sección digital --}}
            <div id="sect-gasto-digital">
                <div class="form-group">
                    <label>Descripción / Motivo</label>
                    <textarea name="descripcion" rows="2" placeholder="Motivo del gasto médico"></textarea>
                </div>
                <div class="form-group">
                    <label>Ítems del Gasto
                        <button type="button" onclick="agregarItemGasto()"
                            style="margin-left:8px;background:rgba(13,110,253,.1);border:1px solid rgba(13,110,253,.3);
                                   color:var(--primary);border-radius:6px;padding:2px 8px;font-size:11px;cursor:pointer;">
                            <i class='bx bx-plus'></i> Agregar ítem
                        </button>
                    </label>
                    <div id="items-gasto-container">
                        <div class="gasto-item-row" style="display:flex;gap:8px;margin-bottom:6px;align-items:center;">
                            <input type="text" name="items[0][concepto]" placeholder="Concepto del gasto"
                                   style="flex:2;" required oninput="calcularTotalGasto()">
                            <input type="number" name="items[0][monto]" placeholder="Monto" step="0.01" min="0"
                                   style="flex:1;" required oninput="calcularTotalGasto()">
                            <button type="button" onclick="this.closest('.gasto-item-row').remove(); calcularTotalGasto();"
                                style="background:rgba(220,53,69,.1);border:1px solid rgba(220,53,69,.3);color:#dc3545;
                                       border-radius:6px;padding:6px 10px;cursor:pointer;">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                    <div style="text-align:right;margin-top:8px;font-size:14px;font-weight:700;color:var(--primary);">
                        Total: <span id="total-gasto-display">0.00</span> Bs.
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-nuevo-gasto')">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class='bx bx-save'></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal confirmación eliminar --}}
<div class="modal" id="modal-delete-gasto">
    <div class="modal-content" style="max-width:400px;">
        <div class="modal-header" style="background:linear-gradient(135deg,#dc3545,#c82333);color:#fff;">
            <h2><i class='bx bx-trash'></i> Eliminar Gasto Médico</h2>
            <button class="modal-close" onclick="closeModal('modal-delete-gasto')" style="color:#fff;">&times;</button>
        </div>
        <div class="modal-form" style="text-align:center;padding:24px;">
            <i class='bx bx-error-circle' style="font-size:48px;color:#dc3545;"></i>
            <p style="margin:12px 0;">¿Eliminar el gasto <strong id="del-gasto-num"></strong>?</p>
            <p style="font-size:12px;color:var(--text-muted);">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal('modal-delete-gasto')">Cancelar</button>
            <button class="btn-danger" id="btn-confirmar-del-gasto" onclick="ejecutarEliminarGasto()"><i class='bx bx-trash'></i> Eliminar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Ítems dinámicos ──────────────────────────────────────────────────────────
let gastoItemIdx = 1;

function agregarItemGasto() {
    const container = document.getElementById('items-gasto-container');
    const idx = gastoItemIdx++;
    const row = document.createElement('div');
    row.className = 'gasto-item-row';
    row.style.cssText = 'display:flex;gap:8px;margin-bottom:6px;align-items:center;';
    row.innerHTML = `
        <input type="text" name="items[${idx}][concepto]" placeholder="Concepto del gasto"
               style="flex:2;" required oninput="calcularTotalGasto()">
        <input type="number" name="items[${idx}][monto]" placeholder="Monto" step="0.01" min="0"
               style="flex:1;" required oninput="calcularTotalGasto()">
        <button type="button" onclick="this.closest('.gasto-item-row').remove(); calcularTotalGasto();"
            style="background:rgba(220,53,69,.1);border:1px solid rgba(220,53,69,.3);color:#dc3545;border-radius:6px;padding:6px 10px;cursor:pointer;">
            <i class='bx bx-trash'></i>
        </button>`;
    container.appendChild(row);
}

function calcularTotalGasto() {
    let total = 0;
    document.querySelectorAll('#items-gasto-container input[type=number]').forEach(inp => {
        total += parseFloat(inp.value) || 0;
    });
    document.getElementById('total-gasto-display').textContent = total.toFixed(2);
}

function switchTipoGasto(tipo) {
    const isDigital = tipo === 'digital';
    document.getElementById('sect-gasto-digital').style.display    = isDigital ? 'block' : 'none';
    document.getElementById('sect-gasto-manuscrita').style.display = isDigital ? 'none'  : 'block';
    document.getElementById('sect-gasto-digital').querySelectorAll('input,select,textarea').forEach(el => {
        el.disabled = !isDigital;
    });
    document.getElementById('sect-gasto-manuscrita').querySelectorAll('input,select,textarea').forEach(el => {
        el.disabled = isDigital;
    });
    const tabD = document.getElementById('tab-gasto-digital');
    const tabM = document.getElementById('tab-gasto-manuscrita');
    if (isDigital) {
        tabD.style.borderColor = 'var(--primary)'; tabD.style.background = 'rgba(13,110,253,.08)';
        tabD.querySelector('span').style.background = 'var(--primary)'; tabD.querySelector('span').style.color = '#fff';
        tabM.style.borderColor = 'var(--border-color)'; tabM.style.background = 'transparent';
        tabM.querySelector('span').style.background = 'var(--border-color)'; tabM.querySelector('span').style.color = 'var(--text-muted)';
    } else {
        tabM.style.borderColor = 'var(--primary)'; tabM.style.background = 'rgba(13,110,253,.08)';
        tabM.querySelector('span').style.background = 'var(--primary)'; tabM.querySelector('span').style.color = '#fff';
        tabD.style.borderColor = 'var(--border-color)'; tabD.style.background = 'transparent';
        tabD.querySelector('span').style.background = 'var(--border-color)'; tabD.querySelector('span').style.color = 'var(--text-muted)';
    }
    document.querySelector('#form-nuevo-gasto [name=tipo_documento][value='+tipo+']').checked = true;
    // En manuscrita los ítems no son required
    document.querySelectorAll('#items-gasto-container input[required]').forEach(i => {
        i.required = isDigital;
    });
}

function mostrarNombreArchivo(input, targetId) {
    const el = document.getElementById(targetId);
    if (el && input.files && input.files[0]) {
        el.textContent = input.files[0].name;
        el.style.color = 'var(--success, #198754)';
    }
}

// ── Guardar gasto ────────────────────────────────────────────────────────────
document.getElementById('form-nuevo-gasto').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('[type=submit]');
    btn.disabled = true;
    fetch('{{ route("gastos-medicos.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body: new FormData(this),
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast(d.message, 'success');
            closeModal('modal-nuevo-gasto');
            if (d.tipo_documento !== 'manuscrita') {
                window.open('{{ url("gastos-medicos") }}/' + d.id + '/imprimir', '_blank');
            }
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(d.message || 'Error al guardar.', 'error');
        }
    })
    .catch(() => showToast('Error de conexión.', 'error'))
    .finally(() => { btn.disabled = false; });
});

// ── Eliminar gasto ───────────────────────────────────────────────────────────
let deleteGastoId = null;
function confirmarEliminarGasto(id, num) {
    deleteGastoId = id;
    document.getElementById('del-gasto-num').textContent = num;
    openModal('modal-delete-gasto');
}

function ejecutarEliminarGasto() {
    if (!deleteGastoId) return;
    const btn = document.getElementById('btn-confirmar-del-gasto');
    btn.disabled = true;
    btn.textContent = 'Eliminando...';

    const url = window.BASE_URL + '/gastos-medicos/' + deleteGastoId;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.CSRF_TOKEN,
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: '_method=DELETE',
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) {
            showToast(d.message, 'success');
            closeModal('modal-delete-gasto');
            setTimeout(function() { location.reload(); }, 800);
        } else {
            showToast(d.message || 'Error al eliminar.', 'error');
        }
    })
    .catch(function(err) { showToast('Error: ' + err.message, 'error'); })
    .finally(function() {
        btn.disabled = false;
        btn.textContent = 'Eliminar';
        deleteGastoId = null;
    });
}
</script>
@endpush
