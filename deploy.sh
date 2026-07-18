#!/bin/sh
# Deploy der VIBE.LINK Website auf Hostinger (tobiasganster.de)
set -e
K="$HOME/.ssh/hostinger_deploy"
H="u554408335@82.25.96.191"
P=65002
D="domains/tobiasganster.de/public_html"
SRC="/c/Users/Home/Desktop/vibelink-website-live"

echo "== Backup auf dem Server =="
ssh -i "$K" -p $P -o BatchMode=yes "$H" "cp $D/blog.html $D/blog_backup_vor_claude_20260718.html; echo BACKUP_OK"

echo "== Upload blog.html =="
scp -i "$K" -P $P -o BatchMode=yes "$SRC/blog.html" "$H:$D/blog.html"
echo UPLOAD_OK

echo "== Kontrolle =="
ssh -i "$K" -p $P -o BatchMode=yes "$H" "ls -la $D/"
