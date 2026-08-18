#!/usr/bin/env node
/**
 * Capture before/after evidence for the header + hero layout fix.
 *
 * "after"  = the shipped stylesheet/JS as served.
 * "before" = the pre-fix behaviour re-created in the page (non-wrapping centred
 *            nav, no adaptive tiering) so both states are captured on the same
 *            content and viewport set.
 *
 * Usage: node scripts/capture_header_hero_before_after.mjs
 */
import { mkdirSync } from 'node:fs';
import path from 'node:path';
import process from 'node:process';
import { chromium } from 'playwright';

const baseUrl = (process.env.PLAYWRIGHT_BASE_URL || 'http://svicloud10p.svic.local').replace(/\/$/, '');
const outDir = path.resolve('.playwright/header-audit');
mkdirSync(outDir, { recursive: true });

const preFixCss = `
.lumen-nav__list { flex-wrap: nowrap !important; }
.lumen-nav { max-width: min(100%, calc(100% - 320px)) !important; }
.lumen-header--nav-tiered .lumen-header__inner,
.lumen-header--nav-collapsed .lumen-header__inner {
  grid-template-areas: none !important;
  grid-template-columns: minmax(164px, auto) minmax(0, 1fr) auto !important;
}
.lumen-header--nav-tiered .lumen-nav,
.lumen-header--nav-collapsed .lumen-nav {
  grid-area: auto !important;
  display: flex !important;
  width: fit-content !important;
  border-top: 0 !important;
}
.lumen-header--nav-tiered .lumen-header__toggle,
.lumen-header--nav-collapsed .lumen-header__toggle { display: none !important; }
`;

const viewports = [[1920, 1080], [1512, 950], [1280, 900], [1024, 900], [390, 844]];
const browser = await chromium.launch();
for (const [label, css] of [['before', preFixCss], ['after', null]]) {
  for (const [width, height] of viewports) {
    const page = await browser.newPage({ viewport: { width, height }, reducedMotion: 'reduce' });
    await page.goto(`${baseUrl}/`, { waitUntil: 'networkidle' });
    if (css) await page.addStyleTag({ content: css });
    await page.waitForTimeout(500);
    await page.screenshot({ path: path.join(outDir, `${label}-viewport-${width}.png`) });
    await page.locator('.lumen-header').screenshot({ path: path.join(outDir, `${label}-header-${width}.png`) });
    if (label === 'after') {
      await page.evaluate(() => window.scrollTo(0, 400));
      await page.waitForTimeout(300);
      await page.locator('.lumen-header').screenshot({ path: path.join(outDir, `after-header-scrolled-${width}.png`) });
    }
    await page.close();
  }
}
await browser.close();
console.log(`before/after screenshots: ${outDir}`);
