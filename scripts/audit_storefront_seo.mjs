#!/usr/bin/env node
import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname } from 'node:path';
import { chromium } from 'playwright';

const baseUrl = (process.env.PLAYWRIGHT_BASE_URL || 'http://svicloud10p.svic.local').replace(/\/$/, '');
const outputPath = process.env.SEO_AUDIT_OUTPUT || '.playwright/seo-audit/report.json';
const locales = [
  { code: 'en', prefix: '', marker: 'SVICLOUD', title: 'SVICLOUD 15P Backorder | 小雲盒子 U.S. Authorized Dealer' },
  { code: 'zh-TW', prefix: '/zh', marker: '小雲', title: '小雲 15P 缺貨訂購｜小雲盒子美國授權經銷' },
  { code: 'zh-CN', prefix: '/zh-cn', marker: '小云', title: '小云 15P 缺货订购｜小云盒子美国授权经销' },
];
const routes = [
  { key: 'home', path: '/', image: '.hero-15p__image' },
  { key: 'shop', path: '/shop/', image: '.shop-product-card--backorder img' },
  { key: 'compare', path: '/compare/', image: '.compare-product-card:has(a[href*="/product/svicloud-15p/"]) img' },
  { key: 'product', path: '/product/svicloud-15p/', image: '.product-hero-image' },
];
const viewports = [
  { key: 'desktop', width: 1512, height: 950 },
  { key: 'mobile', width: 390, height: 844 },
];
const report = { capturedAt: new Date().toISOString(), baseUrl, pages: [], infrastructure: {}, links: [], issues: [] };
const issue = (scope, message) => report.issues.push({ scope, message });
const localizedPath = (prefix, path) => `${prefix}${path}`.replace(/\/+/g, '/');
const absolute = path => `${baseUrl}${path}`;

function expectedAlternates(path) {
  return {
    'en-US': absolute(localizedPath('', path)),
    'zh-Hant-US': absolute(localizedPath('/zh', path)),
    'zh-Hans-US': absolute(localizedPath('/zh-cn', path)),
    'x-default': absolute(localizedPath('', path)),
  };
}

function normalizeUrl(value) {
  const url = new URL(value, baseUrl);
  url.hash = '';
  return url.href;
}

function flattenTypes(value, found = []) {
  if (Array.isArray(value)) value.forEach(item => flattenTypes(item, found));
  else if (value && typeof value === 'object') {
    const type = value['@type'];
    if (typeof type === 'string') found.push(type);
    else if (Array.isArray(type)) found.push(...type.filter(item => typeof item === 'string'));
    Object.values(value).forEach(child => flattenTypes(child, found));
  }
  return found;
}

