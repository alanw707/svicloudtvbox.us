import { test, expect } from '@playwright/test';

const BASE_URL = process.env.PLAYWRIGHT_BASE_URL || 'http://svicloud10p.svic.local';
const TEST_PRODUCT_PATH = process.env.PLAYWRIGHT_TEST_PRODUCT_PATH || '/product/svicloud-10p-plus/';
const STRIPE_TEST_CARD = process.env.PLAYWRIGHT_STRIPE_TEST_CARD || '4242424242424242';
const STRIPE_EXPIRY_MMYY = process.env.PLAYWRIGHT_STRIPE_TEST_EXPIRY || '12/34';
const STRIPE_CVC = process.env.PLAYWRIGHT_STRIPE_TEST_CVC || '123';
const STRIPE_ZIP = process.env.PLAYWRIGHT_STRIPE_TEST_ZIP || '90001';

const BASE_WITH_TRAILING = BASE_URL.replace(/\/$/, '') + '/';

async function seedCart(page: import('@playwright/test').Page) {
  const productURL = `${BASE_WITH_TRAILING}${TEST_PRODUCT_PATH.replace(/^\/+/, '')}`;
  await page.goto(productURL, { waitUntil: 'domcontentloaded' });

  const addButton = page.locator('form.cart button[type="submit"], .single_add_to_cart_button');
  await expect(addButton).toBeVisible();
  await addButton.click();
  await page.waitForLoadState('networkidle');

  await page.goto(`${BASE_WITH_TRAILING}cart/`, { waitUntil: 'domcontentloaded' });
  const cartRows = await page.locator('.woocommerce-cart-form__cart-item, .lumen-cart-product').count();
  if (cartRows === 0) {
    throw new Error('Cart is empty after adding via product page.');
  }
}

async function fillBillingDetails(page: import('@playwright/test').Page) {
  const fieldEntries: Array<[string, string]> = [
    ['#billing_first_name', 'Play'],
    ['#billing_last_name', 'Wright'],
    ['#billing_address_1', '123 Checkout Lane'],
    ['#billing_city', 'Los Angeles'],
    ['#billing_postcode', '90001'],
    ['#billing_phone', '5551239876'],
    ['#billing_email', `playwright-${Date.now()}@example.com`],
  ];

  for (const [selector, value] of fieldEntries) {
    const field = page.locator(selector);
    await expect(field).toBeVisible();
    await field.fill(value);
  }

  const stateSelect = page.locator('#billing_state');
  if (await stateSelect.count()) {
    await stateSelect.selectOption('CA');
  } else {
    const select2Input = page.locator('#select2-billing_state-container');
    if (await select2Input.count()) {
      await select2Input.click();
      const searchField = page.locator('.select2-search__field');
      await searchField.fill('California');
      await searchField.press('Enter');
    }
  }

  const shipDiffCheckbox = page.locator('#ship-to-different-address-checkbox');
  if ((await shipDiffCheckbox.count()) && (await shipDiffCheckbox.isChecked())) {
    await shipDiffCheckbox.uncheck();
  }
}

