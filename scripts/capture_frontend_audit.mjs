import { chromium } from 'playwright';
import fs from 'node:fs/promises';
import path from 'node:path';

const baseURL = process.env.PLAYWRIGHT_BASE_URL || 'http://svicloud10p.svic.local';
const phase = process.argv[2] || 'before';
const outputDir = path.resolve('docs/frontend-audit', phase);
const routes = [
  ['home', '/'],
  ['shop', '/shop/'],
  ['compare', '/compare/'],
  ['product-15p', '/product/svicloud-15p/'],
  ['product-10p', '/product/svicloud-10p-plus/'],
  ['product-9p', '/product/svicloud-9p/'],
  ['cart', '/cart/'],
  ['checkout', '/checkout/'],
];
const viewports = [
  ['desktop', { width: 1440, height: 900 }],
  ['mobile', { width: 390, height: 844 }],
];

await fs.mkdir(outputDir, { recursive: true });
const browser = await chromium.launch();
const report = { phase, baseURL, capturedAt: new Date().toISOString(), pages: [] };

for (const [viewportName, viewport] of viewports) {
  for (const [routeName, routePath] of routes) {
    const context = await browser.newContext({ viewport, reducedMotion: 'reduce' });
    const page = await context.newPage();
    const consoleErrors = [];
    page.on('console', (message) => {
      if (message.type() === 'error') consoleErrors.push(message.text());
    });
    page.on('pageerror', (error) => consoleErrors.push(error.message));

    if (routeName === 'cart' || routeName === 'checkout') {
      await page.goto(`${baseURL}/?add-to-cart=12`, { waitUntil: 'domcontentloaded' });
    }

    const response = await page.goto(`${baseURL}${routePath}`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(350);
    await page.screenshot({ path: path.join(outputDir, `${routeName}-${viewportName}-viewport.png`) });

    if (routeName === 'home' && viewportName === 'mobile') {
      const menuToggle = page.locator('[data-lumen-toggle]');
      await menuToggle.click();
      await page.screenshot({ path: path.join(outputDir, 'navigation-mobile-open.png') });
      await menuToggle.click();
    }

    await page.evaluate(async () => {
      const step = Math.max(400, window.innerHeight * 0.75);
      for (let y = 0; y < document.documentElement.scrollHeight; y += step) {
        window.scrollTo(0, y);
        await new Promise((resolve) => setTimeout(resolve, 35));
      }
      window.scrollTo(0, 0);
    });
    await page.waitForTimeout(150);

    const audit = await page.evaluate(() => {
      const visible = (element) => {
        const style = getComputedStyle(element);
        const rect = element.getBoundingClientRect();
        return style.display !== 'none' && style.visibility !== 'hidden' && rect.width > 0 && rect.height > 0;
      };
      const accessibleName = (element) =>
        element.getAttribute('aria-label') ||
        element.getAttribute('title') ||
        element.textContent?.trim() ||
        (element instanceof HTMLInputElement ? element.value : '');
      const inputHasLabel = (input) => {
        if (input.getAttribute('aria-label') || input.getAttribute('aria-labelledby')) return true;
        if (input.id && document.querySelector(`label[for="${CSS.escape(input.id)}"]`)) return true;
        return Boolean(input.closest('label'));
      };
      const duplicateIds = [...document.querySelectorAll('[id]')]
        .map((element) => element.id)
        .filter((id, index, ids) => id && ids.indexOf(id) !== index)
        .filter((id, index, ids) => ids.indexOf(id) === index);
      const smallTargets = [...document.querySelectorAll('a, button, input, select, textarea, [role="button"]')]
        .filter(visible)
        .map((element) => {
          const rect = element.getBoundingClientRect();
          return { tag: element.tagName.toLowerCase(), text: accessibleName(element).slice(0, 80), width: Math.round(rect.width), height: Math.round(rect.height) };
        })
        .filter(({ width, height }) => width < 24 || height < 24)
        .slice(0, 25);
      const unlabeledInputs = [...document.querySelectorAll('input:not([type="hidden"]), select, textarea')]
        .filter(visible)
        .filter((element) => !inputHasLabel(element))
        .map((element) => ({ tag: element.tagName.toLowerCase(), id: element.id, name: element.getAttribute('name'), type: element.getAttribute('type') }));
      const unnamedControls = [...document.querySelectorAll('a, button, [role="button"]')]
        .filter(visible)
        .filter((element) => !accessibleName(element))
        .map((element) => ({ tag: element.tagName.toLowerCase(), class: element.className }));
      const imagesWithoutAlt = [...document.images]
        .filter((image) => !image.hasAttribute('alt'))
        .map((image) => image.currentSrc || image.src);
      const stylesheets = [...document.querySelectorAll('link[rel="stylesheet"]')].map((link) => ({
        href: link.href,
        media: link.media || 'all',
        loaded: Boolean(link.sheet),
      }));
      return {
        title: document.title,
        language: document.documentElement.lang,
        viewport: { width: innerWidth, height: innerHeight },
        documentSize: { width: document.documentElement.scrollWidth, height: document.documentElement.scrollHeight },
        horizontalOverflow: document.documentElement.scrollWidth > innerWidth + 1,
        bodyClasses: document.body.className,
        mainCount: document.querySelectorAll('main').length,
        navCount: document.querySelectorAll('nav').length,
        h1: [...document.querySelectorAll('h1')].map((heading) => heading.textContent?.trim()),
        headingOrder: [...document.querySelectorAll('h1,h2,h3,h4,h5,h6')].map((heading) => Number(heading.tagName[1])),
        stylesheets,
        duplicateIds,
        unnamedControls,
        unlabeledInputs,
        imagesWithoutAlt,
        smallTargets,
      };
    });

    await page.screenshot({ path: path.join(outputDir, `${routeName}-${viewportName}.png`), fullPage: true });
    report.pages.push({ routeName, routePath, viewportName, status: response?.status() ?? null, finalURL: page.url(), consoleErrors, ...audit });
    await context.close();
  }
}

await browser.close();
await fs.writeFile(path.join(outputDir, 'audit.json'), `${JSON.stringify(report, null, 2)}\n`);
console.log(`Captured ${report.pages.length} pages in ${outputDir}`);
