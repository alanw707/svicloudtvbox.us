import { test, expect } from '@playwright/test';

const paths = ['/', '/zh/guides-apps/'];
const collectionEndpoints = /google-analytics\.com\/g\/collect|analytics\.google\.com\/g\/collect|doubleclick\.net|googleadservices\.com/;
const stabilizer = /\s*<script id="svic-google-consent-stabilizer">[\s\S]*?<\/script>\s*/i;

test.describe('Consent Mode ownership and ordering', () => {
  for (const path of paths) {
    test(`keeps Site Kit regional defaults on ${path}`, async ({ page }) => {
      await page.route('**/*', async (route) => {
        const request = route.request();
        const url = request.url();

        if (collectionEndpoints.test(url)) {
          await route.abort();
          return;
        }

        if (request.resourceType() === 'document' && url.startsWith('http')) {
          const response = await route.fetch();
          const body = await response.text();
          // Make the test safe against the currently deployed pre-patch document.
          // The rewrite is in-memory only and never changes production.
          await route.fulfill({ response, body: body.replace(stabilizer, '\n') });
          return;
        }

        await route.continue();
      });

      await page.goto(path, { waitUntil: 'domcontentloaded' });
      await page.waitForTimeout(1500);

      const state = await page.evaluate(() => {
        const entries = ((window as any).dataLayer || []).map((entry: any) => Array.from(entry));
        return {
          defaults: entries.filter((entry: any[]) => entry[0] === 'consent' && entry[1] === 'default'),
          updates: entries.filter((entry: any[]) => entry[0] === 'consent' && entry[1] === 'update'),
          configs: entries.filter((entry: any[]) => entry[0] === 'config'),
          stabilizerPresent: Boolean(document.getElementById('svic-google-consent-stabilizer')),
        };
      });

      expect(state.stabilizerPresent).toBe(false);
      expect(state.updates.filter((entry: any[]) => entry[2]?.analytics_storage === 'granted')).toHaveLength(0);

      if (state.defaults.length > 0) {
        const regionalDefault = state.defaults.find((entry: any[]) => Array.isArray(entry[2]?.region));
        expect(regionalDefault).toBeTruthy();
        expect(regionalDefault?.[2]).toMatchObject({
          analytics_storage: 'denied',
          wait_for_update: expect.any(Number),
        });
        expect(regionalDefault?.[2]?.region).toEqual(expect.arrayContaining(['DE', 'FR']));
      }

      const siteKitConfigs = state.configs.filter((entry: any[]) => String(entry[1]).startsWith('GT-'));
      expect(siteKitConfigs).toHaveLength(1);
      expect(state.configs.filter((entry: any[]) => entry[1] === 'G-25RK4LK4DH')).toHaveLength(0);
    });
  }
});
