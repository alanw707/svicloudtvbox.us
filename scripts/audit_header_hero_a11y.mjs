#!/usr/bin/env node
/**
 * Accessibility checklist for the homepage header + hero (above-the-fold) area.
 *
 * Checks: accessible names, visible keyboard focus, WCAG AA contrast (worst-case
 * gradient stop), decorative graphics hidden from AT, and heading order.
 *
 * Usage: node scripts/audit_header_hero_a11y.mjs [--scrolled] [--width 1512]
 */
import { mkdirSync, writeFileSync } from 'node:fs';
import path from 'node:path';
import process from 'node:process';
import zlib from 'node:zlib';
import { chromium } from 'playwright';

/** Minimal decoder for the non-interlaced 8-bit PNGs Playwright produces. */
function decodePng(buffer) {
  let offset = 8;
  let width = 0;
  let height = 0;
  let colorType = 6;
  const idat = [];
  while (offset < buffer.length) {
    const length = buffer.readUInt32BE(offset);
    const type = buffer.toString('ascii', offset + 4, offset + 8);
    const data = buffer.subarray(offset + 8, offset + 8 + length);
    if (type === 'IHDR') {
      width = data.readUInt32BE(0);
      height = data.readUInt32BE(4);
      colorType = data[9];
    } else if (type === 'IDAT') {
      idat.push(data);
    } else if (type === 'IEND') {
      break;
    }
    offset += length + 12;
  }
  const channels = colorType === 6 ? 4 : colorType === 2 ? 3 : 1;
  const raw = zlib.inflateSync(Buffer.concat(idat));
  const stride = width * channels;
  const out = Buffer.alloc(height * stride);
  let previous = Buffer.alloc(stride);
  for (let y = 0; y < height; y += 1) {
    const filter = raw[y * (stride + 1)];
    const line = raw.subarray(y * (stride + 1) + 1, y * (stride + 1) + 1 + stride);
    const current = Buffer.alloc(stride);
    for (let x = 0; x < stride; x += 1) {
      const rawByte = line[x];
      const left = x >= channels ? current[x - channels] : 0;
      const up = previous[x];
      const upLeft = x >= channels ? previous[x - channels] : 0;
      let value = rawByte;
      if (filter === 1) value = rawByte + left;
      else if (filter === 2) value = rawByte + up;
      else if (filter === 3) value = rawByte + ((left + up) >> 1);
      else if (filter === 4) {
        const p = left + up - upLeft;
        const pa = Math.abs(p - left);
        const pb = Math.abs(p - up);
        const pc = Math.abs(p - upLeft);
        value = rawByte + (pa <= pb && pa <= pc ? left : pb <= pc ? up : upLeft);
      }
      current[x] = value & 0xff;
    }
    current.copy(out, y * stride);
    previous = current;
  }
  return { width, height, channels, data: out };
}

const relativeLuminance = ([r, g, b]) => {
  const channel = (raw) => {
    const value = raw / 255;
    return value <= 0.03928 ? value / 12.92 : Math.pow((value + 0.055) / 1.055, 2.4);
  };
  return 0.2126 * channel(r) + 0.7152 * channel(g) + 0.0722 * channel(b);
};
const contrastRatio = (a, b) => {
  const la = relativeLuminance(a);
  const lb = relativeLuminance(b);
  return (Math.max(la, lb) + 0.05) / (Math.min(la, lb) + 0.05);
};

const baseUrl = (process.env.PLAYWRIGHT_BASE_URL || 'http://svicloud10p.svic.local').replace(/\/$/, '');
const outDir = path.resolve('.playwright/header-audit');
const widthArgIndex = process.argv.indexOf('--width');
const widthValue = widthArgIndex === -1 ? 1512 : Number(process.argv[widthArgIndex + 1]) || 1512;
const reportName = `a11y-report-${process.argv.includes('--scrolled') ? 'scrolled-' : ''}${widthValue}.json`;
mkdirSync(outDir, { recursive: true });
const scopeSelector = '.lumen-header, .hero-dashboard';