const browser = await chromium.launch({ headless: true });
const internalLinks = new Set();
try {
  for (const viewport of viewports) {
    const context = await browser.newContext({ viewport, reducedMotion: 'reduce' });
    for (const locale of locales) {
      for (const route of routes) {
        const scope = `${viewport.key}:${locale.code}:${route.key}`;
        const path = localizedPath(locale.prefix, route.path);
        const requestedUrl = absolute(path);
        const page = await context.newPage();
        const runtimeErrors = [];
        page.on('pageerror', error => runtimeErrors.push(`page: ${error.message}`));
        page.on('console', message => {
          if (message.type() !== 'error' || message.location().url?.startsWith('https://fonts.gstatic.com/')) return;
          runtimeErrors.push(`console: ${message.text()}`);
        });
        page.on('response', response => {
          if (response.status() >= 400 && !response.url().startsWith('https://fonts.gstatic.com/')) runtimeErrors.push(`response: ${response.status()} ${response.url()}`);
        });

        const response = await page.goto(requestedUrl, { waitUntil: 'domcontentloaded', timeout: 60_000 });
        await page.waitForTimeout(750);
        const data = await page.evaluate(() => {
          const content = selector => [...document.querySelectorAll(selector)].map(node => node.getAttribute('content') || '');
          const blocks = [...document.querySelectorAll('script[type="application/ld+json"]')].map(node => node.textContent || '');
          const values = [];
          let invalidJsonLd = 0;
          for (const block of blocks) {
            try { values.push(JSON.parse(block)); } catch { invalidJsonLd += 1; }
          }
          const products = [];
          const visit = value => {
            if (Array.isArray(value)) return value.forEach(visit);
            if (!value || typeof value !== 'object') return;
            const types = Array.isArray(value['@type']) ? value['@type'] : [value['@type']];
            if (types.includes('Product')) products.push(value);
            Object.values(value).forEach(visit);
          };
          values.forEach(visit);
          const headings = [...document.querySelectorAll('h1,h2,h3,h4,h5,h6')].map(node => ({ level: Number(node.tagName.slice(1)), text: (node.textContent || '').trim().replace(/\s+/g, ' ').slice(0, 160) }));
          const images = [...document.images].filter(image => {
            try { return new URL(image.currentSrc || image.src, location.href).origin === location.origin; }
            catch { return false; }
          });
          return {
            title: document.title,
            descriptions: content('meta[name="description"]'),
            robots: content('meta[name="robots"]'),
            canonicals: [...document.querySelectorAll('link[rel="canonical"]')].map(node => node.href),
            alternates: [...document.querySelectorAll('link[rel="alternate"][hreflang]')].map(node => ({ lang: node.getAttribute('hreflang'), href: node.href })),
            social: {
              ogTitle: content('meta[property="og:title"]'), ogDescription: content('meta[property="og:description"]'), ogUrl: content('meta[property="og:url"]'), ogImage: content('meta[property="og:image"]'),
              twitterCard: content('meta[name="twitter:card"]'), twitterTitle: content('meta[name="twitter:title"]'), twitterDescription: content('meta[name="twitter:description"]'), twitterImage: content('meta[name="twitter:image"]'),
            },
            h1: document.querySelectorAll('h1').length,
            main: document.querySelectorAll('main').length,
            headings,
            overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
            text: document.body.innerText || '',
            images: { total: images.length, absentAlt: images.filter(image => !image.hasAttribute('alt')).length, failed: images.filter(image => image.complete && image.naturalWidth === 0).length, missingDimensions: images.filter(image => !image.hasAttribute('width') || !image.hasAttribute('height')).length },
            jsonLd: { blocks: blocks.length, invalid: invalidJsonLd, values, products },
            links: [...document.querySelectorAll('a[href]')].map(node => node.href),
          };
        });

        const status = response?.status() || 0;
        const finalUrl = normalizeUrl(page.url());
        const expectedUrl = normalizeUrl(requestedUrl);
        if (status !== 200) issue(scope, `HTTP ${status}`);
        if (finalUrl !== expectedUrl) issue(scope, `redirected to ${finalUrl}`);
        if (runtimeErrors.length) issue(scope, runtimeErrors.join('; '));
        if (data.descriptions.length !== 1 || !data.descriptions[0]) issue(scope, `expected one description, got ${data.descriptions.length}`);
        if (data.canonicals.length !== 1 || normalizeUrl(data.canonicals[0] || requestedUrl) !== expectedUrl) issue(scope, `self-canonical mismatch: ${data.canonicals.join(', ')}`);
        if (data.robots.some(value => /\bnoindex\b/i.test(value))) issue(scope, 'noindex present');
        if (data.h1 !== 1 || data.main !== 1) issue(scope, `expected one h1/main, got ${data.h1}/${data.main}`);
        for (let index = 1; index < data.headings.length; index += 1) {
          if (data.headings[index].level > data.headings[index - 1].level + 1) issue(scope, `heading level skips ${data.headings[index - 1].level}→${data.headings[index].level}`);
        }
        if (data.overflow > 1) issue(scope, `horizontal overflow ${data.overflow}px`);
        if (data.images.absentAlt || data.images.failed) issue(scope, `image failures: absentAlt=${data.images.absentAlt} failed=${data.images.failed}`);

        const expectedHreflang = expectedAlternates(route.path);
        const actualHreflang = Object.fromEntries(data.alternates.map(item => [item.lang, normalizeUrl(item.href)]));
        for (const [lang, href] of Object.entries(expectedHreflang)) {
          if (actualHreflang[lang] !== normalizeUrl(href)) issue(scope, `hreflang ${lang} mismatch: ${actualHreflang[lang] || 'missing'}`);
        }
        if (Object.keys(actualHreflang).length !== 4) issue(scope, `expected 4 hreflang values, got ${Object.keys(actualHreflang).length}`);

        for (const [field, values] of Object.entries(data.social)) {
          const uniqueValues = [...new Set(values.filter(Boolean))];
          if (uniqueValues.length !== 1) issue(scope, `expected one unique ${field}, got ${values.length}`);
        }
        if (normalizeUrl(data.social.ogUrl[0] || requestedUrl) !== expectedUrl) issue(scope, 'og:url does not match canonical');
        if (!data.title.includes('15P') || !data.title.includes(locale.marker)) issue(scope, `title lacks locale/15P relevance: ${data.title}`);
        if (!data.descriptions[0]?.includes('299') || !data.descriptions[0]?.includes('379')) issue(scope, 'description lacks 299/379 pricing');
        if (route.key === 'home' && data.title !== locale.title) issue(scope, `homepage title differs from approved title: ${data.title}`);
        if (/price (and release date )?(has|have) not been announced/i.test(data.text) || /價格與推出日期尚未公布|价格与推出日期尚未公布/.test(data.text)) issue(scope, 'obsolete price-unannounced copy remains');

        const primary = page.locator(route.image).first();
        if (await primary.count() !== 1) issue(scope, 'primary 15P image missing');
        else {
          await primary.scrollIntoViewIfNeeded();
          const image = await primary.evaluate(node => ({ loaded: node.complete && node.naturalWidth > 0, alt: node.getAttribute('alt'), width: node.getAttribute('width'), height: node.getAttribute('height') }));
          if (!image.loaded || !image.alt?.trim() || !image.width || !image.height) issue(scope, `primary image metadata incomplete: ${JSON.stringify(image)}`);
        }

        const types = [...new Set(flattenTypes(data.jsonLd.values))].sort();
        const productIds = data.jsonLd.products.map(product => product['@id'] || '');
        if (data.jsonLd.invalid) issue(scope, `${data.jsonLd.invalid} invalid JSON-LD blocks`);
        if (new Set(productIds).size !== productIds.length) issue(scope, 'duplicate Product @id values');
        const fifteenProducts = data.jsonLd.products.filter(product => String(product['@id'] || product.url || '').includes('svicloud-15p'));
        if (fifteenProducts.length !== 1) issue(scope, `expected one 15P Product node, got ${fifteenProducts.length}`);
        else {
          const offers = Array.isArray(fifteenProducts[0].offers) ? fifteenProducts[0].offers : [fifteenProducts[0].offers].filter(Boolean);
          if (offers.length !== 1) issue(scope, `expected one 15P Offer, got ${offers.length}`);
          else {
            const offer = offers[0];
            if (Number(offer.price) !== 299 || offer.priceCurrency !== 'USD' || offer.availability !== 'https://schema.org/BackOrder') issue(scope, `15P Offer mismatch: ${JSON.stringify({ price: offer.price, currency: offer.priceCurrency, availability: offer.availability })}`);
            if (JSON.stringify(offer.shippingDetails || '').includes('deliveryTime')) issue(scope, 'BackOrder Offer includes deliveryTime');
          }
        }

        if (viewport.key === 'desktop') {
          for (const href of data.links) {
            try {
              const url = new URL(href, baseUrl);
              if (url.origin !== new URL(baseUrl).origin || !/^https?:$/.test(url.protocol) || url.searchParams.has('add-to-cart') || url.pathname.startsWith('/wp-admin')) continue;
              url.hash = '';
              internalLinks.add(url.href);
            } catch { /* Ignore non-HTTP links. */ }
          }
        }

        report.pages.push({ scope, status, requestedUrl, finalUrl, title: data.title, description: data.descriptions[0] || '', canonical: data.canonicals[0] || '', hreflang: actualHreflang, h1: data.h1, headings: data.headings.map(item => item.level), overflow: data.overflow, images: data.images, jsonLd: { blocks: data.jsonLd.blocks, types, products: data.jsonLd.products.length }, runtimeErrors });
        await page.close();
      }
    }
    await context.close();
  }

  const request = await browser.newContext();
  const linkList = [...internalLinks].sort();
  for (let offset = 0; offset < linkList.length; offset += 8) {
    const batch = linkList.slice(offset, offset + 8);
    const results = await Promise.all(batch.map(async url => {
      try {
        const response = await request.request.get(url, { timeout: 30_000, maxRedirects: 8 });
        return { url, status: response.status(), finalUrl: response.url() };
      } catch (error) { return { url, status: 0, finalUrl: '', error: error.message }; }
    }));
    for (const result of results) {
      report.links.push(result);
      if (result.status === 0 || result.status >= 400) issue('links', `${result.status} ${result.url}`);
      const path = new URL(result.url).pathname;
      const transactional = ['/cart/', '/checkout/', '/my-account/'].some(prefix => path.startsWith(prefix));
      if (!transactional && result.finalUrl && normalizeUrl(result.finalUrl) !== normalizeUrl(result.url)) issue('links', `redirect ${result.url} → ${result.finalUrl}`);
    }
  }

  const robotsResponse = await request.request.get(`${baseUrl}/robots.txt`);
  const robotsText = await robotsResponse.text();
  const sitemapUrls = [...robotsText.matchAll(/^Sitemap:\s*(\S+)/gim)].map(match => match[1]);
  const storefrontSitemap = sitemapUrls.find(url => !url.includes('agent-friendly-sitemap')) || '';
  report.infrastructure.robots = { status: robotsResponse.status(), sitemapUrls };
  if (robotsResponse.status() !== 200 || !storefrontSitemap) issue('infrastructure', 'robots.txt lacks active storefront sitemap');
  const robotsGroups = robotsText.split(/\n(?=User-agent:\s*)/i);
  const wildcardRobotsGroup = robotsGroups.find(group => /^User-agent:\s*\*\s*$/im.test(group)) || '';
  const wildcardAllowsRoot = /^Allow:\s*\/\s*$/im.test(wildcardRobotsGroup);
  if (!wildcardAllowsRoot && /Disallow:\s*\/$/im.test(wildcardRobotsGroup)) {
    issue('infrastructure', 'robots.txt blocks the whole site for the wildcard user-agent');
  }

  const sitemapQueue = storefrontSitemap ? [storefrontSitemap] : [];
  const visited = new Set();
  let fifteenInSitemap = false;
  while (sitemapQueue.length && visited.size < 20) {
    const url = sitemapQueue.shift();
    if (!url || visited.has(url)) continue;
    visited.add(url);
    const response = await request.request.get(url, { timeout: 30_000, maxRedirects: 4 });
    const xml = await response.text();
    if (response.status() !== 200) issue('infrastructure', `sitemap HTTP ${response.status()}: ${url}`);
    if (xml.includes('/product/svicloud-15p/')) fifteenInSitemap = true;
    for (const match of xml.matchAll(/<loc>([^<]+)<\/loc>/g)) {
      if (/sitemap.*\.xml|wp-sitemap.*\.xml/.test(match[1])) sitemapQueue.push(match[1]);
    }
  }
  report.infrastructure.sitemap = { root: storefrontSitemap, checked: [...visited], fifteenInSitemap };
  if (!fifteenInSitemap) issue('infrastructure', 'English 15P product missing from active sitemap');
  await request.close();
} finally {
  await browser.close();
}

mkdirSync(dirname(outputPath), { recursive: true });
writeFileSync(outputPath, JSON.stringify(report, null, 2) + '\n');
console.log(JSON.stringify({ pages: report.pages.length, links: report.links.length, issues: report.issues.length, sitemap: report.infrastructure.sitemap?.root || '', output: outputPath }, null, 2));
if (report.issues.length) {
  for (const entry of report.issues) console.error(`[${entry.scope}] ${entry.message}`);
  process.exitCode = 1;
}
