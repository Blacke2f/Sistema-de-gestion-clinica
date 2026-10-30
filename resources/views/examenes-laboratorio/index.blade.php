@extends('layouts.app')

@section('title', 'Exámenes de Laboratorio')
@section('nav-title', 'Exámenes de Laboratorio')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Solicitudes de Exámenes de Laboratorio</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Exámenes de Laboratorio</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <button class="btn-primary" onclick="openModal('modal-nuevo-examen')">
            <i class='bx bx-plus'></i> Nueva Solicitud
        </button>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3><i class='bx bx-test-tube'></i> Listado de Solicitudes</h3>
        <span class="badge badge-info">{{ $examenes->total() }} registros</span>
        @if($search)
            <span class="search-active-badge">
                <i class='bx bx-search'></i> "{{ $search }}"
                <a href="{{ route('examenes-laboratorio.index') }}">✕</a>
            </span>
        @endif
    </div>

    {{-- Buscador --}}
    <div class="filter-toolbar">
        <form method="GET" class="filter-toolbar-form">
            <div class="filter-search-box">
                <i class='bx bx-search'></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por paciente, cédula o N° de solicitud...">
            </div>
            <button type="submit" class="btn-primary btn-sm"><i class='bx bx-search'></i> Buscar</button>
            @if($search)
                <a href="{{ route('examenes-laboratorio.index') }}" class="btn-secondary btn-sm"><i class='bx bx-x'></i> Limpiar</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>N° Solicitud</th>
                    <th>Paciente</th>
                    <th>Fecha</th>
                    <th>Exámenes Solicitados</th>
                    <th>Médico</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($examenes as $i => $exa)
                <tr>
                    <td>{{ $examenes->firstItem() + $i }}</td>
                    <td><span style="font-weight:700;color:var(--primary);">{{ $exa->numero_solicitud }}</span></td>
                    <td>
                        <div class="cell-user">
                            <img src="" data-avatar="{{ $exa->paciente->nombre_completo }}" data-bg="#0d6efd" data-size="32" alt="">
                            <div>
                                <span>{{ $exa->paciente->nombre_completo }}</span><br>
                                <small style="color:var(--text-muted);">{{ $exa->paciente->cedula }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $exa->fecha->format('d/m/Y') }}</td>
                    <td style="max-width:260px;">
                        @php $marcados = $exa->examenes_solicitados; @endphp
                        @if(count($marcados) > 0)
                            @foreach(array_slice($marcados, 0, 3) as $ex)
                                <span class="badge badge-primary" style="font-size:10px;margin:1px 2px 1px 0;">{{ $ex }}</span>
                            @endforeach
                            @if(count($marcados) > 3)
                                <span class="badge badge-secondary" style="font-size:10px;">+{{ count($marcados) - 3 }} más</span>
                            @endif
                        @else
                            <span style="color:var(--text-muted);font-size:12px;">Sin exámenes marcados</span>
                        @endif
                    </td>
                    <td style="font-size:12px;">{{ $exa->medico_nombre }}</td>
                    <td class="actions">
                        <a href="{{ route('expediente.show', $exa->paciente_id) }}"
                           class="btn-icon btn-primary" title="Ver Expediente">
                            <i class='bx bx-folder-open'></i>
                        </a>
                        <a href="{{ route('examenes-laboratorio.imprimir', $exa->id) }}" target="_blank"
                           class="btn-icon btn-edit" title="Imprimir PDF">
                            <i class='bx bx-printer'></i>
                        </a>
                        <button class="btn-icon btn-delete"
                                onclick="confirmarEliminarExamen({{ $exa->id }}, '{{ addslashes($exa->numero_solicitud) }}')"
                                title="Eliminar">
                            <i class='bx bx-trash'></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-row">
                        <div class="empty-state">
                            <i class='bx bx-test-tube'></i>
                            <p>No hay solicitudes de exámenes registradas</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">{{ $examenes->links('partials.pagination') }}</div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     MODAL: NUEVA SOLICITUD DE EXÁMENES
