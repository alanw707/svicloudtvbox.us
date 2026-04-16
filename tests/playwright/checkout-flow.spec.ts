import { test, expect } from '@playwright/test';

const BASE_URL = process.env.PLAYWRIGHT_BASE_URL || 'http://svicloud10p.svic.local';
const TEST_PRODUCT_PATH = process.env.PLAYWRIGHT_TEST_PRODUCT_PATH || '/product/svicloud-10p-plus/';
const STRIPE_TEST_CARD = process.env.PLAYWRIGHT_STRIPE_TEST_CARD || '4242424242424242';
const STRIPE_EXPIRY_MMYY = process.env.PLAYWRIGHT_STRIPE_TEST_EXPIRY || '12/34';
const STRIPE_CVC = process.env.PLAYWRIGHT_STRIPE_TEST_CVC || '123';
const STRIPE_ZIP = process.env.PLAYWRIGHT_STRIPE_TEST_ZIP || '89101';
const TEST_CUSTOMER_EMAIL = process.env.PLAYWRIGHT_TEST_EMAIL || process.env.WP_MAIL_SMTP_TEST_EMAIL || 'support@svicloudtvbox.us';

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
  const timestamp = Date.now().toString();
  let billingEmail = TEST_CUSTOMER_EMAIL;
  const emailMatch = TEST_CUSTOMER_EMAIL.match(/^[^@]+@[^@]+$/);

  if (emailMatch) {
    const [local, domain] = TEST_CUSTOMER_EMAIL.split('@');
    const sanitizedLocal = local.replace(/\+.*/, '');
    billingEmail = `${sanitizedLocal}+${timestamp}@${domain}`;
  }

  const fieldEntries: Array<[string, string]> = [
    ['#billing_first_name', 'Play'],
    ['#billing_last_name', 'Wright'],
    ['#billing_address_1', '123 Checkout Lane'],
    ['#billing_city', 'Las Vegas'],
    ['#billing_postcode', '89101'],
    ['#billing_phone', '5551239876'],
    ['#billing_email', billingEmail],
  ];

  for (const [selector, value] of fieldEntries) {
    const field = page.locator(selector);
    await expect(field).toBeVisible();
    await field.fill(value);
  }

  const stateSelect = page.locator('#billing_state');
  if (await stateSelect.count()) {
    await stateSelect.selectOption('NV');
  } else {
    const select2Input = page.locator('#select2-billing_state-container');
    if (await select2Input.count()) {
      await select2Input.click();
      const searchField = page.locator('.select2-search__field');
      await searchField.fill('Nevada');
      await searchField.press('Enter');
    }
  }

  const shipDiffCheckbox = page.locator('#ship-to-different-address-checkbox');
  if ((await shipDiffCheckbox.count()) && (await shipDiffCheckbox.isChecked())) {
    await shipDiffCheckbox.uncheck();
  }
}

async function waitForStripeLocator(
  page: import('@playwright/test').Page,
  selector: string,
  timeout = 20000,
): Promise<import('@playwright/test').Locator | null> {
  const deadline = Date.now() + timeout;
  while (Date.now() < deadline) {
    for (const frame of page.frames()) {
      if (!frame.url().includes('stripe.com')) {
        continue;
      }
      const locator = frame.locator(selector);
      if (await locator.count()) {
        return locator.first();
      }
    }
    await page.waitForTimeout(150);
  }
  return null;
}

async function fillStripeCard(page: import('@playwright/test').Page) {
  const cardNumberField = await waitForStripeLocator(
    page,
    'input[data-elements-stable-field-name="cardNumber"], input[name="cardnumber"], input[name="number"], input[placeholder*="1234"]',
  );
  expect(cardNumberField, 'Stripe card number field not found').not.toBeNull();
  await cardNumberField!.fill('');
  await cardNumberField!.type(STRIPE_TEST_CARD, { delay: 10 });

  const expiryField = await waitForStripeLocator(
    page,
    'input[data-elements-stable-field-name="cardExpiry"], input[name="exp-date"], input[name="exp"], input[placeholder*="MM / YY"]',
    8000,
  );
  if (expiryField) {
    await expiryField.fill('');
    await expiryField.type(STRIPE_EXPIRY_MMYY, { delay: 30 });
  }

  const cvcField = await waitForStripeLocator(
    page,
    'input[data-elements-stable-field-name="cardCvc"], input[name="cvc"], input[placeholder*="CVC" i]',
    8000,
  );
  if (cvcField) {
    await cvcField.fill('');
    await cvcField.type(STRIPE_CVC, { delay: 20 });
  }

  const zipField = await waitForStripeLocator(
    page,
    'input[data-elements-stable-field-name="billingPostalCode"], input[name="postal"], input[name="zip"], input[placeholder*="ZIP" i]',
    8000,
  );
  if (zipField) {
    await zipField.fill(STRIPE_ZIP);
  }
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

    type CheckoutResponsePayload = {
      result?: string;
      redirect?: string;
    } | null;

    const responseStatus = checkoutResponse.status();
    let checkoutResult: CheckoutResponsePayload = null;

    try {
      checkoutResult = (await checkoutResponse.json()) as CheckoutResponsePayload;
    } catch {
      // eslint-disable-next-line no-console
      console.log('Failed to parse checkout response as JSON', { status: responseStatus });
    }

    // eslint-disable-next-line no-console
    console.log('checkout result raw', { status: responseStatus, checkoutResult });

    if (checkoutResult?.result === 'success' && checkoutResult?.redirect) {
      await page.goto(checkoutResult.redirect, { waitUntil: 'domcontentloaded' });
    }

    const bodyClass = await page.evaluate(() => document.body.className);
    const orderSummary = page.locator('.lumen-order-summary, .woocommerce-order-overview, .woocommerce-thankyou-order-details');

    // eslint-disable-next-line no-console
    console.log('console logs during checkout', consoleLogs);
    // eslint-disable-next-line no-console
    console.log('body class on final page', bodyClass);

    await expect(page).toHaveURL(/\/checkout\/order-received\//);
    expect(bodyClass).toContain('woocommerce-order-received');
    await expect(orderSummary).toContainText(/Order|Total/i);

    const thankyouTracking = await page.evaluate(() => {
      type TrackEntry = {
        event?: string;
        svic_location?: string;
        svic_order_number?: string;
        svic_order_total?: string;
      };

      const pageWindow = window as Window & { dataLayer?: TrackEntry[] };
      const dataLayer = Array.isArray(pageWindow.dataLayer) ? pageWindow.dataLayer : [];
      return dataLayer.find((entry) => entry && entry.event === 'svic_order_received') || null;
    });

    expect(thankyouTracking).toBeTruthy();
    expect(thankyouTracking?.svic_location).toBe('thankyou');
    expect(thankyouTracking?.svic_order_number).toBeTruthy();
    expect(thankyouTracking?.svic_order_total).toBeTruthy();

    const thankyouNextCtas = page.locator('[data-svic-location="thankyou_next"]');
    await expect(thankyouNextCtas).toHaveCount(3);
  });
});
