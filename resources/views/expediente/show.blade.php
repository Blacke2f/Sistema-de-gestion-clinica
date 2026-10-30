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
.exp-stat { background: var(--bg-card); border-radius: 12px; padding: 16px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,.06); border: 1px solid var(--border-color); }
.exp-stat .num { font-size: 24px; font-weight: 700; color: var(--primary); }
.exp-stat .lbl { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
.timeline { position: relative; padding-left: 28px; }
.timeline::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background: var(--border-color); }
.timeline-item { position: relative; margin-bottom: 20px; }
.timeline-dot { position: absolute; left: -24px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: var(--primary); border: 2px solid var(--bg-card); }
.timeline-card { background: var(--bg-card); border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,.06); border: 1px solid var(--border-color); }
.timeline-card .tc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; flex-wrap: wrap; gap: 6px; }
.timeline-card .tc-fecha { font-size: 12px; color: var(--text-muted); }
.sv-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin-top: 10px; }
.sv-item { background: var(--bg-body); border-radius: 8px; padding: 10px; text-align: center; }
.sv-item .sv-val { font-size: 16px; font-weight: 700; color: var(--text-primary); }
.sv-item .sv-lbl { font-size: 10px; color: var(--text-muted); }
.alergias-badge { background: #fff3cd; color: #856404; border-radius: 6px; padding: 6px 12px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; }
body.dark-mode .alergias-badge { background: rgba(255,193,7,.15); color: #ffc107; }
.option-pill-label { display: inline-flex; align-items: center; gap: 6px; padding: 7px 13px; border: 1.5px solid var(--border-color); border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all .18s ease; background: var(--bg-body); }
.option-pill-label:hover { border-color: var(--primary); }
.option-pill-label input[type="radio"], .option-pill-label input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--primary); margin: 0; }
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

