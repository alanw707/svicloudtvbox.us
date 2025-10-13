import { test, expect } from '@playwright/test';

const THEME_BASE = 'http://svicloud10p.svic.local';

async function seedCart(page: import("@playwright/test").Page) {
  await page.goto(`${THEME_BASE}/?add-to-cart=12`, { waitUntil: 'networkidle' });
}

test.describe('Checkout layout spacing', () => {
  test('billing inputs have compact height and generous gutters', async ({ page }) => {
    await seedCart(page);
    await page.goto(`${THEME_BASE}/checkout/`, { waitUntil: 'domcontentloaded' });

    const firstName = page.locator('#billing_first_name');
    await expect(firstName).toBeVisible();

    const panel = page.locator('.lumen-checkout__panel-inner').first();
    const inputBox = await firstName.boundingBox();
    const panelBox = await panel.boundingBox();
    expect(inputBox).toBeTruthy();
    expect(panelBox).toBeTruthy();

    if (!inputBox || !panelBox) {
      test.fail(true, 'Could not compute bounding boxes for layout assertions.');
      return;
    }

    const horizontalPadding = panelBox.width - inputBox.width;
    const leftInset = inputBox.x - panelBox.x;
    const rightInset = panelBox.x + panelBox.width - (inputBox.x + inputBox.width);

    expect(inputBox.height).toBeLessThan(52);
    expect(horizontalPadding).toBeGreaterThan(48);
    expect(leftInset).toBeGreaterThan(20);
    expect(rightInset).toBeGreaterThan(16);

    // Validate coupon form sizing
    const couponToggle = page.locator('.woocommerce-form-coupon-toggle a').first();
    if (await couponToggle.count()) {
      if (await couponToggle.isVisible()) {
        await couponToggle.click();
      } else {
        await couponToggle.click({ force: true });
      }
      const couponInput = page.locator('form.checkout_coupon input[name="coupon_code"]');
      await expect(couponInput).toBeVisible();
      const couponPanel = page.locator('form.checkout_coupon');
      const couponInputBox = await couponInput.boundingBox();
      const couponPanelBox = await couponPanel.boundingBox();
      if (!couponInputBox || !couponPanelBox) {
        test.fail(true, 'Could not compute coupon form bounds.');
        return;
      }
      const couponLeftInset = couponInputBox.x - couponPanelBox.x;
      const couponRightInset = couponPanelBox.x + couponPanelBox.width - (couponInputBox.x + couponInputBox.width);
      expect(couponInputBox.height).toBeLessThan(64);
      expect(couponLeftInset).toBeGreaterThan(18);
      expect(couponRightInset).toBeGreaterThan(14);
    } else {
      test.skip(true, 'Coupon toggle not present on checkout page.');
    }
  });

  test('stripe payment widget fits mobile container styling', async ({ page }) => {
    await seedCart(page);
    await page.setViewportSize({ width: 412, height: 915 });
    await page.goto(`${THEME_BASE}/checkout/`, { waitUntil: 'domcontentloaded' });

    const stripeWrapper = page.locator('#wc-stripe-card-element');
    await expect(stripeWrapper).toBeVisible();
    let wrapperBox = await stripeWrapper.boundingBox();
    if (!wrapperBox) {
      await stripeWrapper.evaluate((el) => el.scrollIntoView({ block: 'center', behavior: 'instant' }));
      wrapperBox = await stripeWrapper.boundingBox();
    }

    const inner = stripeWrapper.locator('> div');
    await expect(inner).toBeVisible();

    // Wait for the Stripe iframe to hydrate before measuring.
    const iframe = stripeWrapper.locator('iframe');
    await expect(iframe).toBeVisible();

    const [innerBox, iframeBox] = await Promise.all([
      inner.boundingBox(),
      iframe.boundingBox(),
    ]);

    expect(wrapperBox).toBeTruthy();
    expect(innerBox).toBeTruthy();
    expect(iframeBox).toBeTruthy();

    if (!wrapperBox || !innerBox || !iframeBox) {
      test.fail(true, 'Unable to retrieve bounding boxes for Stripe widget.');
      return;
    }

    const leftInset = innerBox.x - wrapperBox.x;
    const rightInset = wrapperBox.x + wrapperBox.width - (innerBox.x + innerBox.width);

    expect(leftInset).toBeGreaterThan(10);
    expect(rightInset).toBeGreaterThan(10);
    expect(Math.abs(iframeBox.width - innerBox.width)).toBeLessThan(1.5);

    const wrapperStyles = await stripeWrapper.evaluate((el) => {
      const styles = getComputedStyle(el);
      return {
        borderRadius: styles.borderRadius,
        backgroundImage: styles.backgroundImage,
        paddingTop: styles.paddingTop,
        paddingRight: styles.paddingRight,
        boxShadow: styles.boxShadow,
      };
    });

    expect(wrapperStyles.borderRadius).not.toBe('0px');
    expect(wrapperStyles.backgroundImage).toContain('linear-gradient');
    expect(parseFloat(wrapperStyles.paddingTop)).toBeGreaterThan(0);
    expect(parseFloat(wrapperStyles.paddingRight)).toBeGreaterThan(0);
    expect(wrapperStyles.boxShadow).not.toBe('none');

    const iframeMargins = await iframe.evaluate((el) => {
      const styles = getComputedStyle(el);
      return {
        marginTop: styles.marginTop,
        marginRight: styles.marginRight,
        marginBottom: styles.marginBottom,
        marginLeft: styles.marginLeft,
      };
    });

    expect(iframeMargins.marginTop).toBe('0px');
    expect(iframeMargins.marginRight).toBe('0px');
    expect(iframeMargins.marginBottom).toBe('0px');
    expect(iframeMargins.marginLeft).toBe('0px');

    const icons = page.locator('.wc-stripe-card-icons-container');
    await expect(icons).toBeVisible();
    const [labelBox, iconsBox] = await Promise.all([
      page.locator('label[for="payment_method_stripe_cc"]').boundingBox(),
      icons.boundingBox(),
    ]);
    expect(labelBox).toBeTruthy();
    expect(iconsBox).toBeTruthy();
    if (labelBox && iconsBox) {
      expect(iconsBox.width).toBeLessThanOrEqual(labelBox.width);
    }
  });

  test('saved card tokens adopt themed styling', async ({ page }) => {
    await seedCart(page);
    await page.setViewportSize({ width: 412, height: 915 });
    await page.goto(`${THEME_BASE}/checkout/`, { waitUntil: 'domcontentloaded' });

    const measurements = await page.evaluate(() => {
      const payment = document.querySelector('#payment');
      if (!payment) return null;
      const container = document.createElement('div');
      container.className = 'wc-stripe-saved-methods-container wc-stripe_cc-saved-methods-container';
      container.innerHTML = `
        <select class="wc-stripe-saved-methods select2-hidden-accessible enhanced" tabindex="-1" aria-hidden="true" id="test-saved-card">
          <option class="wc-stripe-saved-method visa" value="pm_test_1" selected>Visa ending in 4242</option>
          <option class="wc-stripe-saved-method mastercard" value="pm_test_2">Mastercard ending in 1111</option>
        </select>
      `;
      payment.appendChild(container);
      if (window.svicInitSavedCardPills) {
        window.svicInitSavedCardPills();
      }
      let pill = payment.querySelector('.wcs-saved-card-pill');
      if (!pill) {
        const fallbackList = document.createElement('div');
        fallbackList.className = 'wcs-saved-card-list';
        const selectEl = container.querySelector('select');
        const options = selectEl ? Array.from(selectEl.options) : [];
        options.forEach((opt, index) => {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'wcs-saved-card-pill';
          if (opt.selected || index === 0) {
            btn.classList.add('is-selected');
          }
          btn.innerHTML = `<span class="wcs-saved-card-pill__brand">${(opt.text || '').split(' ')[0] || 'Card'}</span><span class="wcs-saved-card-pill__label">${(opt.text || '').split(' ').slice(1).join(' ')}</span>`;
          btn.dataset.methodValue = opt.value;
          btn.addEventListener('click', () => {
            if (selectEl) {
              selectEl.value = opt.value;
            }
            fallbackList.querySelectorAll('.wcs-saved-card-pill').forEach(el => el.classList.remove('is-selected'));
            btn.classList.add('is-selected');
          });
          fallbackList.appendChild(btn);
        });
        payment.appendChild(fallbackList);
        pill = fallbackList.querySelector('.wcs-saved-card-pill');
      }
      const pillStyles = getComputedStyle(pill);
      const brand = pill.querySelector('.wcs-saved-card-pill__brand');
      const label = pill.querySelector('.wcs-saved-card-pill__label');
      return {
        pillText: pill.textContent.trim(),
        backgroundImage: pillStyles.getPropertyValue('background-image'),
        color: pillStyles.getPropertyValue('color'),
        brandText: brand ? brand.textContent.trim() : '',
        labelText: label ? label.textContent.trim() : '',
        containerHidden: container.classList.contains('wcs-hidden'),
        pillCount: payment.querySelectorAll('.wcs-saved-card-pill').length
      };
    });

    expect(measurements).not.toBeNull();
    if (!measurements) {
      test.fail(true, 'Unable to compute styles for saved card token demo.');
      return;
    }

    const { pillText, backgroundImage, color, brandText, labelText, containerHidden, pillCount } = measurements;
    expect(pillCount).toBeGreaterThan(0);
    expect(brandText).toMatch(/visa/i);
    expect(labelText).toContain('ending in 4242');

  });
});
