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

      const urlObj = new URL(path, baseURL);
      urlObj.searchParams.set('cb', Date.now().toString());
      const resp = await page.goto(urlObj.toString(), { waitUntil: 'domcontentloaded' });
      expect(resp?.ok()).toBeTruthy();

      // Basic header checks
      await expect(page.locator('header.lumen-header')).toBeVisible();
      await expect(page.locator('.lumen-header__logo-image')).toHaveCount(1, { timeout: 5000 });

      // Page specific probes
      if (path.startsWith('/product/')) {
        await expect(page.locator('.product-hero')).toBeVisible();
        await expect(page.locator('.product-hero-price')).toBeVisible();
        const thumbCount = await page.locator('.product-thumb').count();
        if (thumbCount > 1) {
          const thumbLocator = page.locator('.product-thumb').nth(thumbCount - 1);
          const firstSrc = await page.locator('.product-hero-image').first().getAttribute('src');
          const targetSrc = await thumbLocator.getAttribute('data-image');
          await thumbLocator.click();
          await expect(thumbLocator).toHaveAttribute('aria-pressed', 'true');
          if (firstSrc && targetSrc && targetSrc !== firstSrc) {
            await expect(page.locator('.product-hero-image').first()).toHaveAttribute('src', targetSrc);
          }
        }
        const addBtn = page.locator('.single_add_to_cart_button');
        await expect(addBtn).toBeVisible();
        await expect(addBtn).toHaveText(/add to cart/i);
        await page.evaluate(() => {
          const btn = document.querySelector('.single_add_to_cart_button');
          if (!btn) return;
          btn.addEventListener('click', (event) => event.preventDefault(), { once: true, capture: true });
        });
        await addBtn.click();
        await expect(addBtn).toHaveClass(/is-loading/, { timeout: 600 });
        await expect(addBtn).toHaveAttribute('aria-busy', 'true');
        await page.evaluate(() => {
          if (window.jQuery) {
            const $btn = window.jQuery('.single_add_to_cart_button');
            if ($btn && $btn.length) {
              window.jQuery(document.body).trigger('added_to_cart', [$btn]);
            }
          }
        });
        await expect(addBtn).not.toHaveClass(/is-loading/, { timeout: 5000 });
        await expect(addBtn).toHaveAttribute('aria-busy', 'false');
      }

      if (path === '/') {
        await expect(page.locator('.hero-dashboard')).toBeVisible();
        await expect(page.locator('.hero-dashboard__card')).toBeVisible();
        await expect(page.locator('.lumen-metric')).toHaveCount(4);
        await expect(page.locator('.lumen-feature-card')).toHaveCount(3);
      }

      if (path === '/compare/') {
        const tableLocator = page.locator('.comparison-table table');
        if (await tableLocator.isVisible()) {
          const rowHeaders = await page.locator('.comparison-table tbody th').count();
          expect(rowHeaders).toBeGreaterThan(0);
        } else {
          const cardCount = await page.locator('.comparison-card').count();
          expect(cardCount).toBeGreaterThan(0);
        }
        const bilingualCount = await page.locator('.comparison-panel .hide-en').count();
        expect(bilingualCount).toBeGreaterThan(0);
      }

      // No console errors
      expect(errors, errors.join('\n')).toHaveLength(0);
    });
  }
});