<div style="display:block; width:100%; gap:20px; align-items:start;">

    @php
        $svHistorial = \App\Models\SignoVital::where('paciente_id', $paciente->id)->orderBy('created_at', 'asc')->limit(15)->get();
    @endphp
    @if($svHistorial->count() > 0)
    <div class="table-container" style="margin-bottom:20px;padding:18px 20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px;">
            <h3 style="margin:0;font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px;">
                <i class='bx bx-pulse' style="color:#dc3545;font-size:20px;"></i> Evolución de Signos Vitales ({{ $svHistorial->count() }} mediciones)
            </h3>
            @php $ultSv = $svHistorial->last(); @endphp
            <div style="display:flex;gap:8px;flex-wrap:wrap;font-size:11px;">
                @if($ultSv->presion_sistolica)<span class="badge badge-primary">PA: {{ $ultSv->presion_sistolica }}/{{ $ultSv->presion_diastolica }} mmHg</span>@endif
                @if($ultSv->frecuencia_cardiaca)<span class="badge badge-info">FC: {{ $ultSv->frecuencia_cardiaca }} lpm</span>@endif
                @if($ultSv->peso)<span class="badge badge-success">Peso: {{ $ultSv->peso }} kg</span>@endif
                @if($ultSv->temperatura)<span class="badge badge-warning">Temp: {{ $ultSv->temperatura }} °C</span>@endif
            </div>
        </div>
        <canvas id="chart-signos-vitales" style="max-height:210px;"></canvas>
    </div>
    @endif

    {{-- Historial Clínico Unificado --}}
    <div>
        <div class="table-container">
            <div class="table-header" style="flex-wrap:wrap;gap:10px;">
                <h3><i class='bx bx-history'></i> Historial Clínico Integral</h3>
                <div style="display:flex;gap:6px;flex-wrap:wrap;margin-left:auto;">
                    <button type="button" onclick="filtrarTimeline('todos', this)" class="tl-filter-btn active" style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;border:1px solid var(--primary);background:var(--primary);color:#fff;cursor:pointer;">Todos ({{ count($historial) }})</button>
                    <button type="button" onclick="filtrarTimeline('consulta', this)" class="tl-filter-btn" style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;border:1px solid var(--border-color);background:var(--bg-body);color:var(--text-secondary);cursor:pointer;">Consultas</button>
                    <button type="button" onclick="filtrarTimeline('receta', this)" class="tl-filter-btn" style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;border:1px solid var(--border-color);background:var(--bg-body);color:var(--text-secondary);cursor:pointer;">Recetas</button>
                    <button type="button" onclick="filtrarTimeline('informe', this)" class="tl-filter-btn" style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;border:1px solid var(--border-color);background:var(--bg-body);color:var(--text-secondary);cursor:pointer;">Informes</button>
                    <button type="button" onclick="filtrarTimeline('examen', this)" class="tl-filter-btn" style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;border:1px solid var(--border-color);background:var(--bg-body);color:var(--text-secondary);cursor:pointer;">Laboratorio</button>
                    <button type="button" onclick="filtrarTimeline('gasto', this)" class="tl-filter-btn" style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;border:1px solid var(--border-color);background:var(--bg-body);color:var(--text-secondary);cursor:pointer;">Gastos</button>
                </div>
            </div>
            <div style="padding:16px;">
                @forelse($historial as $evento)
                
                @php
                    $tipo = $evento['tipo'];
                    $fecha = $evento['fecha'];
                    $modelo = $evento['modelo'];
                    
                    $color = '#0d6efd';
                    $icon = 'bx-calendar';
                    $badgeClass = 'primary';
                    $titulo = '';
                    
                    if ($tipo == 'consulta') {
                        $color = $modelo->estado === 'completada' ? '#198754' : ($modelo->estado === 'cancelada' ? '#dc3545' : '#0d6efd');
                        $icon = 'bx-stethoscope';
                        $titulo = 'Consulta Médica';
                        $badgeClass = $modelo->estado === 'completada' ? 'success' : ($modelo->estado === 'cancelada' ? 'danger' : 'primary');
                    } elseif ($tipo == 'informe') {
                        $color = '#6f42c1';
                        $icon = 'bx-file';
                        $titulo = 'Informe Médico';
                        $badgeClass = 'info';
                    } elseif ($tipo == 'gasto') {
                        $color = '#fd7e14';
                        $icon = 'bx-wallet-alt';
                        $titulo = 'Gasto Médico';
                        $badgeClass = 'warning';
                    } elseif ($tipo == 'examen') {
                        $color = '#20c997';
                        $icon = 'bx-test-tube';
                        $titulo = 'Examen de Laboratorio';
                        $badgeClass = 'success';
                    } elseif ($tipo == 'receta') {
                        $color = '#e83e8c';
                        $icon = 'bx-capsule';
                        $titulo = 'Receta Médica';
                        $badgeClass = 'secondary';
                    }
                @endphp
                
                <div class="timeline" data-tl-tipo="{{ $tipo }}">
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:{{ $color }}; display:flex; align-items:center; justify-content:center; width:24px; height:24px; left:-29px; border-radius:50%; color:white; font-size:14px; border:2px solid var(--bg-card);">
                            <i class='bx {{ $icon }}'></i>
                        </div>
                        <div class="timeline-card" style="border-left: 4px solid {{ $color }};">
                            <div class="tc-header">
                                <div>
                                    <strong style="color:{{ $color }};"><i class='bx {{ $icon }}'></i> {{ $titulo }}</strong>
                                    <span style="color:var(--text-muted); font-size:12px; margin-left:8px;">{{ $fecha->format('d/m/Y H:i') }}</span>
                                    
                                    @if($tipo == 'consulta')
                                        <span class="badge badge-{{ $badgeClass }}" style="margin-left:8px;">{{ $modelo->estado_texto }}</span>
                                    @elseif($tipo == 'informe' || $tipo == 'gasto' || $tipo == 'examen')
                                        <span class="badge badge-{{ $badgeClass }}" style="margin-left:8px; text-transform:capitalize;">{{ $modelo->tipo_documento }}</span>
                                    @endif
                                </div>
                                <span class="tc-fecha">
                                    {{ $modelo->medico_nombre ?? ($modelo->medico->nombre_completo ?? 'N/D') }}
                                </span>
                            </div>
                            
                            {{-- Contenido específico según el tipo --}}
                            
                            @if($tipo == 'consulta')
                                <p style="margin:0 0 8px;font-size:13px;"><strong>Motivo:</strong> {{ $modelo->motivo }}</p>
                                @if($modelo->diagnostico)
                                <p style="margin:0 0 8px;font-size:13px;"><strong>Diagnóstico:</strong> {{ $modelo->diagnostico }}</p>
                                @endif
                                @if($modelo->tratamiento)
                                <p style="margin:0 0 8px;font-size:13px;"><strong>Tratamiento:</strong> {{ $modelo->tratamiento }}</p>
                                @endif

                                {{-- Signos vitales de esta consulta --}}
                                @if($modelo->signosVitales && $modelo->signosVitales->isNotEmpty())
                                @php $sv = $modelo->signosVitales->first(); @endphp
                                <div class="sv-grid">
                                    @if($sv->presion_sistolica)
                                    <div class="sv-item"><div class="sv-val">{{ $sv->presion_sistolica }}/{{ $sv->presion_diastolica }}</div><div class="sv-lbl">Presión (mmHg)</div></div>
                                    @endif
                                    @if($sv->frecuencia_cardiaca)
                                    <div class="sv-item"><div class="sv-val">{{ $sv->frecuencia_cardiaca }}</div><div class="sv-lbl">FC (lpm)</div></div>
                                    @endif
                                    @if($sv->temperatura)
                                    <div class="sv-item"><div class="sv-val">{{ $sv->temperatura }}°C</div><div class="sv-lbl">Temp</div></div>
                                    @endif
                                    @if($sv->saturacion_oxigeno)
                                    <div class="sv-item"><div class="sv-val">{{ $sv->saturacion_oxigeno }}%</div><div class="sv-lbl">SpO₂</div></div>
                                    @endif
                                    @if($sv->peso)
                                    <div class="sv-item"><div class="sv-val">{{ $sv->peso }} kg</div><div class="sv-lbl">Peso</div></div>
                                    @endif
                                </div>
                                @endif
                                
                                {{-- Botón nueva receta para esta consulta --}}
                                <div style="margin-top:10px;padding-top:10px;border-top:1px solid var(--border-color);">
                                    <a href="{{ route('recetas.index') }}?paciente_id={{ $paciente->id }}&consulta_id={{ $modelo->id }}"
                                       class="btn-secondary btn-sm" style="font-size:11px;">
                                        <i class='bx bx-file-plus'></i> Nueva Receta (Vinculada)
                                    </a>
                                </div>
                                
                            @elseif($tipo == 'informe')
                                <p style="margin:0 0 8px;font-size:13px;"><strong># Informe:</strong> {{ $modelo->numero_informe }}</p>
                                @if($modelo->diagnostico)
                                <p style="margin:0 0 8px;font-size:13px;"><strong>Diagnóstico:</strong> {{ $modelo->diagnostico }}</p>
                                @endif
                                @if($modelo->recomendaciones)
                                <p style="margin:0 0 8px;font-size:13px;"><strong>Recomendaciones:</strong> {{ $modelo->recomendaciones }}</p>
                                @endif
                                
                                <div style="margin-top:10px;">
                                    @if($modelo->tipo_documento === 'digital')
                                        <a href="{{ route('informes-medicos.imprimir', $modelo->id) }}" target="_blank" class="btn-primary btn-sm" style="font-size:11px; background-color:{{ $color }};">
                                            <i class='bx bx-printer'></i> Imprimir PDF
                                        </a>
                                    @elseif($modelo->imagen_escaneo)
                                        <a href="{{ asset('storage/' . $modelo->imagen_escaneo) }}" target="_blank" class="btn-secondary btn-sm" style="font-size:11px;">
                                            <i class='bx bx-image'></i> Ver Escaneo
                                        </a>
                                    @endif
                                </div>
                                
                            @elseif($tipo == 'gasto')
                                <p style="margin:0 0 8px;font-size:13px;"><strong>Motivo:</strong> {{ $modelo->descripcion ?? '—' }}</p>
                                <p style="margin:0 0 8px;font-size:13px;"><strong>Monto Total:</strong> {{ number_format($modelo->total, 2) }} Bs.</p>
                                
                                <div style="margin-top:10px;">
                                    @if($modelo->tipo_documento === 'digital')
                                        <a href="{{ route('gastos-medicos.imprimir', $modelo->id) }}" target="_blank" class="btn-primary btn-sm" style="font-size:11px; background-color:{{ $color }};">
                                            <i class='bx bx-printer'></i> Imprimir Comprobante
                                        </a>
                                    @elseif($modelo->imagen_escaneo)
                                        <a href="{{ asset('storage/' . $modelo->imagen_escaneo) }}" target="_blank" class="btn-secondary btn-sm" style="font-size:11px;">
                                            <i class='bx bx-image'></i> Ver Escaneo
                                        </a>
                                    @endif
                                </div>
                                
                            @elseif($tipo == 'examen')
                                <p style="margin:0 0 8px;font-size:13px;"><strong># Solicitud:</strong> {{ $modelo->numero_solicitud }}</p>
                                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px;">
                                    @if($modelo->hematologia_completa) <span class="badge badge-secondary">Hematología</span> @endif
                                    @if($modelo->glicemia_ayunas) <span class="badge badge-secondary">Glicemia</span> @endif
                                    @if($modelo->urea) <span class="badge badge-secondary">Urea</span> @endif
                                    @if($modelo->creatinina) <span class="badge badge-secondary">Creatinina</span> @endif
                                    @if($modelo->colesterol_total) <span class="badge badge-secondary">Colesterol</span> @endif
                                    @if($modelo->trigliceridos) <span class="badge badge-secondary">Triglicéridos</span> @endif
                                    @if($modelo->examen_orina) <span class="badge badge-secondary">Orina</span> @endif
                                    @if($modelo->examen_heces) <span class="badge badge-secondary">Heces</span> @endif
                                </div>
                                @if($modelo->otros)
                                <p style="margin:0 0 8px;font-size:13px;"><strong>Otros:</strong> {{ $modelo->otros }}</p>
                                @endif
                                
                                <div style="margin-top:10px;">
                                    @if($modelo->tipo_documento === 'digital')
                                        <a href="{{ route('examenes-laboratorio.imprimir', $modelo->id) }}" target="_blank" class="btn-primary btn-sm" style="font-size:11px; background-color:{{ $color }};">
                                            <i class='bx bx-printer'></i> Imprimir Solicitud
                                        </a>
                                    @elseif($modelo->imagen_escaneo)
                                        <a href="{{ asset('storage/' . $modelo->imagen_escaneo) }}" target="_blank" class="btn-secondary btn-sm" style="font-size:11px;">
                                            <i class='bx bx-image'></i> Ver Escaneo
                                        </a>
                                    @endif
                                </div>
                                
                            @elseif($tipo == 'receta')
                                <p style="margin:0 0 8px;font-size:13px;"><strong># Receta:</strong> {{ $modelo->numero_receta }}</p>
                                @if($modelo->diagnostico)
                                <p style="margin:0 0 8px;font-size:13px;"><strong>Diagnóstico:</strong> {{ $modelo->diagnostico }}</p>
                                @endif
                                
                                <div style="margin-top:10px;">
                                    <a href="{{ route('recetas.imprimir', $modelo->id) }}" target="_blank" class="btn-primary btn-sm" style="font-size:11px; background-color:{{ $color }}; border-color:{{ $color }};">
                                        <i class='bx bx-printer'></i> Imprimir PDF
                                    </a>
                                </div>
                                
                            @endif

                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state"><i class='bx bx-clipboard'></i><p>Sin historial clínico registrado</p></div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL: NUEVO INFORME MÉDICO
