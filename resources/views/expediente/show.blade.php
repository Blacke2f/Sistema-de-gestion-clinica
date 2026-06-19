@extends('layouts.app')

@section('title', 'Expediente — ' . $paciente->nombre_completo)
@section('nav-title', 'Expediente Clínico')

@push('styles')
<style>
.exp-header { background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%); border-radius: 16px; padding: 28px; color: #fff; margin-bottom: 24px; display: flex; align-items: center; gap: 20px; }
.exp-avatar { width: 80px; height: 80px; border-radius: 50%; border: 3px solid rgba(255,255,255,.4); }
.exp-header h2 { margin: 0 0 4px; font-size: 1.4rem; }
.exp-header p  { margin: 0; opacity: .85; font-size: 13px; }
.exp-badge { background: rgba(255,255,255,.2); border-radius: 20px; padding: 3px 12px; font-size: 12px; display: inline-block; margin-top: 6px; }
.exp-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 14px; margin-bottom: 24px; }
.exp-stat { background: var(--bg-card); border-radius: 12px; padding: 16px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.exp-stat .num { font-size: 24px; font-weight: 700; color: var(--primary); }
.exp-stat .lbl { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
.timeline { position: relative; padding-left: 28px; }
.timeline::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background: var(--border-color); }
.timeline-item { position: relative; margin-bottom: 20px; }
.timeline-dot { position: absolute; left: -24px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: var(--primary); border: 2px solid var(--bg-card); }
.timeline-card { background: var(--bg-card); border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.timeline-card .tc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; flex-wrap: wrap; gap: 6px; }
.timeline-card .tc-fecha { font-size: 12px; color: var(--text-muted); }
.sv-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin-top: 10px; }
.sv-item { background: var(--bg-body); border-radius: 8px; padding: 10px; text-align: center; }
.sv-item .sv-val { font-size: 16px; font-weight: 700; color: var(--text-primary); }
.sv-item .sv-lbl { font-size: 10px; color: var(--text-muted); }
.alergias-badge { background: #fff3cd; color: #856404; border-radius: 6px; padding: 6px 12px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; }
body.dark-mode .alergias-badge { background: rgba(255,193,7,.15); color: #ffc107; }
</style>
@endpush

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Expediente Clínico</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a href="{{ route('pacientes.index') }}">Pacientes</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">{{ $paciente->nombre_completo }}</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <a href="{{ route('pacientes.index') }}" class="btn-secondary"><i class='bx bx-arrow-back'></i> Volver</a>
    </div>
</div>

{{-- Header del paciente --}}
<div class="exp-header">
                <img src="" data-avatar="{{ $paciente->nombre_completo }}" data-bg="#ffffff" data-size="80" class="exp-avatar" alt="" style="background:#0d6efd;">
    <div style="flex:1;">
        <h2>{{ $paciente->nombre_completo }}</h2>
        <p><i class='bx bx-id-card'></i> {{ $paciente->cedula }}
           &nbsp;|&nbsp; <i class='bx bx-calendar'></i> {{ $paciente->fecha_nacimiento?->format('d/m/Y') ?? 'N/D' }}
           @if($paciente->edad) ({{ $paciente->edad }} años) @endif
           &nbsp;|&nbsp; <i class='bx bx-male-female'></i> {{ $paciente->genero_texto }}
        </p>
        @if($paciente->tipo_sangre)
        <span class="exp-badge"><i class='bx bx-droplet'></i> {{ $paciente->tipo_sangre }}</span>
        @endif
        @if($paciente->alergias)
        <span class="exp-badge" style="background:rgba(220,53,69,.25);"><i class='bx bx-error'></i> Alergias: {{ $paciente->alergias }}</span>
        @endif
        @if($paciente->telefono)
        <span class="exp-badge"><i class='bx bx-phone'></i> {{ $paciente->telefono }}</span>
        @endif
    </div>
</div>

{{-- Estadísticas --}}
<div class="exp-stats">
    <div class="exp-stat">
        <div class="num">{{ $stats['total_consultas'] }}</div>
        <div class="lbl">Consultas Totales</div>
    </div>
    <div class="exp-stat">
        <div class="num">{{ $stats['consultas_año'] }}</div>
        <div class="lbl">Consultas Este Año</div>
    </div>
    <div class="exp-stat">
        <div class="num">{{ $stats['total_recetas'] }}</div>
        <div class="lbl">Recetas Emitidas</div>
    </div>
    <div class="exp-stat">
        <div class="num" style="font-size:14px;">{{ $stats['ultima_visita']?->format('d/m/Y') ?? 'N/D' }}</div>
        <div class="lbl">Última Visita</div>
    </div>
    <div class="exp-stat">
        <div class="num">{{ $stats['total_informes'] }}</div>
        <div class="lbl">Informes Médicos</div>
    </div>
    <div class="exp-stat">
        <div class="num">{{ $stats['total_examenes'] }}</div>
        <div class="lbl">Exámenes Solicitados</div>
    </div>
</div>

{{-- Acciones rápidas para formularios médicos --}}
<div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <button class="btn-primary" onclick="openModal('modal-informe-medico')" style="font-size:13px;">
        <i class='bx bx-file-plus'></i> Nuevo Informe Médico
    </button>
    <button class="btn-secondary" onclick="openModal('modal-gasto-medico')" style="font-size:13px;">
        <i class='bx bx-wallet-alt'></i> Nuevo Gasto Médico
    </button>
    <button class="btn-secondary" onclick="openModal('modal-examen-laboratorio')" style="font-size:13px;">
        <i class='bx bx-test-tube'></i> Solicitar Exámenes
    </button>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

    {{-- Historial de consultas --}}
    <div>
        <div class="table-container">
            <div class="table-header">
                <h3><i class='bx bx-history'></i> Historial de Consultas</h3>
                <span class="badge badge-primary">{{ $consultas->count() }} consultas</span>
            </div>
            <div style="padding:16px;">
                @forelse($consultas as $c)
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:{{ $c->estado === 'completada' ? '#198754' : ($c->estado === 'cancelada' ? '#dc3545' : '#0d6efd') }};"></div>
                        <div class="timeline-card">
                            <div class="tc-header">
                                <div>
                                    <strong>{{ $c->fecha_hora->format('d/m/Y H:i') }}</strong>
                                    <span class="badge badge-{{ $c->estado === 'completada' ? 'success' : ($c->estado === 'cancelada' ? 'danger' : 'primary') }}" style="margin-left:8px;">{{ $c->estado_texto }}</span>
                                </div>
                                <span class="tc-fecha">{{ $c->medico_nombre }}</span>
                            </div>
                            <p style="margin:0 0 8px;font-size:13px;"><strong>Motivo:</strong> {{ $c->motivo }}</p>
                            @if($c->diagnostico)
                            <p style="margin:0 0 8px;font-size:13px;"><strong>Diagnóstico:</strong> {{ $c->diagnostico }}</p>
                            @endif
                            @if($c->tratamiento)
                            <p style="margin:0 0 8px;font-size:13px;"><strong>Tratamiento:</strong> {{ $c->tratamiento }}</p>
                            @endif

                            {{-- Signos vitales de esta consulta --}}
                            @if($c->signosVitales->isNotEmpty())
                            @php $sv = $c->signosVitales->first(); @endphp
                            <div class="sv-grid">
                                @if($sv->presion_sistolica)
                                <div class="sv-item"><div class="sv-val">{{ $sv->presion_sistolica }}/{{ $sv->presion_diastolica }}</div><div class="sv-lbl">Presión (mmHg)</div></div>
                                @endif
                                @if($sv->frecuencia_cardiaca)
                                <div class="sv-item"><div class="sv-val">{{ $sv->frecuencia_cardiaca }}</div><div class="sv-lbl">FC (lpm)</div></div>
                                @endif
                                @if($sv->temperatura)
                                <div class="sv-item"><div class="sv-val">{{ $sv->temperatura }}°C</div><div class="sv-lbl">Temperatura</div></div>
                                @endif
                                @if($sv->saturacion_oxigeno)
                                <div class="sv-item"><div class="sv-val">{{ $sv->saturacion_oxigeno }}%</div><div class="sv-lbl">SpO₂</div></div>
                                @endif
                                @if($sv->peso)
                                <div class="sv-item"><div class="sv-val">{{ $sv->peso }} kg</div><div class="sv-lbl">Peso</div></div>
                                @endif
                                @if($sv->imc)
                                <div class="sv-item"><div class="sv-val">{{ $sv->imc }}</div><div class="sv-lbl">IMC ({{ $sv->clasificacion_imc }})</div></div>
                                @endif
                            </div>
                            @endif

                            {{-- Medicamentos recetados --}}
                            @if($c->medicamentos->isNotEmpty())
                            <div style="margin-top:10px;">
                                <small style="color:var(--text-muted);font-weight:600;">MEDICAMENTOS:</small>
                                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;">
                                    @foreach($c->medicamentos as $med)
                                    <span class="badge badge-secondary">{{ $med->nombre_medicamento }} ({{ $med->pivot->cantidad }})</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Recetas --}}
                            @if($c->recetas->isNotEmpty())
                            <div style="margin-top:10px;">
                                @foreach($c->recetas as $r)
                                <a href="{{ route('recetas.imprimir', $r->id) }}" target="_blank" class="btn-secondary btn-sm" style="font-size:11px;">
                                    <i class='bx bx-printer'></i> {{ $r->numero_receta }}
                                </a>
                                @endforeach
                            </div>
                            @endif
                            {{-- Botón nueva receta para esta consulta --}}
                            <div style="margin-top:10px;padding-top:10px;border-top:1px solid var(--border-color);">
                                <a href="{{ route('recetas.index') }}?paciente_id={{ $paciente->id }}&consulta_id={{ $c->id }}"
                                   class="btn-secondary btn-sm" style="font-size:11px;">
                                    <i class='bx bx-file-plus'></i> Nueva Receta para esta consulta
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state"><i class='bx bx-clipboard'></i><p>Sin consultas registradas</p></div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Panel lateral --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Medicamentos más frecuentes --}}
        @if($medicamentosFrec->isNotEmpty())
        <div class="table-container">
            <div class="table-header"><h3><i class='bx bxs-capsule'></i> Top Medicamentos</h3></div>
            <div style="padding:12px;">
                @foreach($medicamentosFrec as $m)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-color);">
                    <span style="font-size:13px;">{{ $m->nombre_medicamento }}</span>
                    <span class="badge badge-primary">{{ $m->veces }}x</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Recetas recientes --}}
        @if($recetas->isNotEmpty())
        <div class="table-container">
            <div class="table-header"><h3><i class='bx bx-file'></i> Recetas</h3></div>
            <div style="padding:12px;">
                @foreach($recetas->take(5) as $r)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-color);">
                    <div>
                        <div style="font-size:12px;font-weight:600;">{{ $r->numero_receta }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">
                            {{ $r->fecha_emision->format('d/m/Y') }}
                            &nbsp;·&nbsp;
                            <span class="badge badge-{{ $r->tipo_receta === 'manuscrita' ? 'purple' : 'primary' }}" style="font-size:9px;">
                                {{ $r->tipo_receta === 'manuscrita' ? 'Manuscrita' : 'Digital' }}
                            </span>
                        </div>
                    </div>
                    <div style="display:flex;gap:6px;">
                        @if($r->tipo_receta === 'manuscrita' && $r->imagen_receta)
                        <a href="{{ asset('storage/' . $r->imagen_receta) }}" target="_blank"
                           class="btn-icon btn-primary" title="Ver imagen escaneada"
                           style="font-size:11px;padding:4px 8px;border-radius:6px;">
                            <i class='bx bx-image'></i>
                        </a>
                        @else
                        <a href="{{ route('recetas.imprimir', $r->id) }}" target="_blank"
                           class="btn-icon btn-primary" title="Imprimir PDF"
                           style="font-size:11px;padding:4px 8px;border-radius:6px;">
                            <i class='bx bx-printer'></i>
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Facturas --}}
        {{-- Eliminado: sistema institucional sin cobros --}}

        {{-- Informes Médicos --}}
        @if($informesMedicos->isNotEmpty())
        <div class="table-container">
            <div class="table-header"><h3><i class='bx bx-file-blank'></i> Informes Médicos</h3></div>
            <div style="padding:12px;">
                @foreach($informesMedicos as $inf)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-color);">
                    <div>
                        <div style="font-size:12px;font-weight:600;">{{ $inf->numero_informe }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">
                            {{ $inf->fecha->format('d/m/Y') }}
                            @if($inf->tipo_consulta)
                            &nbsp;·&nbsp; <span class="badge badge-primary" style="font-size:9px;">{{ $inf->tipo_consulta_texto }}</span>
                            @endif
                            @if($inf->aptitud)
                            &nbsp;·&nbsp; <span class="badge badge-{{ $inf->aptitud === 'apto' ? 'success' : ($inf->aptitud === 'no_apto' ? 'danger' : 'warning') }}" style="font-size:9px;">{{ $inf->aptitud_texto }}</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('informes-medicos.imprimir', $inf->id) }}" target="_blank"
                       class="btn-icon btn-primary" title="Imprimir PDF"
                       style="font-size:11px;padding:4px 8px;border-radius:6px;">
                        <i class='bx bx-printer'></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Gastos Médicos --}}
        @if($gastosMedicos->isNotEmpty())
        <div class="table-container">
            <div class="table-header"><h3><i class='bx bx-wallet-alt'></i> Gastos Médicos</h3></div>
            <div style="padding:12px;">
                @foreach($gastosMedicos as $gasto)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-color);">
                    <div>
                        <div style="font-size:12px;font-weight:600;">{{ $gasto->numero_gasto }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">
                            {{ $gasto->fecha->format('d/m/Y') }}
                            &nbsp;·&nbsp; Total: <strong>{{ number_format($gasto->total, 2) }} Bs.</strong>
                        </div>
                    </div>
                    <a href="{{ route('gastos-medicos.imprimir', $gasto->id) }}" target="_blank"
                       class="btn-icon btn-primary" title="Imprimir PDF"
                       style="font-size:11px;padding:4px 8px;border-radius:6px;">
                        <i class='bx bx-printer'></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Exámenes de Laboratorio --}}
        @if($examenesLaboratorio->isNotEmpty())
        <div class="table-container">
            <div class="table-header">
                <h3><i class='bx bx-test-tube'></i> Exámenes de Laboratorio</h3>
                <span class="badge badge-primary">{{ $examenesLaboratorio->count() }}</span>
            </div>
            <div style="padding:12px;">
                @foreach($examenesLaboratorio as $exa)
                <div style="padding:10px 0;border-bottom:1px solid var(--border-color);">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                <span style="font-size:12px;font-weight:700;color:var(--primary);">{{ $exa->numero_solicitud }}</span>
                                <span style="font-size:11px;color:var(--text-muted);">{{ $exa->fecha->format('d/m/Y') }}</span>
                            </div>
                            @php $marcados = $exa->examenes_solicitados; @endphp
                            @if(count($marcados) > 0)
                            <div style="display:flex;flex-wrap:wrap;gap:3px;margin-bottom:4px;">
                                @foreach($marcados as $ex)
                                <span class="badge badge-primary" style="font-size:9px;">{{ $ex }}</span>
                                @endforeach
                            </div>
                            @endif
                            @if($exa->otros)
                            <div style="font-size:11px;color:var(--text-muted);">
                                <i class='bx bx-plus-circle'></i> Otros: {{ $exa->otros }}
                            </div>
                            @endif
                            <div style="font-size:11px;color:var(--text-muted);margin-top:3px;">
                                <i class='bx bx-user-check'></i> {{ $exa->medico_nombre }}
                            </div>
                        </div>
                        <a href="{{ route('examenes-laboratorio.imprimir', $exa->id) }}" target="_blank"
                           class="btn-icon btn-primary" title="Imprimir PDF"
                           style="font-size:11px;padding:4px 8px;border-radius:6px;flex-shrink:0;">
                            <i class='bx bx-printer'></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MODAL: NUEVO INFORME MÉDICO
