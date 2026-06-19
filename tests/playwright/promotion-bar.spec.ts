import { test, expect } from '@playwright/test';

test.describe('Father\'s Day promotion bar', () => {
  test('renders as a calm single offer on desktop and mobile', async ({ page, baseURL }) => {
    const url = new URL('/', baseURL);
    url.searchParams.set('cb', Date.now().toString());

    const resp = await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
    expect(resp?.ok()).toBeTruthy();

    const bar = page.locator('.svic-promo-bar');
    await expect(bar).toBeVisible();
    await expect(bar.locator('.svic-promo-bar__offer')).toHaveText(/All SVICLOUD products save 5%/);
    await expect(bar.locator('.svic-promo-bar__code')).toHaveText(/DAD2026/);
    await expect(bar.locator('.svic-promo-bar__cta')).toBeVisible();
    await expect(bar.locator('.svic-promo-bar__chip')).toHaveCount(0);

    const box = await bar.boundingBox();
    expect(box?.height ?? 0).toBeLessThanOrEqual(96);

    await page.setViewportSize({ width: 390, height: 844 });
    await expect(bar).toBeVisible();
    const mobileBox = await bar.boundingBox();
    expect(mobileBox?.height ?? 0).toBeLessThanOrEqual(150);
  });
});
