#!/bin/sh
# Deploy der VIBE.LINK Website auf Hostinger (tobiasganster.de)
# Nutzung: sh deploy.sh            -> laedt alle Standard-Dateien hoch
#          sh deploy.sh datei.html -> laedt nur diese Datei hoch
set -e
K="$HOME/.ssh/hostinger_deploy"
H="u554408335@82.25.96.191"
P=65002
D="domains/tobiasganster.de/public_html"
SRC="/c/Users/Home/Desktop/vibelink-website-live"

if [ -n "$1" ]; then
  FILES="$1"
else
  FILES="index.html blog.html quotes.json startseite.mp4 startseite2.mp4"
fi

echo "== Upload: $FILES =="
for f in $FILES; do
  if [ -f "$SRC/$f" ]; then
    scp -i "$K" -P $P -o BatchMode=yes "$SRC/$f" "$H:$D/$f"
    echo "  OK $f"
  else
    echo "  FEHLT (uebersprungen): $f"
  fi
done

echo "== Kontrolle =="
ssh -i "$K" -p $P -o BatchMode=yes "$H" "ls -la $D/"
