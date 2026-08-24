#!/usr/bin/env node
import { mkdirSync } from 'node:fs';
import { chromium } from 'playwright';

const baseUrl = (process.env.PLAYWRIGHT_BASE_URL || 'http://svicloud10p.svic.local').replace(/\/$/, '');
const outputDir = '.playwright/15p-audit';
mkdirSync(outputDir, { recursive: true });

const locales = [
  {
    code: 'en', prefix: '', title: 'SVICLOUD 15P TV Box', availability: 'In stock now', action: 'Buy 15P', delivery: 'In-stock delivery', metaMarker: 'SVICLOUD',
    forbiddenPolicies: ['48-Hour U.S. Shipping', '1-Year U.S. Warranty', 'Every SVICLOUD device is genuine with U.S. warranty support', 'Shipping speed, warranty, and setup help'],
  },
  {
    code: 'zh-TW', prefix: '/zh', title: '小雲 15P 電視盒', availability: '現貨供應', action: '購買 15P', delivery: '現貨配送', metaMarker: '小雲',
    forbiddenPolicies: ['一年美國保固', '標準配送與追蹤', '提供原廠保固與在地服務', '配送時程、保固退換與中文安裝協助'],
  },
  {
    code: 'zh-CN', prefix: '/zh-cn', title: '小云 15P 电视盒', availability: '现货供应', action: '购买 15P', delivery: '现货配送', metaMarker: '小云',
    forbiddenPolicies: ['1-Year U.S. Warranty', '48-Hour U.S. Shipping', '一年美国保修', '配送时效、保修退换、中文安装协助'],
  },
];
const routes = [
  { key: 'home', path: '/', image: '.hero-15p__image', cta: '.hero-15p__cta' },
  { key: 'shop', path: '/shop/', image: '.shop-product-card--backorder img', cta: '.shop-product-card--backorder .shop-product-card__cta' },
  { key: 'compare', path: '/compare/', image: '.compare-product-card:has(a[href*="/product/svicloud-15p/"]) img', cta: '.compare-product-card a[href*="/product/svicloud-15p/"]' },
  { key: 'product', path: '/product/svicloud-15p/', image: '.product-hero-image', cta: '.single_add_to_cart_button' },
];
const viewports = [
  { key: 'desktop', width: 1512, height: 950 },
  { key: 'tablet', width: 1024, height: 900 },
  { key: 'mobile', width: 390, height: 844 },
];

function fail(message) {
  throw new Error(message);
}

function isExternalFont(url = '') {
  return url.startsWith('https://fonts.gstatic.com/');
}

