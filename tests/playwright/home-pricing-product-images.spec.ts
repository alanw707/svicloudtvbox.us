import { test, expect } from '@playwright/test';

test.describe('Homepage pricing product images', () => {
  test('10P+ card uses the approved homepage product image', async ({ page, baseURL }) => {
    const url = new URL('/', baseURL);
    url.searchParams.set('cb', Date.now().toString());

    const response = await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
    expect(response?.ok()).toBeTruthy();

    const tenpCard = page.locator('[data-svic-card="10p"], .shop-product-card').filter({
      has: page.locator('.shop-product-card__title', { hasText: /SVICLOUD 10P\+/ }),
    });
    await expect(tenpCard).toBeVisible();

    const image = tenpCard.locator('.shop-product-card__media img');
    await expect(image).toHaveAttribute('src', /\/assets\/images\/svicloud-hero-product\.webp$/);
    await expect(image).not.toHaveAttribute('src', /\/assets\/images\/svicloud-10p-plus\.png$/);
  });

  test('10S card uses the 10S product image', async ({ page, baseURL }) => {
    const url = new URL('/', baseURL);
    url.searchParams.set('cb', Date.now().toString());

    const response = await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
    expect(response?.ok()).toBeTruthy();

    const tensCard = page.locator('[data-svic-card="10s"], .shop-product-card').filter({
      has: page.locator('.shop-product-card__title', { hasText: /SVICLOUD 10S/ }),
    });
    await expect(tensCard).toBeVisible();

    const image = tensCard.locator('.shop-product-card__media img');
    await expect(image).toHaveAttribute('src', /\/assets\/images\/svicloud-tvbox-10s\.jpg$/);
    await expect(image).not.toHaveAttribute('src', /\/assets\/images\/svicloud-hero-product\.webp$/);
  });
});