════════════════════════════════════════════════════════════════════ --}}
<div class="modal" id="modal-nuevo-examen">
    <div class="modal-content" style="max-width:600px;">
        <div class="modal-header">
            <h2><i class='bx bx-test-tube'></i> Nueva Solicitud de Exámenes</h2>
            <button class="modal-close" onclick="closeModal('modal-nuevo-examen')">&times;</button>
        </div>
        <form id="form-nuevo-examen" class="modal-form" enctype="multipart/form-data">
            @csrf

            {{-- ── Selector tipo ─────────────────────────────────────── --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
                <label id="tab-examen-digital"
                    onclick="switchTipoExamen('digital')"
                    style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px 16px;
                           border:2px solid var(--primary);border-radius:10px;font-weight:600;
                           background:rgba(13,110,253,.08);transition:all .2s;">
                    <input type="radio" name="tipo_documento" value="digital" checked style="display:none;">
                    <span style="width:34px;height:34px;border-radius:8px;background:var(--primary);
                                 display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;">
                        <i class='bx bx-check-square'></i>
                    </span>
                    <div>
                        <div style="font-size:13px;">Digital</div>
                        <div style="font-size:11px;font-weight:400;color:var(--text-muted);">Marcar exámenes</div>
                    </div>
                </label>
                <label id="tab-examen-manuscrita"
                    onclick="switchTipoExamen('manuscrita')"
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
                    <label>Médico Solicitante</label>
                    <select name="empleado_id">
                        <option value="">Seleccionar médico</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre_completo }} — {{ $m->cargo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group" style="max-width:180px;">
                    <label>Fecha *</label>
                    <input type="date" name="fecha" required value="{{ date('Y-m-d') }}">
                </div>
            </div>

            {{-- Sección manuscrita --}}
            <div id="sect-examen-manuscrita" style="display:none;">
                <div class="form-group">
                    <label>Archivo Escaneado * <small style="color:var(--text-muted);font-weight:400;">(JPG, PNG, WEBP o PDF — máx. 5MB)</small></label>
                    <div style="border:2px dashed var(--primary);border-radius:10px;padding:24px;text-align:center;
                                background:rgba(13,110,253,.03);cursor:pointer;"
                         onclick="document.getElementById('file-examen').click()">
                        <i class='bx bx-cloud-upload' style="font-size:32px;color:var(--primary);display:block;margin-bottom:6px;"></i>
                        <div style="font-size:13px;font-weight:600;color:var(--primary);">Haz clic o arrastra el archivo aquí</div>
                        <div id="file-examen-name" style="font-size:12px;color:var(--text-muted);margin-top:4px;">Ningún archivo seleccionado</div>
                        <input type="file" id="file-examen" name="imagen_escaneo" disabled
                               accept="image/*,.pdf" style="display:none;"
                               onchange="mostrarNombreArchivo(this,'file-examen-name')">
                    </div>
                </div>
            </div>

            {{-- Sección digital --}}
            <div id="sect-examen-digital">
                <div class="form-group">
                    <label style="font-weight:700;margin-bottom:10px;display:block;">Exámenes a Solicitar</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        <div>
                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--primary);
                                        letter-spacing:0.8px;border-bottom:1px solid var(--border-color);
                                        padding-bottom:4px;margin-bottom:8px;">Hematología</div>
                            <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;
                                          padding:7px 8px;border-radius:8px;border:1px solid var(--border-color);">
                                <input type="checkbox" name="hematologia_completa" value="1" style="width:15px;height:15px;">
                                Hematología Completa
                            </label>
                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--primary);
                                        letter-spacing:0.8px;border-bottom:1px solid var(--border-color);
                                        padding-bottom:4px;margin-bottom:8px;margin-top:12px;">Química Sanguínea</div>
                            <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;padding:7px 8px;border-radius:8px;border:1px solid var(--border-color);margin-bottom:4px;">
                                <input type="checkbox" name="glicemia_ayunas" value="1" style="width:15px;height:15px;">
                                Glicemia en Ayunas
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;padding:7px 8px;border-radius:8px;border:1px solid var(--border-color);margin-bottom:4px;">
                                <input type="checkbox" name="urea" value="1" style="width:15px;height:15px;">
                                Urea
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;padding:7px 8px;border-radius:8px;border:1px solid var(--border-color);">
                                <input type="checkbox" name="creatinina" value="1" style="width:15px;height:15px;">
                                Creatinina
                            </label>
                        </div>
                        <div>
                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--primary);
                                        letter-spacing:0.8px;border-bottom:1px solid var(--border-color);
                                        padding-bottom:4px;margin-bottom:8px;">Lípidos</div>
                            <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;padding:7px 8px;border-radius:8px;border:1px solid var(--border-color);margin-bottom:4px;">
                                <input type="checkbox" name="colesterol_total" value="1" style="width:15px;height:15px;">
                                Colesterol Total
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;padding:7px 8px;border-radius:8px;border:1px solid var(--border-color);">
                                <input type="checkbox" name="trigliceridos" value="1" style="width:15px;height:15px;">
                                Triglicéridos
                            </label>
                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--primary);
                                        letter-spacing:0.8px;border-bottom:1px solid var(--border-color);
                                        padding-bottom:4px;margin-bottom:8px;margin-top:12px;">Uroanálisis</div>
                            <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;padding:7px 8px;border-radius:8px;border:1px solid var(--border-color);margin-bottom:4px;">
                                <input type="checkbox" name="examen_orina" value="1" style="width:15px;height:15px;">
                                Examen de Orina
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;padding:7px 8px;border-radius:8px;border:1px solid var(--border-color);">
                                <input type="checkbox" name="examen_heces" value="1" style="width:15px;height:15px;">
                                Examen de Heces
                            </label>
                        </div>
                    </div>
                    <div style="margin-top:12px;">
                        <label style="font-weight:700;display:block;margin-bottom:4px;">Otros exámenes</label>
                        <textarea name="otros" rows="2" placeholder="Especifique otros exámenes no listados..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-nuevo-examen')">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class='bx bx-save'></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal confirmación eliminar --}}