const browser = await chromium.launch();
const results = [];
try {
  for (const locale of locales) {
    for (const viewport of viewports) {
      for (const route of routes) {
        const page = await browser.newPage({ viewport, reducedMotion: 'reduce' });
        const errors = [];
        page.on('pageerror', error => errors.push(`page: ${error.message}`));
        page.on('console', message => {
          if (message.type() === 'error') {
            const location = message.location();
            if (!isExternalFont(location.url)) errors.push(`console: ${message.text()}${location.url ? ` (${location.url})` : ''}`);
          }
        });
        page.on('response', resource => {
          if (resource.status() >= 400 && !isExternalFont(resource.url())) errors.push(`response: ${resource.status()} ${resource.url()}`);
        });
        const localizedPath = `${locale.prefix}${route.path}`.replace(/\/+/g, '/');
        const response = await page.goto(`${baseUrl}${localizedPath}`, { waitUntil: 'domcontentloaded', timeout: 60_000 });
        if (response?.status() !== 200) fail(`${locale.code} ${viewport.key} ${route.key}: HTTP ${response?.status()}`);

        const target = page.locator(route.image).first();
        await target.waitFor({ state: 'visible' });
        await target.scrollIntoViewIfNeeded();
        await page.waitForFunction(node => node.complete && node.naturalWidth > 0, await target.elementHandle());

        const metrics = await page.evaluate(() => ({
          overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
          h1: document.querySelectorAll('h1').length,
          main: document.querySelectorAll('main').length,
          text: document.body.innerText,
        }));
        if (metrics.overflow > 1) fail(`${locale.code} ${viewport.key} ${route.key}: horizontal overflow ${metrics.overflow}px`);
        if (metrics.h1 !== 1) fail(`${locale.code} ${viewport.key} ${route.key}: expected one h1, got ${metrics.h1}`);
        if (metrics.main !== 1) fail(`${locale.code} ${viewport.key} ${route.key}: expected one main landmark, got ${metrics.main}`);
        if (!(await target.getAttribute('alt'))?.trim()) fail(`${locale.code} ${viewport.key} ${route.key}: 15P image alt text missing`);
        if (/\bTBC\b|待確認|待确认/.test(metrics.text)) fail(`${locale.code} ${viewport.key} ${route.key}: placeholder text remains`);
        for (const phrase of ['current wireless', 'current Android 14 and Wi-Fi 6 platform', 'Amlogic S905Y5 power', 'Android 14 performance', 'current playback formats', 'practical set of TV and audio connections']) {
          if (metrics.text.includes(phrase)) fail(`${locale.code} ${viewport.key} ${route.key}: unsupported qualitative copy remains: ${phrase}`);
        }
        if (!metrics.text.includes('Android 14')) fail(`${locale.code} ${viewport.key} ${route.key}: Android 14 copy missing`);
        const normalizedText = metrics.text.toLocaleLowerCase();
        for (const required of [locale.availability, locale.action, locale.delivery, '288', '379']) {
          if (!normalizedText.includes(required.toLocaleLowerCase())) fail(`${locale.code} ${viewport.key} ${route.key}: required in-stock content missing: ${required}`);
        }
        if (/pre-order|preorder|release window|1 to 2 weeks|接受預購|上市時程|接受预订|上市时间/i.test(metrics.text)) fail(`${locale.code} ${viewport.key} ${route.key}: stale preorder copy remains`);
        if (/price (and release date )?(has|have) not been announced/i.test(metrics.text)) fail(`${locale.code} ${viewport.key} ${route.key}: obsolete price copy remains`);
        if (errors.length) fail(`${locale.code} ${viewport.key} ${route.key}: ${errors.join('; ')}`);

        if (route.key !== 'home') {
          const metadata = {
            title: await page.title(),
            description: await page.locator('meta[name="description"]').first().getAttribute('content') || '',
            ogTitle: await page.locator('meta[property="og:title"]').first().getAttribute('content') || '',
            ogDescription: await page.locator('meta[property="og:description"]').first().getAttribute('content') || '',
          };
          for (const [field, value] of Object.entries(metadata)) {
            if (!value.includes('15P') || !value.includes(locale.metaMarker)) fail(`${locale.code} ${route.key}: ${field} is not localized for 15P`);
          }
          if (route.key === 'compare' && (!metadata.title.includes('10P+') || !metadata.title.includes('10S'))) {
            fail(`${locale.code}: compare metadata omits a displayed model`);
          }
        }

        const cta = page.locator(route.cta).first();
        await cta.focus();
        const ctaState = await cta.evaluate(node => {
          const style = getComputedStyle(node);
          const rect = node.getBoundingClientRect();
          return {
            name: (node.getAttribute('aria-label') || node.textContent || '').trim(),
            focusVisible: style.outlineStyle !== 'none' || style.boxShadow !== 'none',
            width: rect.width,
            height: rect.height,
          };
        });
        if (!ctaState.name) fail(`${locale.code} ${viewport.key} ${route.key}: primary CTA has no accessible name`);
        if (!ctaState.focusVisible) fail(`${locale.code} ${viewport.key} ${route.key}: primary CTA has no visible focus treatment`);
        if (viewport.key === 'mobile' && (ctaState.width < 24 || ctaState.height < 24)) fail(`${locale.code} ${route.key}: primary CTA is below WCAG target size`);

        if (route.key === 'shop') {
          if (await page.locator('.shop-product-card').count() !== 4) fail(`${locale.code} ${viewport.key}: shop does not have four cards`);
          if ((await page.locator('.shop-product-card--backorder h2').innerText()).trim() !== locale.title) fail(`${locale.code} ${viewport.key}: localized shop title mismatch`);
        }
        if (route.key === 'compare' && await page.locator('.compare-product-card').count() !== 3) {
          fail(`${locale.code} ${viewport.key}: compare does not have three product cards`);
        }
        if (route.key === 'product') {
          if ((await page.locator('h1').innerText()).trim() !== locale.title) fail(`${locale.code} ${viewport.key}: localized product title mismatch`);
          if (await page.locator('form.cart').count() !== 1 || await page.locator('.single_add_to_cart_button').count() !== 1) fail(`${locale.code} ${viewport.key}: purchasable controls missing`);
          const productNodes = await page.locator('script[type="application/ld+json"]').evaluateAll(nodes => {
            const products = [];
            const visit = value => {
              if (Array.isArray(value)) return value.forEach(visit);
              if (!value || typeof value !== 'object') return;
              const types = Array.isArray(value['@type']) ? value['@type'] : [value['@type']];
              if (types.includes('Product')) products.push(value);
              Object.values(value).forEach(visit);
            };
            for (const node of nodes) visit(JSON.parse(node.textContent || '{}'));
            return products;
          });
          if (productNodes.length !== 1) fail(`${locale.code} ${viewport.key}: expected one Product node, got ${productNodes.length}`);
          const offer = productNodes[0]?.offers;
          if (!offer || offer.price !== '288.00' || offer.availability !== 'https://schema.org/InStock' || offer.availabilityStarts) fail(`${locale.code} ${viewport.key}: InStock Offer mismatch`);
          for (const policy of locale.forbiddenPolicies) {
            if (metrics.text.includes(policy)) fail(`${locale.code} ${viewport.key}: unverified 15P policy rendered: ${policy}`);
          }
          const galleryButtons = page.locator('.product-hero-thumbs button');
          for (let index = 0; index < await galleryButtons.count(); index += 1) {
            const button = galleryButtons.nth(index);
            const name = await button.locator('img').getAttribute('alt');
            if (!name?.trim() || !(await button.getAttribute('aria-pressed'))) fail(`${locale.code} ${viewport.key}: gallery button ${index + 1} is not named/stateful`);
          }
        }

        await page.evaluate(() => {
          if (document.activeElement instanceof HTMLElement) document.activeElement.blur();
          window.scrollTo(0, 0);
        });
        await page.waitForTimeout(100);
        await page.screenshot({ path: `${outputDir}/${locale.code}-${route.key}-${viewport.key}.png`, fullPage: true });
        results.push({ locale: locale.code, viewport: viewport.key, route: route.key, status: 200, overflow: metrics.overflow, errors: 0 });
        await page.close();
      }
    }
  }
} finally {
  await browser.close();
}
console.log(JSON.stringify({ checks: results.length, passed: results.length, results }, null, 2));
