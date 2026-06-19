# Update documentacion_codigo.html
$path = "c:\laragon\www\SISGECLIN\docs\documentacion_codigo.html"
$content = [System.IO.File]::ReadAllText($path)

# Change 1: Add new models after PermisoUsuario.php
$old1 = '<li>PermisoUsuario.php — permisos granulares ACL</li>'
$new1 = '<li>PermisoUsuario.php — permisos granulares ACL</li>
          <li>InformeMedico.php — informes medico-ocupacionales</li>
          <li>GastoMedico.php / GastoMedicoItem.php — gastos medicos</li>
          <li>ExamenLaboratorio.php — solicitudes de examenes de lab.</li>'
$content = $content.Replace($old1, $new1)
Write-Host "Change 1 done: $($content.Contains('InformeMedico.php'))"

# Change 2: Add new controllers after PerfilController (last one in the second list)
$old2 = '<li>PerfilController — perfil y contrasena</li>
          <li>ExportController — exportaciones PDF/Excel/CSV</li>'
$new2 = '<li>PerfilController — perfil y contrasena</li>
          <li>InformeMedicoController — informes medico-ocupacionales + PDF</li>
          <li>GastoMedicoController — gastos medicos + PDF</li>
          <li>ExamenLaboratorioController — examenes de laboratorio + PDF</li>
          <li>ExportController — exportaciones PDF/Excel/CSV</li>'
$content = $content.Replace($old2, $new2)
Write-Host "Change 2 done: $($content.Contains('InformeMedicoController'))"

# Change 3: Update views list - exports/ line
$old3 = '<li>exports/ — templates PDF para mPDF</li>'
$new3 = '<li>exports/ — templates PDF para mPDF</li>
          <li>informes-medicos/, gastos-medicos/, examenes-laboratorio/ — modulos ocupacionales</li>
          <li>recetas/pdf_maxical.blade.php — formato Informe Medico Maxical A5</li>'
$content = $content.Replace($old3, $new3)
Write-Host "Change 3 done: $($content.Contains('informes-medicos/'))"

# Change 4a: Update route count from 115+ to 130+
$old4a = '<div class="info-card"><div class="info-card-icon icon-red"><i class="bx bx-map"></i></div><div><h3>115+</h3><p>Rutas HTTP</p></div></div>'
$new4a = '<div class="info-card"><div class="info-card-icon icon-red"><i class="bx bx-map"></i></div><div><h3>130+</h3><p>Rutas HTTP</p></div></div>'
$content = $content.Replace($old4a, $new4a)
Write-Host "Change 4a done: $($content.Contains('130+'))"

# Change 4b: Update tables count (Tablas activas en BD)
$old4b = '<div class="info-card"><div class="info-card-icon icon-cyan"><i class="bx bx-table"></i></div><div><h3>21</h3><p>Tablas activas en BD</p></div></div>'
$new4b = '<div class="info-card"><div class="info-card-icon icon-cyan"><i class="bx bx-table"></i></div><div><h3>22</h3><p>Tablas activas en BD</p></div></div>'
$content = $content.Replace($old4b, $new4b)
Write-Host "Change 4b done: $($content.Contains('<h3>22</h3><p>Tablas activas en BD</p>'))"

# Change 5: Update permisos modulo description
$old5 = '<td><code>modulo</code></td><td>VARCHAR</td><td>Nombre del modulo: pacientes, consultas, medicina, inventario, nomina, mensajes, usuarios, bitacora, solicitudes</td></tr>'
$new5 = '<td><code>modulo</code></td><td>VARCHAR</td><td>Nombre del modulo: pacientes, consultas, citas, recetas, informes, gastos, examenes, medicina, inventario, nomina, mensajes, usuarios, bitacora, solicitudes, reportes</td></tr>'
$content = $content.Replace($old5, $new5)
Write-Host "Change 5 done: $($content.Contains('informes, gastos, examenes'))"

[System.IO.File]::WriteAllText($path, $content)
Write-Host "documentacion_codigo.html saved successfully"