<div class="modal" id="modal-delete-examen">
    <div class="modal-content" style="max-width:400px;">
        <div class="modal-header" style="background:linear-gradient(135deg,#dc3545,#c82333);color:#fff;">
            <h2><i class='bx bx-trash'></i> Eliminar Solicitud</h2>
            <button class="modal-close" onclick="closeModal('modal-delete-examen')" style="color:#fff;">&times;</button>
        </div>
        <div class="modal-form" style="text-align:center;padding:24px;">
            <i class='bx bx-error-circle' style="font-size:48px;color:#dc3545;"></i>
            <p style="margin:12px 0;">¿Eliminar la solicitud <strong id="del-examen-num"></strong>?</p>
            <p style="font-size:12px;color:var(--text-muted);">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal('modal-delete-examen')">Cancelar</button>
            <button class="btn-danger" id="btn-confirmar-del-examen" onclick="ejecutarEliminarExamen()"><i class='bx bx-trash'></i> Eliminar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Guardar solicitud ────────────────────────────────────────────────────────
document.getElementById('form-nuevo-examen').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('[type=submit]');
    btn.disabled = true;
    fetch('{{ route("examenes-laboratorio.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body: new FormData(this),
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast(d.message, 'success');
            closeModal('modal-nuevo-examen');
            if (d.tipo_documento !== 'manuscrita') {
                window.open('{{ url("examenes-laboratorio") }}/' + d.id + '/imprimir', '_blank');
            }
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(d.message || 'Error al guardar.', 'error');
        }
    })
    .catch(() => showToast('Error de conexión.', 'error'))
    .finally(() => { btn.disabled = false; });
});

function switchTipoExamen(tipo) {
    const isDigital = tipo === 'digital';
    document.getElementById('sect-examen-digital').style.display    = isDigital ? 'block' : 'none';
    document.getElementById('sect-examen-manuscrita').style.display = isDigital ? 'none'  : 'block';
    document.getElementById('sect-examen-digital').querySelectorAll('input,select,textarea').forEach(el => {
        el.disabled = !isDigital;
    });
    document.getElementById('sect-examen-manuscrita').querySelectorAll('input,select,textarea').forEach(el => {
        el.disabled = isDigital;
    });
    const tabD = document.getElementById('tab-examen-digital');
    const tabM = document.getElementById('tab-examen-manuscrita');
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
    document.querySelector('#form-nuevo-examen [name=tipo_documento][value='+tipo+']').checked = true;
}

function mostrarNombreArchivo(input, targetId) {
    const el = document.getElementById(targetId);
    if (el && input.files && input.files[0]) {
        el.textContent = input.files[0].name;
        el.style.color = 'var(--success, #198754)';
    }
}

// ── Eliminar solicitud ───────────────────────────────────────────────────────
let deleteExamenId = null;
function confirmarEliminarExamen(id, num) {
    deleteExamenId = id;
    document.getElementById('del-examen-num').textContent = num;
    openModal('modal-delete-examen');
}

function ejecutarEliminarExamen() {
    if (!deleteExamenId) return;
    const btn = document.getElementById('btn-confirmar-del-examen');
    btn.disabled = true;
    btn.textContent = 'Eliminando...';

    const url = window.BASE_URL + '/examenes-laboratorio/' + deleteExamenId;

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
            closeModal('modal-delete-examen');
            setTimeout(function() { location.reload(); }, 800);
        } else {
            showToast(d.message || 'Error al eliminar.', 'error');
        }
    })
    .catch(function(err) { showToast('Error: ' + err.message, 'error'); })
    .finally(function() {
        btn.disabled = false;
        btn.textContent = 'Eliminar';
        deleteExamenId = null;
    });
}
</script>
@endpush
