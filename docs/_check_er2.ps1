$path = "c:\laragon\www\SISGECLIN\docs\modelo_er.html"
$content = [System.IO.File]::ReadAllText($path)

# Find the completo tab button
$idx = $content.IndexOf('er-completo')
Write-Host "er-completo found at: $idx"
if ($idx -ge 0) {
    Write-Host $content.Substring([Math]::Max(0,$idx-100), 300)
}

Write-Host "---"
Write-Host "er-ocupacional found: $($content.Contains('er-ocupacional'))"
Write-Host "mermaid-ocupacional: $($content.Contains('mermaid-ocupacional'))"
