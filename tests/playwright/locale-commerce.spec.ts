import { test, expect } from '@playwright/test';

test.describe('Localized 15P backorder cart flow', () => {
  for (const locale of [
    { prefix: '/zh', added: '已加入您的購物車', shipping: '預計下單後 2–3 週送達' },
    { prefix: '/zh-cn', added: '已加入您的购物车', shipping: '预计下单后 2–3 周送达' },
  ]) {
    test(`keeps ${locale.prefix} through add-to-cart, cart, and checkout`, async ({ page }) => {
      await page.goto(`${locale.prefix}/product/svicloud-15p/`, { waitUntil: 'networkidle' });
      const formAction = await page.locator('form.cart').getAttribute('action');
      expect(formAction).toContain(`${locale.prefix}/`);

      await expect(page.locator('.svic-15p-delivery-banner')).toContainText(locale.shipping);

      await page.locator('.single_add_to_cart_button').click();
      await page.waitForLoadState('networkidle');
      expect(page.url()).toContain(`${locale.prefix}/`);
      await expect(page.locator('.woocommerce-message')).toContainText(locale.added);
      await expect(page.locator('.woocommerce-message a')).toHaveAttribute('href', new RegExp(`${locale.prefix}/cart/`));

      await page.goto(`${locale.prefix}/cart/`, { waitUntil: 'networkidle' });
      expect(page.url()).toContain(`${locale.prefix}/`);
      await expect(page.locator('.svic-15p-delivery-banner')).toContainText(locale.shipping);

      await page.goto(`${locale.prefix}/checkout/`, { waitUntil: 'networkidle' });
      expect(page.url()).toContain(`${locale.prefix}/`);
      await expect(page.locator('.svic-15p-delivery-banner')).toContainText(locale.shipping);
    });
  }
});
