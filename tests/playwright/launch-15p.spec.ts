import { test, expect, type Locator } from '@playwright/test';

function collectProductNodes(jsonLdBlocks: string[]): Array<Record<string, unknown>> {
  const products: Array<Record<string, unknown>> = [];

  for (const block of jsonLdBlocks) {
    const parsed = JSON.parse(block);
    const nodes = Array.isArray(parsed?.['@graph']) ? parsed['@graph'] : [parsed];

    for (const node of nodes) {
      if (node?.['@type'] === 'Product') products.push(node);
    }
  }

  return products;
}

async function expectLoadedImage(locator: Locator) {
  await expect(locator).toBeVisible();
  await locator.scrollIntoViewIfNeeded();
  await expect.poll(
    () => locator.evaluate((image: HTMLImageElement) => image.complete ? image.naturalWidth : 0),
    { timeout: 10_000 },
  ).toBeGreaterThan(0);
}

test.describe('SVICLOUD 15P launch safeguards', () => {
  test('homepage renders source-confirmed 15P backorder pricing without unsupported policies', async ({ page }) => {
    const response = await page.goto('/', { waitUntil: 'networkidle' });
    expect(response?.ok()).toBeTruthy();
    await expect(page).toHaveTitle(/SVICLOUD 15P/);

    const hero = page.locator('.hero-dashboard');
    const heroText = await hero.innerText();
    for (const claim of ['Android 14', 'Amlogic S905Y5', '4 GB DDR3', '64 GB eMMC', 'Wi-Fi 6', 'Bluetooth 5.4', '4K HDR']) {
      expect(heroText).toContain(claim);
    }
    for (const unverifiedPolicy of ['48-hour U.S. shipping', '1-year U.S. warranty', '14-day returns', 'Price TBC', '[POLICY TBC]', 'next-generation', 'newest confirmed', 'Amlogic S905Y5 power']) {
      expect(heroText).not.toContain(unverifiedPolicy);
    }
    await expectLoadedImage(page.locator('.hero-15p__image'));

    const pricingCard = page.locator('.lumen-pricing .shop-product-card--new');
    const pricingCardText = await pricingCard.innerText();
    expect(pricingCardText.toLowerCase()).toContain('available for pre-order');
    expect(pricingCardText.toLowerCase()).toContain('release window: 1 to 2 weeks');
    expect(pricingCardText).toContain('$288.00');
    expect(pricingCardText).toContain('$379.00');
    expect(pricingCardText).not.toContain('Coming Soon');
    expect(pricingCardText).not.toContain('warranty');
    expect(pricingCardText).not.toContain('Android 14 performance');
    await expect(pricingCard.locator('.shop-product-card__cta')).toHaveText('Pre-order 15P');
    await expectLoadedImage(pricingCard.locator('img'));
  });

  test('shop 15P card shows aligned backorder pricing and action', async ({ page }) => {
    const response = await page.goto('/shop/', { waitUntil: 'networkidle' });
    expect(response?.ok()).toBeTruthy();

    const card = page.locator('.shop-product-card--backorder');
    const cardText = await card.innerText();
    for (const claim of ['Available for pre-order', 'Release window: 1 to 2 weeks', '$288.00', '$379.00', 'Amlogic S905Y5', 'Android 14', '4 GB DDR3', '64 GB eMMC', 'Wi-Fi 6', 'Bluetooth 5.4', 'AV1']) {
      expect(cardText.toLowerCase()).toContain(claim.toLowerCase());
    }
    expect(cardText).not.toContain('Coming Soon');
    for (const unverifiedPolicy of ['Ships from Nevada warehouse', 'Includes 1-year U.S. warranty', 'Bilingual concierge onboarding', 'TBC', 'Buyers who want', 'next-generation connectivity']) {
      expect(cardText).not.toContain(unverifiedPolicy);
    }
    await expect(card.locator('.shop-product-card__cta')).toHaveText('Pre-order 15P');
    await expectLoadedImage(card.locator('img'));
    await expect(card.locator('a')).toHaveAttribute('href', /\/product\/svicloud-15p\/$/);

    const currentCard = page.locator('.shop-product-card--premium');
    const geometry = async (target: Locator) => target.evaluate((node) => {
      const rect = (selector: string) => {
        const box = node.querySelector(selector)?.getBoundingClientRect();
        return box ? { x: box.x, bottom: box.bottom, center: box.x + box.width / 2 } : null;
      };
      const box = node.getBoundingClientRect();
      return { x: box.x, center: box.x + box.width / 2, header: rect('.shop-product-card__header'), price: rect('.shop-product-card__price-line'), title: rect('.shop-product-card__title'), lead: rect('.shop-product-card__lead'), cta: rect('.shop-product-card__cta') };
    });
    const [backorderGeometry, currentGeometry] = await Promise.all([geometry(card), geometry(currentCard)]);
    for (const key of ['price', 'title', 'lead'] as const) {
      expect(Math.abs((backorderGeometry[key]?.x || 0) - (backorderGeometry.price?.x || 0))).toBeLessThanOrEqual(1);
      expect(Math.abs(((backorderGeometry[key]?.x || 0) - backorderGeometry.x) - ((currentGeometry[key]?.x || 0) - currentGeometry.x))).toBeLessThanOrEqual(1);
    }
    expect(Math.abs((backorderGeometry.cta?.center || 0) - backorderGeometry.center)).toBeLessThanOrEqual(1);
    if ((page.viewportSize()?.width || 0) >= 960) {
      expect(Math.abs((backorderGeometry.header?.bottom || 0) - (currentGeometry.header?.bottom || 0))).toBeLessThanOrEqual(1);
    }
  });

  test('15P gallery keeps every selected image fully visible', async ({ page }) => {
    await page.goto('/product/svicloud-15p/', { waitUntil: 'networkidle' });
    const stage = page.locator('.product-hero-stage');
    const image = page.locator('.product-hero-image');
    for (let index = 0; index < await page.locator('.product-thumb').count(); index += 1) {
      await page.locator('.product-thumb').nth(index).click();
      await expectLoadedImage(image);
      const geometry = await image.evaluate((node) => {
        const imageRect = node.getBoundingClientRect();
        const stageRect = node.parentElement?.getBoundingClientRect();
        const style = getComputedStyle(node);
        return {
          objectFit: style.objectFit,
          leftGap: imageRect.left - (stageRect?.left || 0),
          topGap: imageRect.top - (stageRect?.top || 0),
          rightGap: imageRect.right - (stageRect?.right || 0),
          bottomGap: imageRect.bottom - (stageRect?.bottom || 0),
        };
      });
      expect(geometry.objectFit).toBe('contain');
      expect(geometry.leftGap).toBeGreaterThanOrEqual(0);
      expect(geometry.topGap).toBeGreaterThanOrEqual(0);
      expect(geometry.rightGap).toBeLessThanOrEqual(0);
      expect(geometry.bottomGap).toBeLessThanOrEqual(0);
    }
  });

  test('15P exposes one PreOrder Offer and supports notified backorders', async ({ page }) => {
    const response = await page.goto('/product/svicloud-15p/', { waitUntil: 'domcontentloaded' });
    expect(response?.ok()).toBeTruthy();

    const productNodes = collectProductNodes(await page.locator('script[type="application/ld+json"]').allTextContents());
    const productIds = productNodes.map((node) => node['@id']);
    expect(productNodes).toHaveLength(1);
    expect(new Set(productIds).size).toBe(productIds.length);

    const offer = productNodes[0].offers as Record<string, unknown>;
    expect(offer).toMatchObject({
      '@type': 'Offer',
      priceCurrency: 'USD',
      price: '288.00',
      availability: 'https://schema.org/PreOrder',
      availabilityStarts: '2026-09-06',
    });
    expect((offer.shippingDetails as Record<string, unknown> | undefined)?.deliveryTime).toBeUndefined();

    await expectLoadedImage(page.locator('.product-hero-image'));
    await expect(page.locator('.product-hero-price')).toContainText('$288.00');
    await expect(page.locator('.product-hero-price')).toContainText('$379.00');
    await expect(page.locator('.stock.available-on-backorder')).toContainText('Available for pre-order');
    const button = page.locator('.single_add_to_cart_button');
    await expect(button).toHaveText('Pre-order 15P');

    await expect(page.locator('.svic-15p-delivery-banner')).toBeVisible();
    await expect(page.locator('.svic-15p-delivery-banner')).toContainText('Pre-order delivery');
    await expect(page.locator('.svic-15p-delivery-banner')).toContainText('Release window: 1 to 2 weeks');

    const body = await page.locator('body').innerText();
    for (const claim of ['Android 14', 'Amlogic S905Y5', '4 GB DDR3', '64 GB eMMC', 'Wi-Fi 6', 'Bluetooth 5.4', 'HDR10+', 'AV1', 'Release window: 1 to 2 weeks']) {
      expect(body).toContain(claim);
    }
    expect(body).not.toMatch(/price (and release date )?(has|have) not been announced/i);
    for (const policy of ['48-Hour U.S. Shipping', '1-Year U.S. Warranty', 'Every SVICLOUD device is genuine with U.S. warranty support', 'Shipping speed, warranty, and setup help']) {
      expect(body).not.toContain(policy);
    }

    await button.click();
    await expect(page.locator('.woocommerce-message')).toContainText(/added to (your )?cart/i);
    await page.goto('/cart/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('.svic-15p-delivery-banner')).toContainText('Pre-order delivery');
    await expect(page.locator('.svic-15p-delivery-banner')).toContainText('Release window: 1 to 2 weeks');
    let commerceText = await page.locator('body').innerText();
    expect(commerceText).not.toMatch(/48-hour U\.S\. shipping|1-year U\.S\. warranty|ships from our Nevada warehouse/i);
    await page.goto('/checkout/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('.svic-15p-delivery-banner')).toContainText('Pre-order delivery');
    await expect(page.locator('.svic-15p-delivery-banner')).toContainText('Release window: 1 to 2 weeks');
    commerceText = await page.locator('body').innerText();
    expect(commerceText).not.toMatch(/48-hour U\.S\. shipping|1-year U\.S\. warranty|ships from Nevada within 48 hours/i);
  });

  test('localizes launch metadata and shows Android 12 for 10P+ and 10S in every locale', async ({ page }) => {
    const locales = [
      { prefix: '', marker: 'SVICLOUD', included: 'Included', action: 'Pre-order 15P', availability: 'Available for pre-order' },
      { prefix: '/zh', marker: '小雲', included: '內含', action: '預購 15P', availability: '接受預購' },
      { prefix: '/zh-cn', marker: '小云', included: '内含', action: '预订 15P', availability: '接受预订' },
    ];
    for (const locale of locales) {
      for (const route of ['/', '/shop/', '/compare/', '/product/svicloud-15p/']) {
        await page.goto(`${locale.prefix}${route}`, { waitUntil: 'domcontentloaded' });
        const metadata = [
          await page.title(),
          await page.locator('meta[name="description"]').first().getAttribute('content') || '',
          await page.locator('meta[property="og:title"]').first().getAttribute('content') || '',
          await page.locator('meta[property="og:description"]').first().getAttribute('content') || '',
        ];
        for (const value of metadata) {
          expect(value).toContain('15P');
          expect(value).toContain(locale.marker);
        }
        expect(metadata.join(' ')).toContain('288');
        const routeText = await page.locator('body').innerText();
        expect(routeText.toLocaleLowerCase()).toContain(locale.action.toLocaleLowerCase());
        expect(routeText.toLocaleLowerCase()).toContain(locale.availability.toLocaleLowerCase());
        expect(routeText).toMatch(/288/);
        expect(routeText).toMatch(/379/);
        if (route === '/product/svicloud-15p/') {
          await expect(page.locator('.single_add_to_cart_button')).toHaveText(locale.action);
          await expect(page.locator('.stock.available-on-backorder')).toContainText(locale.availability);
        }
        if (route === '/compare/') {
          expect((await page.locator('body').innerText())).not.toContain('current wireless');
          expect(await page.locator('.compare-product-card dd').filter({ hasText: /^Android 12$/ }).count()).toBe(2);
          for (const cardIndex of [0, 1]) {
            for (const rowIndex of [6, 7]) {
              await expect(page.locator('.compare-product-card').nth(cardIndex).locator('.compare-product-card__comparison-item').nth(rowIndex).locator('dd')).toHaveText(locale.included);
            }
          }
          expect(metadata[0]).toContain('10P+');
          expect(metadata[0]).toContain('10S');
        }
      }
      for (const slug of ['svicloud-10p-plus', 'svicloud-10s']) {
        await page.goto(`${locale.prefix}/product/${slug}/`, { waitUntil: 'domcontentloaded' });
        await expect(page.locator('.product-description').first()).toContainText('Android 12');
      }
    }
  });

  test('current 10P+ product remains live and links to the 15P backorder page', async ({ page }) => {
    const consoleErrors: string[] = [];
    page.on('console', (message) => {
      if (message.type() === 'error') consoleErrors.push(message.text());
    });

    const response = await page.goto('/product/svicloud-10p-plus/', { waitUntil: 'networkidle' });
    expect(response?.ok()).toBeTruthy();
    await expect(page.locator('.product-hero-title')).toContainText('10P+');
    await expect(page.locator('.product-description').first()).toContainText('Android 12');
    await expect(page.locator('.pdp-crosslink a')).toHaveAttribute('href', /\/product\/svicloud-15p\/$/);
    expect(consoleErrors).toEqual([]);
  });
});
