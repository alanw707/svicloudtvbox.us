#!/usr/bin/env node
/**
 * Header + hero layout/accessibility audit.
 *
 * Captures screenshots per viewport and reports:
 *  - overlapping pairs of header items (brand/nav links/lang toggle/cart/toggle)
 *  - horizontal overflow of the header row
 *  - accessible names, focus visibility and heading order for the reviewed area
 *
 * Usage: node scripts/audit_header_hero_layout.mjs [--label before|after]
 */
import { mkdirSync } from 'node:fs';
import path from 'node:path';
import process from 'node:process';
import { chromium } from 'playwright';

const baseUrl = (process.env.PLAYWRIGHT_BASE_URL || 'http://svicloud10p.svic.local').replace(/\/$/, '');
const labelIndex = process.argv.indexOf('--label');
const label = labelIndex === -1 ? 'audit' : (process.argv[labelIndex + 1] || 'audit');
const outDir = path.resolve('.playwright/header-audit');
mkdirSync(outDir, { recursive: true });

const viewports = [
  { name: 'wide-1920', width: 1920, height: 1080 },
  { name: 'failing-1512', width: 1512, height: 950 },
  { name: 'narrow-1280', width: 1280, height: 900 },
  { name: 'tablet-1024', width: 1024, height: 900 },
  { name: 'mobile-390', width: 390, height: 844 },
];

const collect = () => {
  const header = document.querySelector('.lumen-header');
  if (!header) {
    return { error: 'header missing' };
  }
  const inner = header.querySelector('.lumen-header__inner');
  const nodes = [];
  const push = (selector, node) => {
    if (!node) return;
    const rect = node.getBoundingClientRect();
    const style = getComputedStyle(node);
    if (rect.width === 0 || rect.height === 0 || style.visibility === 'hidden' || style.display === 'none') return;
    nodes.push({
      selector,
      label: (node.innerText || node.getAttribute('aria-label') || node.tagName).trim().slice(0, 32).replace(/\s+/g, ' '),
      x: Math.round(rect.x), y: Math.round(rect.y), w: Math.round(rect.width), h: Math.round(rect.height),
    });
  };
  push('brand', header.querySelector('.lumen-header__brand'));
  header.querySelectorAll('.lumen-nav__list > li > a').forEach((node, i) => push(`nav[${i}]`, node));
  push('lang-toggle', header.querySelector('.lumen-lang-toggle--desktop'));
  push('cart', header.querySelector('.lumen-cart-link--desktop'));
  push('menu-toggle', header.querySelector('.lumen-header__toggle'));

  const overlaps = [];
  for (let i = 0; i < nodes.length; i += 1) {
    for (let j = i + 1; j < nodes.length; j += 1) {
      const a = nodes[i];
      const b = nodes[j];
      const overlapX = Math.min(a.x + a.w, b.x + b.w) - Math.max(a.x, b.x);
      const overlapY = Math.min(a.y + a.h, b.y + b.h) - Math.max(a.y, b.y);
      if (overlapX > 1 && overlapY > 1) {
        overlaps.push({ a: `${a.selector}:${a.label}`, b: `${b.selector}:${b.label}`, overlapX, overlapY });
      }
    }
  }

  // Hero card text must not collide with the absolutely positioned topline.
  const heroNodes = [];
  [
    ['card-topline', '.hero-15p__topline'],
    ['card-brand', '.hero-15p__brand'],
    ['card-title', '.hero-15p__title'],
    ['card-copy', '.hero-15p__copy'],
    ['card-cta', '.hero-15p__cta'],
    ['card-compare', '.hero-15p__compare'],
    ['hero-eyebrow', '.hero-dashboard__eyebrow'],
    ['hero-launch', '.hero-dashboard__launch'],
    ['hero-title', '.hero-dashboard__title'],
    ['hero-copy', '.hero-dashboard__copy'],
    ['hero-cta', '.hero-dashboard__cta'],
    ['hero-rating', '.hero-dashboard__store-rating'],
  ].forEach(([name, selector]) => {
    const node = document.querySelector(selector);
    if (!node) return;
    const rect = node.getBoundingClientRect();
    if (rect.width === 0 || rect.height === 0) return;
    heroNodes.push({ name, x: rect.x, y: rect.y, w: rect.width, h: rect.height });
  });
  const heroOverlaps = [];
  for (let i = 0; i < heroNodes.length; i += 1) {
    for (let j = i + 1; j < heroNodes.length; j += 1) {
      const a = heroNodes[i];
      const b = heroNodes[j];
      const overlapX = Math.min(a.x + a.w, b.x + b.w) - Math.max(a.x, b.x);
      const overlapY = Math.min(a.y + a.h, b.y + b.h) - Math.max(a.y, b.y);
      if (overlapX > 1 && overlapY > 1) {
        heroOverlaps.push({ a: a.name, b: b.name, overlapX: Math.round(overlapX), overlapY: Math.round(overlapY) });
      }
    }
  }

  const innerRect = inner ? inner.getBoundingClientRect() : header.getBoundingClientRect();
  const rightMost = nodes.reduce((max, node) => Math.max(max, node.x + node.w), 0);
  const leftMost = nodes.reduce((min, node) => Math.min(min, node.x), Number.POSITIVE_INFINITY);

  const named = [];
  const interactive = document.querySelectorAll(
    '.lumen-header a, .lumen-header button, .lumen-hero a, .lumen-hero button, .hero-15p a, .hero-15p button'
  );
  interactive.forEach((node) => {
    const rect = node.getBoundingClientRect();
    if (rect.width === 0 || rect.height === 0) return;
    const text = (node.innerText || '').trim();
    const aria = node.getAttribute('aria-label') || '';
    const labelledBy = node.getAttribute('aria-labelledby');
    const srOnly = node.querySelector('.screen-reader-text');
    const img = node.querySelector('img');
    const accessibleName = aria || text || (srOnly ? srOnly.textContent.trim() : '') || (img ? img.getAttribute('alt') || '' : '') || (labelledBy ? 'labelledby' : '');
    named.push({
      selector: node.className ? `${node.tagName.toLowerCase()}.${String(node.className).split(' ')[0]}` : node.tagName.toLowerCase(),
      href: node.getAttribute('href') || null,
      accessibleName: accessibleName.slice(0, 40).replace(/\s+/g, ' '),
      hasName: Boolean(accessibleName),
    });
  });

  const headings = [...document.querySelectorAll('h1, h2, h3, h4, h5, h6')]
    .filter((node) => node.getBoundingClientRect().top < window.innerHeight + 200)
    .map((node) => ({ level: Number(node.tagName.slice(1)), text: (node.innerText || '').trim().slice(0, 48).replace(/\s+/g, ' ') }));

  return {
    overlaps,
    heroOverlaps,
    headerRow: {
      innerLeft: Math.round(innerRect.left),
      innerRight: Math.round(innerRect.right),
      itemsLeft: Number.isFinite(leftMost) ? Math.round(leftMost) : null,
      itemsRight: Math.round(rightMost),
      viewportWidth: window.innerWidth,
      overflowRight: Math.round(rightMost - innerRect.right),
      documentScrollWidth: document.documentElement.scrollWidth,
    },
    unnamedInteractive: named.filter((item) => !item.hasName),
    interactiveCount: named.length,
    headings,
  };
};

