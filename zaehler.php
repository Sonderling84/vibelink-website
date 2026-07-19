<?php
/*
  VIBE.LINK — Einwilligungsfreier Seitenzaehler
  ---------------------------------------------
  Speichert AUSSCHLIESSLICH: Datum, Uhrzeit (Stunde), Seitenname, Herkunfts-Domain.

  Bewusst NICHT gespeichert:
    - keine IP-Adresse
    - keine Cookies
    - kein Fingerprinting, keine Nutzerkennung
    - keine vollstaendige Herkunfts-URL (nur die Domain)

  Dadurch entstehen keine personenbezogenen Daten -> kein Einwilligungsbanner noetig.

  Die Daten liegen AUSSERHALB des oeffentlichen Ordners (../vibelink-stats/),
  sind also aus dem Netz nicht abrufbar.
*/

// Ordner ausserhalb des Webroots
$ordner = __DIR__ . '/../vibelink-stats';
if (!is_dir($ordner)) { @mkdir($ordner, 0750, true); }

// --- Bots aussortieren (zaehlen sonst die Statistik kaputt) ---
$ua = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
$istBot = ($ua === '') || preg_match('/bot|crawl|spider|slurp|bing|yandex|baidu|duckduck|facebookexternal|headless|preview|monitor|uptime|curl|wget|python|scan/i', $ua);

if (!$istBot) {
    // Seitenname saeubern (nur Pfad, max 120 Zeichen)
    $seite = isset($_GET['p']) ? $_GET['p'] : '/';
    $seite = preg_replace('/[^a-zA-Z0-9\/\.\-_]/', '', $seite);
    $seite = substr($seite, 0, 120);
    if ($seite === '' ) { $seite = '/'; }

    // Herkunft: nur die Domain, keine vollstaendige URL
    $herkunft = isset($_GET['r']) ? $_GET['r'] : '';
    $herkunft = preg_replace('/[^a-zA-Z0-9\.\-]/', '', $herkunft);
    $herkunft = substr($herkunft, 0, 60);
    if ($herkunft === '') { $herkunft = 'direkt'; }

    $datei = $ordner . '/' . date('Y-m') . '.csv';
    $zeile = date('Y-m-d') . ';' . date('H') . ';' . $seite . ';' . $herkunft . "\n";
    @file_put_contents($datei, $zeile, FILE_APPEND | LOCK_EX);
}

// Unsichtbares 1x1-Pixel zurueckgeben
header('Content-Type: image/gif');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