══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal" id="modal-informe-medico">
    <div class="modal-content" style="max-width:660px;">
        <div class="modal-header">
            <h2><i class='bx bx-file'></i> Nuevo Informe Médico</h2>
            <button class="modal-close" onclick="closeModal('modal-informe-medico')">&times;</button>
        </div>
        <form id="form-informe-medico" class="modal-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">

            {{-- Selector tipo --}}
            <div class="form-group">
                <label style="font-weight:700;margin-bottom:8px;display:block;">Tipo de Informe</label>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <label id="lbl-exp-inf-digital" style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 18px;border:2px solid var(--primary);border-radius:10px;font-weight:600;background:rgba(13,110,253,.07);">
                        <input type="radio" name="tipo_documento" value="digital" checked
                               onchange="toggleTipoInforme('digital')">
                        <i class='bx bx-file-blank'></i> Digital
                    </label>
                    <label id="lbl-exp-inf-manuscrita" style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 18px;border:2px solid var(--border-color);border-radius:10px;font-weight:600;">
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
                    <input type="file" name="imagen_escaneo" accept="image/*,.pdf" disabled
                           style="padding:10px;border:2px dashed var(--border-color);border-radius:10px;width:100%;cursor:pointer;">
                    <small style="color:var(--text-muted);">JPG, PNG, WEBP o PDF — máx. 5MB</small>
                </div>
            </div>

            {{-- Médico y Fecha siempre visibles --}}
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

            {{-- Formulario digital --}}
            <div id="inf-digital-sect">
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
                    <label>Tipo de Consulta</label>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                        <label class="option-pill-label"><input type="radio" name="tipo_consulta" value="preempleo" checked onchange="document.getElementById('tipo-otros-inf').style.display='none'"> Pre-empleo</label>
                        <label class="option-pill-label"><input type="radio" name="tipo_consulta" value="prevacacional" onchange="document.getElementById('tipo-otros-inf').style.display='none'"> Pre-vacacional</label>
                        <label class="option-pill-label"><input type="radio" name="tipo_consulta" value="postvacacional" onchange="document.getElementById('tipo-otros-inf').style.display='none'"> Post-vacacional</label>
                        <label class="option-pill-label"><input type="radio" name="tipo_consulta" value="egreso" onchange="document.getElementById('tipo-otros-inf').style.display='none'"> Egreso</label>
                        <label class="option-pill-label"><input type="radio" name="tipo_consulta" value="reubicacion" onchange="document.getElementById('tipo-otros-inf').style.display='none'"> Reubicación</label>
                        <label class="option-pill-label"><input type="radio" name="tipo_consulta" value="otros" onchange="document.getElementById('tipo-otros-inf').style.display = this.checked ? 'block' : 'none'"> Otros</label>
                    </div>
                    <div id="tipo-otros-inf" style="display:none;margin-top:8px;">
                        <input type="text" name="tipo_consulta_otros" placeholder="Especifique otro tipo...">
                    </div>
                </div>

                <div class="form-group">
                    <label>Aptitud del Trabajador</label>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <label class="option-pill-label"><input type="radio" name="aptitud" value="apto" checked> Apto</label>
                        <label class="option-pill-label"><input type="radio" name="aptitud" value="no_apto"> No Apto</label>
                        <label class="option-pill-label"><input type="radio" name="aptitud" value="apto_condicionado"> Apto Condicionado</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Diagnóstico</label>
                    <textarea name="diagnostico" rows="2" placeholder="Diagnóstico médico general..."></textarea>
                </div>

                <div class="form-group">
                    <label>Recomendaciones</label>
                    <textarea name="recomendaciones" rows="2" placeholder="Indicaciones y recomendaciones..."></textarea>
                </div>
            </div>{{-- /inf-digital-sect --}}

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-informe-medico')">Cancelar</button>
                <button type="submit" class="btn-primary"><i class='bx bx-save'></i> Guardar y Generar PDF</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL: NUEVO GASTO MÉDICO
