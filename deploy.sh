#!/bin/sh
# Deploy der VIBE.LINK Website auf Hostinger (tobiasganster.de)
# Nutzung: sh deploy.sh              -> alle Standard-Dateien
#          sh deploy.sh datei.html   -> nur diese Datei(en)
#          sh deploy.sh --assets     -> zusaetzlich Schriften/Symbole
set -e
K="$HOME/.ssh/hostinger_deploy"
H="u554408335@82.25.96.191"
P=65002
D="domains/tobiasganster.de/public_html"
SRC="/c/Users/Home/Desktop/vibelink-website-live"

if [ "$1" = "--assets" ]; then
  echo "== Ordner anlegen =="
  ssh -i "$K" -p $P -o BatchMode=yes "$H" "mkdir -p $D/fonts $D/webfonts"
  echo "== Schriften hochladen =="
  scp -i "$K" -P $P -o BatchMode=yes "$SRC"/fonts/* "$H:$D/fonts/"
  scp -i "$K" -P $P -o BatchMode=yes "$SRC"/webfonts/* "$H:$D/webfonts/"
  echo "  OK Schriften + Symbole"
  FILES="fonts.css fontawesome.css index.html blog.html ki.html impressum.html datenschutz.html recht.css"
elif [ -n "$1" ]; then
  FILES="$1"
else
  FILES="index.html blog.html ki.html impressum.html datenschutz.html recht.css fonts.css fontawesome.css quotes.json startseite.mp4 startseite2.mp4 blog-bg1.mp4 media.php bg-rotator.js"
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
ssh -i "$K" -p $P -o BatchMode=yes "$H" "ls -la $D/ | head -20"
