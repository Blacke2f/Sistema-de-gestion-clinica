@extends('layouts.app')

@section('title', 'Informes Médicos')
@section('nav-title', 'Informes Médicos')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Informes Médicos Ocupacionales</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Informes Médicos</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <button class="btn-primary" onclick="openModal('modal-nuevo-informe')">
            <i class='bx bx-plus'></i> Nuevo Informe
        </button>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3><i class='bx bx-file-blank'></i> Listado de Informes Médicos</h3>
        <span class="badge badge-info">{{ $informes->total() }} registros</span>
        @if($search)
            <span class="search-active-badge">
                <i class='bx bx-search'></i> "{{ $search }}"
                <a href="{{ route('informes-medicos.index') }}">✕</a>
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
            @if($search)<a href="{{ route('informes-medicos.index') }}" class="btn-secondary" style="font-size:13px;padding:8px 14px;">Limpiar</a>@endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>N° Informe</th>
                    <th>Paciente</th>
                    <th>Fecha</th>
                    <th>Tipo Consulta</th>
                    <th>Aptitud</th>
                    <th>Médico</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($informes as $i => $inf)
                <tr>
                    <td>{{ $informes->firstItem() + $i }}</td>
                    <td><span style="font-weight:700;color:var(--primary);">{{ $inf->numero_informe }}</span></td>
                    <td>
                        <div class="cell-user">
                            <img src="" data-avatar="{{ $inf->paciente->nombre_completo }}" data-bg="#0d6efd" data-size="32" alt="">
                            <div>
                                <span>{{ $inf->paciente->nombre_completo }}</span><br>
                                <small style="color:var(--text-muted);">{{ $inf->paciente->cedula }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $inf->fecha->format('d/m/Y') }}</td>
                    <td>
                        @if($inf->tipo_consulta)
                        <span class="badge badge-primary" style="font-size:11px;">{{ $inf->tipo_consulta_texto }}</span>
                        @else
                        <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($inf->aptitud)
                        <span class="badge badge-{{ $inf->aptitud === 'apto' ? 'success' : ($inf->aptitud === 'no_apto' ? 'danger' : 'warning') }}">
                            {{ $inf->aptitud_texto }}
                        </span>
                        @else
                        <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td style="font-size:12px;">{{ $inf->medico_nombre }}</td>
                    <td class="actions">
                        <a href="{{ route('expediente.show', $inf->paciente_id) }}" class="btn-icon btn-primary" title="Ver Expediente"><i class='bx bx-folder-open'></i></a>
                        <a href="{{ route('informes-medicos.imprimir', $inf->id) }}" target="_blank" class="btn-icon btn-edit" title="Imprimir PDF"><i class='bx bx-printer'></i></a>
                        <button class="btn-icon btn-delete" onclick="confirmarEliminarInforme({{ $inf->id }}, '{{ addslashes($inf->numero_informe) }}')" title="Eliminar"><i class='bx bx-trash'></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="empty-row">
                    <div class="empty-state"><i class='bx bx-file-blank'></i><p>No hay informes registrados</p></div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $informes->links('partials.pagination') }}</div>
</div>

