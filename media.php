<?php
/*
  VIBE.LINK Medien-Verzeichnis
  ----------------------------
  Liefert alle Videos und Bilder eines Bereichs als JSON.
  Aufruf: media.php?bereich=start   (oder blog, gaming, ki, kunst, about)

  NEUE MEDIEN HINZUFUEGEN:
  Einfach eine Datei in den passenden Ordner legen, z.B.
      media/start/mein-neues-video.mp4
      media/kunst/bild01.jpg
  Die Seite nimmt sie beim naechsten Aufruf automatisch in die Rotation.
  Kein Code aendern, kein Deploy noetig.
*/

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

$bereich = isset($_GET['bereich']) ? $_GET['bereich'] : 'start';
$bereich = preg_replace('/[^a-z0-9\-]/i', '', $bereich);   // Sicherheit: keine Pfad-Tricks
if ($bereich === '') { $bereich = 'start'; }

$ordner  = __DIR__ . '/media/' . $bereich;
$erlaubt = array('mp4', 'webm', 'jpg', 'jpeg', 'png', 'webp', 'gif');

$videos = array();
$bilder = array();

if (is_dir($ordner)) {
    $eintraege = scandir($ordner);
    if ($eintraege !== false) {
        foreach ($eintraege as $datei) {
            if ($datei === '.' || $datei === '..') { continue; }
            if (substr($datei, 0, 1) === '.') { continue; }
            $ext = strtolower(pathinfo($datei, PATHINFO_EXTENSION));
            if (!in_array($ext, $erlaubt, true)) { continue; }

            $url = 'media/' . $bereich . '/' . rawurlencode($datei);
            if ($ext === 'mp4' || $ext === 'webm') {
                $videos[] = $url;
            } else {
                $bilder[] = $url;
            }
        }
    }
}

sort($videos);
sort($bilder);

echo json_encode(array(
    'bereich' => $bereich,
    'videos'  => $videos,
    'bilder'  => $bilder,
    'anzahl'  => count($videos) + count($bilder)
), JSON_UNESCAPED_SLASHES);