══════════════════════════════════════════════════════════════════════════ --}}
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
                    <label id="lbl-exp-gasto-digital" style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 18px;border:2px solid var(--primary);border-radius:10px;font-weight:600;background:rgba(13,110,253,.07);">
                        <input type="radio" name="tipo_documento" value="digital" checked
                               onchange="toggleTipoGasto('digital')">
                        <i class='bx bx-list-ul'></i> Digital
                    </label>
                    <label id="lbl-exp-gasto-manuscrita" style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 18px;border:2px solid var(--border-color);border-radius:10px;font-weight:600;">
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
                    <input type="file" name="imagen_escaneo" accept="image/*,.pdf" disabled
                           style="padding:10px;border:2px dashed var(--border-color);border-radius:10px;width:100%;cursor:pointer;">
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
                            style="margin-left:8px;background:rgba(13,110,253,.1);border:1px solid rgba(13,110,253,.3);color:var(--primary);border-radius:6px;padding:3px 10px;font-size:11px;font-weight:600;cursor:pointer;">
                            <i class='bx bx-plus'></i> Agregar
                        </button>
                    </label>
                    <div id="items-gasto">
                        <div class="gasto-item-row" style="display:flex;gap:8px;margin-bottom:6px;">
                            <input type="text" name="items[0][concepto]" placeholder="Concepto" style="flex:2;" required>
                            <input type="number" name="items[0][monto]" placeholder="Monto" step="0.01" min="0" style="flex:1;" required>
                            <button type="button" onclick="this.closest('.gasto-item-row').remove();calcularTotalGasto()"
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

