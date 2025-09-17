import { test, expect } from '@playwright/test';

test.describe('Commerce smoke', () => {
  test('guest can browse PDP and reach checkout', async ({ page }) => {
    await page.goto('/');

    await expect(page.getByRole('heading', { name: /premium chinese iptv/i })).toBeVisible();
    await expect(page.getByRole('link', { name: /Shop 10P\+/i })).toBeVisible();

    await page.getByRole('link', { name: /Shop 10P\+/i }).click();
    await expect(page).toHaveURL(/\/product\/svicloud-10p-plus\/?/);
    await expect(page.getByRole('heading', { name: /SviCloud TV Box 10P\+/i })).toBeVisible();

    await page.getByRole('button', { name: /Add to cart/i }).click();
    const message = page.locator('.woocommerce-message');
    await expect(message).toBeVisible();

    const viewCartLink = message.getByRole('link', { name: /view cart/i });
    if (await viewCartLink.count()) {
      await viewCartLink.first().click();
    } else {
      await page.goto('/cart/');
    }

    await expect(page).toHaveURL(/\/cart\//);
    await expect(page.getByRole('heading', { name: /cart/i })).toBeVisible();
    await expect(page.getByRole('link', { name: /proceed to checkout/i })).toBeVisible();

    await page.getByRole('link', { name: /proceed to checkout/i }).click();
    await expect(page).toHaveURL(/\/checkout\//);
    await expect(page.getByRole('heading', { name: /checkout/i })).toBeVisible();
  });
});