{{-- MODAL NUEVO INFORME --}}
<div class="modal" id="modal-nuevo-informe">
    <div class="modal-content" style="max-width:700px;">
        <div class="modal-header">
            <h2><i class='bx bx-file-plus'></i> Nuevo Informe Médico</h2>
            <button class="modal-close" onclick="closeModal('modal-nuevo-informe')">&times;</button>
        </div>
        <form id="form-nuevo-informe" class="modal-form" enctype="multipart/form-data">
            @csrf

            {{-- ── Selector tipo ─────────────────────────────────────── --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
                <label id="tab-inf-digital"
                    onclick="switchTipoInforme('digital')"
                    style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px 16px;
                           border:2px solid var(--primary);border-radius:10px;font-weight:600;
                           background:rgba(13,110,253,.08);transition:all .2s;">
                    <input type="radio" name="tipo_documento" value="digital" checked style="display:none;">
                    <span style="width:34px;height:34px;border-radius:8px;background:var(--primary);
                                 display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;">
                        <i class='bx bx-file-blank'></i>
                    </span>
                    <div>
                        <div style="font-size:13px;">Digital</div>
                        <div style="font-size:11px;font-weight:400;color:var(--text-muted);">Llenar formulario</div>
                    </div>
                </label>
                <label id="tab-inf-manuscrita"
                    onclick="switchTipoInforme('manuscrita')"
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

            {{-- ── Sección: Subir escaneo ────────────────────────────── --}}
            <div id="sect-inf-manuscrita" style="display:none;">
                <div class="form-row">
                    <div class="form-group">
                        <label>Paciente *</label>
                        <select name="paciente_id_man_inf" required>
                            <option value="">Seleccionar paciente</option>
                            @foreach($pacientes as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre_completo }} — {{ $p->cedula }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha *</label>
                        <input type="date" name="fecha_man_inf" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Archivo Escaneado * <small style="color:var(--text-muted);font-weight:400;">(JPG, PNG, WEBP o PDF — máx. 5MB)</small></label>
                    <div style="border:2px dashed var(--primary);border-radius:10px;padding:24px;text-align:center;
                                background:rgba(13,110,253,.03);cursor:pointer;position:relative;"
                         onclick="document.getElementById('file-inf').click()">
                        <i class='bx bx-cloud-upload' style="font-size:32px;color:var(--primary);display:block;margin-bottom:6px;"></i>
                        <div style="font-size:13px;font-weight:600;color:var(--primary);">Haz clic o arrastra el archivo aquí</div>
                        <div id="file-inf-name" style="font-size:12px;color:var(--text-muted);margin-top:4px;">Ningún archivo seleccionado</div>
                        <input type="file" id="file-inf" name="imagen_escaneo"
                               accept="image/*,.pdf" style="display:none;"
                               onchange="mostrarNombreArchivo(this,'file-inf-name')">
                    </div>
                </div>
            </div>

            {{-- ── Sección: Formulario digital ──────────────────────── --}}
            <div id="sect-inf-digital">
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
                                <option value="{{ $m->id }}">{{ $m->nombre_completo }}</option>
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
                <div class="form-row">
                    <div class="form-group">
                        <label>Cargo</label>
                        <input type="text" name="cargo" placeholder="Cargo del trabajador">
                    </div>
                    <div class="form-group">
                        <label>Antigüedad en el Cargo</label>
                        <input type="text" name="antiguedad_cargo" placeholder="Ej: 2 años">
                    </div>
                </div>
                <div class="form-group">
                    <label>Tipo de Consulta / Actitud</label>
                    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:6px;">
                        @foreach(['preempleo'=>'Pre-empleo','prevacacional'=>'Pre-vacacional','postvacacional'=>'Post-vacacional','egreso'=>'Egreso','reubicacion'=>'Reubicación','otros'=>'Otros'] as $val => $lbl)
                        <label style="display:flex;align-items:center;gap:6px;font-weight:400;cursor:pointer;">
                            <input type="radio" name="tipo_consulta" value="{{ $val }}"
                                   onchange="document.getElementById('otros-wrap-idx').style.display=this.value==='otros'?'block':'none'">
                            {{ $lbl }}
                        </label>
                        @endforeach
                    </div>
                    <div id="otros-wrap-idx" style="display:none;margin-top:6px;">
                        <input type="text" name="tipo_consulta_otros" placeholder="Especifique...">
                    </div>
                </div>
                <div class="form-group">
                    <label>Resultado / Aptitud</label>
                    <div style="display:flex;gap:20px;margin-top:6px;">
                        @foreach(['apto'=>'Apto','no_apto'=>'No Apto','apto_condicionado'=>'Apto Condicionado'] as $val => $lbl)
                        <label style="display:flex;align-items:center;gap:6px;font-weight:400;cursor:pointer;">
                            <input type="radio" name="aptitud" value="{{ $val }}">{{ $lbl }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label>Diagnóstico</label>
                    <textarea name="diagnostico" rows="2" placeholder="Diagnóstico médico"></textarea>
                </div>
                <div class="form-group">
                    <label>Estudios Paraclínicos</label>
                    <textarea name="estudios_paraclinicos" rows="2" placeholder="Exámenes o estudios realizados"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Referencia</label>
                        <textarea name="referencia" rows="2" placeholder="Referencia a especialista"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Recomendaciones</label>
                        <textarea name="recomendaciones" rows="2" placeholder="Recomendaciones médicas"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-nuevo-informe')">Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-save'></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal confirmación eliminar --}}
<div class="modal" id="modal-delete-informe">
    <div class="modal-content" style="max-width:400px;">
        <div class="modal-header" style="background:linear-gradient(135deg,#dc3545,#c82333);color:#fff;">
            <h2><i class='bx bx-trash'></i> Eliminar Informe</h2>
            <button class="modal-close" onclick="closeModal('modal-delete-informe')" style="color:#fff;">&times;</button>
        </div>
        <div class="modal-form" style="text-align:center;padding:24px;">
            <i class='bx bx-error-circle' style="font-size:48px;color:#dc3545;"></i>
            <p style="margin:12px 0;">¿Eliminar el informe <strong id="del-informe-num"></strong>?</p>
            <p style="font-size:12px;color:var(--text-muted);">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal('modal-delete-informe')">Cancelar</button>
            <button class="btn-danger" id="btn-confirmar-del-informe" onclick="ejecutarEliminarInforme()"><i class='bx bx-trash'></i> Eliminar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Crear informe
document.getElementById('form-nuevo-informe').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('[type=submit]');
    btn.disabled = true;

    // Normalizar campos según el tipo
    const tipo = this.querySelector('[name=tipo_documento]').value;
    if (tipo === 'manuscrita') {
        // Copiar paciente_id y fecha del lado manuscrita al campo real
        const pid = this.querySelector('[name=paciente_id_man_inf]')?.value;
        const fec = this.querySelector('[name=fecha_man_inf]')?.value;
        if (pid) { let h = document.createElement('input'); h.type='hidden'; h.name='paciente_id'; h.value=pid; this.appendChild(h); }
        if (fec) { let h = document.createElement('input'); h.type='hidden'; h.name='fecha'; h.value=fec; this.appendChild(h); }
    }

    fetch('{{ route("informes-medicos.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body: new FormData(this),
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast(d.message, 'success');
            closeModal('modal-nuevo-informe');
            if (d.tipo_documento !== 'manuscrita') {
                window.open('{{ url("informes-medicos") }}/' + d.id + '/imprimir', '_blank');
            }
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(d.message || 'Error al guardar.', 'error');
        }
    })
    .catch(() => showToast('Error de conexión.', 'error'))
    .finally(() => { btn.disabled = false; });
});

