@extends('layouts.app')

@section('title', 'Recetas Médicas')
@section('nav-title', 'Recetas Médicas')

@push('styles')
<style>
.tipo-receta-tabs { display:flex; gap:8px; margin-bottom:16px; }
.tipo-tab { flex:1; padding:10px; border:2px solid var(--border-color); border-radius:10px; cursor:pointer; text-align:center; font-size:13px; font-weight:600; color:var(--text-secondary); background:var(--bg-body); transition:all .2s; }
.tipo-tab.active { border-color:var(--primary); background:rgba(13,110,253,.08); color:var(--primary); }
.tipo-tab i { font-size:20px; display:block; margin-bottom:4px; }
.upload-area { border:2px dashed var(--border-color); border-radius:12px; padding:30px; text-align:center; cursor:pointer; transition:all .2s; }
.upload-area:hover { border-color:var(--primary); background:rgba(13,110,253,.04); }
.upload-area i { font-size:40px; color:var(--text-muted); display:block; margin-bottom:8px; }
.upload-preview { max-width:100%; max-height:300px; border-radius:8px; margin-top:12px; display:none; }
.badge-manuscrita { background:rgba(111,66,193,.12); color:#6f42c1; }
.badge-digital    { background:rgba(13,110,253,.12); color:#0d6efd; }
/* Select del inventario - ocultar flecha nativa y usar la custom */
#receta-items select { -webkit-appearance:none; -moz-appearance:none; appearance:none; }
/* Opciones de dispensación */
.disp-opcion { transition: border-color .15s, background .15s, color .15s; }
.disp-opcion:hover { opacity:.85; }
/* Dark mode para los items de receta */
body.dark-mode #receta-items [style*="background:var(--bg-card)"] {
    border-color: #334155 !important;
}
body.dark-mode #receta-items select {
    background-color: #0f172a !important;
    border-color: #334155 !important;
    color: #f1f5f9 !important;
}
body.dark-mode #receta-items input {
    background-color: #0f172a !important;
    border-color: #334155 !important;
    color: #f1f5f9 !important;
}
/* Tarjeta de item de receta */
.receta-item-card {
    background: var(--bg-body);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 10px;
}
.receta-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.receta-item-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: .4px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.receta-item-title i { color: var(--primary); }
.btn-agregar-med {
    width: 100%;
    padding: 9px 16px;
    border: 1.5px dashed var(--border-color);
    border-radius: 8px;
    background: var(--bg-body);
    color: var(--primary);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 4px;
}
.btn-agregar-med:hover {
    background: rgba(13,110,253,.06);
    border-color: var(--primary);
    border-style: solid;
}
</style>
@endpush

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Recetas Médicas</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Recetas</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <button class="btn-primary" onclick="openModal('modal-receta')">
            <i class='bx bx-plus'></i> Nueva Receta
        </button>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3><i class='bx bx-file-blank'></i> Listado de Recetas</h3>
        <span class="badge badge-info">{{ $recetas->total() }} recetas</span>
    </div>

    <form method="GET" style="padding:12px 16px;display:flex;gap:10px;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por número o paciente..." class="form-control" style="max-width:280px;">
        <button type="submit" class="btn-primary btn-sm"><i class='bx bx-search'></i> Buscar</button>
        @if($search)<a href="{{ route('recetas.index') }}" class="btn-secondary btn-sm">Limpiar</a>@endif
    </form>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>N° Receta</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Medicamentos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recetas as $r)
                <tr>
                    <td><code>{{ $r->numero_receta }}</code></td>
                    <td>
                        <div class="cell-user">
                            <img src="" data-avatar="{{ $r->paciente->nombre_completo }}" data-bg="#0d6efd" data-size="32" alt="">
                            <span>{{ $r->paciente->nombre_completo }}</span>
                        </div>
                    </td>
                    <td>{{ $r->medico_nombre }}</td>
                    <td>
                        <span class="badge badge-{{ $r->tipo_receta === 'manuscrita' ? 'purple' : 'primary' }}">
                            <i class='bx bx-{{ $r->tipo_receta === 'manuscrita' ? 'pencil' : 'desktop' }}'></i>
                            {{ $r->tipo_receta === 'manuscrita' ? 'Manuscrita' : 'Digital' }}
                        </span>
                    </td>
                    <td>{{ $r->fecha_emision->format('d/m/Y') }}</td>
                    <td>
                        @if($r->tipo_receta === 'manuscrita')
                            <span class="badge badge-secondary"><i class='bx bx-image'></i> Imagen adjunta</span>
                        @else
                            <span class="badge badge-secondary">{{ $r->items->count() }} medicamento(s)</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $r->estado === 'activa' ? 'success' : ($r->estado === 'anulada' ? 'danger' : 'secondary') }}">
                            {{ ucfirst($r->estado) }}
                        </span>
                    </td>
                    <td class="actions">
                        @if($r->tipo_receta === 'manuscrita' && $r->imagen_receta)
                        <a href="{{ asset('storage/' . $r->imagen_receta) }}" target="_blank" class="btn-icon btn-primary" title="Ver imagen">
                            <i class='bx bx-image'></i>
                        </a>
                        @else
                        <a href="{{ route('recetas.imprimir', $r->id) }}" target="_blank" class="btn-icon btn-primary" title="Imprimir PDF">
                            <i class='bx bx-printer'></i>
                        </a>
                        @endif
                        @if($r->estado === 'activa')
                        <button class="btn-icon btn-delete" onclick="anularReceta({{ $r->id }}, '{{ $r->numero_receta }}')" title="Anular">
                            <i class='bx bx-x-circle'></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="empty-row">
                    <div class="empty-state"><i class='bx bx-file-blank'></i><p>No hay recetas registradas</p></div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $recetas->links('partials.pagination') }}</div>
