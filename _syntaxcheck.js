const fs = require('fs');
const html = fs.readFileSync(process.argv[2] || 'blog.html', 'utf8');
const re = /<script(?![^>]*\bsrc=)[^>]*>([\s\S]*?)<\/script>/gi;
let m, n = 0;
while ((m = re.exec(html)) !== null) {
  n++;
  const code = m[1];
  const lineStart = html.slice(0, m.index).split('\n').length;
  try {
    new Function(code);
    console.log(`Script #${n} (ab Zeile ${lineStart}, ${code.length} Zeichen): OK`);
  } catch (e) {
    console.log(`Script #${n} (ab Zeile ${lineStart}): FEHLER -> ${e.message}`);
    const mm = /(\d+)/.exec(e.stack.split('\n')[0] || '');
    const lines = code.split('\n');
    for (let i = 0; i < lines.length; i++) {
      try { new Function(lines.slice(0, i + 1).join('\n') + '\n}'.repeat(0)); } catch (_) {}
    }
    console.log('--- Stack ---');
    console.log(e.stack.split('\n').slice(0, 4).join('\n'));
  }
}
if (n === 0) console.log('keine inline scripts gefunden');