function switchTipoInforme(tipo) {
    const isDigital = tipo === 'digital';
    document.getElementById('sect-inf-digital').style.display    = isDigital ? 'block' : 'none';
    document.getElementById('sect-inf-manuscrita').style.display = isDigital ? 'none'  : 'block';
    // Deshabilitar campos ocultos para que no bloqueen el submit
    document.getElementById('sect-inf-digital').querySelectorAll('input,select,textarea').forEach(el => {
        el.disabled = !isDigital;
    });
    document.getElementById('sect-inf-manuscrita').querySelectorAll('input,select,textarea').forEach(el => {
        el.disabled = isDigital;
    });
    // Estilo del tab activo
    const tabD = document.getElementById('tab-inf-digital');
    const tabM = document.getElementById('tab-inf-manuscrita');
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
    // Actualizar radio oculto
    document.querySelector('#form-nuevo-informe [name=tipo_documento][value='+tipo+']').checked = true;
}

function mostrarNombreArchivo(input, targetId) {
    const el = document.getElementById(targetId);
    if (el && input.files && input.files[0]) {
        el.textContent = input.files[0].name;
        el.style.color = 'var(--success, #198754)';
    }
}

// Eliminar informe
let deleteInformeId = null;
function confirmarEliminarInforme(id, num) {
    deleteInformeId = id;
    document.getElementById('del-informe-num').textContent = num;
    openModal('modal-delete-informe');
}

function ejecutarEliminarInforme() {
    if (!deleteInformeId) return;
    const btn = document.getElementById('btn-confirmar-del-informe');
    btn.disabled = true;
    btn.textContent = 'Eliminando...';

    const url = window.BASE_URL + '/informes-medicos/' + deleteInformeId;

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
            closeModal('modal-delete-informe');
            setTimeout(function() { location.reload(); }, 800);
        } else {
            showToast(d.message || 'Error al eliminar.', 'error');
        }
    })
    .catch(function(err) { showToast('Error: ' + err.message, 'error'); })
    .finally(function() {
        btn.disabled = false;
        btn.textContent = 'Eliminar';
        deleteInformeId = null;
    });
}
</script>
@endpush
