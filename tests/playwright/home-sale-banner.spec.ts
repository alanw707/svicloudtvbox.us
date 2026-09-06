import { test, expect } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';

test.beforeEach(async ({ page }) => {
  await page.route(/google-analytics\.com|googletagmanager\.com|googleadservices\.com|doubleclick\.net/, r => r.abort());
  if (process.env.SVIC_LOCAL_ASSETS === '1') {
    const fixtures = JSON.parse(fs.readFileSync('/tmp/svic-banner-fixtures.json', 'utf8'));
    await page.route('**/*', async route => {
      const url = new URL(route.request().url());
      const locale = url.pathname.replace(/\/$/, '');
      if (route.request().resourceType() === 'document' && Object.hasOwn(fixtures, locale)) {
        const response = await route.fetch();
        const html = (await response.text()).replace(/(<main\b[^>]*>)/, '$1' + fixtures[locale]);
        await route.fulfill({ response, body: html });
      } else { await route.fallback(); }
    });
    await page.route(/\/assets\/css\/style\.css(?:\?|$)/, r => r.fulfill({ path: path.resolve('theme/svicloudtvbox-lumen/assets/css/style.css'), contentType: 'text/css' }));
    await page.route(/\/assets\/js\/theme(?:\.min)?\.js(?:\?|$)/, r => r.fulfill({ path: path.resolve('theme/svicloudtvbox-lumen/assets/js/theme.js'), contentType: 'application/javascript' }));
  }
});

for (const locale of ['', '/zh', '/zh-cn']) {
  test(`sale banner and localized destination ${locale || 'en'}`, async ({ page }, testInfo) => {
    await page.goto(`${locale}/`, { waitUntil: 'domcontentloaded' });
    const banner = page.locator('.svic-promo-bar--home-sale');
    await expect(banner).toBeVisible();
    await expect(banner).toContainText('$234.99');
    await expect(banner).toContainText('$34.01');
    await expect(banner).not.toContainText('GOOGLE5');
    const link = banner.locator('a');
    await expect(link).toHaveAttribute('href', new RegExp(`${locale}/product/svicloud-10p-plus/$`));
    const box = await banner.boundingBox();
    const button = await link.boundingBox();
    expect(box!.x).toBeGreaterThanOrEqual(0);
    expect(box!.x + box!.width).toBeLessThanOrEqual(page.viewportSize()!.width + 1);
    expect(button!.height).toBeGreaterThanOrEqual(44);
    expect(button!.y + button!.height).toBeLessThanOrEqual(box!.y + box!.height + 1);
    await page.screenshot({ path: testInfo.outputPath('homepage-sale.png') });
    await link.click();
    await expect(page).toHaveURL(new RegExp(`${locale}/product/svicloud-10p-plus/`));
    await expect(page.locator('.svic-promo-bar--home-sale')).toHaveCount(0);
  });
}

test('cached homepage banner disappears at its expiry', async ({ page }) => {
  await page.clock.install();
  await page.goto('/', { waitUntil: 'load' });
  const banner = page.locator('.svic-promo-bar--home-sale');
  await expect(banner).toBeVisible();
  const expires = Number(await banner.getAttribute('data-sale-expires')) * 1000;
  await page.clock.fastForward(Math.max(1, expires - await page.evaluate(() => Date.now()) + 1000));
  await expect(banner).toBeHidden();
});