{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL: SOLICITUD DE EXÁMENES DE LABORATORIO
══════════════════════════════════════════════════════════════════════════ --}}
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
                    <label id="lbl-exp-exa-digital" style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 18px;border:2px solid var(--primary);border-radius:10px;font-weight:600;background:rgba(13,110,253,.07);">
                        <input type="radio" name="tipo_documento" value="digital" checked
                               onchange="toggleTipoExamen('digital')">
                        <i class='bx bx-check-square'></i> Digital
                    </label>
                    <label id="lbl-exp-exa-manuscrita" style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 18px;border:2px solid var(--border-color);border-radius:10px;font-weight:600;">
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
                    <input type="file" name="imagen_escaneo" accept="image/*,.pdf" disabled
                           style="padding:10px;border:2px dashed var(--border-color);border-radius:10px;width:100%;cursor:pointer;">
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
                        <label class="option-pill-label" style="font-weight:500;">
                            <input type="checkbox" name="{{ $campo }}" value="1">
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

function _styleRadioPills(lblDigitalId, lblManId, isDigital) {
    const lDig = document.getElementById(lblDigitalId);
    const lMan = document.getElementById(lblManId);
    if (lDig) {
        lDig.style.borderColor = isDigital ? 'var(--primary)' : 'var(--border-color)';
        lDig.style.background  = isDigital ? 'rgba(13,110,253,.07)' : 'transparent';
    }
    if (lMan) {
        lMan.style.borderColor = !isDigital ? 'var(--primary)' : 'var(--border-color)';
        lMan.style.background  = !isDigital ? 'rgba(13,110,253,.07)' : 'transparent';
    }
}

