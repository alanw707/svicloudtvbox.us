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
