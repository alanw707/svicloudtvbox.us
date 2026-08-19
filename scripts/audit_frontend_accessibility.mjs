import { chromium } from 'playwright';
import fs from 'node:fs/promises';
import path from 'node:path';

const baseURL = process.env.PLAYWRIGHT_BASE_URL || 'http://svicloud10p.svic.local';
const output = path.resolve('docs/frontend-audit/accessibility-audit.json');
const routes = [
  ['home', '/'], ['shop', '/shop/'], ['compare', '/compare/'],
  ['product-15p', '/product/svicloud-15p/'], ['product-10p', '/product/svicloud-10p-plus/'],
  ['product-9p', '/product/svicloud-9p/'], ['cart', '/cart/'], ['checkout', '/checkout/'],
];
const browser = await chromium.launch();
const report = { baseURL, auditedAt: new Date().toISOString(), pages: [], interactions: {} };

async function prepare(page, name, route) {
  if (name === 'cart' || name === 'checkout') {
    await page.goto(`${baseURL}/?add-to-cart=12`, { waitUntil: 'domcontentloaded' });
  }
  await page.goto(`${baseURL}${route}`, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(300);
}

for (const width of [1440, 390, 320]) {
  for (const [name, route] of routes) {
    const context = await browser.newContext({ viewport: { width, height: width === 1440 ? 900 : 844 }, reducedMotion: 'reduce' });
    const page = await context.newPage();
    await prepare(page, name, route);
    const data = await page.evaluate(() => {
      const visible = (element) => {
        const style = getComputedStyle(element);
        const rect = element.getBoundingClientRect();
        return style.display !== 'none' && style.visibility !== 'hidden' && rect.width > 0 && rect.height > 0;
      };
      const nameOf = (element) => element.getAttribute('aria-label') || element.getAttribute('title') || element.textContent?.trim() || (element instanceof HTMLInputElement ? element.value : '');
      const selectorOf = (element) => {
        if (element.id) return `#${CSS.escape(element.id)}`;
        const classes = typeof element.className === 'string' ? element.className.trim().split(/\s+/).slice(0, 3).map((item) => `.${CSS.escape(item)}`).join('') : '';
        return `${element.tagName.toLowerCase()}${classes}`;
      };
      const focusables = [...document.querySelectorAll('a[href],button:not([disabled]),input:not([type="hidden"]):not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])')].filter(visible);
      const focusIndicators = focusables.slice(0, 60).map((element) => {
        element.blur();
        const before = getComputedStyle(element);
        const normal = [before.outlineStyle, before.outlineWidth, before.outlineColor, before.boxShadow, before.borderColor, before.backgroundColor, before.color];
        element.focus();
        const after = getComputedStyle(element);
        const focused = [after.outlineStyle, after.outlineWidth, after.outlineColor, after.boxShadow, after.borderColor, after.backgroundColor, after.color];
        return { selector: selectorOf(element), name: nameOf(element).slice(0, 80), changed: normal.some((value, index) => value !== focused[index]), focused };
      });
      const duplicateIds = [...document.querySelectorAll('[id]')].map((e) => e.id).filter((id, index, ids) => id && ids.indexOf(id) !== index).filter((id, index, ids) => ids.indexOf(id) === index);
      const inputs = [...document.querySelectorAll('input:not([type="hidden"]),select,textarea')].filter(visible);
      const unlabeled = inputs.filter((input) => {
        if (input.getAttribute('aria-label') || input.getAttribute('aria-labelledby')) return false;
        if (input.id && document.querySelector(`label[for="${CSS.escape(input.id)}"]`)) return false;
        return !input.closest('label');
      }).map((input) => selectorOf(input));
      const unnamed = focusables.filter((element) => !nameOf(element)).map((element) => selectorOf(element));
      const targets = focusables.map((element) => {
        const rect = element.getBoundingClientRect();
        return { selector: selectorOf(element), name: nameOf(element).slice(0, 80), width: Math.round(rect.width), height: Math.round(rect.height) };
      });
      const headings = [...document.querySelectorAll('h1,h2,h3,h4,h5,h6')].map((h) => ({ level: Number(h.tagName[1]), text: h.textContent?.trim() }));
      const skippedHeadings = headings.filter((heading, index) => index > 0 && heading.level > headings[index - 1].level + 1);
      const activeMotion = [...document.querySelectorAll('*')].filter(visible).map((element) => {
        const style = getComputedStyle(element);
        return { selector: selectorOf(element), animation: style.animationDuration, transition: style.transitionDuration };
      }).filter((item) => item.animation.split(',').some((value) => parseFloat(value) > 0) || item.transition.split(',').some((value) => parseFloat(value) > 0)).slice(0, 40);
      return {
        lang: document.documentElement.lang,
        landmarks: { main: document.querySelectorAll('main').length, nav: document.querySelectorAll('nav').length, footer: document.querySelectorAll('footer').length },
        h1Count: headings.filter((h) => h.level === 1).length,
        skippedHeadings,
        duplicateIds,
        unlabeled,
        unnamed,
        targetUnder24: targets.filter((target) => target.width < 24 || target.height < 24),
        targetUnder44: targets.filter((target) => target.width < 44 || target.height < 44),
        focusWithoutVisualChange: focusIndicators.filter((item) => !item.changed),
        activeMotion,
        horizontalOverflow: document.documentElement.scrollWidth > innerWidth + 1,
      };
    });
    report.pages.push({ name, route, width, ...data });
    await context.close();
  }
}

{
  const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  await page.goto(`${baseURL}/`, { waitUntil: 'domcontentloaded' });
  const toggle = page.locator('[data-lumen-toggle]');
  await toggle.focus();
  await page.keyboard.press('Enter');
  const openState = await page.evaluate(() => ({
    expanded: document.querySelector('[data-lumen-toggle]')?.getAttribute('aria-expanded'),
    navHidden: document.querySelector('#lumen-mobile-nav')?.hasAttribute('hidden'),
    bodyOverflow: getComputedStyle(document.body).overflow,
    inertOutside: [...document.body.children].filter((element) => !element.matches('.lumen-header,script')).some((element) => element.hasAttribute('inert')),
  }));
  const tabNames = [];
  for (let index = 0; index < 14; index += 1) {
    await page.keyboard.press('Tab');
    tabNames.push(await page.evaluate(() => document.activeElement?.getAttribute('aria-label') || document.activeElement?.textContent?.trim().replace(/\s+/g, ' ').slice(0, 80) || document.activeElement?.tagName));
  }
  await page.keyboard.press('Escape');
  const closedState = await page.evaluate(() => ({
    expanded: document.querySelector('[data-lumen-toggle]')?.getAttribute('aria-expanded'),
    navHidden: document.querySelector('#lumen-mobile-nav')?.hasAttribute('hidden'),
    focusReturned: document.activeElement?.matches('[data-lumen-toggle]') || false,
  }));
  report.interactions.mobileNavigation = { openState, tabNames, closedState };
  await context.close();
}

{
  const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  await prepare(page, 'checkout', '/checkout/');
  const placeOrder = page.locator('#place_order');
  if (await placeOrder.isEnabled()) {
    await placeOrder.click();
    await page.waitForTimeout(700);
  }
  report.interactions.checkoutValidation = await page.evaluate(() => ({
    focused: document.activeElement?.id || document.activeElement?.getAttribute('name') || document.activeElement?.tagName,
    invalidFields: [...document.querySelectorAll('.woocommerce-invalid-required-field input, input[aria-invalid="true"]')].map((input) => input.id || input.getAttribute('name')),
    errors: [...document.querySelectorAll('.woocommerce-error li, .woocommerce-error')].map((error) => error.textContent?.trim()).filter(Boolean),
    errorRoles: [...document.querySelectorAll('.woocommerce-error')].map((error) => error.getAttribute('role')),
    placeOrderDisabled: document.querySelector('#place_order')?.hasAttribute('disabled') || false,
    placeOrderText: document.querySelector('#place_order')?.textContent?.trim() || '',
  }));
  await context.close();
}

await browser.close();
await fs.mkdir(path.dirname(output), { recursive: true });
await fs.writeFile(output, `${JSON.stringify(report, null, 2)}\n`);
console.log(`Accessibility audit wrote ${output}`);