async function fillStripeCard(page: import('@playwright/test').Page) {
  const iframeLocator = page.locator('iframe[name^="__privateStripeFrame"]');
  await expect(iframeLocator.first()).toBeVisible({ timeout: 20000 });

  const frameCount = await iframeLocator.count();
  let cardFrameUsed = false;
  let zipFilled = false;

  for (let i = 0; i < frameCount; i += 1) {
    const frameLocator = page.frameLocator('iframe[name^="__privateStripeFrame"]').nth(i);

    if (!cardFrameUsed) {
      const numberInput = frameLocator.locator('input[data-elements-stable-field-name="cardNumber"], input[name="cardnumber"], input[name="number"], input[placeholder*="1234"]');
      const expInput = frameLocator.locator('input[data-elements-stable-field-name="cardExpiry"], input[name="exp-date"], input[name="exp"], input[placeholder*="MM / YY"]');
      const cvcInput = frameLocator.locator('input[data-elements-stable-field-name="cardCvc"], input[name="cvc"], input[placeholder*="CVC" i]');

      if (await numberInput.count()) {
        await numberInput.fill('');
        await numberInput.type(STRIPE_TEST_CARD, { delay: 10 });
      }

      if (await expInput.count()) {
        await expInput.fill('');
        await expInput.type(STRIPE_EXPIRY_MMYY, { delay: 30 });
      }

      if (await cvcInput.count()) {
        await cvcInput.fill('');
        await cvcInput.type(STRIPE_CVC, { delay: 20 });
      }

      if ((await numberInput.count()) || (await expInput.count()) || (await cvcInput.count())) {
        cardFrameUsed = true;
      }
    }

    if (!zipFilled) {
      const zipInput = frameLocator.locator('input[data-elements-stable-field-name="billingPostalCode"], input[name="postal"], input[name="zip"], input[placeholder*="ZIP" i]');
      if (await zipInput.count()) {
        await zipInput.fill(STRIPE_ZIP);
        zipFilled = true;
      }
    }
  }

  expect(cardFrameUsed, 'Failed to populate Stripe card fields').toBeTruthy();
}

async function acceptTerms(page: import('@playwright/test').Page) {
  const termsCheckbox = page.locator('#terms');
  if (await termsCheckbox.count()) {
    if (!(await termsCheckbox.isChecked())) {
      await termsCheckbox.check({ force: true });
    }
  }
}

test.describe('Checkout purchase flow', () => {
  test('submits Stripe payment and lands on order summary', async ({ page }) => {
    const consoleLogs: Array<{ type: string; text: string }> = [];
    page.on('console', (msg) => {
      consoleLogs.push({ type: msg.type(), text: msg.text() });
    });
    page.on('pageerror', (err) => {
      consoleLogs.push({ type: 'pageerror', text: err.message });
    });

    await seedCart(page);
    await page.goto(`${BASE_WITH_TRAILING}checkout/`, { waitUntil: 'domcontentloaded' });

    const reviewTable = page.locator('.woocommerce-checkout-review-order-table');
    await expect(reviewTable).toBeVisible();
    const subtotalText = await reviewTable.locator('.cart-subtotal .amount').textContent();
    expect(subtotalText).toBeTruthy();
    expect(subtotalText?.replace(/[^0-9.]/g, '')).not.toBe('0.00');

    await fillBillingDetails(page);

    const stripeRadio = page.locator('input[name="payment_method"][value="stripe_cc"]');
    if (await stripeRadio.count()) {
      await stripeRadio.check({ force: true });
    }

    await fillStripeCard(page);
    await acceptTerms(page);

    const placeOrderButton = page.locator('#place_order');
    await expect(placeOrderButton).toBeEnabled();

    const checkoutResponsePromise = page.waitForResponse((res) => res.url().includes('wc-ajax=checkout'));

    const [checkoutResponse] = await Promise.all([
      checkoutResponsePromise,
      placeOrderButton.click(),
    ]);

    const responseStatus = checkoutResponse.status();
    let checkoutResult: any = null;

    try {
      checkoutResult = await checkoutResponse.json();
    } catch (err) {
      // eslint-disable-next-line no-console
      console.log('Failed to parse checkout response as JSON', { status: responseStatus });
    }

    // eslint-disable-next-line no-console
    console.log('checkout result raw', { status: responseStatus, checkoutResult });

    if (checkoutResult?.result === 'success' && checkoutResult?.redirect) {
      await page.goto(checkoutResult.redirect, { waitUntil: 'domcontentloaded' });
    }

    const bodyClass = await page.evaluate(() => document.body.className);
    const orderSummary = page.locator('.woocommerce-order-overview, .woocommerce-thankyou-order-details');

    // eslint-disable-next-line no-console
    console.log('console logs during checkout', consoleLogs);
    // eslint-disable-next-line no-console
    console.log('body class on final page', bodyClass);

    await expect(page).toHaveURL(/\/checkout\/order-received\//);
    expect(bodyClass).toContain('woocommerce-order-received');
    await expect(orderSummary).toContainText(/Order|Total/i);
  });
});
