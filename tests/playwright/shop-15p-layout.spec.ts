import { test, expect } from '@playwright/test';

test('keeps 15P feature bullets compact on desktop', async ({ page }) => {
  await page.setViewportSize({ width: 1026, height: 678 });
  await page.goto('/shop/', { waitUntil: 'domcontentloaded' });

  const rows = await page.locator('.shop-product-card--backorder .shop-product-card__features li').evaluateAll((items) => items.map((item) => {
    const rect = item.getBoundingClientRect();
    const range = document.createRange();
    range.selectNodeContents(item);
    const contentHeight = range.getBoundingClientRect().height;
    const lineHeight = parseFloat(getComputedStyle(item).lineHeight);
    return { height: rect.height, contentHeight, lineHeight };
  }));

  expect(rows.length).toBe(4);
  expect(rows.every(({ height, contentHeight, lineHeight }) => height <= contentHeight + lineHeight)).toBe(true);
  await expect(page.locator('.shop-product-card--backorder .shop-product-card__best-for')).toContainText('Family rooms, sports fans, and mobile app users');
});
