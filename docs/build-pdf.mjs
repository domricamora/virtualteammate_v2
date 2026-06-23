#!/usr/bin/env node
// Builds PDF versions of the documentation Markdown files.
//
// Dependency-free: converts the Markdown subset used in these docs to a styled,
// self-contained HTML file, then prints it to PDF with headless Chrome.
//
// Usage:  node docs/build-pdf.mjs

import { readFileSync, writeFileSync, existsSync, mkdtempSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join, dirname, basename } from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';

const DOCS_DIR = dirname(fileURLToPath(import.meta.url));

const FILES = [
  'marketing-site-guide.md',
  'marketing-site-dev.md',
  'portal-user-guide.md',
  'portal-dev.md',
  'portal-guide-clients.md',
  'portal-guide-csm.md',
  'portal-guide-vt.md',
];

const CHROME_CANDIDATES = [
  'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
  'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
  'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
  'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
];

// ---------- tiny Markdown -> HTML (subset we author) ----------

const esc = (s) =>
  s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

// inline: `code`, **bold**, [text](url) — applied to already-escaped text
function inline(text) {
  let out = '';
  let i = 0;
  while (i < text.length) {
    // inline code
    if (text[i] === '`') {
      const end = text.indexOf('`', i + 1);
      if (end !== -1) {
        out += '<code>' + esc(text.slice(i + 1, end)) + '</code>';
        i = end + 1;
        continue;
      }
    }
    // link [text](url)
    if (text[i] === '[') {
      const close = text.indexOf(']', i);
      if (close !== -1 && text[close + 1] === '(') {
        const paren = text.indexOf(')', close + 2);
        if (paren !== -1) {
          const label = text.slice(i + 1, close);
          const url = text.slice(close + 2, paren);
          out += '<a href="' + esc(url) + '">' + inline(esc(label)) + '</a>';
          i = paren + 1;
          continue;
        }
      }
    }
    out += text[i];
    i++;
  }
  // bold (after escaping, so ** is intact)
  out = out.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
  // emphasis *italic* (avoid matching ** already consumed)
  out = out.replace(/(^|[^*])\*([^*\n]+)\*/g, '$1<em>$2</em>');
  return out;
}

function tableRow(line) {
  // split on | but ignore leading/trailing empties
  const cells = line.split('|').slice(1, -1).map((c) => c.trim());
  return cells;
}

