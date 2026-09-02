import { test, expect } from '@playwright/test';

test.describe('Shop product card media framing', () => {
  test('mobile homepage pricing images stay centered inside their media frame', async ({ page, baseURL }) => {
    await page.setViewportSize({ width: 430, height: 932 });
    const url = new URL('/', baseURL);
    url.searchParams.set('cb', Date.now().toString());
    const response = await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
    expect(response?.ok()).toBeTruthy();

    const cards = page.locator('.lumen-pricing .shop-product-card').filter({
      has: page.locator('.shop-product-card__media img'),
    });
    await expect(cards.first()).toBeVisible();

    const measurements = await cards.evaluateAll((nodes) => nodes.map((card) => {
      const media = card.querySelector('.shop-product-card__media');
      const image = media?.querySelector('img');
      const mediaRect = media?.getBoundingClientRect();
      const imageRect = image?.getBoundingClientRect();
      const title = card.querySelector('.shop-product-card__title')?.textContent?.trim() || '';

      return {
        title,
        mediaWidth: mediaRect?.width || 0,
        mediaHeight: mediaRect?.height || 0,
        imageWidth: imageRect?.width || 0,
        imageHeight: imageRect?.height || 0,
        centerOffsetX: mediaRect && imageRect
          ? Math.abs((imageRect.left + imageRect.width / 2) - (mediaRect.left + mediaRect.width / 2))
          : Infinity,
        centerOffsetY: mediaRect && imageRect
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

  test('mobile product images stay centered inside their media frame', async ({ page, baseURL }) => {
    await page.setViewportSize({ width: 430, height: 932 });
    const url = new URL('/shop/', baseURL);
    url.searchParams.set('cb', Date.now().toString());
    const response = await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
    expect(response?.ok()).toBeTruthy();

    const cards = page.locator('.shop-product-card').filter({
      has: page.locator('.shop-product-card__media img'),
    });
    await expect(cards.first()).toBeVisible();

    const measurements = await cards.evaluateAll((nodes) => nodes.map((card) => {
      const media = card.querySelector('.shop-product-card__media');
      const image = media?.querySelector('img');
      const mediaRect = media?.getBoundingClientRect();
      const imageRect = image?.getBoundingClientRect();
      const title = card.querySelector('.shop-product-card__title')?.textContent?.trim() || '';

      return {
        title,
        cardLeft: card.getBoundingClientRect().left,
        cardRight: card.getBoundingClientRect().right,
        mediaWidth: mediaRect?.width || 0,
        mediaHeight: mediaRect?.height || 0,
        mediaLeft: mediaRect?.left || 0,
        mediaRight: mediaRect?.right || 0,
        imageWidth: imageRect?.width || 0,
        imageHeight: imageRect?.height || 0,
        centerOffsetX: mediaRect && imageRect
          ? Math.abs((imageRect.left + imageRect.width / 2) - (mediaRect.left + mediaRect.width / 2))
          : Infinity,
        centerOffsetY: mediaRect && imageRect
          ? Math.abs((imageRect.top + imageRect.height / 2) - (mediaRect.top + mediaRect.height / 2))
          : Infinity,
      };
    }));

    for (const measurement of measurements) {
      expect(measurement.mediaLeft, `${measurement.title} media stays inside card left edge`).toBeGreaterThanOrEqual(measurement.cardLeft - 1);
      expect(measurement.mediaRight, `${measurement.title} media stays inside card right edge`).toBeLessThanOrEqual(measurement.cardRight + 1);
      expect(measurement.imageWidth, `${measurement.title} image width`).toBeLessThanOrEqual(measurement.mediaWidth + 1);
      expect(measurement.imageHeight, `${measurement.title} image height`).toBeLessThanOrEqual(measurement.mediaHeight + 1);
      expect(measurement.centerOffsetX, `${measurement.title} horizontal centering`).toBeLessThanOrEqual(1);
      expect(measurement.centerOffsetY, `${measurement.title} vertical centering`).toBeLessThanOrEqual(1);
    }
  });
});
