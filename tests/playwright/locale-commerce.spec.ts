import { test, expect } from '@playwright/test';

test.describe('Localized 15P in-stock cart flow', () => {
  for (const locale of [
    { prefix: '/zh', added: '已加入您的購物車', stock: '現貨供應' },
    { prefix: '/zh-cn', added: '已加入您的购物车', stock: '现货供应' },
  ]) {
    test(`keeps ${locale.prefix} through add-to-cart, cart, and checkout`, async ({ page }) => {
      await page.goto(`${locale.prefix}/product/svicloud-15p/`, { waitUntil: 'domcontentloaded' });
      const formAction = await page.locator('form.cart').getAttribute('action');
      expect(formAction).toContain(`${locale.prefix}/`);

      await expect(page.locator('.svic-15p-delivery-banner')).toContainText(locale.stock);

      await page.locator('.single_add_to_cart_button').click();
      await page.waitForTimeout(1500);
      expect(page.url()).toContain(`${locale.prefix}/`);
      await expect(page.locator('.woocommerce-message')).toContainText(locale.added);
      await expect(page.locator('.woocommerce-message a')).toHaveAttribute('href', new RegExp(`${locale.prefix}/cart/`));

      await page.goto(`${locale.prefix}/cart/`, { waitUntil: 'domcontentloaded' });
      expect(page.url()).toContain(`${locale.prefix}/`);
      await expect(page.locator('.svic-15p-delivery-banner')).toHaveCount(0);

      await page.goto(`${locale.prefix}/checkout/`, { waitUntil: 'domcontentloaded' });
      expect(page.url()).toContain(`${locale.prefix}/`);
      await expect(page.locator('.svic-15p-delivery-banner')).toHaveCount(0);
    });
  }
});
