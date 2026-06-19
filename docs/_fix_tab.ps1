$path = "c:\laragon\www\SISGECLIN\docs\modelo_er.html"
$content = [System.IO.File]::ReadAllText($path)

# Find exact chars of the completo button area
$idx = $content.IndexOf("er-completo")
$snippet = $content.Substring($idx - 80, 200)
Write-Host "Snippet around er-completo button:"
Write-Host "[$snippet]"
Write-Host ""

# Print char codes for newlines to check CRLF vs LF
$crIdx = $snippet.IndexOf([char]13)
$lfIdx = $snippet.IndexOf([char]10)
Write-Host "CR at: $crIdx, LF at: $lfIdx"
