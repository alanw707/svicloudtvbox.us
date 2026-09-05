import { test, expect } from '@playwright/test';
import path from 'node:path';
test.beforeEach(async ({page}) => {
 if (process.env.SVIC_LOCAL_ASSETS === '1') {
  await page.route(/\/assets\/js\/theme(?:\.min)?\.js(?:\?|$)/, r => r.fulfill({path:path.resolve('theme/svicloudtvbox-lumen/assets/js/theme.js'),contentType:'application/javascript'}));
  await page.route(/\/assets\/css\/woocommerce\.css(?:\?|$)/, r => r.fulfill({path:path.resolve('theme/svicloudtvbox-lumen/assets/css/woocommerce.css'),contentType:'text/css'}));
 }
});
for (const locale of ['', '/zh', '/zh-cn']) {
 test(`one unit and cart correction ${locale || 'en'}`, async ({page}) => {
  await page.route(/google-analytics\.com|googletagmanager\.com|googleadservices\.com|doubleclick\.net/,r=>r.abort());
  await page.goto(`${locale}/product/svicloud-15p/`,{waitUntil:'domcontentloaded'});
  const qty=page.locator('form.cart input.qty');
  await expect(qty).toHaveValue('1');
  await page.locator('form.cart .single_add_to_cart_button').click();
  await page.waitForLoadState('domcontentloaded');
  await page.goto(`${locale}/cart/`,{waitUntil:'domcontentloaded'});
  const cartqty=page.locator('.woocommerce-cart-form__cart-item input.qty').first();
  await expect(cartqty).toHaveValue('1');
  await cartqty.fill('2');await cartqty.dispatchEvent('change');
  await Promise.all([page.waitForResponse(r => r.request().method() === 'POST' && r.url().includes('/cart/')), page.locator('[name="update_cart"]').click()]);
  await expect(page.locator('.blockUI')).toHaveCount(0);
  await expect(cartqty).toHaveValue('2');
  await cartqty.fill('1');await cartqty.dispatchEvent('change');
  await Promise.all([page.waitForResponse(r => r.request().method() === 'POST' && r.url().includes('/cart/')), page.locator('[name="update_cart"]').click()]);
  await expect(page.locator('.blockUI')).toHaveCount(0);
  await expect(cartqty).toHaveValue('1');
  await page.locator('.checkout-button').first().click();
  await expect(page.locator('.product-quantity').first()).toContainText('1');
 });
}

test('checkout uses the quantity currently displayed in cart', async ({page}) => {
 await page.route(/google-analytics\.com|googletagmanager\.com|googleadservices\.com|doubleclick\.net/,r=>r.abort());
 await page.goto('/zh/product/svicloud-15p/',{waitUntil:'domcontentloaded'});
 await page.locator('form.cart input.qty').fill('2');
 await page.locator('form.cart .single_add_to_cart_button').click();
 await page.waitForLoadState('domcontentloaded');
 await page.goto('/zh/cart/',{waitUntil:'domcontentloaded'});
 const qty=page.locator('.woocommerce-cart-form__cart-item input.qty').first();
 await expect(qty).toHaveValue('2');
 await page.locator('[data-qty-control="decrease"]').first().click();
 await expect(qty).toHaveValue('1');
 await page.locator('.checkout-button').first().click();
 await expect(page.locator('.product-quantity').first()).toContainText('1');
});

test('reloading after add-to-cart does not add a second unit', async ({page}) => {
 await page.route(/google-analytics\.com|googletagmanager\.com|googleadservices\.com|doubleclick\.net/,r=>r.abort());
 await page.goto('/product/svicloud-15p/',{waitUntil:'domcontentloaded'});
 await page.locator('form.cart .single_add_to_cart_button').click();
 await page.waitForLoadState('domcontentloaded');
 await expect(page.locator('.woocommerce-message').first()).toBeVisible();
 await page.reload({waitUntil:'domcontentloaded'});
 await page.goto('/cart/',{waitUntil:'domcontentloaded'});
 await expect(page.locator('.woocommerce-cart-form input.qty').first()).toHaveValue('1');
});

test('buy now quantity one replaces existing quantity for that product', async ({page}) => {
 await page.route(/google-analytics\.com|googletagmanager\.com|googleadservices\.com|doubleclick\.net/,r=>r.abort());
 await page.goto('/product/svicloud-15p/',{waitUntil:'domcontentloaded'});
 const id=await page.locator('form.cart .single_add_to_cart_button').getAttribute('value');
 await page.locator('form.cart .single_add_to_cart_button').click();
 await page.waitForLoadState('domcontentloaded');
 await expect(page.locator('.woocommerce-message').first()).toBeVisible();
 await page.goto(`/?add-to-cart=${id}&quantity=1&svic_buynow=1`,{waitUntil:'domcontentloaded'});
 await expect(page).toHaveURL(/checkout/);
 await expect(page.locator('.product-quantity').first()).toContainText('1');
});

test('failed cart save stays on cart and prevents express payment of old quantity', async ({page}) => {
 await page.route(/google-analytics\.com|googletagmanager\.com|googleadservices\.com|doubleclick\.net/,r=>r.abort());
 await page.goto('/product/svicloud-15p/',{waitUntil:'domcontentloaded'});
 await page.locator('form.cart input.qty').fill('2');
 await page.locator('form.cart .single_add_to_cart_button').click();
 await page.waitForLoadState('domcontentloaded');
 await page.goto('/cart/',{waitUntil:'domcontentloaded'});
 await page.locator('[data-qty-control="decrease"]').first().click();
 await expect(page.locator('[data-cart-page]')).toHaveClass(/svic-cart-unsaved/);
 // A frame inside the hidden express container must not be clickable/focusable.
 const express=page.locator('.wc-proceed-to-checkout > :not(.checkout-button):not(.svic-cart-save-notice)');
 for (const el of await express.all()) await expect(el).toBeHidden();
 await page.route('**/cart/**',r=>r.request().method()==='POST'?r.abort():r.continue());
 await page.locator('.checkout-button').first().click();
 await expect(page.locator('.svic-cart-save-notice')).toContainText(/Could not confirm|尚未確認|尚未确认/);
 await expect(page).toHaveURL(/\/cart\//);
 await expect(page.locator('.woocommerce-cart-form input.qty').first()).toHaveValue('1');
});
