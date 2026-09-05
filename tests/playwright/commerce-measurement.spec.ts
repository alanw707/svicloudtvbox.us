import { test, expect } from '@playwright/test';

for (const locale of ['', '/zh', '/zh-cn']) {
  test(`commerce events and locale continuity ${locale || '/en'}`, async ({ page }) => {
    // Observe queued events, never send synthetic journeys to analytics or Ads.
    await page.route(/google-analytics\.com|googletagmanager\.com|googleadservices\.com|doubleclick\.net/, route => route.abort());
    await page.goto(`${locale}/product/svicloud-10p-plus/`, { waitUntil: 'domcontentloaded' });
    await expect(page.locator('#svic-ga4-commerce-events')).toHaveCount(1);
    const queued = () => page.evaluate(() => (window as any).dataLayer.map((entry: any) => Array.from(entry)).filter((entry: any) => entry[0] === 'event' && entry[2]?.send_to !== 'GLA'));
    let events = await queued();
    expect(events.filter((e: any) => e[1] === 'view_item')).toHaveLength(1);
    expect(events.find((e: any) => e[1] === 'view_item')?.[2]).toMatchObject({ currency: 'USD', items: [{ quantity: 1 }] });
    await page.locator('form.cart .single_add_to_cart_button').click();
    await page.waitForLoadState('domcontentloaded');
    // Woo may render the product again or redirect to cart; both are valid.
    await expect(page.locator('.woocommerce-message, .woocommerce-notices-wrapper').first()).toBeAttached();
    events = await queued();
    expect(events.filter((e: any) => e[1] === 'add_to_cart')).toHaveLength(1);
    await page.goto(`${locale}/cart/`, { waitUntil: 'domcontentloaded' });
    events = await queued();
    expect(events.filter((e: any) => e[1] === 'add_to_cart')).toHaveLength(0);
    expect(events.filter((e: any) => e[1] === 'view_cart')).toHaveLength(1);
    await page.locator('.checkout-button').first().click();
    await expect(page).toHaveURL(new RegExp(`${locale}/checkout/`));
    await expect(page.locator('form.checkout')).toBeVisible();
    events = await queued();
    expect(events.filter((e: any) => e[1] === 'begin_checkout')).toHaveLength(1);
    expect(events.filter((e: any) => e[1] === 'purchase')).toHaveLength(0);
    // No billing details, order creation, payment click or charge.
  });
}
