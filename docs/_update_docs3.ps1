# Update documentacion_codigo.html - changes 1, 2, 3 with correct em-dash
$path = "c:\laragon\www\SISGECLIN\docs\documentacion_codigo.html"
$content = [System.IO.File]::ReadAllText($path)

$emdash = [char]8212  # U+2014

# Change 1: Add new models after PermisoUsuario.php
$old1 = "<li>PermisoUsuario.php $emdash permisos granulares ACL</li>"
$new1 = "<li>PermisoUsuario.php $emdash permisos granulares ACL</li>`n          <li>InformeMedico.php $emdash informes medico-ocupacionales</li>`n          <li>GastoMedico.php / GastoMedicoItem.php $emdash gastos medicos</li>`n          <li>ExamenLaboratorio.php $emdash solicitudes de examenes de lab.</li>"
$count1 = ($content.Split($old1)).Length - 1
Write-Host "Change 1: found $count1 occurrences"
$content = $content.Replace($old1, $new1)
Write-Host "Change 1 done: $($content.Contains('InformeMedico.php'))"

# Change 2: Add new controllers - find "PerfilController" line followed by ExportController
$old2 = "<li>PerfilController $emdash perfil y contrasena</li>`n          <li>ExportController $emdash exportaciones PDF/Excel/CSV</li>"
$new2 = "<li>PerfilController $emdash perfil y contrasena</li>`n          <li>InformeMedicoController $emdash informes medico-ocupacionales + PDF</li>`n          <li>GastoMedicoController $emdash gastos medicos + PDF</li>`n          <li>ExamenLaboratorioController $emdash examenes de laboratorio + PDF</li>`n          <li>ExportController $emdash exportaciones PDF/Excel/CSV</li>"
$count2 = ($content.Split($old2)).Length - 1
Write-Host "Change 2: found $count2 occurrences"
$content = $content.Replace($old2, $new2)
Write-Host "Change 2 done: $($content.Contains('InformeMedicoController'))"

# Change 3: Update views list - exports/ line
$old3 = "<li>exports/ $emdash templates PDF para mPDF</li>"
$new3 = "<li>exports/ $emdash templates PDF para mPDF</li>`n          <li>informes-medicos/, gastos-medicos/, examenes-laboratorio/ $emdash modulos ocupacionales</li>`n          <li>recetas/pdf_maxical.blade.php $emdash formato Informe Medico Maxical A5</li>"
$count3 = ($content.Split($old3)).Length - 1
Write-Host "Change 3: found $count3 occurrences"
$content = $content.Replace($old3, $new3)
Write-Host "Change 3 done: $($content.Contains('informes-medicos/'))"

[System.IO.File]::WriteAllText($path, $content)
Write-Host "documentacion_codigo.html saved successfully"