</div>

{{-- MODAL NUEVA RECETA --}}
<div class="modal" id="modal-receta">
    <div class="modal-content" style="max-width:760px;">
        <div class="modal-header">
            <h2><i class='bx bx-file-plus'></i> Nueva Receta Médica</h2>
            <button class="modal-close" onclick="closeModal('modal-receta')">&times;</button>
        </div>
        <form id="form-receta" class="modal-form" enctype="multipart/form-data">
            @csrf

            {{-- Selector de tipo de receta --}}
            <div class="tipo-receta-tabs">
                <div class="tipo-tab active" id="tab-digital" onclick="setTipoReceta('digital')">
                    <i class='bx bx-desktop'></i>
                    Receta Digital
                    <small style="display:block;font-weight:400;font-size:11px;margin-top:2px;">Ingreso manual de medicamentos</small>
                </div>
                <div class="tipo-tab" id="tab-manuscrita" onclick="setTipoReceta('manuscrita')">
                    <i class='bx bx-pencil'></i>
                    Receta Manuscrita
                    <small style="display:block;font-weight:400;font-size:11px;margin-top:2px;">Subir foto o escaneo</small>
                </div>
            </div>
            <input type="hidden" name="tipo_receta" id="tipo_receta" value="digital">

            <div class="form-row">
                <div class="form-group">
                    <label><i class='bx bx-user'></i> Paciente *</label>
                    <select name="paciente_id" id="receta-paciente_id" required onchange="cargarConsultasPaciente(this.value)">
                        <option value="">Seleccionar paciente...</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}" data-alergias="{{ $p->alergias ?? '' }}" data-sangre="{{ $p->tipo_sangre ?? '' }}" data-edad="{{ $p->edad ?? '' }}">{{ $p->nombre_completo }} — {{ $p->cedula }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label><i class='bx bx-clipboard'></i> Consulta asociada</label>
                    <select name="consulta_id" id="receta-consulta_id">
                        <option value="">— Seleccione primero un paciente —</option>
                    </select>
                    <small style="color:var(--text-muted);font-size:11px;">Seleccionar la consulta vincula la receta al historial clínico</small>
                </div>
            </div>
            <div id="receta-alergia-banner" style="display:none;margin-bottom:14px;padding:10px 14px;border-radius:8px;font-size:12px;"></div>
            <div class="form-row" style="display:none;"><div class="form-group">
                </div>
            </div>

            {{-- SECCIÓN RECETA DIGITAL --}}
            <div id="seccion-digital">
                <div class="form-group">
                    <label><i class='bx bx-stethoscope'></i> Diagnóstico</label>
                    <textarea name="diagnostico" rows="2" placeholder="Diagnóstico médico..."></textarea>
                </div>
                <div class="form-group">
                    <label><i class='bx bx-note'></i> Indicaciones Generales</label>
                    <textarea name="indicaciones_generales" rows="2" placeholder="Indicaciones para el paciente..."></textarea>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <strong style="font-size:13px;color:var(--text-primary);display:flex;align-items:center;gap:6px;">
                        <i class='bx bxs-capsule' style="color:var(--primary);"></i> Medicamentos prescritos
                    </strong>
                </div>
                <div id="receta-items"></div>
                <button type="button" class="btn-agregar-med" onclick="agregarItemReceta()">
                    <i class='bx bx-plus-circle'></i> Agregar medicamento
                </button>
            </div>

            {{-- SECCIÓN RECETA MANUSCRITA --}}
            <div id="seccion-manuscrita" style="display:none;">
                <div class="form-group">
                    <label><i class='bx bx-image-add'></i> Imagen de la Receta *</label>
                    <div class="upload-area" onclick="document.getElementById('imagen_receta_input').click()">
                        <i class='bx bx-cloud-upload'></i>
                        <p style="font-size:14px;font-weight:600;color:var(--text-primary);margin:0;">Haz clic para subir la imagen</p>
                        <p style="font-size:12px;color:var(--text-muted);margin:4px 0 0;">JPG, PNG, PDF — máximo 5MB</p>
                        <img id="imagen-preview" class="upload-preview" src="" alt="Vista previa">
                    </div>
                    <input type="file" id="imagen_receta_input" name="imagen_receta"
                           accept="image/jpeg,image/png,image/webp,application/pdf"
                           style="display:none;" onchange="previewImagen(this)" disabled>
                </div>
                <div class="form-group">
                    <label><i class='bx bx-note'></i> Notas adicionales</label>
                    <textarea name="indicaciones_generales" rows="2" placeholder="Observaciones sobre la receta manuscrita..." disabled></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class='bx bx-calendar'></i> Vence</label>
                    <input type="date" name="fecha_vencimiento">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-receta')"><i class='bx bx-x'></i> Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-save'></i> Guardar Receta</button>
            </div>
        </form>
    </div>
