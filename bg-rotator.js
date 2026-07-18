/*
  VIBE.LINK Hintergrund-Rotation (fuer alle Seiten wiederverwendbar)
  ------------------------------------------------------------------
  Einbinden:
    <video class="video-bg" data-bereich="ki" data-fallback="blog-bg1.mp4" autoplay muted loop playsinline></video>
    <script src="bg-rotator.js" defer></script>

  Das Skript holt sich per media.php automatisch ALLE Videos aus dem Ordner
  media/<bereich>/ und rotiert durch sie:
    - beim Laden zufaellig eines
    - danach alle 60 Sekunden sanft zum naechsten ueberblenden

  NEUE HINTERGRUENDE: einfach Datei nach media/<bereich>/ legen. Sonst nichts.
*/
(function () {
  var v = document.querySelector('video[data-bereich]');
  if (!v) return;

  var bereich   = v.getAttribute('data-bereich') || 'start';
  var fallback  = (v.getAttribute('data-fallback') || '').split(',').filter(Boolean);
  var WECHSEL_MS = parseInt(v.getAttribute('data-wechsel') || '60000', 10);
  var FADE_MS    = 1200;

  var liste = fallback.slice();
  var i = 0;

  function play() {
    var p = v.play();
    if (p && p.catch) p.catch(function () {});
  }
  function setSrc(idx) {
    v.src = liste[idx];
    v.load();
    play();
  }
  function start() {
    if (!liste.length) return;
    i = Math.floor(Math.random() * liste.length);
    v.style.transition = 'opacity ' + FADE_MS + 'ms ease';
    setSrc(i);
    if (liste.length > 1) {
      setInterval(function () {
        v.style.opacity = '0';
        setTimeout(function () {
          i = (i + 1) % liste.length;
          setSrc(i);
          v.style.opacity = '1';
        }, FADE_MS);
      }, WECHSEL_MS);
    }
  }

  fetch('media.php?bereich=' + encodeURIComponent(bereich), { cache: 'no-store' })
    .then(function (r) { return r.json(); })
    .then(function (d) { if (d && d.videos && d.videos.length) { liste = d.videos; } })
    .catch(function () { /* Fallback-Liste bleibt */ })
    .then(function () { start(); });
})();
