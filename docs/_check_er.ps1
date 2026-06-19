$path = "c:\laragon\www\SISGECLIN\docs\modelo_er.html"
$content = [System.IO.File]::ReadAllText($path)

# Find footer
$idx = $content.IndexOf('tablas activas')
Write-Host "tablas activas at: $idx"
if ($idx -ge 0) { 
    $s = $content.Substring($idx-30, 200)
    Write-Host "Footer text: [$s]"
    # Print char codes around the em-dash
    $dashIdx = $s.IndexOf('activas') + 8
    for($i = $dashIdx; $i -lt $dashIdx+40; $i++) {
        Write-Host "  Char[$i]: $([int][char]$s[$i]) = '$($s[$i])'"
    }
}

# Check if changes 1-5 were applied
Write-Host "---"
Write-Host "er-ocupacional tab: $($content.Contains('er-ocupacional'))"
Write-Host "mermaid-ocupacional: $($content.Contains('mermaid-ocupacional'))"
Write-Host "informes_medicos in completo: $($content.Contains('informes_medicos'))"
Write-Host "INFORMES_MEDICOS table card: $($content.Contains('INFORMES_MEDICOS'))"
Write-Host "INFORMES_MEDICOS in rel: $($content.Contains('INFORMES_MEDICOS'))"
Write-Host "informes|gastos|examenes: $($content.Contains('informes|gastos|examenes'))"
