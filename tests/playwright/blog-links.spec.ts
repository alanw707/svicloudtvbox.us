import { test, expect } from '@playwright/test';

const pages = [
  {
    path: '/svicloud-10p-plus-vs-unblocktech-ubox-12/',
    sections: [
      'svicloud-10p-plus-vs-unblocktech-ubox-12-side-by-side-specs',
      'key-advantages-of-svicloud-10p-plus',
      'where-ubox-12-still-shines',
      'svicloud-10p-plus-vs-unblocktech-ubox-12-setup-experience',
      'warranty-compliance-and-support',
      'which-device-to-choose',
      'faqs',
      'next-steps',
    ],
    internalLinks: [
      '/compare/',
      '/product/svicloud-10p-plus/',
      '/product/svicloud-10s/',
      '/guides-setup/',
      '/svicloud-10p-plus-vs-unblocktech-ubox-12/',
    ],
  },
  {
    path: '/svicloud-10p-plus-vs-evpad-10-pro/',
    sections: [],
    internalLinks: [
      '/compare/',
      '/product/svicloud-10p-plus/',
      '/guides-setup/',
      '/svicloud-10p-plus-vs-evpad-10-pro/',
    ],
  },
];

test.describe('SVICLOUD blog post internal links', () => {
  for (const pageConfig of pages) {
    test(`${pageConfig.path} anchors scroll into view`, async ({ page, baseURL }) => {
      await page.goto(new URL(pageConfig.path, baseURL).toString(), { waitUntil: 'domcontentloaded' });
      await expect(page.locator('article, section.blog-section').first()).toBeVisible();

      if (!pageConfig.sections.length) test.skip();

      for (const id of pageConfig.sections) {
        await page.evaluate((targetId) => {
          location.hash = targetId;
        }, id);
        await page.waitForTimeout(300);

        const anchor = page.locator(`#${id}`);
        await expect(anchor).toBeVisible();

        const box = await anchor.boundingBox();
        expect(box, `Anchor ${id} should have bounding box`).not.toBeNull();
      }
    });

    test(`${pageConfig.path} internal links reach live pages`, async ({ page, baseURL }) => {
      await page.goto(new URL(pageConfig.path, baseURL).toString(), { waitUntil: 'domcontentloaded' });

      for (const path of pageConfig.internalLinks) {
        const url = new URL(path, baseURL).toString();
        const response = await page.goto(url, { waitUntil: 'domcontentloaded' });
        expect(response?.ok(), `GET ${url} should succeed`).toBeTruthy();
        await expect(page.locator('header.lumen-header')).toBeVisible();
      }
    });
  }
});
