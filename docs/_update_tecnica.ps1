# Update documentacion_tecnica.html
$path = "c:\laragon\www\SISGECLIN\docs\documentacion_tecnica.html"
$content = [System.IO.File]::ReadAllText($path)

# Change 1: Add 4 new tables after permisos_usuario row
$old1 = '<tr><td><code>permisos_usuario</code></td><td>Permisos granulares por usuario y modulo (13 modulos)</td><td><span class="badge badge-gray">Auto</span></td></tr>'
$new1 = '<tr><td><code>permisos_usuario</code></td><td>Permisos granulares por usuario y modulo (13 modulos)</td><td><span class="badge badge-gray">Auto</span></td></tr>
<tr><td><code>informes_medicos</code></td><td>Informes medico-ocupacionales Maxical (tipo pre-empleo, egreso, etc.)</td><td><span class="badge badge-gray">Auto</span></td></tr>
<tr><td><code>gastos_medicos</code></td><td>Gastos de atencion medica con conceptos e importes</td><td><span class="badge badge-gray">Auto</span></td></tr>
<tr><td><code>gasto_medico_items</code></td><td>Items de conceptos de cada gasto medico</td><td><span class="badge badge-gray">Auto</span></td></tr>
<tr><td><code>examenes_laboratorio</code></td><td>Solicitudes de examenes con checkboxes por categoria</td><td><span class="badge badge-gray">Auto</span></td></tr>'
$content = $content.Replace($old1, $new1)
Write-Host "Change 1 (new tables) done: $($content.Contains('informes_medicos'))"

# Change 2: Update route count mention (80+ to 95+)
$old2a = '80+ rutas organizadas por modulo y nivel de acceso'
$new2a = '95+ rutas organizadas por modulo y nivel de acceso'
$content = $content.Replace($old2a, $new2a)
Write-Host "Change 2a (route count header) done: $($content.Contains('95+ rutas'))"

$old2b = '<code>routes/web.php</code></td><td>80+ rutas organizadas por modulo y middleware</td>'
$new2b = '<code>routes/web.php</code></td><td>95+ rutas organizadas por modulo y middleware</td>'
$content = $content.Replace($old2b, $new2b)
Write-Host "Change 2b (routes/web.php description) done: $($content.Contains('95+ rutas organizadas por modulo y middleware'))"

# Change 3: Add new modules in the modulos section - after the Nomina feature-item
$emdash = [char]8212
$old3 = '<div class="feature-item"><i class="bx bxs-user-account" style="color:#1a2234;"></i><div><h4>Usuarios (admin)</h4>'
$new3 = '<div class="feature-item"><i class="bx bx-id-card" style="color:#0dcaf0;"></i><div><h4>Informes Medicos</h4><p>Informes medico-ocupacionales Maxical: pre-empleo, prevacacional, postvacacional, egreso, reubicacion. PDF A5 con aptitud, diagnostico y estudios paraclinicos.</p></div></div>
<div class="feature-item"><i class="bx bx-receipt" style="color:#fd7e14;"></i><div><h4>Gastos Medicos</h4><p>Registro de gastos de atencion medica con conceptos itemizados e importes. Generacion de PDF A5 con tabla de conceptos y totales.</p></div></div>
<div class="feature-item"><i class="bx bx-test-tube" style="color:#198754;"></i><div><h4>Examenes de Laboratorio</h4><p>Solicitudes de examenes con checkboxes: hematologia, glicemia, urea, creatinina, colesterol, trigliceridos, orina, heces y otros. PDF A5 imprimible.</p></div></div>
<div class="feature-item"><i class="bx bxs-user-account" style="color:#1a2234;"></i><div><h4>Usuarios (admin)</h4>'
$content = $content.Replace($old3, $new3)
Write-Host "Change 3 (new modules) done: $($content.Contains('Informes Medicos'))"

[System.IO.File]::WriteAllText($path, $content)
Write-Host "documentacion_tecnica.html saved successfully"
