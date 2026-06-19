$path = "c:\laragon\www\SISGECLIN\docs\modelo_er.html"
$content = [System.IO.File]::ReadAllText($path)

# Add Ocupacional tab button before Completo tab button using string search
$searchStr = "          <button class=""diagram-tab"" onclick=""mostrarDiagrama('er-completo')"">"
$insertBefore = "          <button class=""diagram-tab"" onclick=""mostrarDiagrama('er-ocupacional')"">`r`n            <i class='bx bx-id-card'></i> Ocupacional`r`n          </button>`r`n          <button class=""diagram-tab"" onclick=""mostrarDiagrama('er-completo')"">"
$content = $content.Replace($searchStr, $insertBefore)
Write-Host "Tab button added: $($content.Contains('er-ocupacional'))"

[System.IO.File]::WriteAllText($path, $content)
Write-Host "Done"
