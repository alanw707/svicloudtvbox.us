import { test, expect } from '@playwright/test';

type Target = {
  path: string;
  selector: string;
};

const targets: Target[] = [
  { path: '/', selector: '.frontpage-traffic__links .lumen-pill--primary' },
  { path: '/compare/', selector: '.compare-traffic__links .lumen-pill--primary' },
  { path: '/faq/', selector: '.faq-intent__links .lumen-pill--primary' },
  { path: '/contact/', selector: '.contact-intent__links .lumen-pill--primary' },
  { path: '/support/', selector: '.support-intent__links .lumen-pill--primary' },
  { path: '/product/svicloud-10p-plus/', selector: '.product-traffic__links .lumen-pill--primary' },
  { path: '/return-policy/', selector: '.policy-support__actions .policy-support__cta' },
  { path: '/guides/', selector: '.guides-support__actions .lumen-pill--primary' },
];

test.describe('Mobile action groups', () => {
  for (const target of targets) {
    test(`caps CTA width on mobile: ${target.path}`, async ({ page, baseURL }) => {
      const urlObj = new URL(target.path, baseURL);
      urlObj.searchParams.set('cb', Date.now().toString());

      const resp = await page.goto(urlObj.toString(), { waitUntil: 'domcontentloaded' });
      expect(resp?.ok()).toBeTruthy();

      const capPx = await page.evaluate(() => {
        const value = getComputedStyle(document.documentElement).getPropertyValue('--lumen-action-max-width-mobile').trim();
        const num = Number.parseFloat(value.replace('px', ''));
        return Number.isFinite(num) && num > 0 ? num : 340;
      });

      const locator = page.locator(target.selector).first();
      await expect(locator).toBeVisible();
      await locator.scrollIntoViewIfNeeded();

      const box = await locator.boundingBox();
      expect(box, `Missing bounding box for ${target.selector} on ${target.path}`).toBeTruthy();
      const width = box?.width ?? 0;

      expect(width).toBeGreaterThan(0);
      expect(width).toBeLessThanOrEqual(capPx + 2);
    });
  }
});

