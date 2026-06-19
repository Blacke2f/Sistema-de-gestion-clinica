$path = "c:\laragon\www\SISGECLIN\docs\documentacion_codigo.html"
$content = [System.IO.File]::ReadAllText($path)

# Check change 1
$idx = $content.IndexOf('InformeMedico.php')
Write-Host "InformeMedico.php found: $($idx -ge 0)"
if ($idx -ge 0) { Write-Host $content.Substring($idx-5, 200) }

Write-Host "---"

# Check change 2
Write-Host "InformeMedicoController found: $($content.Contains('InformeMedicoController'))"

# Check change 3
Write-Host "informes-medicos/ found: $($content.Contains('informes-medicos/'))"

# Check change 4a
Write-Host "130+ found: $($content.Contains('130+'))"

# Check change 4b
Write-Host "Tablas 22 found: $($content.Contains('<h3>22</h3><p>Tablas activas en BD</p>'))"

# Check change 5
Write-Host "informes, gastos, examenes found: $($content.Contains('informes, gastos, examenes'))"
