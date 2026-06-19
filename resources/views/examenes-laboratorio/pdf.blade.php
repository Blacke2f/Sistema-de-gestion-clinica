<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,sans-serif; font-size:10pt; color:#1a1a1a; }
table { border-collapse:collapse; }
.bar { height:2pt; background:#003399; margin:3mm 0; }
.stitle { font-size:7pt; font-weight:bold; text-transform:uppercase; color:#003399; background:#eef2ff; padding:1.5mm 3mm; border:1pt solid #003399; border-bottom:none; letter-spacing:.5pt; }
table.dt { width:100%; border:1pt solid #003399; margin-bottom:4mm; }
table.dt td { padding:2mm 3mm; font-size:9pt; border-bottom:1pt solid #d0d8f0; vertical-align:middle; }
table.dt tr:last-child td { border-bottom:none; }
table.exam-wrap { width:100%; border:1.5pt solid #003399; margin-bottom:4mm; }
.exam-sub { text-align:center; font-size:8pt; font-weight:bold; color:#003399; margin-bottom:3mm; border-bottom:1pt solid #d0d8f0; padding-bottom:2mm; }
table.ecols td { vertical-align:top; width:50%; padding:0; }
td.ec-left { padding-right:4mm; border-right:1pt dashed #003399; }
td.ec-right { padding-left:4mm; }
.gtitle { font-size:7pt; font-weight:bold; text-transform:uppercase; color:#003399; background:#f5f7ff; border-bottom:1pt solid #d0d8f0; padding:1mm 2mm; margin-bottom:2mm; margin-top:3mm; }
.gtitle-first { margin-top:0; }
table.ck { width:100%; margin-bottom:2mm; }
table.ck td { padding:1mm 0; vertical-align:middle; }
td.ckb { width:6mm; }
.ck-lbl { font-size:9.5pt; padding-left:1.5mm; color:#1a1a1a; }
.ck-lbl.dim { color:#aaaaaa; }
.ck-row-on { background:#eef2ff; }
.otros-sect { margin-top:3mm; padding-top:2mm; border-top:1pt dashed #003399; }
.otros-line { display:block; border-bottom:1pt solid #003399; min-height:6mm; padding:1mm 2mm; margin-top:1mm; font-size:9pt; }
table.firma { width:100%; margin-top:10mm; }
table.firma td { text-align:center; padding:0; width:50%; }
.fline { border-top:1.5pt solid #1a1a1a; width:40mm; margin:0 auto 2mm; }
.flbl { font-size:9pt; font-weight:bold; color:#1a1a1a; }
.fsub { font-size:7.5pt; color:#555; margin-top:1mm; }
.pie { margin-top:5mm; text-align:center; font-size:7pt; color:#888; border-top:1pt solid #d0d8f0; padding-top:2mm; }
</style>
</head>
<body>

{{-- ENCABEZADO: todo inline para máxima compatibilidad mPDF --}}
<table style="width:100%;border-collapse:collapse;border:1.5pt solid #003399;">
<tr>
  <td class="logo-c">
    @if(!empty($logoSrc))
      <img src="{{ $logoSrc }}" width="20mm" alt="Logo">
    @else
      <span style="font-size:8pt;font-weight:bold;color:#003399;border:1.5pt solid #003399;padding:1mm 2mm;display:block;text-align:center;">CLINICA<br>POPULAR</span>
    @endif
  </td>
  <td class="info-c">
    <div style="font-size:10pt;font-weight:bold;text-transform:uppercase;color:#003399;letter-spacing:.5pt;line-height:1.2;">Clinica Popular Tipo I<br>"Dr. Gaetano Matarozzo"</div>
    <div style="font-size:8pt;color:#555;margin-top:1.5mm;font-weight:normal;">Municipio Pena, Estado Yaracuy</div>
    <div style="font-size:8.5pt;font-weight:bold;color:#003399;letter-spacing:.5pt;margin-top:2mm;">ORDEN DE LABORATORIO</div>
  </td>
  <td style="width:30mm;text-align:center;padding:3mm 2mm;border-left:1.5pt solid #003399;vertical-align:middle;background:#003399;">
    <div style="font-size:8pt;font-weight:bold;color:#ffffff;line-height:1.6;text-transform:uppercase;">Solicitud<br>Examenes<br>Laboratorio</div>
    <div style="font-size:8.5pt;font-weight:bold;color:#003399;background:#ffffff;padding:1mm 3mm;margin-top:2mm;">{{ $examen->numero_solicitud }}</div>
  </td>
</tr>
</table>

<div class="bar"></div>

{{-- DATOS DEL TRABAJADOR --}}
<div class="stitle">Datos del Trabajador</div>
<table class="dt">
  <tr>
    <td style="font-weight:bold;color:#003399;background:#f5f7ff;width:32%;">Nombre y Apellido:</td>
    <td style="width:33%;">{{ $examen->paciente->nombre_completo }}</td>
    <td style="font-weight:bold;color:#003399;background:#f5f7ff;width:15%;">Fecha:</td>
    <td>{{ $examen->fecha->format('d/m/Y') }}</td>
  </tr>
  <tr>
    <td style="font-weight:bold;color:#003399;background:#f5f7ff;">Cedula:</td>
    <td>{{ $examen->paciente->cedula }}</td>
    <td style="font-weight:bold;color:#003399;background:#f5f7ff;">Edad:</td>
    <td>{{ $examen->paciente->edad ? $examen->paciente->edad.' anos' : 'N/D' }}</td>
  </tr>
  @if($examen->medico_nombre && $examen->medico_nombre !== 'Sin asignar')
  <tr>
    <td style="font-weight:bold;color:#003399;background:#f5f7ff;">Medico Solicitante:</td>
    <td colspan="3">{{ $examen->medico_nombre }}</td>
  </tr>
  @endif
</table>

{{-- EXÁMENES SOLICITADOS --}}
<table class="exam-wrap">
  <tr>
    <td style="background:#003399;color:#ffffff;text-align:center;padding:2.5mm 3mm;font-size:8.5pt;font-weight:bold;text-transform:uppercase;letter-spacing:.5pt;">
      Examenes Solicitados
    </td>
  </tr>
  <tr>
    <td style="padding:3mm 4mm;background:#ffffff;">
      <div class="exam-sub">Favor realizar los examenes marcados</div>
      <table class="ecols"><tr>
        <td class="ec-left">
          <div class="gtitle gtitle-first">Hematologia</div>
          <table class="ck {{ $examen->hematologia_completa ? 'ck-row-on':'' }}"><tr>
            <td class="ckb">
              <div style="width:4mm;height:4mm;border:1.5pt solid #003399;text-align:center;font-size:9pt;font-weight:bold;line-height:4mm;background:{{ $examen->hematologia_completa ? '#003399' : '#ffffff' }};color:{{ $examen->hematologia_completa ? '#ffffff' : '#ffffff' }};">{!! $examen->hematologia_completa ? '&#10003;' : '&nbsp;' !!}</div>
            </td>
            <td><span class="ck-lbl {{ $examen->hematologia_completa ? '' : 'dim' }}">Hematologia Completa</span></td>
          </tr></table>

          <div class="gtitle">Quimica Sanguinea</div>
          <table class="ck {{ $examen->glicemia_ayunas ? 'ck-row-on':'' }}"><tr>
            <td class="ckb"><div style="width:4mm;height:4mm;border:1.5pt solid #003399;text-align:center;font-size:9pt;font-weight:bold;line-height:4mm;background:{{ $examen->glicemia_ayunas ? '#003399' : '#ffffff' }};color:#ffffff;">{!! $examen->glicemia_ayunas ? '&#10003;' : '&nbsp;' !!}</div></td>
            <td><span class="ck-lbl {{ $examen->glicemia_ayunas ? '' : 'dim' }}">Glicemia en Ayunas</span></td>
          </tr></table>
          <table class="ck {{ $examen->urea ? 'ck-row-on':'' }}"><tr>
            <td class="ckb"><div style="width:4mm;height:4mm;border:1.5pt solid #003399;text-align:center;font-size:9pt;font-weight:bold;line-height:4mm;background:{{ $examen->urea ? '#003399' : '#ffffff' }};color:#ffffff;">{!! $examen->urea ? '&#10003;' : '&nbsp;' !!}</div></td>
            <td><span class="ck-lbl {{ $examen->urea ? '' : 'dim' }}">Urea</span></td>
          </tr></table>
          <table class="ck {{ $examen->creatinina ? 'ck-row-on':'' }}"><tr>
            <td class="ckb"><div style="width:4mm;height:4mm;border:1.5pt solid #003399;text-align:center;font-size:9pt;font-weight:bold;line-height:4mm;background:{{ $examen->creatinina ? '#003399' : '#ffffff' }};color:#ffffff;">{!! $examen->creatinina ? '&#10003;' : '&nbsp;' !!}</div></td>
            <td><span class="ck-lbl {{ $examen->creatinina ? '' : 'dim' }}">Creatinina</span></td>
          </tr></table>
        </td>
        <td class="ec-right">
          <div class="gtitle gtitle-first">Lipidos</div>
          <table class="ck {{ $examen->colesterol_total ? 'ck-row-on':'' }}"><tr>
            <td class="ckb"><div style="width:4mm;height:4mm;border:1.5pt solid #003399;text-align:center;font-size:9pt;font-weight:bold;line-height:4mm;background:{{ $examen->colesterol_total ? '#003399' : '#ffffff' }};color:#ffffff;">{!! $examen->colesterol_total ? '&#10003;' : '&nbsp;' !!}</div></td>
            <td><span class="ck-lbl {{ $examen->colesterol_total ? '' : 'dim' }}">Colesterol Total</span></td>
          </tr></table>
          <table class="ck {{ $examen->trigliceridos ? 'ck-row-on':'' }}"><tr>
            <td class="ckb"><div style="width:4mm;height:4mm;border:1.5pt solid #003399;text-align:center;font-size:9pt;font-weight:bold;line-height:4mm;background:{{ $examen->trigliceridos ? '#003399' : '#ffffff' }};color:#ffffff;">{!! $examen->trigliceridos ? '&#10003;' : '&nbsp;' !!}</div></td>
            <td><span class="ck-lbl {{ $examen->trigliceridos ? '' : 'dim' }}">Trigliceridos</span></td>
          </tr></table>

          <div class="gtitle">Uroanalisis y Coproanalisis</div>
          <table class="ck {{ $examen->examen_orina ? 'ck-row-on':'' }}"><tr>
            <td class="ckb"><div style="width:4mm;height:4mm;border:1.5pt solid #003399;text-align:center;font-size:9pt;font-weight:bold;line-height:4mm;background:{{ $examen->examen_orina ? '#003399' : '#ffffff' }};color:#ffffff;">{!! $examen->examen_orina ? '&#10003;' : '&nbsp;' !!}</div></td>
            <td><span class="ck-lbl {{ $examen->examen_orina ? '' : 'dim' }}">Examen de Orina</span></td>
          </tr></table>
          <table class="ck {{ $examen->examen_heces ? 'ck-row-on':'' }}"><tr>
            <td class="ckb"><div style="width:4mm;height:4mm;border:1.5pt solid #003399;text-align:center;font-size:9pt;font-weight:bold;line-height:4mm;background:{{ $examen->examen_heces ? '#003399' : '#ffffff' }};color:#ffffff;">{!! $examen->examen_heces ? '&#10003;' : '&nbsp;' !!}</div></td>
            <td><span class="ck-lbl {{ $examen->examen_heces ? '' : 'dim' }}">Examen de Heces</span></td>
          </tr></table>
        </td>
      </tr></table>

      <div class="otros-sect">
        <strong style="font-size:8pt;color:#003399;text-transform:uppercase;">Otros examenes:</strong>
        <span class="otros-line">{{ $examen->otros ?? '' }}</span>
      </div>
    </td>
  </tr>
</table>

{{-- RESUMEN --}}
@php $marcados = $examen->examenes_solicitados; @endphp
@if(count($marcados) > 0)
<div style="background:#003399;color:#ffffff;padding:2mm 4mm;margin-top:3mm;font-size:8.5pt;font-weight:bold;">
  Examenes a realizar ({{ count($marcados) }}): {{ implode(' | ', $marcados) }}
</div>
@endif

{{-- FIRMA --}}
<table class="firma"><tr>
  <td>
    <div class="fline"></div>
    <div class="flbl">Firma del Trabajador</div>
    <div class="fsub">C.I.: {{ $examen->paciente->cedula }}</div>
  </td>
  <td>
    <div class="fline"></div>
    <div class="flbl">{{ ($examen->medico_nombre && $examen->medico_nombre !== 'Sin asignar') ? 'Dr(a). '.$examen->medico_nombre : 'Medico Solicitante' }}</div>
    <div class="fsub">Medico Tratante - Clinica Popular</div>
  </td>
</tr></table>

<div class="pie">Solicitud: {{ $examen->numero_solicitud }} | Fecha: {{ $examen->fecha->format('d/m/Y') }} | Generado: {{ now()->format('d/m/Y H:i') }} | SISGECLIN</div>

</body>
</html>
