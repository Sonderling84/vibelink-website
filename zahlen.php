<?php
/*
  VIBE.LINK — Zahlen-Schnittstelle
  ---------------------------------------------------------------
  Gibt die Besucherzahlen DIESER Seite als JSON aus, damit das
  Dashboard auf ganster.tech sie mit anzeigen kann.

  Aufruf: https://tobiasganster.de/zahlen.php?key=DEIN-SCHLUESSEL

  Es werden nur bereits gezaehlte, anonyme Werte weitergegeben —
  keine IP-Adressen, keine Nutzerkennung. Es entsteht also kein
  neuer Datenschutz-Sachverhalt.
  ---------------------------------------------------------------
*/
$SCHLUESSEL = 'vibelink-2026';   // muss zum Dashboard passen

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['key']) || !hash_equals($SCHLUESSEL, (string)$_GET['key'])) {
    header('HTTP/1.1 403 Forbidden');
    exit(json_encode(array('fehler' => 'Kein Zugriff')));
}

$ordner = __DIR__ . '/../vibelink-stats';
$monat  = isset($_GET['m']) ? preg_replace('/[^0-9\-]/', '', $_GET['m']) : date('Y-m');
$datei  = $ordner . '/' . $monat . '.csv';

$seiten = array(); $tage = array(); $herkunft = array(); $gesamt = 0;

if (is_file($datei)) {
    $fh = fopen($datei, 'r');
    while (($z = fgets($fh)) !== false) {
        $t = explode(';', trim($z));
        if (count($t) < 4) { continue; }
        $gesamt++;
        $tage[$t[0]]     = (isset($tage[$t[0]])     ? $tage[$t[0]]     : 0) + 1;
        $seiten[$t[2]]   = (isset($seiten[$t[2]])   ? $seiten[$t[2]]   : 0) + 1;
        $herkunft[$t[3]] = (isset($herkunft[$t[3]]) ? $herkunft[$t[3]] : 0) + 1;
    }
    fclose($fh);
}
ksort($tage); arsort($seiten); arsort($herkunft);

echo json_encode(array(
    'seite'    => 'tobiasganster.de',
    'monat'    => $monat,
    'gesamt'   => $gesamt,
    'tage'     => $tage,
    'seiten'   => $seiten,
    'herkunft' => $herkunft,
    'stand'    => date('c')
), JSON_UNESCAPED_UNICODE);