const audit = (scope) => {
  const roots = [...document.querySelectorAll(scope)];
  const inScope = (node) => roots.some((root) => root.contains(node));
  const visible = (node) => {
    const rect = node.getBoundingClientRect();
    const style = getComputedStyle(node);
    return rect.width > 0 && rect.height > 0 && style.visibility !== 'hidden' && style.display !== 'none' && Number(style.opacity) > 0.05;
  };
  const describe = (node) => {
    const cls = String(node.className || '').split(' ').filter(Boolean)[0];
    return `${node.tagName.toLowerCase()}${cls ? '.' + cls : ''}`;
  };

  const parseColor = (value) => {
    const match = String(value).match(/rgba?\(([^)]+)\)/);
    if (!match) return null;
    const parts = match[1].split(/[\s,/]+/).filter(Boolean).map(Number);
    return [parts[0] || 0, parts[1] || 0, parts[2] || 0, Number.isFinite(parts[3]) ? parts[3] : 1];
  };
  const over = (front, back) => {
    const alpha = front[3] + back[3] * (1 - front[3]);
    if (!alpha) return [0, 0, 0, 0];
    return [0, 1, 2].map((i) => (front[i] * front[3] + back[i] * back[3] * (1 - front[3])) / alpha).concat(alpha);
  };
  const luminance = (color) => {
    const channel = (raw) => {
      const value = raw / 255;
      return value <= 0.03928 ? value / 12.92 : Math.pow((value + 0.055) / 1.055, 2.4);
    };
    return 0.2126 * channel(color[0]) + 0.7152 * channel(color[1]) + 0.0722 * channel(color[2]);
  };
  const ratio = (a, b) => {
    const la = luminance(a);
    const lb = luminance(b);
    return (Math.max(la, lb) + 0.05) / (Math.min(la, lb) + 0.05);
  };

  /**
   * Effective background behind a node: composite every paint layer from the
   * outermost opaque ancestor down to the node itself. Gradients contribute each
   * colour stop as a separate scenario; the worst-contrast scenario is kept.
   */
  const effectiveBackground = (node, textColor) => {
    const layers = [];
    let hasImage = false;
    let current = node;
    while (current && current.nodeType === 1) {
      const style = getComputedStyle(current);
      const image = style.backgroundImage;
      const candidates = [];
      let opaqueLayer = false;
      const solid = parseColor(style.backgroundColor);
      if (solid && solid[3] > 0) {
        candidates.push(solid);
        if (solid[3] >= 0.999) opaqueLayer = true;
      }
      if (image && image !== 'none') {
        if (/url\(/.test(image)) hasImage = true;
        const stops = [...image.matchAll(/rgba?\([^)]+\)/g)].map((match) => parseColor(match[0])).filter(Boolean);
        stops.forEach((stop) => candidates.push(stop));
        if (stops.length > 0 && stops.every((stop) => stop[3] >= 0.999) && !/url\(/.test(image)) {
          opaqueLayer = true;
        }
      }
      if (candidates.length) {
        layers.unshift(candidates);
      }
      if (opaqueLayer) break;
      current = current.parentElement;
    }

    let background = [3, 10, 25, 1];
    layers.forEach((candidates) => {
      let worstValue = Infinity;
      let worstColor = background;
      candidates.forEach((candidate) => {
        const composited = over(candidate, background);
        const blendedText = textColor[3] >= 0.999 ? textColor : over(textColor, composited);
        const value = ratio(blendedText, composited);
        if (value < worstValue) {
          worstValue = value;
          worstColor = composited;
        }
      });
      background = worstColor;
    });
    return { background, hasImage };
  };

  const contrast = [];
  const textNodes = [...document.querySelectorAll('a, button, p, li, h1, h2, h3, h4, span, strong, em, div')].filter((node) => {
    if (!inScope(node) || !visible(node)) return false;
    const ownText = [...node.childNodes].some((child) => child.nodeType === 3 && child.textContent.trim().length > 1);
    return ownText;
  });
  textNodes.forEach((node) => {
    const style = getComputedStyle(node);
    const color = parseColor(style.color);
    if (!color) return;
    // Transparent text (background-clip lettering) carries no contrast requirement.
    if (color[3] < 0.05) return;
    if (node.closest('[aria-hidden="true"]')) return;
    const { background: worstBg, hasImage } = effectiveBackground(node, color);
    const blendedText = color[3] >= 0.999 ? color : over(color, worstBg);
    const worst = ratio(blendedText, worstBg);
    const sizePx = parseFloat(style.fontSize);
    const weight = Number(style.fontWeight) || 400;
    const large = sizePx >= 24 || (sizePx >= 18.66 && weight >= 700);
    const required = large ? 3 : 4.5;
    node.setAttribute('data-a11y-probe', String(contrast.length));
    contrast.push({
      probe: contrast.length,
      selector: describe(node),
      text: node.textContent.trim().slice(0, 40).replace(/\s+/g, ' '),
      color: style.color,
      background: worstBg ? `rgb(${worstBg.slice(0, 3).map(Math.round).join(',')})` : null,
      fontPx: Math.round(sizePx * 10) / 10,
      weight,
      required,
      ratio: Math.round(worst * 100) / 100,
      pass: worst >= required,
      backgroundImage: hasImage,
    });
  });

  const interactive = [...document.querySelectorAll('a[href], button, [tabindex]:not([tabindex="-1"]), input, select')]
    .filter((node) => inScope(node) && visible(node));
  const names = interactive.map((node) => {
    const aria = node.getAttribute('aria-label') || '';
    const text = (node.innerText || '').trim();
    const sr = node.querySelector('.screen-reader-text');
    const img = node.querySelector('img');
    const labelledBy = node.getAttribute('aria-labelledby');
    const labelledText = labelledBy ? (document.getElementById(labelledBy)?.textContent || '').trim() : '';
    const name = aria || text || (sr ? sr.textContent.trim() : '') || (img ? img.getAttribute('alt') || '' : '') || labelledText;
    return { selector: describe(node), href: node.getAttribute('href') || null, name: name.slice(0, 48).replace(/\s+/g, ' '), pass: Boolean(name) };
  });

  const headings = [...document.querySelectorAll('h1, h2, h3, h4, h5, h6')]
    .filter((node) => inScope(node) && visible(node))
    .map((node) => ({ level: Number(node.tagName.slice(1)), text: node.textContent.trim().slice(0, 44).replace(/\s+/g, ' ') }));

  const decorative = [...document.querySelectorAll('svg, img, canvas')]
    .filter((node) => inScope(node) && visible(node))
    .map((node) => {
      const alt = node.getAttribute('alt');
      const hidden = node.getAttribute('aria-hidden') === 'true' || node.getAttribute('role') === 'presentation' || alt === '';
      const named = Boolean(node.getAttribute('aria-label') || (alt && alt.length));
      return { selector: describe(node), hidden, named, pass: hidden || named };
    });

  return { contrast, names, headings, decorative, interactiveCount: interactive.length };
};