</div>
{{-- MODAL DISPENSAR MEDICAMENTOS (aparece después de guardar receta digital) --}}
<div class="modal" id="modal-dispensar">
    <div class="modal-content" style="max-width:620px;">
        <div class="modal-header" style="background:linear-gradient(135deg,#0d6efd,#0b5ed7);color:#fff;">
            <h2><i class='bx bxs-capsule'></i> Dispensar Medicamentos</h2>
            <button class="modal-close" onclick="cerrarDispensarYRecargar()" style="color:#fff;">&times;</button>
        </div>
        <div class="modal-form" style="padding:20px 24px;">
            <div style="background:rgba(13,110,253,.07);border:1px solid rgba(13,110,253,.2);border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:var(--text-primary);display:flex;gap:10px;align-items:flex-start;">
                <i class='bx bx-info-circle' style="font-size:20px;color:var(--primary);flex-shrink:0;margin-top:1px;"></i>
                <div>
                    Receta <strong id="disp-numero-receta"></strong> guardada correctamente.<br>
                    <span style="color:var(--text-muted);font-size:12px;">Confirma qué medicamentos se entregan del inventario de la clínica. Si el paciente va a comprarlos por su cuenta, márcalos como "No disponible".</span>
                </div>
            </div>

            <form id="form-dispensar">
                @csrf
                <div id="disp-items-list">
                    {{-- Se llena dinámicamente con JS --}}
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="cerrarDispensarYRecargar()">
                <i class='bx bx-x'></i> Omitir dispensación
            </button>
            <button type="button" class="btn-primary" id="btn-confirmar-dispensar" onclick="confirmarDispensacion()">
                <i class='bx bxs-capsule'></i> Confirmar Dispensación
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const ROUTES_RECETAS = {
    store:              '{{ route('recetas.store') }}',
    anular:             '{{ url('recetas') }}/',
    dispensar:          '{{ url('recetas') }}/',   // + id + /dispensar
    consultasPaciente:  '{{ url('recetas/paciente') }}/',
};
const MEDICAMENTOS = {!! json_encode($medicamentos->map(function($m){ return ['id'=>$m->id,'nombre'=>$m->nombre_medicamento]; })->values()) !!};

