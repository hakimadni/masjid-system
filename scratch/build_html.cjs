const fs = require('fs');

const md = fs.readFileSync('masjidos-guides/10_KANBAN_BOARD.md', 'utf8');

// Parse markdown to HTML tables manually
const lines = md.split('\n');
let html = `<!DOCTYPE html><html lang="id"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>MasjidOS Kanban Board</title>
<style>body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;margin:0;background:#0f172a;color:#e2e8f0}.container{max-width:1400px;margin:0 auto;padding:32px}h1{color:#38bdf8;margin-bottom:8px}h2{color:#fbbf24;margin-top:40px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #334155}h3{color:#fbbf24;margin-top:32px}.meta{color:#94a3b8;margin-bottom:24px}table{width:100%;border-collapse:collapse;margin:16px 0 32px;background:#1e293b;border-radius:8px;overflow:hidden}th{background:#1e3a8a;color:#dbeafe;padding:10px 12px;text-align:left;font-size:14px}td{padding:9px 12px;border-top:1px solid #334155;font-size:13px;vertical-align:top}tr:hover{background:#1e3a5f}code{background:#0f172a;padding:2px 4px;border-radius:4px}.done{color:#22c55e;font-weight:700}.review{color:#f59e0b;font-weight:700}.backlog{color:#f87171;font-weight:700}.in-progress,.in_progress{color:#60a5fa;font-weight:700}.blocked{color:#c084fc;font-weight:700}.ready{color:#2dd4bf;font-weight:700}</style></head><body><div class="container">\n`;

let inTable = false;

for (let line of lines) {
  if (line.startsWith('# ')) {
    html += `<h1>${line.slice(2)}</h1>\n`;
  } else if (line.startsWith('## ')) {
    html += `<h2>${line.slice(3)}</h2>\n`;
  } else if (line.match(/^Last updated:/)) {
    const d = new Date();
    html += `<p class="meta">Last updated: ${d.toISOString().replace('T', ' ').slice(0, 19)}</p>\n`;
  } else if (line.startsWith('- ')) {
    html += `<p>${line.slice(2).replace(/`([^`]+)`/g, '<code>$1</code>')}</p>\n`;
  } else if (line.startsWith('|')) {
    if (line.includes('---')) continue; // skip header separator
    
    if (!inTable) {
      html += `<table><thead><tr><th>ID</th><th>Task</th><th>Status</th><th>Owner</th><th>Notes</th></tr></thead><tbody>\n`;
      inTable = true;
      continue; // skip the header row as we hardcoded it
    } else if (line.includes('ID | Task | Status')) {
       // skip the header row text
       continue;
    }
    
    // Parse table row
    const cols = line.split('|').map(c => c.trim()).slice(1, -1);
    if (cols.length === 5) {
      const statusClass = cols[2].toLowerCase().replace(' ', '-');
      const notes = cols[4].replace(/`([^`]+)`/g, '<code>$1</code>');
      html += `<tr><td>${cols[0]}</td><td>${cols[1]}</td><td class="${statusClass}">${cols[2]}</td><td>${cols[3]}</td><td>${notes}</td></tr>\n`;
    }
  } else {
    if (inTable && line.trim() === '') {
      html += `</tbody></table>\n`;
      inTable = false;
    }
  }
}

if (inTable) {
  html += `</tbody></table>\n`;
}

html += `</div></body></html>`;

fs.writeFileSync('masjidos-guides/10_KANBAN_BOARD.html', html);
console.log('Regenerated HTML Kanban Board');
