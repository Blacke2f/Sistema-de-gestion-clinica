<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,sans-serif; font-size:10pt; color:#1a1a1a; }
table { border-collapse:collapse; }
.bar { height:2pt; background:#003399; margin:3mm 0; }
.sec-lbl { font-size:7pt; font-weight:bold; text-transform:uppercase; color:#003399; background:#eef2ff; padding:1.5mm 3mm; border:1pt solid #003399; border-bottom:none; letter-spacing:.5pt; }
table.sec { width:100%; border:1pt solid #003399; margin-bottom:3mm; }
table.sec td { padding:2.5mm 3mm; font-size:9.5pt; border-bottom:1pt solid #d0d8f0; vertical-align:top; }
table.sec tr:last-child td { border-bottom:none; }
table.meds { width:100%; border:1pt solid #003399; margin-bottom:3mm; }
table.meds thead td { background:#003399; color:#ffffff; font-weight:bold; font-size:8.5pt; padding:2mm 3mm; border-right:1pt solid #5577cc; }
table.meds thead td:last-child { border-right:none; }
table.meds tbody td { padding:2mm 3mm; font-size:9pt; border-bottom:1pt solid #d0d8f0; }
table.meds tbody tr:nth-child(even) td { background:#f5f7ff; }
table.meds tbody tr:last-child td { border-bottom:none; }
table.meds tfoot td { background:#003399; color:#ffffff; font-weight:bold; font-size:9pt; padding:2mm 3mm; }
table.footer { width:100%; margin-top:8mm; }
table.footer td { vertical-align:bottom; padding:0; }
.fline { border-top:1.5pt solid #1a1a1a; width:40mm; margin:0 auto 2mm; }
.flbl { font-size:9pt; font-weight:bold; text-align:center; }
.fsub { font-size:7.5pt; color:#555; text-align:center; margin-top:1mm; }
.pie { margin-top:4mm; text-align:center; font-size:7pt; color:#888; border-top:1pt solid #d0d8f0; padding-top:2mm; }
.alerta { background:#fff3cd; border:1pt solid #ffc107; padding:2mm 3mm; font-size:9pt; margin-bottom:3mm; }
</style>
</head>
<body>

{{-- ENCABEZADO --}}
<table style="width:100%;border-collapse:collapse;border:1.5pt solid #003399;">
<tr>
  <td style="width:26mm;text-align:center;padding:3mm 2mm;border-right:1.5pt solid #003399;vertical-align:middle;background:#ffffff;">
    @if(!empty($logoSrc))
      <img src="{{ $logoSrc }}" width="20mm" alt="SISGECLIN">
    @else
      <span style="font-size:9pt;font-weight:bold;color:#003399;border:1.5pt solid #003399;padding:1mm 2mm;">SISGECLIN</span>
    @endif
    <div style="font-size:6pt;color:#555;margin-top:1mm;">Yaritagua, Estado Yaracuy</div>
  </td>
  <td style="text-align:center;padding:3mm 5mm;vertical-align:middle;background:#ffffff;">
    <div style="font-size:7pt;font-weight:bold;text-transform:uppercase;color:#003399;letter-spacing:.5pt;">Clínica Popular Tipo I "Dr. Gaetano Matarozzo"</div>
    <div style="font-size:11pt;font-weight:bold;color:#1a1a1a;margin:1mm 0;">Sistema de Gestión Clínica</div>
    <div style="font-size:8.5pt;font-weight:bold;color:#003399;letter-spacing:.5pt;">RECETA MEDICA</div>
  </td>
  <td style="width:30mm;text-align:center;padding:3mm 2mm;border-left:1.5pt solid #003399;vertical-align:middle;background:#f5f7ff;">
    <div style="font-size:7pt;color:#555;">Fecha</div>
    <div style="font-size:9.5pt;font-weight:bold;color:#1a1a1a;margin-top:1mm;">{{ $receta->fecha_emision->format('d/m/Y') }}</div>
    <div style="font-size:8.5pt;font-weight:bold;color:#003399;background:#eef2ff;border:1pt solid #003399;padding:1mm 2mm;margin-top:2mm;">{{ $receta->numero_receta }}</div>
  </td>
</tr>
</table>

<div class="bar"></div>

{{-- DATOS DEL PACIENTE --}}
<div class="sec-lbl">Datos del Paciente</div>
<table class="sec">
  <tr>
    <td style="font-weight:bold;color:#003399;background:#f5f7ff;width:30%;">Nombre y Apellido</td>
    <td>{{ $receta->paciente->nombre_completo }}</td>
    <td style="font-weight:bold;color:#003399;background:#f5f7ff;width:20%;">Cedula</td>
    <td>{{ $receta->paciente->cedula }}</td>
  </tr>
  <tr>
    <td style="font-weight:bold;color:#003399;background:#f5f7ff;">Fecha Nacimiento</td>
    <td>{{ $receta->paciente->fecha_nacimiento?->format('d/m/Y') ?? 'N/D' }}</td>
    <td style="font-weight:bold;color:#003399;background:#f5f7ff;">Tipo de Sangre</td>
    <td>{{ $receta->paciente->tipo_sangre ?? 'N/D' }}</td>
  </tr>
  @if($receta->paciente->alergias)
  <tr>
    <td style="font-weight:bold;color:#dc3545;background:#fff5f5;">Alergias</td>
    <td colspan="3" style="color:#dc3545;">{{ $receta->paciente->alergias }}</td>
  </tr>
  @endif
</table>

{{-- DIAGNÓSTICO --}}
@if($receta->diagnostico)
<div class="sec-lbl">Diagnostico</div>
<table class="sec">
  <tr><td style="font-size:9.5pt;">{{ $receta->diagnostico }}</td></tr>
</table>
@endif

{{-- MEDICAMENTOS --}}
<div class="sec-lbl">Medicamentos Prescritos</div>
<table class="meds">
  <thead>
    <tr>
      <td style="width:5%;">#</td>
      <td style="width:28%;">Medicamento</td>
      <td style="width:14%;">Dosis</td>
      <td style="width:16%;">Frecuencia</td>
      <td style="width:14%;">Duracion</td>
      <td style="width:8%;">Cant.</td>
      <td>Indicaciones</td>
    </tr>
  </thead>
  <tbody>
    @forelse($receta->items as $i => $item)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>
        <strong>{{ $item->nombre_medicamento }}</strong>
        @if($item->presentacion)<br><span style="color:#555;font-size:8.5pt;">{{ $item->presentacion }}</span>@endif
      </td>
      <td>{{ $item->dosis }}</td>
      <td>{{ $item->frecuencia }}</td>
      <td>{{ $item->duracion }}</td>
      <td>{{ $item->cantidad }}</td>
      <td style="font-size:8.5pt;">{{ $item->indicaciones ?? '-' }}</td>
    </tr>
    @empty
    <tr><td colspan="7" style="text-align:center;color:#888;font-style:italic;padding:4mm;">Sin medicamentos prescritos</td></tr>
    @endforelse
  </tbody>
</table>

{{-- INDICACIONES GENERALES --}}
@if($receta->indicaciones_generales)
<div class="sec-lbl">Indicaciones Generales</div>
<table class="sec">
  <tr><td style="font-size:9.5pt;">{{ $receta->indicaciones_generales }}</td></tr>
</table>
@endif

{{-- ALERTA VENCIMIENTO --}}
@if($receta->fecha_vencimiento)
<div class="alerta">Esta receta es valida hasta el <strong>{{ $receta->fecha_vencimiento->format('d/m/Y') }}</strong></div>
@endif

{{-- FIRMA --}}
<table class="footer"><tr>
  <td style="width:55%;font-size:8pt;color:#888;vertical-align:bottom;">
    Generado: {{ now()->format('d/m/Y H:i') }}<br>
    SISGECLIN - Sistema de Gestion Clinica
  </td>
  <td style="width:45%;text-align:center;vertical-align:bottom;">
    <div class="fline"></div>
    <div class="flbl">{{ $receta->medico_nombre }}</div>
    <div class="fsub">Medico Tratante - Clínica Dr. Gaetano Matarozzo</div>
  </td>
</tr></table>

<div class="pie">{{ $receta->numero_receta }} | Generado: {{ now()->format('d/m/Y H:i') }} | SISGECLIN</div>

</body>
</html>
