# PowerShell wrapper to generate PDF with pandoc
if (-not (Get-Command pandoc -ErrorAction SilentlyContinue)) {
  Write-Error "pandoc not found. Install pandoc for Windows (https://pandoc.org/installing.html)"
  exit 1
}
$input = "docs\Informe_Final.md"
$out = "docs\Informe_Final.pdf"
pandoc $input -o $out --pdf-engine=xelatex --variable mainfont="DejaVu Sans"
Write-Host "Generated $out"