const browser = await chromium.launch();
const width = widthValue;
const page = await browser.newPage({ viewport: { width, height: width < 700 ? 844 : 950 }, reducedMotion: 'reduce' });
await page.goto(`${baseUrl}/`, { waitUntil: 'networkidle' });
await page.waitForTimeout(1200); // let reveal animations settle on their resting colours
if (process.argv.includes('--scrolled')) {
  await page.evaluate(() => window.scrollTo(0, 400));
  await page.waitForTimeout(300);
}
const report = await page.evaluate(audit, scopeSelector);

/**
 * Pixel-verify every heuristic contrast failure: hide the text, screenshot the
 * element box, and measure the real painted background behind the glyphs.
 */
for (const item of report.contrast.filter((entry) => !entry.pass)) {
  const locator = page.locator(`[data-a11y-probe="${item.probe}"]`);
  if ((await locator.count()) !== 1) continue;
  const probeSelector = `[data-a11y-probe="${item.probe}"]`;
  await page.addStyleTag({
    content: `${probeSelector}, ${probeSelector} * { color: transparent !important; text-shadow: none !important; text-decoration-color: transparent !important; animation: none !important; transition: none !important; }
${probeSelector}::before, ${probeSelector}::after, ${probeSelector} *::before, ${probeSelector} *::after { display: none !important; }`,
  });
  let buffer;
  try {
    buffer = await locator.screenshot();
  } catch {
    continue;
  }
  const png = decodePng(buffer);
  const parts = item.color.match(/rgba?\(([^)]+)\)/)[1].split(/[\s,/]+/).filter(Boolean).map(Number);
  const textRgb = [parts[0], parts[1], parts[2]];
  const textAlpha = Number.isFinite(parts[3]) ? parts[3] : 1;
  const pixels = [];
  for (let y = 0; y < png.height; y += 1) {
    for (let x = 0; x < png.width; x += 1) {
      const index = y * png.width * png.channels + x * png.channels;
      const rgb = [png.data[index], png.data[index + 1], png.data[index + 2]];
      pixels.push({ rgb, luminance: relativeLuminance(rgb) });
    }
  }
  pixels.sort((a, b) => a.luminance - b.luminance);
  // Worst realistic background: 95th/5th percentile pixel toward the text luminance.
  const towardsLight = relativeLuminance(textRgb) > 0.5;
  const backgroundRgb = pixels[Math.floor((pixels.length - 1) * (towardsLight ? 0.95 : 0.05))].rgb;
  const blended = textAlpha >= 0.999
    ? textRgb
    : textRgb.map((channel, i) => channel * textAlpha + backgroundRgb[i] * (1 - textAlpha));
  const measured = Math.round(contrastRatio(blended, backgroundRgb) * 100) / 100;
  item.pixelBackground = `rgb(${backgroundRgb.join(',')})`;
  item.pixelRatio = measured;
  item.pass = measured >= item.required;
  item.verifiedByPixels = true;
}
await page.evaluate(() => document.querySelectorAll('[data-a11y-probe]').forEach((node) => node.removeAttribute('data-a11y-probe')));

