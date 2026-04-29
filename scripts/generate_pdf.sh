#!/usr/bin/env bash
# Generate PDF from docs/Informe_Final.md using pandoc
set -e
if ! command -v pandoc >/dev/null 2>&1; then
  echo "pandoc not found. Install pandoc first (e.g., sudo apt install pandoc)." >&2
  exit 1
fi
INPUT=docs/Informe_Final.md
OUT=docs/Informe_Final.pdf
pandoc "$INPUT" -o "$OUT" --pdf-engine=xelatex --variable mainfont="DejaVu Sans"
echo "Generated $OUT"