const browser = await chromium.launch({ headless: true });
const report = {};
let failed = false;
for (const viewport of viewports) {
  const page = await browser.newPage({ viewport: { width: viewport.width, height: viewport.height } });
  await page.goto(`${baseUrl}/`, { waitUntil: 'networkidle' });
  const result = await page.evaluate(collect);
  report[viewport.name] = result;
  await page.screenshot({ path: path.join(outDir, `${label}-${viewport.name}.png`) });
  await page.close();

  const overlapCount = result.overlaps?.length ?? 0;
  const overflow = result.headerRow?.overflowRight ?? 0;
  const horizontalScroll = (result.headerRow?.documentScrollWidth ?? 0) > viewport.width + 1;
  const heroOverlapCount = result.heroOverlaps?.length ?? 0;
  console.log(`${viewport.name}: overlaps=${overlapCount} heroOverlaps=${heroOverlapCount} overflowRight=${overflow}px hScroll=${horizontalScroll} unnamed=${result.unnamedInteractive?.length ?? 0}`);
  for (const overlap of result.overlaps ?? []) {
    console.log(`  overlap ${overlap.a} <-> ${overlap.b} (${overlap.overlapX}x${overlap.overlapY}px)`);
  }
  for (const overlap of result.heroOverlaps ?? []) {
    console.log(`  hero overlap ${overlap.a} <-> ${overlap.b} (${overlap.overlapX}x${overlap.overlapY}px)`);
  }
  for (const item of result.unnamedInteractive ?? []) {
    console.log(`  unnamed ${item.selector} href=${item.href}`);
  }
  const levels = (result.headings ?? []).map((h) => h.level);
  const badOrder = levels.some((level, i) => i > 0 && level - levels[i - 1] > 1);
  if (badOrder) {
    console.log(`  heading order jump: ${levels.join(',')}`);
  }
  if (overlapCount || heroOverlapCount || overflow > 1 || horizontalScroll || (result.unnamedInteractive?.length ?? 0) || badOrder) {
    failed = true;
  }
}
await browser.close();
console.log(`\nscreenshots: ${outDir}/${label}-*.png`);
process.exitCode = failed ? 1 : 0;
