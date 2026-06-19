# Update documentacion_codigo.html - remaining changes (1, 2, 3)
$path = "c:\laragon\www\SISGECLIN\docs\documentacion_codigo.html"
$content = [System.IO.File]::ReadAllText($path)

# Find what the li items look like (check for em-dash encoding)
$idx = $content.IndexOf('PermisoUsuario.php')
Write-Host "Found PermisoUsuario at index: $idx"
if ($idx -ge 0) {
    $snippet = $content.Substring($idx - 5, 150)
    Write-Host "Snippet: [$snippet]"
    # Check the character codes around the dash
    $dashIdx = $snippet.IndexOf('php') + 3
    Write-Host "Char at dashIdx: $([int][char]$snippet[$dashIdx])"
    Write-Host "Char at dashIdx+1: $([int][char]$snippet[$dashIdx+1])"
    Write-Host "Char at dashIdx+2: $([int][char]$snippet[$dashIdx+2])"
}