════════════════════════════════════════════════════════════════════════════ --}}
<div class="modal" id="modal-informe-medico">
    <div class="modal-content" style="max-width:700px;">
        <div class="modal-header">
            <h2><i class='bx bx-file-blank'></i> Nuevo Informe Médico</h2>
            <button class="modal-close" onclick="closeModal('modal-informe-medico')">&times;</button>
        </div>
        <form id="form-informe-medico" class="modal-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">

            {{-- Selector tipo --}}
            <div class="form-group">
                <label style="font-weight:700;margin-bottom:8px;display:block;">Tipo de Informe</label>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 16px;border:2px solid var(--primary);border-radius:8px;font-weight:600;background:rgba(13,110,253,.07);">
                        <input type="radio" name="tipo_documento" value="digital" checked
                               onchange="toggleTipoInforme('digital')">
                        <i class='bx bx-file-blank'></i> Digital
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 16px;border:2px solid var(--border-color);border-radius:8px;font-weight:600;">
                        <input type="radio" name="tipo_documento" value="manuscrita"
                               onchange="toggleTipoInforme('manuscrita')">
                        <i class='bx bx-scan'></i> Manuscrita (subir escaneo)
                    </label>
                </div>
            </div>
            {{-- Escaneo --}}
            <div id="inf-manuscrita-sect" style="display:none;">
                <div class="form-group">
                    <label>Imagen del Informe Escaneado *</label>
                    <input type="file" name="imagen_escaneo" accept="image/*,.pdf"
                           style="padding:8px;border:2px dashed var(--border-color);border-radius:8px;width:100%;cursor:pointer;">
                    <small style="color:var(--text-muted);">JPG, PNG, WEBP o PDF — máx. 5MB</small>
                </div>
            </div>
            {{-- Formulario digital --}}
            <div id="inf-digital-sect">
                <div class="form-group">
                    <label>Médico / Responsable</label>
                    <select name="empleado_id">
                        <option value="">Seleccionar médico</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre_completo }} — {{ $m->cargo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha *</label>
                    <input type="date" name="fecha" required value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nombre de la Empresa</label>
                    <input type="text" name="nombre_empresa" placeholder="Empresa del trabajador">
                </div>
                <div class="form-group">
                    <label>Cargo</label>
                    <input type="text" name="cargo" placeholder="Cargo del trabajador">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Edad</label>
                    <input type="number" name="edad" min="1" max="120" placeholder="Años"
                           value="{{ $paciente->edad ?? '' }}">
                </div>
                <div class="form-group">
                    <label>Antigüedad en el Cargo</label>
                    <input type="text" name="antiguedad_cargo" placeholder="Ej: 2 años">
                </div>
            </div>

            <div class="form-group">
                <label>Tipo de Consulta / Actitud</label>
                <div style="display:flex;flex-wrap:wrap;gap:14px;margin-top:6px;">
                    @foreach(['preempleo' => 'Pre-empleo','prevacacional' => 'Pre-vacacional','postvacacional' => 'Post-vacacional','egreso' => 'Egreso','reubicacion' => 'Reubicación','otros' => 'Otros'] as $val => $label)
                    <label style="display:flex;align-items:center;gap:6px;font-weight:400;cursor:pointer;">
                        <input type="radio" name="tipo_consulta" value="{{ $val }}"
                               {{ $val === 'preempleo' ? 'checked' : '' }}
                               onchange="document.getElementById('tipo-otros-inf').style.display = this.value === 'otros' ? 'block' : 'none'">
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
                <div id="tipo-otros-inf" style="display:none;margin-top:8px;">
                    <input type="text" name="tipo_consulta_otros" placeholder="Especifique...">
                </div>
            </div>

            <div class="form-group">
                <label>Resultado / Aptitud</label>
                <div style="display:flex;gap:20px;margin-top:6px;">
                    @foreach(['apto' => 'Apto','no_apto' => 'No Apto','apto_condicionado' => 'Apto Condicionado'] as $val => $label)
                    <label style="display:flex;align-items:center;gap:6px;font-weight:400;cursor:pointer;">
                        <input type="radio" name="aptitud" value="{{ $val }}">
                        {{ $label }}
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
            <div class="form-group">
                <label>Referencia</label>
                <textarea name="referencia" rows="2" placeholder="Referencia a especialista u otro servicio"></textarea>
            </div>
            <div class="form-group">
                <label>Recomendaciones</label>
                <textarea name="recomendaciones" rows="2" placeholder="Recomendaciones médicas"></textarea>
            </div>
            </div>{{-- /inf-digital-sect --}}

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-informe-medico')">Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-save'></i> Guardar y Generar PDF</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MODAL: NUEVO GASTO MÉDICO
════════════════════════════════════════════════════════════════════════════ --}}
<div class="modal" id="modal-gasto-medico">
    <div class="modal-content" style="max-width:660px;">
        <div class="modal-header">
            <h2><i class='bx bx-wallet-alt'></i> Nuevo Gasto Médico</h2>
            <button class="modal-close" onclick="closeModal('modal-gasto-medico')">&times;</button>
        </div>
        <form id="form-gasto-medico" class="modal-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">

            {{-- Selector tipo --}}
            <div class="form-group">
                <label style="font-weight:700;margin-bottom:8px;display:block;">Tipo de Gasto</label>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 16px;border:2px solid var(--primary);border-radius:8px;font-weight:600;background:rgba(13,110,253,.07);">
                        <input type="radio" name="tipo_documento" value="digital" checked
                               onchange="toggleTipoGasto('digital')">
                        <i class='bx bx-list-ul'></i> Digital
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 16px;border:2px solid var(--border-color);border-radius:8px;font-weight:600;">
                        <input type="radio" name="tipo_documento" value="manuscrita"
                               onchange="toggleTipoGasto('manuscrita')">
                        <i class='bx bx-scan'></i> Manuscrita (subir escaneo)
                    </label>
                </div>
            </div>
            {{-- Escaneo --}}
            <div id="gasto-manuscrita-sect" style="display:none;">
                <div class="form-group">
                    <label>Imagen del Gasto Escaneado *</label>
                    <input type="file" name="imagen_escaneo" accept="image/*,.pdf"
                           style="padding:8px;border:2px dashed var(--border-color);border-radius:8px;width:100%;cursor:pointer;">
                    <small style="color:var(--text-muted);">JPG, PNG, WEBP o PDF — máx. 5MB</small>
                </div>
            </div>
            {{-- Campos siempre visibles --}}
            <div class="form-row">
                <div class="form-group">
                    <label>Médico / Responsable</label>
                    <select name="empleado_id">
                        <option value="">Seleccionar médico</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre_completo }} — {{ $m->cargo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha *</label>
                    <input type="date" name="fecha" required value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nombre de la Empresa</label>
                    <input type="text" name="nombre_empresa" placeholder="Empresa">
                </div>
            </div>

            <div class="form-group">
                <label>Descripción / Motivo</label>
                <textarea name="descripcion" rows="2" placeholder="Motivo del gasto médico"></textarea>
            </div>

            {{-- Ítems del gasto (solo digital) --}}
            <div id="gasto-digital-sect">
            <div class="form-group">
                <label>Ítems del Gasto
                    <button type="button" onclick="agregarItemGasto()"
                        style="margin-left:8px;background:rgba(13,110,253,.1);border:1px solid rgba(13,110,253,.3);color:var(--primary);border-radius:6px;padding:2px 8px;font-size:11px;cursor:pointer;">
                        <i class='bx bx-plus'></i> Agregar
                    </button>
                </label>
                <div id="items-gasto">
                    <div class="gasto-item-row" style="display:flex;gap:8px;margin-bottom:6px;">
                        <input type="text" name="items[0][concepto]" placeholder="Concepto" style="flex:2;" required>
                        <input type="number" name="items[0][monto]" placeholder="Monto" step="0.01" min="0" style="flex:1;" required>
                        <button type="button" onclick="this.closest('.gasto-item-row').remove()"
                            style="background:rgba(220,53,69,.1);border:1px solid rgba(220,53,69,.3);color:#dc3545;border-radius:6px;padding:4px 8px;cursor:pointer;">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>
                </div>
                <div style="text-align:right;margin-top:6px;font-size:13px;font-weight:700;">
                    Total: <span id="total-gasto">0.00</span> Bs.
                </div>
            </div>
            </div>{{-- /gasto-digital-sect --}}

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-gasto-medico')">Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-save'></i> Guardar y Generar PDF</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MODAL: SOLICITUD DE EXÁMENES DE LABORATORIO
════════════════════════════════════════════════════════════════════════════ --}}
<div class="modal" id="modal-examen-laboratorio">
    <div class="modal-content" style="max-width:580px;">
        <div class="modal-header">
            <h2><i class='bx bx-test-tube'></i> Solicitud de Exámenes de Laboratorio</h2>
            <button class="modal-close" onclick="closeModal('modal-examen-laboratorio')">&times;</button>
        </div>
        <form id="form-examen-laboratorio" class="modal-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">

            {{-- Selector tipo --}}
            <div class="form-group">
                <label style="font-weight:700;margin-bottom:8px;display:block;">Tipo de Solicitud</label>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 16px;border:2px solid var(--primary);border-radius:8px;font-weight:600;background:rgba(13,110,253,.07);">
                        <input type="radio" name="tipo_documento" value="digital" checked
                               onchange="toggleTipoExamen('digital')">
                        <i class='bx bx-check-square'></i> Digital
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 16px;border:2px solid var(--border-color);border-radius:8px;font-weight:600;">
                        <input type="radio" name="tipo_documento" value="manuscrita"
                               onchange="toggleTipoExamen('manuscrita')">
                        <i class='bx bx-scan'></i> Manuscrita (subir escaneo)
                    </label>
                </div>
            </div>
            {{-- Escaneo --}}
            <div id="examen-manuscrita-sect" style="display:none;">
                <div class="form-group">
                    <label>Imagen de la Solicitud Escaneada *</label>
                    <input type="file" name="imagen_escaneo" accept="image/*,.pdf"
                           style="padding:8px;border:2px dashed var(--border-color);border-radius:8px;width:100%;cursor:pointer;">
                    <small style="color:var(--text-muted);">JPG, PNG, WEBP o PDF — máx. 5MB</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Médico / Responsable</label>
                    <select name="empleado_id">
                        <option value="">Seleccionar médico</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre_completo }} — {{ $m->cargo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha *</label>
                    <input type="date" name="fecha" required value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div id="examen-digital-sect">
            <div class="form-group">
                <label>Exámenes a Solicitar</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:8px;">
                    @foreach([
                        'hematologia_completa' => 'Hematología completa',
                        'glicemia_ayunas'      => 'Glicemia en ayunas',
                        'urea'                 => 'Urea',
                        'creatinina'           => 'Creatinina',
                        'colesterol_total'     => 'Colesterol total',
                        'trigliceridos'        => 'Triglicéridos',
                        'examen_orina'         => 'Examen de Orina',
                        'examen_heces'         => 'Examen de Heces',
                    ] as $campo => $etiqueta)
                    <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;padding:8px;border:1px solid var(--border-color);border-radius:8px;">
                        <input type="checkbox" name="{{ $campo }}" value="1" style="width:16px;height:16px;">
                        {{ $etiqueta }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label>Otros Exámenes</label>
                <input type="text" name="otros" placeholder="Especifique otros exámenes...">
            </div>
            </div>{{-- /examen-digital-sect --}}

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-examen-laboratorio')">Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-save'></i> Guardar y Generar PDF</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Contador de ítems del gasto ──────────────────────────────────────────────
let gastoItemIdx = 1;
function agregarItemGasto() {
    const cont = document.getElementById('items-gasto');
    const div  = document.createElement('div');
    div.className = 'gasto-item-row';
    div.style.cssText = 'display:flex;gap:8px;margin-bottom:6px;';
    div.innerHTML = `
        <input type="text"   name="items[${gastoItemIdx}][concepto]" placeholder="Concepto" style="flex:2;" required>
        <input type="number" name="items[${gastoItemIdx}][monto]"    placeholder="Monto" step="0.01" min="0" style="flex:1;" required>
        <button type="button" onclick="this.closest('.gasto-item-row').remove();calcularTotalGasto()"
            style="background:rgba(220,53,69,.1);border:1px solid rgba(220,53,69,.3);color:#dc3545;border-radius:6px;padding:4px 8px;cursor:pointer;">
            <i class='bx bx-trash'></i></button>`;
    cont.appendChild(div);
    gastoItemIdx++;
    cont.querySelectorAll('input[type=number]').forEach(i => i.addEventListener('input', calcularTotalGasto));
}

function calcularTotalGasto() {
    let total = 0;
    document.querySelectorAll('#items-gasto input[type=number]').forEach(i => {
        total += parseFloat(i.value) || 0;
    });
    document.getElementById('total-gasto').textContent = total.toFixed(2);
}
document.getElementById('items-gasto').addEventListener('input', calcularTotalGasto);

// ── Toggle tipo documento ────────────────────────────────────────────────────
function _toggleRequired(sectId, enable) {
    const sect = document.getElementById(sectId);
    if (!sect) return;
    sect.querySelectorAll('input, select, textarea').forEach(function(el) {
        el.disabled = !enable;
    });
}

function toggleTipoInforme(tipo) {
    const isDigital = tipo === 'digital';
    document.getElementById('inf-digital-sect').style.display    = isDigital ? 'block' : 'none';
    document.getElementById('inf-manuscrita-sect').style.display = isDigital ? 'none'  : 'block';
    _toggleRequired('inf-digital-sect',    isDigital);
    _toggleRequired('inf-manuscrita-sect', !isDigital);
    const fechaField = document.querySelector('#form-informe-medico input[name=fecha]');
    if (fechaField) fechaField.disabled = false; // fecha siempre activa
}

function toggleTipoGasto(tipo) {
    const isDigital = tipo === 'digital';
    document.getElementById('gasto-digital-sect').style.display    = isDigital ? 'block' : 'none';
    document.getElementById('gasto-manuscrita-sect').style.display = isDigital ? 'none'  : 'block';
    _toggleRequired('gasto-digital-sect',    isDigital);
    _toggleRequired('gasto-manuscrita-sect', !isDigital);
}

function toggleTipoExamen(tipo) {
    const isDigital = tipo === 'digital';
    document.getElementById('examen-digital-sect').style.display    = isDigital ? 'block' : 'none';
    document.getElementById('examen-manuscrita-sect').style.display = isDigital ? 'none'  : 'block';
    _toggleRequired('examen-digital-sect',    isDigital);
    _toggleRequired('examen-manuscrita-sect', !isDigital);
}

// ── Helper: submit con FormData (soporta archivos) ───────────────────────────
function submitMedicoForm(formId, storeUrl, modalId, pdfBaseUrl) {
    const form = document.getElementById(formId);
    const btn  = form.querySelector('[type=submit]');
    btn.disabled = true;
    fetch(storeUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body: new FormData(form),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeModal(modalId);
            form.reset();
            // Solo abrir PDF si es digital (manuscrita no genera PDF dinámico)
            if (data.tipo_documento !== 'manuscrita') {
                window.open(pdfBaseUrl + '/' + data.id + '/imprimir', '_blank');
            }
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'Error al guardar.', 'error');
        }
    })
    .catch(() => showToast('Error de conexión.', 'error'))
    .finally(() => { btn.disabled = false; });
}

// ── Informe Médico ────────────────────────────────────────────────────────────
document.getElementById('form-informe-medico').addEventListener('submit', function(e) {
    e.preventDefault();
    submitMedicoForm('form-informe-medico', '{{ route("informes-medicos.store") }}',
        'modal-informe-medico', '{{ url("informes-medicos") }}');
});

// ── Gasto Médico ─────────────────────────────────────────────────────────────
document.getElementById('form-gasto-medico').addEventListener('submit', function(e) {
    e.preventDefault();
    submitMedicoForm('form-gasto-medico', '{{ route("gastos-medicos.store") }}',
        'modal-gasto-medico', '{{ url("gastos-medicos") }}');
});

// ── Exámenes de Laboratorio ───────────────────────────────────────────────────
document.getElementById('form-examen-laboratorio').addEventListener('submit', function(e) {
    e.preventDefault();
    submitMedicoForm('form-examen-laboratorio', '{{ route("examenes-laboratorio.store") }}',
        'modal-examen-laboratorio', '{{ url("examenes-laboratorio") }}');
});
</script>
@endpush
