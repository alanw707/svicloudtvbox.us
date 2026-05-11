import { test, expect } from '@playwright/test';

test.describe('Google reviews integrations', () => {
  test('renders Google Customer Reviews badge loader on public pages', async ({ page, baseURL }) => {
    const url = new URL('/', baseURL);
    await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });

    await expect(page.locator('#svic-google-customer-reviews-badge')).toHaveCount(1);

    const googleReviewsMarkup = await page.evaluate(() => document.documentElement.innerHTML);
    expect(googleReviewsMarkup).toContain('svicGooglePlatformReady');
    expect(googleReviewsMarkup).toContain('ratingbadge');
    expect(googleReviewsMarkup).toContain('5317978135');
  });

  test('keeps Google Business Profile review CTA hidden until review URL is configured', async ({ page, baseURL }) => {
    const url = new URL('/support/', baseURL);
    await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });

    const cta = page.locator('[data-svic-location="support_review"][data-svic-label="google_business_review"]');
    const expectedEnabled = process.env.PLAYWRIGHT_EXPECT_GOOGLE_BUSINESS_REVIEW_CTA === '1';

    if (expectedEnabled) {
      await expect(cta).toBeVisible();
      await expect(cta).toHaveAttribute('href', /google\.com\/local\/writereview|g\.page|google\.com\/maps/i);
      return;
    }

    await expect(cta).toHaveCount(0);
  });

  test('shows mobile sticky buy CTA at top only after the hero', async ({ page, baseURL }) => {
    await page.setViewportSize({ width: 360, height: 780 });
    const url = new URL('/', baseURL);
    url.searchParams.set('_pwcachebust', Date.now().toString());
    await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });

    for (const scrollY of [0, 900]) {
      await page.evaluate((value) => window.scrollTo(0, value), scrollY);
      await page.waitForTimeout(150);
      await expect(page.locator('.lumen-sticky-buy')).not.toHaveClass(/is-visible/);
      await expect(page.locator('.lumen-sticky-buy')).toBeHidden();
      await expect(page.locator('body')).not.toHaveClass(/has-lumen-sticky-buy/);
    }

    await page.evaluate(() => {
      const hero = document.getElementById('hero');
      const threshold = hero ? hero.offsetTop + hero.offsetHeight + 24 : 3200;
      window.scrollTo(0, threshold);
    });
    await page.waitForTimeout(150);

    await expect(page.locator('.lumen-sticky-buy')).toHaveClass(/is-visible/);
    await expect(page.locator('body')).toHaveClass(/has-lumen-sticky-buy/);

    const metrics = await page.evaluate(() => {
      const rectFor = (selector: string) => {
        const element = document.querySelector(selector);
        if (!element) {
          return null;
        }
        const rect = element.getBoundingClientRect();
        return {
          top: rect.top,
          right: rect.right,
          bottom: rect.bottom,
          left: rect.left,
          width: rect.width,
          height: rect.height,
        };
      };

      return {
        viewport: {
          width: Math.max(window.innerWidth, window.visualViewport ? window.visualViewport.width : 0),
          height: Math.max(window.innerHeight, window.visualViewport ? window.visualViewport.height : 0, document.documentElement.clientHeight),
        },
        header: rectFor('[data-lumen-header]'),
        sticky: rectFor('.lumen-sticky-buy'),
        google: rectFor('#svic-google-customer-reviews-badge'),
        chat: rectFor('.svic-support-chat'),
      };
    });

    expect(metrics.header).not.toBeNull();
    expect(metrics.sticky).not.toBeNull();
    expect(metrics.sticky!.top).toBeGreaterThanOrEqual(metrics.header!.bottom + 6);
    expect(metrics.sticky!.left).toBeGreaterThanOrEqual(8);
    expect(metrics.sticky!.right).toBeLessThanOrEqual(metrics.viewport.width - 8);
    expect(metrics.sticky!.bottom).toBeLessThanOrEqual(metrics.viewport.height / 2);

    if (metrics.google && metrics.google.width > 1 && metrics.google.height > 1) {
      expect(metrics.google.top).toBeGreaterThan(metrics.sticky!.bottom + 16);
    }

    if (metrics.chat && metrics.chat.width > 1 && metrics.chat.height > 1) {
      expect(metrics.chat.top).toBeGreaterThan(metrics.sticky!.bottom + 16);
    }

    await page.locator('[data-lumen-toggle]').click();
    await expect(page.locator('body')).toHaveClass(/lumen-nav-open/);
    await expect(page.locator('.lumen-sticky-buy')).toBeHidden();
  });

  test('shows store-level Google rating in homepage hero, not shop cards', async ({ page, baseURL }) => {
    await page.goto(new URL('/', baseURL).toString(), { waitUntil: 'domcontentloaded' });
    await expect(page.locator('.hero-dashboard__store-rating')).toBeVisible();
    await expect(page.locator('.hero-dashboard__store-rating')).toContainText(/5\.0/);

    await page.goto(new URL('/shop/', baseURL).toString(), { waitUntil: 'domcontentloaded' });
    await expect(page.locator('.shop-product-card__rating-summary, .shop-product-card__review, .shop-product-card__reviews')).toHaveCount(0);
  });

  test('shows Google average rating on product page without review form or comments', async ({ page, baseURL }) => {
    const url = new URL('/product/svicloud-10p-plus/', baseURL);
    await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });

    await expect(page.locator('#store-reviews')).toBeVisible();
    await expect(page.locator('.product-reviews__average-card')).toContainText(/5\.0/);
    await expect(page.locator('.product-reviews__quote')).toHaveCount(0);
    await expect(page.locator('#commentform, #review_form')).toHaveCount(0);
  });
});