function toggleTipoInforme(tipo) {
    const isDigital = tipo === 'digital';
    document.getElementById('inf-digital-sect').style.display    = isDigital ? 'block' : 'none';
    document.getElementById('inf-manuscrita-sect').style.display = isDigital ? 'none'  : 'block';
    _toggleRequired('inf-digital-sect',    isDigital);
    _toggleRequired('inf-manuscrita-sect', !isDigital);
    _styleRadioPills('lbl-exp-inf-digital', 'lbl-exp-inf-manuscrita', isDigital);
}

function toggleTipoGasto(tipo) {
    const isDigital = tipo === 'digital';
    document.getElementById('gasto-digital-sect').style.display    = isDigital ? 'block' : 'none';
    document.getElementById('gasto-manuscrita-sect').style.display = isDigital ? 'none'  : 'block';
    _toggleRequired('gasto-digital-sect',    isDigital);
    _toggleRequired('gasto-manuscrita-sect', !isDigital);
    _styleRadioPills('lbl-exp-gasto-digital', 'lbl-exp-gasto-manuscrita', isDigital);
}

function toggleTipoExamen(tipo) {
    const isDigital = tipo === 'digital';
    document.getElementById('examen-digital-sect').style.display    = isDigital ? 'block' : 'none';
    document.getElementById('examen-manuscrita-sect').style.display = isDigital ? 'none'  : 'block';
    _toggleRequired('examen-digital-sect',    isDigital);
    _toggleRequired('examen-manuscrita-sect', !isDigital);
    _styleRadioPills('lbl-exp-exa-digital', 'lbl-exp-exa-manuscrita', isDigital);
}

