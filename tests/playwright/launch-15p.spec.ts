import { test, expect } from '@playwright/test';

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

test.describe('SVICLOUD 15P launch safeguards', () => {
  test('homepage renders the 15P SEO title', async ({ page }) => {
    const response = await page.goto('/', { waitUntil: 'domcontentloaded' });
    expect(response?.ok()).toBeTruthy();
    await expect(page).toHaveTitle(/SVICLOUD 15P/);
  });

  test('15P emits one Product schema node and labels unconfirmed claims', async ({ page }) => {
    const response = await page.goto('/product/svicloud-15p/', { waitUntil: 'domcontentloaded' });
    expect(response?.ok()).toBeTruthy();

    const productNodes = collectProductNodes(await page.locator('script[type="application/ld+json"]').allTextContents());
    const productIds = productNodes.map((node) => node['@id']);
    expect(productNodes).toHaveLength(1);
    expect(new Set(productIds).size).toBe(productIds.length);
    expect(productNodes[0]).not.toHaveProperty('offers');
    await expect(page.locator('.single_add_to_cart_button')).toHaveCount(0);

    const body = await page.locator('body').innerText();
    expect(body).toContain('[SPEC TBC]');
    expect(body).toContain('No 15P performance advantage over 9P can be stated');
    expect(body).not.toContain('generational leap in speed');
    expect(body).not.toContain('much faster processor than the 9P');
    expect(body).not.toContain('Faster boot, faster app switching');
  });

  test('legacy 9P page remains live and funnels owners to 15P', async ({ page }) => {
    const consoleErrors: string[] = [];
    page.on('console', (message) => {
      if (message.type() === 'error') consoleErrors.push(message.text());
    });

    const response = await page.goto('/product/svicloud-9p/', { waitUntil: 'networkidle' });
    expect(response?.ok()).toBeTruthy();
    await expect(page.locator('.product-hero-title')).toContainText('9P');
    await expect(page.locator('.pdp-crosslink a')).toHaveAttribute('href', /\/product\/svicloud-15p\/$/);
    expect(consoleErrors).toEqual([]);
  });
});
