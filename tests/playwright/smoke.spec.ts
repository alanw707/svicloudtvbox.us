import { test, expect } from '@playwright/test';

const paths = [
  '/',
  '/compare/',
  '/shop/',
  '/product/svicloud-10p-plus/',
  '/product/svicloud-10s/',
  '/my-account/',
];

test.describe('SVICLOUD site smoke', () => {
  for (const path of paths) {
    test(`loads ${path} without console errors`, async ({ page, baseURL }) => {
      const errors: string[] = [];
      page.on('console', (msg) => {
        if (msg.type() === 'error') errors.push(msg.text());
      });

      const url = new URL(path, baseURL).toString();
      const resp = await page.goto(url, { waitUntil: 'domcontentloaded' });
      expect(resp?.ok()).toBeTruthy();

      // Basic header checks
      await expect(page.locator('header.site-header')).toBeVisible();
      await expect(page.locator('.site-logo img')).toHaveCount(1, { timeout: 5000 });

      // Page specific probes
      if (path.startsWith('/product/')) {
        await expect(page.locator('.price')).toBeVisible();
        await expect(page.getByRole('button', { name: /add to cart/i })).toBeVisible();
      }

      if (path === '/') {
        await expect(page.locator('.hero-modern')).toBeVisible();
      }

      // No console errors
      expect(errors, errors.join('\n')).toHaveLength(0);
    });
  }
});
