/*
  VIBE.LINK — Seitenzaehler (einwilligungsfrei)
  Sendet nur den Seitennamen und die Herkunfts-Domain an den eigenen Server.
  Keine Cookies, keine IP-Speicherung, kein Fremdserver.
  Einbinden mit:  <script src="zaehler.js" defer></script>
*/
(function () {
  try {
    var seite = location.pathname || '/';
    var herkunft = '';
    if (document.referrer) {
      try {
        var h = new URL(document.referrer).hostname;
        if (h && h !== location.hostname) { herkunft = h; }
      } catch (e) {}
    }
    var url = 'zaehler.php?p=' + encodeURIComponent(seite)
            + '&r=' + encodeURIComponent(herkunft)
            + '&t=' + Date.now();
    new Image().src = url;
  } catch (e) { /* Zaehler darf die Seite niemals stoeren */ }
})();