// ── Helper: submit con FormData (soporta archivos) ───────────────────────────
function submitMedicoForm(formId, storeUrl, modalId, pdfBaseUrl) {
    const form = document.getElementById(formId);
    const btn  = form.querySelector('[type=submit]');
    btn.disabled = true;
    fetch(storeUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json'
        },
        body: new FormData(form),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeModal(modalId);
            form.reset();
            if (data.tipo_documento !== 'manuscrita') {
                window.open(pdfBaseUrl + '/' + data.id + '/imprimir', '_blank');
            }
            setTimeout(() => location.reload(), 800);
        } else {
            const firstErr = data.errors ? Object.values(data.errors)[0]?.[0] : null;
            showToast(firstErr || data.message || 'Error al guardar.', 'error');
        }
    })
    .catch(() => showToast('Error de conexión.', 'error'))
    .finally(() => { btn.disabled = false; });
}

// ── Informe Médico ───────────────────────────────────────────────────────────
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

// ── Exámenes de Laboratorio ──────────────────────────────────────────────────
document.getElementById('form-examen-laboratorio').addEventListener('submit', function(e) {
    e.preventDefault();
    submitMedicoForm('form-examen-laboratorio', '{{ route("examenes-laboratorio.store") }}',
        'modal-examen-laboratorio', '{{ url("examenes-laboratorio") }}');
});

function filtrarTimeline(tipo, btnEl) {
    document.querySelectorAll('.tl-filter-btn').forEach(function(b) {
        b.style.background = 'var(--bg-body)';
        b.style.color = 'var(--text-secondary)';
        b.style.borderColor = 'var(--border-color)';
    });
    if (btnEl) {
        btnEl.style.background = 'var(--primary)';
        btnEl.style.color = '#fff';
        btnEl.style.borderColor = 'var(--primary)';
    }
    document.querySelectorAll('.timeline[data-tl-tipo]').forEach(function(el) {
        el.style.display = (tipo === 'todos' || el.getAttribute('data-tl-tipo') === tipo) ? 'block' : 'none';
    });
}
</script>
@if(isset($svHistorial) && $svHistorial->count() > 0)
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
(function() {
    const canvas = document.getElementById('chart-signos-vitales');
    if (!canvas || typeof Chart === 'undefined') return;
    const isDark = document.body.classList.contains('dark-mode') || localStorage.getItem('darkMode') === 'true';
    const gridColor = isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.06)';
    new Chart(canvas, {
        type: 'line',
        data: {
            labels: @json($svHistorial->map(fn($s) => $s->created_at->format('d/m/Y'))->values()),
            datasets: [
                {
                    label: 'P. Sistólica (mmHg)',
                    data: @json($svHistorial->map(fn($s) => $s->presion_sistolica)->values()),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220,53,69,.1)',
                    tension: 0.3,
                    borderWidth: 2,
                },
                {
                    label: 'P. Diastólica (mmHg)',
                    data: @json($svHistorial->map(fn($s) => $s->presion_diastolica)->values()),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,.1)',
                    tension: 0.3,
                    borderWidth: 2,
                },
                {
                    label: 'Frec. Cardíaca (lpm)',
                    data: @json($svHistorial->map(fn($s) => $s->frecuencia_cardiaca)->values()),
                    borderColor: '#0dcaf0',
                    backgroundColor: 'rgba(13,202,240,.1)',
                    tension: 0.3,
                    borderWidth: 2,
                },
                {
                    label: 'Peso (kg)',
                    data: @json($svHistorial->map(fn($s) => $s->peso)->values()),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25,135,84,.1)',
                    tension: 0.3,
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
            scales: {
                y: { grid: { color: gridColor } },
                x: { grid: { display: false } }
            }
        }
    });
})();
</script>
@endif
@endpush
