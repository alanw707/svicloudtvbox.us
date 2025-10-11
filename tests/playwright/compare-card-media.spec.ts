import { test, expect } from '@playwright/test';

test.describe('Compare product card media sizing', () => {
  test('desktop: images fill most of media well', async ({ page, baseURL }) => {
    const url = new URL('/compare/', baseURL);
    url.searchParams.set('cb', Date.now().toString());
    const resp = await page.goto(url.toString(), { waitUntil: 'networkidle' });
    expect(resp?.ok()).toBeTruthy();

    const media = page.locator('.compare-product-card__media').first();
    await expect(media).toBeVisible();

    const ratio = await media.evaluate((node) => {
      const img = node.querySelector('img');
      if (!img) return 0;
      const mw = (node as HTMLElement).getBoundingClientRect().width;
      const iw = (img as HTMLImageElement).getBoundingClientRect().width;
      return iw / mw;
    });
    expect(ratio).toBeGreaterThanOrEqual(0.85);
  });

  test('mobile: images expand to near full width', async ({ page, baseURL }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    const url = new URL('/compare/', baseURL);
    url.searchParams.set('cb', Date.now().toString());
    const resp = await page.goto(url.toString(), { waitUntil: 'networkidle' });
    expect(resp?.ok()).toBeTruthy();

    const media = page.locator('.compare-product-card__media').first();
    await expect(media).toBeVisible();

    const ratio = await media.evaluate((node) => {
      const img = node.querySelector('img');
      if (!img) return 0;
      const mw = (node as HTMLElement).getBoundingClientRect().width;
      const iw = (img as HTMLImageElement).getBoundingClientRect().width;
      return iw / mw;
    });
    expect(ratio).toBeGreaterThanOrEqual(0.92);
  });
});