function mdToHtml(md) {
  const lines = md.replace(/\r\n/g, '\n').split('\n');
  let html = '';
  let i = 0;

  const flushParagraph = (buf) => {
    if (buf.length) html += '<p>' + inline(esc(buf.join(' '))) + '</p>\n';
  };

  let para = [];

  while (i < lines.length) {
    let line = lines[i];

    // fenced code block
    if (/^```/.test(line)) {
      flushParagraph(para);
      para = [];
      const code = [];
      i++;
      while (i < lines.length && !/^```/.test(lines[i])) {
        code.push(lines[i]);
        i++;
      }
      i++; // skip closing fence
      html += '<pre><code>' + esc(code.join('\n')) + '</code></pre>\n';
      continue;
    }

    // horizontal rule
    if (/^---\s*$/.test(line)) {
      flushParagraph(para);
      para = [];
      html += '<hr>\n';
      i++;
      continue;
    }

    // heading
    const h = line.match(/^(#{1,6})\s+(.*)$/);
    if (h) {
      flushParagraph(para);
      para = [];
      const level = h[1].length;
      html += `<h${level}>` + inline(esc(h[2].trim())) + `</h${level}>\n`;
      i++;
      continue;
    }

    // blockquote (collapse consecutive > lines)
    if (/^>\s?/.test(line)) {
      flushParagraph(para);
      para = [];
      const buf = [];
      while (i < lines.length && /^>\s?/.test(lines[i])) {
        buf.push(lines[i].replace(/^>\s?/, ''));
        i++;
      }
      html += '<blockquote>' + inline(esc(buf.join(' '))) + '</blockquote>\n';
      continue;
    }

    // table (header row followed by a |---| separator)
    if (/^\|.*\|\s*$/.test(line) && i + 1 < lines.length && /^\|[\s:|-]+\|\s*$/.test(lines[i + 1])) {
      flushParagraph(para);
      para = [];
      const header = tableRow(line);
      i += 2; // skip header + separator
      const rows = [];
      while (i < lines.length && /^\|.*\|\s*$/.test(lines[i])) {
        rows.push(tableRow(lines[i]));
        i++;
      }
      html += '<table><thead><tr>';
      header.forEach((c) => (html += '<th>' + inline(esc(c)) + '</th>'));
      html += '</tr></thead><tbody>';
      rows.forEach((r) => {
        html += '<tr>';
        r.forEach((c) => (html += '<td>' + inline(esc(c)) + '</td>'));
        html += '</tr>';
      });
      html += '</tbody></table>\n';
      continue;
    }

    // unordered list
    if (/^\s*[-*]\s+/.test(line)) {
      flushParagraph(para);
      para = [];
      html += '<ul>\n';
      while (i < lines.length && /^\s*[-*]\s+/.test(lines[i])) {
        const item = lines[i].replace(/^\s*[-*]\s+/, '');
        html += '<li>' + inline(esc(item)) + '</li>\n';
        i++;
      }
      html += '</ul>\n';
      continue;
    }

    // ordered list
    if (/^\s*\d+\.\s+/.test(line)) {
      flushParagraph(para);
      para = [];
      html += '<ol>\n';
      while (i < lines.length && /^\s*\d+\.\s+/.test(lines[i])) {
        const item = lines[i].replace(/^\s*\d+\.\s+/, '');
        html += '<li>' + inline(esc(item)) + '</li>\n';
        i++;
      }
      html += '</ol>\n';
      continue;
    }

    // blank line ends a paragraph
    if (/^\s*$/.test(line)) {
      flushParagraph(para);
      para = [];
      i++;
      continue;
    }

    // accumulate paragraph text
    para.push(line.trim());
    i++;
  }
  flushParagraph(para);
  return html;
}

const CSS = `
  @page { size: Letter; margin: 18mm 16mm; }
  * { box-sizing: border-box; }
  body { font: 11pt/1.55 -apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
         color: #1f2430; margin: 0; }
  h1 { font-size: 23pt; color: #2a1290; margin: 0 0 4pt; line-height: 1.2; }
  h2 { font-size: 15pt; color: #3919ba; margin: 20pt 0 6pt; padding-bottom: 3pt;
       border-bottom: 2px solid #e7e2f6; }
  h3 { font-size: 12.5pt; color: #1a1535; margin: 14pt 0 4pt; }
  h4 { font-size: 11pt; color: #3d3860; margin: 12pt 0 3pt; }
  p { margin: 0 0 7pt; }
  em { color: #5b5577; }
  a { color: #6b46c1; text-decoration: none; }
  ul, ol { margin: 0 0 8pt; padding-left: 20pt; }
  li { margin: 2pt 0; }
  code { font-family: Consolas,"SF Mono",Menlo,monospace; font-size: 9.5pt;
         background: #f3f1fa; color: #4327a0; padding: 1px 4px; border-radius: 3px; }
  pre { background: #1a1535; color: #ece9f7; padding: 11pt 13pt; border-radius: 6px;
        overflow-x: auto; margin: 0 0 10pt; }
  pre code { background: none; color: inherit; padding: 0; font-size: 9pt; line-height: 1.45; }
  blockquote { margin: 0 0 10pt; padding: 7pt 12pt; background: #faf7ef;
               border-left: 4px solid #dfa949; color: #4a4530; border-radius: 0 4px 4px 0; }
  blockquote p { margin: 0; }
  table { border-collapse: collapse; width: 100%; margin: 0 0 12pt; font-size: 9.5pt; }
  th, td { border: 1px solid #e1ddee; padding: 5pt 8pt; text-align: left; vertical-align: top; }
  th { background: #efeafa; color: #2a1290; font-weight: 600; }
  tr:nth-child(even) td { background: #faf9fd; }
  hr { border: none; border-top: 1px solid #e1ddee; margin: 16pt 0; }
  h1, h2, h3, h4 { page-break-after: avoid; }
  pre, table, blockquote { page-break-inside: avoid; }
`;

function wrapHtml(title, body) {
  return `<!doctype html><html><head><meta charset="utf-8"><title>${esc(title)}</title>
<style>${CSS}</style></head><body>${body}</body></html>`;
}

// ---------- main ----------

const chrome = CHROME_CANDIDATES.find((p) => existsSync(p));
if (!chrome) {
  console.error('Could not find Chrome or Edge. Checked:\n  ' + CHROME_CANDIDATES.join('\n  '));
  process.exit(1);
}
console.log('Using browser: ' + chrome);

const tmp = mkdtempSync(join(tmpdir(), 'vtdocs-'));
let ok = 0;

for (const file of FILES) {
  const mdPath = join(DOCS_DIR, file);
  if (!existsSync(mdPath)) {
    console.warn('SKIP (missing): ' + file);
    continue;
  }
  const md = readFileSync(mdPath, 'utf8');
  const title = (md.match(/^#\s+(.*)$/m)?.[1] || basename(file, '.md')).trim();
  const htmlPath = join(tmp, basename(file, '.md') + '.html');
  const pdfPath = join(DOCS_DIR, basename(file, '.md') + '.pdf');
  writeFileSync(htmlPath, wrapHtml(title, mdToHtml(md)), 'utf8');

  const res = spawnSync(
    chrome,
    [
      '--headless=new',
      '--disable-gpu',
      '--no-sandbox',
      '--no-pdf-header-footer',
      '--print-to-pdf=' + pdfPath,
      'file:///' + htmlPath.replace(/\\/g, '/'),
    ],
    { stdio: 'inherit', timeout: 120000 }
  );

  if (res.status === 0 && existsSync(pdfPath)) {
    console.log('  OK  -> ' + basename(pdfPath));
    ok++;
  } else {
    console.error('  FAIL -> ' + file + ' (chrome exit ' + res.status + ')');
  }
}

rmSync(tmp, { recursive: true, force: true });
console.log(`\nDone. ${ok}/${FILES.length} PDFs generated in ${DOCS_DIR}`);
process.exit(ok === FILES.length ? 0 : 1);