// Keyboard focus visibility: focus each control and diff the focus indicator.
const focusHandles = await page.$$(`${scopeSelector.split(', ').map((s) => `${s} a[href], ${s} button`).join(', ')}`);
report.focus = [];
for (const handle of focusHandles) {
  const result = await handle.evaluate((node) => {
    const snapshot = () => {
      const style = getComputedStyle(node);
      return `${style.outlineStyle}|${style.outlineWidth}|${style.outlineColor}|${style.boxShadow}|${style.backgroundColor}|${style.borderColor}`;
    };
    const rect = node.getBoundingClientRect();
    if (rect.width === 0 || rect.height === 0) return null;
    const before = snapshot();
    node.focus({ preventScroll: true });
    node.classList.add('__focus-probe');
    const after = snapshot();
    node.blur();
    node.classList.remove('__focus-probe');
    const cls = String(node.className || '').split(' ').filter((c) => c && c !== '__focus-probe')[0];
    return { selector: `${node.tagName.toLowerCase()}${cls ? '.' + cls : ''}`, before, after };
  });
  if (!result) continue;
  report.focus.push({ selector: result.selector, changed: result.before !== result.after });
}

// :focus-visible cannot be triggered by .focus() alone in all cases — verify via real Tab.
await page.evaluate(() => window.scrollTo(0, 0));
await page.keyboard.press('Tab');
const tabIndicators = [];
for (let i = 0; i < 12; i += 1) {
  const info = await page.evaluate(() => {
    const node = document.activeElement;
    if (!node || node === document.body) return null;
    const style = getComputedStyle(node);
    const cls = String(node.className || '').split(' ').filter(Boolean)[0];
    return {
      selector: `${node.tagName.toLowerCase()}${cls ? '.' + cls : ''}`,
      outline: `${style.outlineStyle} ${style.outlineWidth} ${style.outlineColor}`,
      boxShadow: style.boxShadow,
      visibleIndicator: (style.outlineStyle !== 'none' && parseFloat(style.outlineWidth) >= 1) || style.boxShadow !== 'none',
    };
  });
  if (info) tabIndicators.push(info);
  await page.keyboard.press('Tab');
}
report.tabOrder = tabIndicators;
await browser.close();

const contrastFailures = report.contrast.filter((item) => !item.pass);
const nameFailures = report.names.filter((item) => !item.pass);
const focusFailures = report.focus.filter((item) => !item.changed);
const decorativeFailures = report.decorative.filter((item) => !item.pass);
const levels = report.headings.map((h) => h.level);
const headingJump = levels.some((level, i) => i > 0 && level - levels[i - 1] > 1);
const h1Count = levels.filter((level) => level === 1).length;
const tabFailures = report.tabOrder.filter((item) => !item.visibleIndicator);

console.log(`ACCESSIBILITY CHECKLIST — header + hero (${width}px${process.argv.includes('--scrolled') ? ', scrolled' : ''})`);
console.log(`- accessible names: ${nameFailures.length === 0 ? 'PASS' : 'FAIL'} (${report.names.length} controls, ${nameFailures.length} unnamed)`);
nameFailures.forEach((item) => console.log(`    FAIL ${item.selector} href=${item.href}`));
console.log(`- focus indicator changes on focus: ${focusFailures.length === 0 ? 'PASS' : 'FAIL'} (${report.focus.length} controls)`);
focusFailures.forEach((item) => console.log(`    FAIL ${item.selector}`));
console.log(`- Tab-visible focus ring (first ${report.tabOrder.length} stops): ${tabFailures.length === 0 ? 'PASS' : 'FAIL'}`);
tabFailures.forEach((item) => console.log(`    FAIL ${item.selector} outline=${item.outline} shadow=${item.boxShadow}`));
console.log(`- contrast WCAG AA: ${contrastFailures.length === 0 ? 'PASS' : 'FAIL'} (${report.contrast.length} text nodes)`);
contrastFailures.forEach((item) => console.log(`    FAIL ${item.pixelRatio ?? item.ratio}:1 (needs ${item.required}) ${item.selector} "${item.text}" ${item.color} on ${item.pixelBackground ?? item.background}${item.verifiedByPixels ? ' [pixel-verified]' : ''}`));
console.log(`- decorative graphics hidden/named: ${decorativeFailures.length === 0 ? 'PASS' : 'FAIL'} (${report.decorative.length} graphics)`);
decorativeFailures.forEach((item) => console.log(`    FAIL ${item.selector}`));
console.log(`- heading order: ${!headingJump && h1Count === 1 ? 'PASS' : 'FAIL'} (levels ${levels.join(',')}, h1 count ${h1Count})`);

const reportPath = path.join(outDir, reportName);
writeFileSync(reportPath, JSON.stringify(report, null, 2));
console.log(`\nreport: ${reportPath}`);
process.exitCode = (nameFailures.length + focusFailures.length + tabFailures.length + contrastFailures.length + decorativeFailures.length) === 0 && !headingJump && h1Count === 1 ? 0 : 1;
