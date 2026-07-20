<?php
/*
  VIBE.LINK — Statistik-Uebersicht
  Aufruf: https://tobiasganster.de/statistik.php?key=DEIN-SCHLUESSEL
  Den Schluessel unten aendern, wenn du willst.
*/
$SCHLUESSEL = 'vl-neon-racer-4471';

if (!isset($_GET['key']) || $_GET['key'] !== $SCHLUESSEL) {
    header('HTTP/1.1 403 Forbidden');
    exit('Kein Zugriff.');
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
        $tage[$t[0]]       = (isset($tage[$t[0]])       ? $tage[$t[0]]       : 0) + 1;
        $seiten[$t[2]]     = (isset($seiten[$t[2]])     ? $seiten[$t[2]]     : 0) + 1;
        $herkunft[$t[3]]   = (isset($herkunft[$t[3]])   ? $herkunft[$t[3]]   : 0) + 1;
    }
    fclose($fh);
}
arsort($seiten); arsort($herkunft); ksort($tage);

// verfuegbare Monate
$monate = array();
if (is_dir($ordner)) {
    foreach (scandir($ordner) as $f) {
        if (preg_match('/^(\d{4}-\d{2})\.csv$/', $f, $m)) { $monate[] = $m[1]; }
    }
    rsort($monate);
}
function balken($wert, $max) {
    $n = $max > 0 ? (int)round(($wert / $max) * 30) : 0;
    return str_repeat('█', max($n, 1));
}
?><!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Statistik — VIBE.LINK</title>
<style>
  body { background:#050a05; color:#33ff33; font-family:"Courier New",monospace;
         line-height:1.7; padding:26px; }
  h1 { color:#4fd8ff; letter-spacing:4px; font-size:1.5rem; margin-bottom:4px; }
  .sub { color:#1f8f2f; font-size:0.8rem; letter-spacing:2px; margin-bottom:26px; }
  h2 { color:#4fd8ff; font-size:1rem; letter-spacing:2px; margin:30px 0 10px;
       border-bottom:1px solid #1f8f2f; padding-bottom:6px; }
  table { border-collapse:collapse; width:100%; max-width:760px; }
  td { padding:3px 10px 3px 0; vertical-align:top; }
  td.z { color:#ffb000; text-align:right; width:70px; }
  td.b { color:#1f8f2f; }
  .gross { font-size:2.4rem; color:#ffb000; }
  a { color:#4fd8ff; }
  .leer { color:#1f8f2f; }
</style>
</head>
<body>
<h1>STATISTIK</h1>
<div class="sub">VIBE.LINK · MONAT <?php echo htmlspecialchars($monat); ?></div>

<div class="gross"><?php echo number_format($gesamt, 0, ',', '.'); ?></div>
<div class="sub">SEITENAUFRUFE GESAMT</div>

<?php if (count($monate) > 1): ?>
<div>Monat wählen:
<?php foreach ($monate as $m): ?>
  <a href="?key=<?php echo urlencode($SCHLUESSEL); ?>&m=<?php echo $m; ?>"><?php echo $m; ?></a>
<?php endforeach; ?>
</div>
<?php endif; ?>

<h2>▸ SEITEN</h2>
<table>
<?php if (!$seiten): ?><tr><td class="leer">Noch keine Daten.</td></tr><?php endif;
$max = $seiten ? max($seiten) : 0;
foreach ($seiten as $s => $n): ?>
  <tr><td class="z"><?php echo $n; ?></td>
      <td class="b"><?php echo balken($n, $max); ?></td>
      <td><?php echo htmlspecialchars($s); ?></td></tr>
<?php endforeach; ?>
</table>

<h2>▸ VERLAUF (TAGE)</h2>
<table>
<?php $max = $tage ? max($tage) : 0;
foreach ($tage as $t => $n): ?>
  <tr><td class="z"><?php echo $n; ?></td>
      <td class="b"><?php echo balken($n, $max); ?></td>
      <td><?php echo htmlspecialchars($t); ?></td></tr>
<?php endforeach; ?>
</table>

<h2>▸ WOHER KOMMEN DIE BESUCHER</h2>
<table>
<?php $max = $herkunft ? max($herkunft) : 0;
foreach ($herkunft as $h => $n): ?>
  <tr><td class="z"><?php echo $n; ?></td>
      <td class="b"><?php echo balken($n, $max); ?></td>
      <td><?php echo htmlspecialchars($h); ?></td></tr>
<?php endforeach; ?>
</table>

<p style="margin-top:34px;color:#1f8f2f;font-size:0.8rem">
  Gespeichert werden nur Datum, Stunde, Seitenname und Herkunfts-Domain.<br>
  Keine IP-Adressen, keine Cookies, keine Nutzerkennung — daher einwilligungsfrei.
</p>
</body>
</html>
