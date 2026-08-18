#!/usr/bin/env node
import { chromium } from 'playwright';

const base = process.env.PLAYWRIGHT_BASE_URL || 'http://svicloud10p.svic.local';
const slug = 'svicloud-bluetooth-remote-10p-plus';
const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1512, height: 950 } });
const errors = [];
page.on('pageerror', error => errors.push(error.message));
page.on('console', message => message.type() === 'error' && errors.push(message.text()));

const apiResponse = await page.request.get(`${base}/wp-json/wc/store/v1/products?slug=${slug}`);
const [product] = await apiResponse.json();
const response = await page.goto(`${base}/shop/`, { waitUntil: 'networkidle' });
const card = page.locator('.shop-product-card--accessory');
const cardLinks = card.locator(`a[href*="/product/${slug}/"]`);
await card.scrollIntoViewIfNeeded();
const image = card.locator('img');
await image.waitFor({ state: 'visible' });
await page.waitForFunction(node => node.complete && node.naturalWidth > 0, await image.elementHandle());

const result = {
  apiProduct: product?.name || null,
  shopStatus: response?.status() || null,
  shopArchive: await page.locator('body.woocommerce-shop').count() === 1,
  remoteCards: await card.count(),
  visibleShopCardLinks: await cardLinks.evaluateAll(links => links.filter(link => link.getClientRects().length).length),
  imageLoaded: await image.evaluate(node => node.complete && node.naturalWidth > 0),
  errors,
};
console.log(JSON.stringify(result));
await browser.close();

if (!product || result.shopStatus !== 200 || !result.shopArchive || result.remoteCards !== 1 || result.visibleShopCardLinks !== 1 || !result.imageLoaded || errors.length) {
  process.exit(1);
}
