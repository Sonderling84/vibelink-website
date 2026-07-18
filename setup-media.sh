#!/bin/sh
# Legt die Medien-Ordner auf dem Server an und sortiert die vorhandenen Videos ein.
set -e
K="$HOME/.ssh/hostinger_deploy"
H="u554408335@82.25.96.191"
P=65002
D="domains/tobiasganster.de/public_html"

ssh -i "$K" -p $P -o BatchMode=yes "$H" "
  cd $D
  mkdir -p media/start media/blog media/gaming media/ki media/kunst media/about
  # vorhandene Hintergrundvideos einsortieren (kopieren, Originale bleiben liegen)
  [ -f startseite.mp4 ]  && cp -n startseite.mp4  media/start/  || true
  [ -f startseite2.mp4 ] && cp -n startseite2.mp4 media/start/  || true
  [ -f blog-bg1.mp4 ]    && cp -n blog-bg1.mp4    media/blog/   || true
  [ -f startseite.mp4 ]  && cp -n startseite.mp4  media/blog/   || true
  [ -f startseite2.mp4 ] && cp -n startseite2.mp4 media/blog/   || true
  [ -f blog-bg1.mp4 ]    && cp -n blog-bg1.mp4    media/ki/     || true
  echo '--- Ordner ---'
  ls -la media/
  echo '--- start ---'; ls media/start/
  echo '--- blog ---';  ls media/blog/
  echo '--- ki ---';    ls media/ki/
"
