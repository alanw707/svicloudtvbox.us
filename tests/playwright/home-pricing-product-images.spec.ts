import { test, expect } from '@playwright/test';
import type { Page } from '@playwright/test';

async function productCardSources(page: Page) {
  return page.locator('.shop-product-card').evaluateAll((cards) => {
    const sources: Record<string, string> = {};

    for (const card of cards) {
      const explicitKey = card.getAttribute('data-svic-card');
      const title = card.querySelector('.shop-product-card__title')?.textContent?.trim() || '';
      const key = explicitKey
        || (title.includes('10P+') ? '10p' : '')
        || (title.includes('10S') ? '10s' : '')
        || (title.includes('15P') ? '15p' : '');
      const image = card.querySelector('.shop-product-card__media img') as HTMLImageElement | null;

      if (key && image) {
        sources[key] = image.currentSrc || image.src;
      }
    }

    return sources;
  });
}

test.describe('Homepage pricing product images', () => {
  test('10P+ and 10S cards match the compare page product images', async ({ page, baseURL }) => {
    const url = new URL('/', baseURL);
    url.searchParams.set('cb', Date.now().toString());

    const response = await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
    expect(response?.ok()).toBeTruthy();

    const homeSources = await productCardSources(page);

    const compareUrl = new URL('/compare/', baseURL);
    compareUrl.searchParams.set('cb', Date.now().toString());
    const compareResponse = await page.goto(compareUrl.toString(), { waitUntil: 'domcontentloaded' });
    expect(compareResponse?.ok()).toBeTruthy();

    const compareSources = await productCardSources(page);

    expect(homeSources['10p']).toBe(compareSources['10p']);
    expect(homeSources['10s']).toBe(compareSources['10s']);
    expect(homeSources['10p']).toContain('/wp-content/uploads/');
    expect(homeSources['10s']).toContain('/wp-content/uploads/');
    expect(homeSources['10p']).not.toContain('/assets/images/svicloud-hero-product.webp');
    expect(homeSources['10s']).not.toContain('/assets/images/svicloud-hero-product.webp');
  });
});