let itemCount = 0;

// ── Cargar consultas del paciente seleccionado ────────────────────────────────
async function cargarConsultasPaciente(pacienteId) {
    const selPac = document.getElementById('receta-paciente_id');
    const banner = document.getElementById('receta-alergia-banner');
    if (selPac && banner) {
        const opt = selPac.options[selPac.selectedIndex];
        const alergias = opt ? (opt.dataset.alergias || '').trim() : '';
        const sangre   = opt ? (opt.dataset.sangre || '').trim() : '';
        const edad     = opt ? (opt.dataset.edad || '').trim() : '';
        let chips = [];
        if (edad)   chips.push('<strong>Edad:</strong> ' + edad + ' años');
        if (sangre) chips.push('<strong>Sangre:</strong> ' + sangre);
        if (opt && opt.value && alergias && !['ninguna', 'no', '-'].includes(alergias.toLowerCase())) {
            banner.style.display = 'block';
            banner.style.background = 'rgba(220,53,69,.1)';
            banner.style.border = '1px solid rgba(220,53,69,.35)';
            banner.innerHTML = '<div style="color:#dc3545;font-weight:700;"><i class="bx bx-error"></i> ⚠️ Alergias del paciente: ' + alergias + ' — Verificar antes de prescribir</div>'
                + (chips.length ? '<div style="margin-top:3px;font-size:11px;color:var(--text-secondary);">' + chips.join(' · ') + '</div>' : '');
        } else if (opt && opt.value && chips.length) {
            banner.style.display = 'block';
            banner.style.background = 'rgba(13,110,253,.07)';
            banner.style.border = '1px solid rgba(13,110,253,.2)';
            banner.innerHTML = '<i class="bx bx-user-check" style="color:var(--primary);"></i> ' + chips.join(' · ') + ' · Sin alergias reportadas';
        } else {
            banner.style.display = 'none';
        }
    }
    const sel = document.getElementById('receta-consulta_id');
    sel.innerHTML = '<option value="">Cargando consultas...</option>';

    if (!pacienteId) {
        sel.innerHTML = '<option value="">— Seleccione primero un paciente —</option>';
        return;
    }

    try {
        const res  = await fetch(ROUTES_RECETAS.consultasPaciente + pacienteId + '/consultas', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
        });
        const data = await res.json();

        if (data.success && data.data.length > 0) {
            sel.innerHTML = '<option value="">Sin consulta asociada</option>';
            data.data.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value       = c.id;
                opt.textContent = c.label + (c.medico ? ' (' + c.medico + ')' : '');
                sel.appendChild(opt);
            });
        } else {
            sel.innerHTML = '<option value="">Sin consultas recientes para este paciente</option>';
        }
    } catch(e) {
        sel.innerHTML = '<option value="">Error al cargar consultas</option>';
    }
}

// ── Tipo de receta ────────────────────────────────────────────────────────────
function setTipoReceta(tipo) {
    document.getElementById('tipo_receta').value = tipo;
    document.getElementById('tab-digital').classList.toggle('active', tipo === 'digital');
    document.getElementById('tab-manuscrita').classList.toggle('active', tipo === 'manuscrita');
    document.getElementById('seccion-digital').style.display    = tipo === 'digital'    ? 'block' : 'none';
    document.getElementById('seccion-manuscrita').style.display = tipo === 'manuscrita' ? 'block' : 'none';
    if (tipo === 'digital' && document.getElementById('receta-items').children.length === 0) {
        agregarItemReceta();
    }

    // ── Habilitar / deshabilitar required según la sección visible ─────────
    const seccDigital = document.getElementById('seccion-digital');
    seccDigital.querySelectorAll('input[required], select[required], textarea[required]').forEach(function(el) {
        if (tipo === 'digital') {
            el.removeAttribute('disabled');
        } else {
            // No podemos quitar required (lo necesitamos al volver a digital),
            // así que deshabilitamos el campo — los campos disabled son ignorados
            // por la validación nativa del navegador.
            el.disabled = true;
        }
    });

    const seccMan = document.getElementById('seccion-manuscrita');
    seccMan.querySelectorAll('input, select, textarea').forEach(function(el) {
        if (tipo === 'manuscrita') {
            el.disabled = false;
        } else {
            el.disabled = true;
        }
    });
}

