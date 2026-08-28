import { test, expect } from '@playwright/test';

test.describe('Compare product card media sizing', () => {
  test('desktop: images fill most of media well', async ({ page, baseURL }) => {
    const url = new URL('/compare/', baseURL);
    url.searchParams.set('cb', Date.now().toString());
    const resp = await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
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

  test('desktop: every product image stays centered inside its media well', async ({ page, baseURL }) => {
    const url = new URL('/compare/', baseURL);
    url.searchParams.set('cb', Date.now().toString());
    const resp = await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
    expect(resp?.ok()).toBeTruthy();

    const measurements = await page.locator('.compare-product-card__media').evaluateAll((nodes) => nodes.map((media) => {
      const image = media.querySelector('img');
      const card = media.closest('.shop-product-card');
      const title = card?.querySelector('.shop-product-card__title')?.textContent?.trim() || '';
      const mediaRect = media.getBoundingClientRect();
      const imageRect = image?.getBoundingClientRect();

      return {
        title,
        mediaWidth: mediaRect.width,
        mediaHeight: mediaRect.height,
        imageWidth: imageRect?.width || 0,
        imageHeight: imageRect?.height || 0,
        centerOffsetX: imageRect
          ? Math.abs((imageRect.left + imageRect.width / 2) - (mediaRect.left + mediaRect.width / 2))
          : Infinity,
        centerOffsetY: imageRect
          ? Math.abs((imageRect.top + imageRect.height / 2) - (mediaRect.top + mediaRect.height / 2))
          : Infinity,
      };
    }));

    for (const measurement of measurements) {
      expect(measurement.imageWidth, `${measurement.title} image width`).toBeLessThanOrEqual(measurement.mediaWidth + 1);
      expect(measurement.imageHeight, `${measurement.title} image height`).toBeLessThanOrEqual(measurement.mediaHeight + 1);
      expect(measurement.centerOffsetX, `${measurement.title} horizontal centering`).toBeLessThanOrEqual(1);
      expect(measurement.centerOffsetY, `${measurement.title} vertical centering`).toBeLessThanOrEqual(1);
    }
  });

  test('mobile: images expand to near full width', async ({ page, baseURL }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    const url = new URL('/compare/', baseURL);
    url.searchParams.set('cb', Date.now().toString());
    const resp = await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
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
