# Laedt Google Fonts und Font Awesome einmalig herunter und legt sie lokal ab.
# Danach werden keine fremden Server mehr kontaktiert -> keine IP-Uebertragung in die USA.
$base = "$HOME\Desktop\vibelink-website-live"
New-Item -ItemType Directory -Force -Path "$base\fonts"    | Out-Null
New-Item -ItemType Directory -Force -Path "$base\webfonts" | Out-Null
$UA = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"

Write-Output "=== 1. Google Fonts ==="
$gUrl = "https://fonts.googleapis.com/css2?family=Silkscreen:wght@400;700&family=Space+Mono:ital,wght@0,400;0,700;1,400&family=Pixelify+Sans:wght@400;700&display=swap"
$css = (Invoke-WebRequest -Uri $gUrl -UserAgent $UA -UseBasicParsing).Content
$urls = [regex]::Matches($css, 'https://fonts\.gstatic\.com/[^\)]+?\.woff2') | ForEach-Object { $_.Value } | Select-Object -Unique
foreach ($u in $urls) {
  $name = ($u -split '/')[-1]
  try {
    Invoke-WebRequest -Uri $u -OutFile "$base\fonts\$name" -UseBasicParsing -UserAgent $UA
    $css = $css.Replace($u, "fonts/$name")
  } catch { Write-Output "  FEHLER bei $name" }
}
Set-Content -Path "$base\fonts.css" -Value $css -Encoding UTF8
Write-Output ("  " + $urls.Count + " Schriftdateien geladen")

Write-Output "=== 2. Font Awesome ==="
$faBase = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1"
$faCss = (Invoke-WebRequest -Uri "$faBase/css/all.min.css" -UseBasicParsing -UserAgent $UA).Content
$faFiles = [regex]::Matches($faCss, '\.\./webfonts/([a-zA-Z0-9\-]+\.woff2)') | ForEach-Object { $_.Groups[1].Value } | Select-Object -Unique
foreach ($f in $faFiles) {
  try {
    Invoke-WebRequest -Uri "$faBase/webfonts/$f" -OutFile "$base\webfonts\$f" -UseBasicParsing -UserAgent $UA
  } catch { Write-Output "  FEHLER bei $f" }
}
# Pfade lokal machen und die schweren TTF-Verweise entfernen
$faCss = $faCss -replace '\.\./webfonts/', 'webfonts/'
$faCss = $faCss -replace ',url\(webfonts/[a-zA-Z0-9\-]+\.ttf\)\s*format\("truetype"\)', ''
Set-Content -Path "$base\fontawesome.css" -Value $faCss -Encoding UTF8
Write-Output ("  " + $faFiles.Count + " Symboldateien geladen")

Write-Output "=== Ergebnis ==="
"{0,-22} {1,10}" -f "fonts.css", (Get-Item "$base\fonts.css").Length
"{0,-22} {1,10}" -f "fontawesome.css", (Get-Item "$base\fontawesome.css").Length
Get-ChildItem "$base\fonts","$base\webfonts" -File | ForEach-Object { "{0,-22} {1,10}" -f $_.Name, $_.Length }