// ── Preview de imagen ─────────────────────────────────────────────────────────
function previewImagen(input) {
    const preview = document.getElementById('imagen-preview');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.type === 'application/pdf') {
            preview.style.display = 'none';
            document.querySelector('.upload-area p').textContent = '📄 ' + file.name;
        } else {
            const reader = new FileReader();
            reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
            reader.readAsDataURL(file);
        }
        document.querySelector('.upload-area p').textContent = '✓ ' + file.name;
    }
}

// ── Items de medicamentos (vinculados al inventario) ──────────────────────────
const MEDICAMENTOS_INV = {!! json_encode($medicamentos->map(function($m) { return ['id'=>$m->id,'nombre'=>$m->nombre_medicamento,'stock'=>$m->cantidad,'unidad'=>$m->unidad ?? 'unidades','bajo'=>$m->tieneStockBajo()]; })->values()) !!};

function agregarItemReceta() {
    const idx = itemCount++;
    const div = document.createElement('div');
    div.className = 'receta-item';
    div.id = 'ri-' + idx;

    // Construir opciones del inventario
    const optsInv = MEDICAMENTOS_INV.map(function(m) {
        const stockLabel = m.stock <= 0 ? ' \u274c Sin stock' : (m.bajo ? ' \u26a0\ufe0f Bajo' : '');
        const colorStyle = m.bajo ? 'color:#dc3545;font-weight:600;' : (m.stock <= 0 ? 'color:#adb5bd;' : '');
        const disabled   = m.stock <= 0 ? 'disabled' : '';
        return '<option value="' + m.id + '" data-nombre="' + m.nombre.replace(/"/g,'&quot;') +
               '" data-stock="' + m.stock + '" data-unidad="' + m.unidad +
               '" style="' + colorStyle + '" ' + disabled + '>' +
               m.nombre + ' \u2014 Stock: ' + m.stock + ' ' + m.unidad + stockLabel + '</option>';
    }).join('');

    div.innerHTML =
        '<div class="receta-item-card">' +
            '<div class="receta-item-header">' +
                '<span class="receta-item-title">' +
                    '<i class=\'bx bxs-capsule\'></i> Medicamento ' + (idx + 1) +
                '</span>' +
                '<button type="button" class="btn-icon btn-delete" onclick="document.getElementById(\'ri-' + idx + '\').remove()" title="Quitar">' +
                    '<i class=\'bx bx-trash\'></i>' +
                '</button>' +
            '</div>' +

            '<div class="form-group">' +
                '<label><i class=\'bx bxs-capsule\'></i> Del inventario <span style="font-weight:400;font-size:10px;">(opcional — vincula stock)</span></label>' +
                '<div style="position:relative;">' +
                    '<select id="inv-sel-' + idx + '" onchange="seleccionarMedInventario(this,' + idx + ')" style="appearance:none;-webkit-appearance:none;padding-right:32px;">' +
                        '<option value="">\u2014 Seleccionar del inventario \u2014</option>' +
                        optsInv +
                    '</select>' +

                '</div>' +
                '<div id="stock-info-' + idx + '" style="display:none;font-size:11px;margin-top:4px;padding:4px 8px;border-radius:6px;"></div>' +
            '</div>' +

            '<div class="form-row">' +
                '<div class="form-group" style="flex:2;">' +
                    '<label>Nombre del medicamento *</label>' +
                    '<input type="text" name="items[' + idx + '][nombre_medicamento]" id="med-nombre-' + idx + '" placeholder="Nombre del medicamento" required>' +
                    '<input type="hidden" name="items[' + idx + '][medicamento_id]" id="med-id-' + idx + '">' +
                '</div>' +
                '<div class="form-group">' +
                    '<label>Presentación</label>' +
                    '<input type="text" name="items[' + idx + '][presentacion]" placeholder="Tabletas, jarabe...">' +
                '</div>' +
            '</div>' +

            '<div class="form-row">' +
                '<div class="form-group"><label>Dosis *</label><input type="text" name="items[' + idx + '][dosis]" placeholder="500mg" required></div>' +
                '<div class="form-group"><label>Frecuencia *</label><input type="text" name="items[' + idx + '][frecuencia]" placeholder="Cada 8 horas" required></div>' +
                '<div class="form-group"><label>Duración *</label><input type="text" name="items[' + idx + '][duracion]" placeholder="7 días" required></div>' +
                '<div class="form-group" style="max-width:80px;"><label>Cant.</label><input type="number" name="items[' + idx + '][cantidad]" id="med-cant-' + idx + '" value="1" min="1" required></div>' +
            '</div>' +

            '<div class="form-group">' +
                '<label>Indicaciones</label>' +
                '<input type="text" name="items[' + idx + '][indicaciones]" placeholder="Tomar con alimentos...">' +
            '</div>' +
        '</div>';

    document.getElementById('receta-items').appendChild(div);
}

function seleccionarMedInventario(select, idx) {
    const opt     = select.options[select.selectedIndex];
    const infoBox = document.getElementById('stock-info-' + idx);

    if (!opt.value) { infoBox.style.display = 'none'; return; }

    const nombre = opt.dataset.nombre;
    const stock  = parseInt(opt.dataset.stock || 0);
    const unidad = opt.dataset.unidad || 'unidades';

    document.getElementById('med-nombre-' + idx).value = nombre;
    document.getElementById('med-id-'     + idx).value = opt.value;

    infoBox.style.display = 'block';
    if (stock <= 0) {
        infoBox.style.background = 'rgba(220,53,69,.1)';
        infoBox.style.color      = '#dc3545';
        infoBox.innerHTML        = '<i class="bx bx-error"></i> <strong>Sin stock.</strong> No disponible en inventario.';
        select.style.borderColor = '#dc3545';
    } else if (stock <= 5) {
        infoBox.style.background = 'rgba(255,193,7,.12)';
        infoBox.style.color      = '#856404';
        infoBox.innerHTML        = '<i class="bx bx-error"></i> Stock bajo: <strong>' + stock + ' ' + unidad + '</strong>. Verifica disponibilidad.';
        select.style.borderColor = '#ffc107';
    } else {
        infoBox.style.background = 'rgba(25,135,84,.1)';
        infoBox.style.color      = '#0f5132';
        infoBox.innerHTML        = '<i class="bx bx-check-circle"></i> Disponible: <strong>' + stock + ' ' + unidad + '</strong> en inventario.';
        select.style.borderColor = '#198754';
    }
}

// ── Formulario ────────────────────────────────────────────────────────────────
let _recetaSubmitting = false;
let _recetaIdActual   = null;

document.getElementById('form-receta').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (_recetaSubmitting) return;

    const tipo = document.getElementById('tipo_receta').value;
    if (tipo === 'digital' && document.getElementById('receta-items').children.length === 0) {
        showToast('Agregue al menos un medicamento.', 'error'); return;
    }
    if (tipo === 'manuscrita' && !document.getElementById('imagen_receta_input').files.length) {
        showToast('Suba la imagen de la receta.', 'error'); return;
    }

    _recetaSubmitting = true;
    const btn = this.querySelector('[type=submit]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Guardando...'; }

    try {
        const res  = await fetch(ROUTES_RECETAS.store, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
            body: new FormData(this)
        });
        const data = await res.json();
        if (data.success) {
            closeModal('modal-receta');
            showToast(data.message, 'success');

            // Si es receta digital con items vinculados al inventario, mostrar modal dispensar
            if (data.tipo_receta === 'digital' && data.items_con_stock && data.items_con_stock.length > 0) {
                _recetaIdActual = data.receta_id;
                abrirModalDispensar(data.numero_receta, data.items_con_stock);
            } else {
                // Sin items de inventario → recargar directamente
                setTimeout(() => location.reload(), 800);
            }
        } else {
            showToast(data.message || 'Error al guardar.', 'error');
        }
    } catch(err) {
        showToast('Error de conexión.', 'error');
    } finally {
        _recetaSubmitting = false;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Guardar Receta'; }
    }
});

