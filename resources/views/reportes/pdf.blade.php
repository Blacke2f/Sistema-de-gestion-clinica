<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
@page { margin: 18mm 15mm 22mm 15mm; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: "DejaVu Sans", Arial, sans-serif; font-size: 10px; color: #1e293b; background: #fff; }

.header { width: 100%; border-bottom: 3px solid #0d6efd; padding-bottom: 10px; margin-bottom: 14px; }
.header-logo { width: 50px; height: 50px; }
.header-title h1 { font-size: 16px; font-weight: 800; color: #0d6efd; }
.header-title p  { font-size: 8.5px; color: #64748b; margin-top: 2px; }
.badge-periodo   { background: #eff6ff; color: #0d6efd; border: 1px solid #bfdbfe; border-radius: 20px; padding: 2px 9px; font-size: 8.5px; font-weight: 700; }
.generado        { font-size: 7.5px; color: #94a3b8; margin-top: 4px; }

.section-title { font-size: 11px; font-weight: 800; color: #0f172a; background: #eff6ff; padding: 6px 10px; border-left: 4px solid #0d6efd; border-radius: 0 4px 4px 0; margin: 18px 0 10px; text-transform: uppercase; letter-spacing: 0.5px; }

.kpi { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 7px; padding: 9px 7px; text-align: center; border-top: 3px solid #0d6efd; }
.kpi.green  { border-top-color: #16a34a; }
.kpi.orange { border-top-color: #ea580c; }
.kpi.red    { border-top-color: #dc2626; }
.kpi.purple { border-top-color: #7c3aed; }
.kpi.teal   { border-top-color: #0891b2; }
.kpi .num { font-size: 17px; font-weight: 800; color: #0f172a; }
.kpi .lbl { font-size: 7.5px; color: #64748b; margin-top: 2px; line-height: 1.3; }

table.dt { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 8.5px; }
table.dt thead tr  { background: #0d6efd; color: #fff; }
table.dt thead th  { padding: 5px 7px; text-align: left; font-weight: 700; }
table.dt tbody tr:nth-child(even) { background: #f8fafc; }
table.dt tbody tr:nth-child(odd)  { background: #fff; }
table.dt tbody td  { padding: 4px 7px; border-bottom: 1px solid #e2e8f0; }
table.dt tfoot td  { background: #eff6ff; font-weight: 700; padding: 5px 7px; }

.badge { padding: 1px 6px; border-radius: 8px; font-size: 7.5px; font-weight: 700; }
.badge-success { background: #dcfce7; color: #16a34a; }
.badge-warning { background: #fef9c3; color: #ca8a04; }
.badge-danger  { background: #fee2e2; color: #dc2626; }
.badge-info    { background: #e0f2fe; color: #0369a1; }
.tr { text-align: right; }
.tc { text-align: center; }
.no-data { text-align: center; color: #94a3b8; padding: 10px; font-style: italic; font-size: 9px; background: #f8fafc; }

.chart-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 7px; padding: 8px; text-align: center; }
.chart-box h4 { font-size: 9px; font-weight: 700; color: #475569; margin-bottom: 6px; }
.chart-box img { max-width: 100%; height: auto; }
</style>
</head>
<body>

{{-- ENCABEZADO --}}
<table style="width:100%;border-bottom:3px solid #0d6efd;padding-bottom:10px;margin-bottom:14px;">
<tr>
  <td style="vertical-align:middle;" width="60">
    <img src="{{ public_path('img/image-Photoroom.png') }}" width="50" height="50" style="object-fit:contain;">
  </td>
  <td style="vertical-align:middle;padding-left:10px;">
    <div style="font-size:16px;font-weight:800;color:#0d6efd;">SISGECLIN</div>
    <div style="font-size:8.5px;color:#64748b;">Sistema de Gestión Clínica Ocupacional</div>
    <div style="font-size:8.5px;color:#64748b;">Clinica Popular Tipo I "Dr. Gaetano Matarozzo" — Municipio Pena, Yaracuy</div>
  </td>
  <td style="text-align:right;vertical-align:middle;">
    <div style="font-size:11px;font-weight:700;color:#1e293b;">Reporte General de Actividades</div>
    <div style="margin-top:4px;">
      <span class="badge-periodo">{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</span>
    </div>
    <div class="generado">Generado: {{ now()->format('d/m/Y H:i') }}</div>
  </td>
</tr>
</table>

{{-- KPIs GENERALES --}}
<div class="section-title">Resumen Ejecutivo del Período</div>
<table style="width:100%;margin-bottom:14px;">
<tr>
  <td width="14%" style="padding:3px;"><div class="kpi"><div class="num">{{ $kpis['total_pacientes'] }}</div><div class="lbl">Total Pacientes</div></div></td>
  <td width="14%" style="padding:3px;"><div class="kpi green"><div class="num">{{ $kpis['pacientes_nuevos'] }}</div><div class="lbl">Pacientes Nuevos</div></div></td>
  <td width="14%" style="padding:3px;"><div class="kpi"><div class="num">{{ $kpis['consultas'] }}</div><div class="lbl">Consultas</div></div></td>
  <td width="14%" style="padding:3px;"><div class="kpi orange"><div class="num">{{ $kpis['citas_total'] }}</div><div class="lbl">Citas</div></div></td>
  <td width="14%" style="padding:3px;"><div class="kpi purple"><div class="num">{{ $kpis['recetas'] }}</div><div class="lbl">Recetas</div></div></td>
  <td width="14%" style="padding:3px;"><div class="kpi teal"><div class="num">{{ $kpis['examenes'] }}</div><div class="lbl">Exámenes Lab.</div></div></td>
  <td width="14%" style="padding:3px;"><div class="kpi red"><div class="num">{{ $kpis['gastos_total'] }}</div><div class="lbl">Gastos Méd.</div></div></td>
</tr>
</table>

{{-- GRÁFICAS --}}
@if(!empty($graficaConsultas) || !empty($graficaCitas) || !empty($graficaGastos))
<div class="section-title">Gráficas Estadísticas</div>
<table style="width:100%;margin-bottom:14px;">
<tr>
  @if(!empty($graficaConsultas))
  <td style="padding:4px;" width="{{ empty($graficaCitas) && empty($graficaGastos) ? '100%' : (empty($graficaCitas) || empty($graficaGastos) ? '50%' : '33%') }}">
    <div class="chart-box"><h4>Consultas por Mes</h4><img src="{{ $graficaConsultas }}"></div>
  </td>
  @endif
  @if(!empty($graficaCitas))
  <td style="padding:4px;">
    <div class="chart-box"><h4>Citas: Cumplidas vs Canceladas</h4><img src="{{ $graficaCitas }}"></div>
  </td>
  @endif
  @if(!empty($graficaGastos))
  <td style="padding:4px;">
    <div class="chart-box"><h4>Gastos por Categoría</h4><img src="{{ $graficaGastos }}"></div>
  </td>
  @endif
</tr>
</table>
@endif

{{-- PACIENTES --}}
<div class="section-title">Pacientes Registrados en el Período</div>
@if($pacientes->count())
<table class="dt">
  <thead><tr><th>#</th><th>Nombre Completo</th><th>Cédula</th><th>Género</th><th>Fecha Nac.</th><th>Teléfono</th><th>Empresa</th><th>Registro</th></tr></thead>
  <tbody>
  @foreach($pacientes as $i => $p)
  <tr>
    <td class="tc">{{ $i+1 }}</td>
    <td>{{ $p->nombre }} {{ $p->apellido }}</td>
    <td>{{ $p->cedula }}</td>
    <td class="tc">{{ $p->genero === 'M' ? 'Masc.' : 'Fem.' }}</td>
    <td class="tc">{{ $p->fecha_nacimiento ? \Carbon\Carbon::parse($p->fecha_nacimiento)->format('d/m/Y') : '—' }}</td>
    <td>{{ $p->telefono ?? '—' }}</td>
    <td>{{ $p->empresa ?? '—' }}</td>
    <td class="tc">{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') }}</td>
  </tr>
  @endforeach
  </tbody>
</table>
@else
<div class="no-data">No se registraron pacientes en este período.</div>
@endif

{{-- CONSULTAS --}}
<div class="section-title">Consultas Médicas</div>
@if($consultas->count())
<table class="dt">
  <thead><tr><th>#</th><th>Paciente</th><th>Fecha</th><th>Tipo</th><th>Motivo</th><th>Estado</th><th>Médico</th></tr></thead>
  <tbody>
  @foreach($consultas as $i => $c)
  @php $est = strtolower($c->estado ?? ''); @endphp
  <tr>
    <td class="tc">{{ $i+1 }}</td>
    <td>{{ optional($c->paciente)->nombre }} {{ optional($c->paciente)->apellido }}</td>
    <td class="tc">{{ \Carbon\Carbon::parse($c->fecha_hora)->format('d/m/Y') }}</td>
    <td>{{ $c->tipo_consulta ?? '—' }}</td>
    <td>{{ \Str::limit($c->motivo_consulta ?? '—', 45) }}</td>
    <td class="tc"><span class="badge {{ $est==='completada'?'badge-success':($est==='pendiente'?'badge-warning':'badge-info') }}">{{ ucfirst($c->estado??'—') }}</span></td>
    <td>{{ $c->medico ?? '—' }}</td>
  </tr>
  @endforeach
  </tbody>
</table>
@else
<div class="no-data">No hay consultas en este período.</div>
@endif

{{-- CITAS --}}
<div class="section-title">Agenda de Citas</div>
@if($citas->count())
<table class="dt">
  <thead><tr><th>#</th><th>Paciente</th><th>Fecha</th><th>Hora</th><th>Motivo</th><th>Estado</th></tr></thead>
  <tbody>
  @foreach($citas as $i => $c)
  @php $est = strtolower($c->estado ?? ''); @endphp
  <tr>
    <td class="tc">{{ $i+1 }}</td>
    <td>{{ optional($c->paciente)->nombre }} {{ optional($c->paciente)->apellido }}</td>
    <td class="tc">{{ \Carbon\Carbon::parse($c->fecha_cita)->format('d/m/Y') }}</td>
    <td class="tc">{{ $c->hora_cita ?? '—' }}</td>
    <td>{{ \Str::limit($c->motivo ?? '—', 45) }}</td>
    <td class="tc"><span class="badge {{ $est==='completada'?'badge-success':($est==='cancelada'?'badge-danger':'badge-warning') }}">{{ ucfirst($c->estado??'—') }}</span></td>
  </tr>
  @endforeach
  </tbody>
</table>
@else
<div class="no-data">No hay citas en este período.</div>
@endif

{{-- RECETAS --}}
<div class="section-title">Recetas Médicas</div>
@if($recetas->count())
<table class="dt">
  <thead><tr><th>#</th><th>Paciente</th><th>Fecha</th><th>Tipo</th><th>Diagnóstico</th><th>Médico</th></tr></thead>
  <tbody>
  @foreach($recetas as $i => $r)
  <tr>
    <td class="tc">{{ $i+1 }}</td>
    <td>{{ optional($r->paciente)->nombre }} {{ optional($r->paciente)->apellido }}</td>
    <td class="tc">{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y') }}</td>
    <td>{{ $r->tipo ?? 'Digital' }}</td>
    <td>{{ \Str::limit($r->diagnostico ?? '—', 45) }}</td>
    <td>{{ $r->medico ?? '—' }}</td>
  </tr>
  @endforeach
  </tbody>
</table>
@else
<div class="no-data">No hay recetas en este período.</div>
@endif

{{-- EXÁMENES --}}
<div class="section-title">Exámenes de Laboratorio</div>
@if($examenes->count())
<table class="dt">
  <thead><tr><th>#</th><th>Paciente</th><th>Fecha</th><th>Tipo Examen</th><th>Laboratorio</th><th>Estado</th></tr></thead>
  <tbody>
  @foreach($examenes as $i => $e)
  <tr>
    <td class="tc">{{ $i+1 }}</td>
    <td>{{ optional($e->paciente)->nombre }} {{ optional($e->paciente)->apellido }}</td>
    <td class="tc">{{ \Carbon\Carbon::parse($e->created_at)->format('d/m/Y') }}</td>
    <td>{{ $e->tipo_examen ?? '—' }}</td>
    <td>{{ $e->laboratorio ?? '—' }}</td>
    <td class="tc"><span class="badge {{ $e->estado==='entregado'?'badge-success':'badge-warning' }}">{{ ucfirst($e->estado??'Pendiente') }}</span></td>
  </tr>
  @endforeach
  </tbody>
</table>
@else
<div class="no-data">No hay exámenes de laboratorio en este período.</div>
@endif

{{-- GASTOS --}}
<div class="section-title">Gastos Médicos</div>
@if($gastos->count())
@php $totalGastos = $gastos->sum('monto_total'); @endphp
<table class="dt">
  <thead><tr><th>#</th><th>Paciente</th><th>Fecha</th><th>Descripción</th><th>Categoría</th><th class="tr">Monto (Bs.)</th></tr></thead>
  <tbody>
  @foreach($gastos as $i => $g)
  <tr>
    <td class="tc">{{ $i+1 }}</td>
    <td>{{ optional($g->paciente)->nombre }} {{ optional($g->paciente)->apellido }}</td>
    <td class="tc">{{ \Carbon\Carbon::parse($g->created_at)->format('d/m/Y') }}</td>
    <td>{{ \Str::limit($g->descripcion ?? '—', 40) }}</td>
    <td>{{ $g->categoria ?? '—' }}</td>
    <td class="tr">{{ number_format($g->monto_total ?? 0, 2) }}</td>
  </tr>
  @endforeach
  </tbody>
  <tfoot><tr><td colspan="5" class="tr">TOTAL DEL PERÍODO:</td><td class="tr">{{ number_format($totalGastos, 2) }} Bs.</td></tr></tfoot>
</table>
@else
<div class="no-data">No hay gastos médicos en este período.</div>
@endif

{{-- INVENTARIO --}}
<div class="section-title">Movimientos de Inventario</div>
@if($movimientos->count())
<table class="dt">
  <thead><tr><th>#</th><th>Medicamento</th><th>Tipo</th><th class="tc">Cantidad</th><th>Motivo</th><th class="tc">Fecha</th></tr></thead>
  <tbody>
  @foreach($movimientos as $i => $m)
  <tr>
    <td class="tc">{{ $i+1 }}</td>
    <td>{{ optional($m->medicamento)->nombre ?? '—' }}</td>
    <td class="tc"><span class="badge {{ $m->tipo_movimiento==='entrada'?'badge-success':'badge-danger' }}">{{ ucfirst($m->tipo_movimiento) }}</span></td>
    <td class="tc">{{ $m->cantidad }}</td>
    <td>{{ \Str::limit($m->motivo ?? '—', 40) }}</td>
    <td class="tc">{{ \Carbon\Carbon::parse($m->created_at)->format('d/m/Y') }}</td>
  </tr>
  @endforeach
  </tbody>
</table>
@else
<div class="no-data">No hay movimientos de inventario en este período.</div>
@endif

{{-- NÓMINA --}}
<div class="section-title">Personal Activo (Nómina)</div>
@if($nomina->count())
<table class="dt">
  <thead><tr><th>#</th><th>Nombre Completo</th><th>Cédula</th><th>Cargo</th><th>Especialidad</th><th>Teléfono</th></tr></thead>
  <tbody>
  @foreach($nomina as $i => $e)
  <tr>
    <td class="tc">{{ $i+1 }}</td>
    <td>{{ $e->nombre }} {{ $e->apellido }}</td>
    <td>{{ $e->cedula ?? '—' }}</td>
    <td>{{ $e->cargo ?? '—' }}</td>
    <td>{{ $e->especialidad ?? '—' }}</td>
    <td>{{ $e->telefono ?? '—' }}</td>
  </tr>
  @endforeach
  </tbody>
</table>
@else
<div class="no-data">No hay personal en nómina.</div>
@endif

{{-- PIE --}}
<div style="font-size:7.5px;color:#94a3b8;border-top:1px solid #e2e8f0;padding-top:5px;margin-top:16px;text-align:center;">
  SISGECLIN v3.0 — Clinica Popular Tipo I | Reporte Confidencial | Generado: {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