// ── Modal Dispensar ───────────────────────────────────────────────────────────
function abrirModalDispensar(numeroReceta, items) {
    document.getElementById('disp-numero-receta').textContent = numeroReceta;

    const lista = document.getElementById('disp-items-list');
    lista.innerHTML = '';

    items.forEach(function(it) {
        const sinStock       = it.sin_stock;
        const stockSuficiente= it.stock_suficiente;
        const stockLabel     = sinStock
            ? '<span style="color:#dc3545;font-weight:600;"><i class="bx bx-error"></i> Sin stock</span>'
            : (stockSuficiente
                ? '<span style="color:#198754;"><i class="bx bx-check-circle"></i> Stock disponible: <strong>' + it.stock + ' ' + it.unidad + '</strong></span>'
                : '<span style="color:#856404;"><i class="bx bx-error"></i> Stock parcial: <strong>' + it.stock + '/' + it.cantidad + ' ' + it.unidad + '</strong></span>');

        const defaultSi  = !sinStock;
        const defaultNo  = sinStock;

        const card = document.createElement('div');
        card.style.cssText = 'border:1px solid var(--border-color);border-radius:10px;padding:14px 16px;margin-bottom:10px;transition:border-color .2s;';
        card.innerHTML =
            '<div style="display:flex;align-items:flex-start;gap:12px;">' +
                '<i class="bx bxs-capsule" style="font-size:22px;color:var(--primary);flex-shrink:0;margin-top:2px;"></i>' +
                '<div style="flex:1;min-width:0;">' +
                    '<div style="font-weight:600;font-size:13px;margin-bottom:3px;">' + escHtml(it.nombre) +
                        ' <span style="font-weight:400;color:var(--text-muted);font-size:12px;">× ' + it.cantidad + ' ' + it.unidad + '</span>' +
                    '</div>' +
                    '<div style="font-size:12px;margin-bottom:10px;">' + stockLabel + '</div>' +
                    '<div style="display:flex;gap:8px;">' +
                        // Botón SI
                        '<label style="flex:1;cursor:pointer;">' +
                            '<input type="radio" name="dispensar[' + it.item_id + ']" value="si" ' + (defaultSi ? 'checked' : '') + ' ' + (sinStock ? 'disabled' : '') +
                            ' style="display:none;" onchange="resaltarCard(this)">' +
                            '<div class="disp-opcion ' + (defaultSi ? 'disp-si-activo' : '') + '" id="opt-si-' + it.item_id + '" ' +
                            'style="border:2px solid ' + (defaultSi ? '#198754' : 'var(--border-color)') + ';border-radius:8px;padding:8px 10px;text-align:center;font-size:12px;font-weight:600;' +
                            'background:' + (defaultSi ? 'rgba(25,135,84,.1)' : 'var(--bg-body)') + ';' +
                            'color:' + (defaultSi ? '#198754' : 'var(--text-secondary)') + ';' +
                            (sinStock ? 'opacity:.4;cursor:not-allowed;' : 'cursor:pointer;') + '">' +
                                '<i class="bx bx-check-circle" style="font-size:16px;display:block;margin-bottom:2px;"></i>' +
                                'Entregar del<br>inventario' +
                            '</div>' +
                        '</label>' +
                        // Botón NO
                        '<label style="flex:1;cursor:pointer;">' +
                            '<input type="radio" name="dispensar[' + it.item_id + ']" value="no" ' + (defaultNo ? 'checked' : '') +
                            ' style="display:none;" onchange="resaltarCard(this)">' +
                            '<div class="disp-opcion" id="opt-no-' + it.item_id + '" ' +
                            'style="border:2px solid ' + (defaultNo ? '#dc3545' : 'var(--border-color)') + ';border-radius:8px;padding:8px 10px;text-align:center;font-size:12px;font-weight:600;' +
                            'background:' + (defaultNo ? 'rgba(220,53,69,.08)' : 'var(--bg-body)') + ';' +
                            'color:' + (defaultNo ? '#dc3545' : 'var(--text-secondary)') + ';cursor:pointer;">' +
                                '<i class="bx bx-x-circle" style="font-size:16px;display:block;margin-bottom:2px;"></i>' +
                                'No disponible /<br>Compra externa' +
                            '</div>' +
                        '</label>' +
                    '</div>' +
                '</div>' +
            '</div>';

        // Click en las opciones para simular radio
        card.querySelectorAll('label').forEach(function(lbl) {
            lbl.addEventListener('click', function() {
                const radio = this.querySelector('input[type=radio]');
                if (radio && !radio.disabled) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });

        lista.appendChild(card);
    });

    openModal('modal-dispensar');
}

function resaltarCard(radio) {
    // Extraer item_id del name: dispensar[123]
    const match = radio.name.match(/\[(\d+)\]/);
    if (!match) return;
    const itemId = match[1];

    const optSi = document.getElementById('opt-si-' + itemId);
    const optNo = document.getElementById('opt-no-' + itemId);
    if (!optSi || !optNo) return;

    if (radio.value === 'si') {
        optSi.style.borderColor  = '#198754';
        optSi.style.background   = 'rgba(25,135,84,.1)';
        optSi.style.color        = '#198754';
        optNo.style.borderColor  = 'var(--border-color)';
        optNo.style.background   = 'var(--bg-body)';
        optNo.style.color        = 'var(--text-secondary)';
    } else {
        optNo.style.borderColor  = '#dc3545';
        optNo.style.background   = 'rgba(220,53,69,.08)';
        optNo.style.color        = '#dc3545';
        optSi.style.borderColor  = 'var(--border-color)';
        optSi.style.background   = 'var(--bg-body)';
        optSi.style.color        = 'var(--text-secondary)';
    }
}

async function confirmarDispensacion() {
    if (!_recetaIdActual) return;

    const btn = document.getElementById('btn-confirmar-dispensar');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Procesando...'; }

    try {
        const formData = new FormData(document.getElementById('form-dispensar'));
        const res = await fetch(ROUTES_RECETAS.dispensar + _recetaIdActual + '/dispensar', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            closeModal('modal-dispensar');
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 900);
        } else {
            showToast(data.message || 'Error al dispensar.', 'error');
        }
    } catch(err) {
        showToast('Error de conexión.', 'error');
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bxs-capsule"></i> Confirmar Dispensación'; }
    }
}

function cerrarDispensarYRecargar() {
    closeModal('modal-dispensar');
    setTimeout(() => location.reload(), 300);
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function anularReceta(id, numero) {
    document.getElementById('delete-item-name').textContent = 'Receta ' + numero + ' será anulada y no podrá usarse.';
    document.querySelector('#modal-confirm-delete .modal-header').className = 'modal-header modal-header-danger';
    document.querySelector('#modal-confirm-delete h2').innerHTML = "<i class='bx bx-x-circle'></i> Anular Receta";
    document.getElementById('btn-confirm-delete').className = 'btn-danger';
    document.getElementById('btn-confirm-delete').innerHTML = "<i class='bx bx-x-circle'></i> Anular";
    document.getElementById('btn-confirm-delete').onclick = async () => {
        const res  = await fetch(ROUTES_RECETAS.anular + id + '/anular', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: '_method=PATCH'
        });
        const data = await res.json();
        closeModal('modal-confirm-delete');
        if (data.success) { showToast(data.message, 'success'); setTimeout(() => location.reload(), 800); }
        else showToast(data.message, 'error');
    };
    openModal('modal-confirm-delete');
}

// Inicializar: detectar parámetros de URL para pre-seleccionar paciente/consulta
document.addEventListener('DOMContentLoaded', function() {
    // Listener del botón Nueva Receta
    const btnNueva = document.querySelector('[onclick="openModal(\'modal-receta\')"]');
    if (btnNueva) {
        btnNueva.addEventListener('click', () => {
            setTimeout(() => {
                if (document.getElementById('receta-items').children.length === 0) agregarItemReceta();
            }, 100);
        });
    }

    const params     = new URLSearchParams(window.location.search);
    const pacienteId = params.get('paciente_id');
    const consultaId = params.get('consulta_id');

    if (pacienteId) {
        setTimeout(async () => {
            openModal('modal-receta');
            const sel = document.getElementById('receta-paciente_id');
            if (sel) {
                sel.value = pacienteId;
                await cargarConsultasPaciente(pacienteId);
                if (consultaId) {
                    const selC = document.getElementById('receta-consulta_id');
                    if (selC) selC.value = consultaId;
                }
            }
            if (document.getElementById('receta-items').children.length === 0) {
                agregarItemReceta();
            }
        }, 200);
    }
});
</script>
@endpush
