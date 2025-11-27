import { test, expect, type APIRequestContext } from '@playwright/test';

async function fetchAllPostPaths(baseURL: string | undefined, request: APIRequestContext): Promise<string[]> {
  const origin = baseURL ?? 'http://svicloud10p.svic.local';
  const paths: string[] = [];
  const perPage = 100;
  let pageIndex = 1;

  // Paginate through the WordPress posts REST endpoint.
  // Stops when we run out of pages or hit an error.
  // This intentionally assumes the total post count is modest (< 1,000).
  // ... existing code ...

  // eslint-disable-next-line no-constant-condition
  while (true) {
    const apiUrl = new URL(
      `/wp-json/wp/v2/posts?per_page=${perPage}&page=${pageIndex}&_fields=link`,
      origin,
    ).toString();

    const response = await request.get(apiUrl);

    if (response.status() === 400 || response.status() === 404) {
      break;
    }

    expect(response.ok(), `Failed to load posts page ${pageIndex}: ${response.status()} ${response.statusText()}`).toBe(
      true,
    );

    const json = await response.json();
    if (!Array.isArray(json) || json.length === 0) {
      break;
    }

    for (const post of json) {
      if (!post || typeof post.link !== 'string') continue;

      try {
        const url = new URL(post.link);
        const pathWithQuery = `${url.pathname}${url.search}`;
        paths.push(pathWithQuery);
      } catch {
        // Ignore malformed links.
      }
    }

    const totalPagesHeader = response.headers()['x-wp-totalpages'];
    const totalPages = totalPagesHeader ? Number(totalPagesHeader) : pageIndex;

    if (!Number.isFinite(totalPages) || pageIndex >= totalPages) {
      break;
    }

    pageIndex += 1;
  }

  // De-duplicate any paths just in case.
  return Array.from(new Set(paths));
}

test.describe('Blog posts have no broken images', () => {
  test('scan all posts for broken images', async ({ page, baseURL, request }) => {
    test.setTimeout(180_000);

    const postPaths = await fetchAllPostPaths(baseURL, request);
    expect(postPaths.length, 'Expected at least one blog post from REST API').toBeGreaterThan(0);

    const brokenByPost: { url: string; images: string[] }[] = [];
    const origin = baseURL ?? 'http://svicloud10p.svic.local';

    for (const path of postPaths) {
      const url = new URL(path, origin).toString();

      await test.step(`check images on ${url}`, async () => {
        await page.goto(url, { waitUntil: 'networkidle' });

        const brokenSources = await page.evaluate(() => {
          const bad: string[] = [];
          const imgs = Array.from(
            document.querySelectorAll<HTMLImageElement>(
              '.entry-content img, .blog-article__featured img, article img',
            ),
          );

          for (const img of imgs) {
            const src = img.currentSrc || img.src;
            if (!src) continue;

            // Only treat media uploads as content images. Theme icons and other
            // chrome assets (e.g. SVG sprites) are ignored even if they report
            // zero natural width.
            if (!src.includes('/wp-content/uploads/')) continue;

            if (!img.complete || img.naturalWidth === 0) {
              bad.push(src);
            }
          }

          return Array.from(new Set(bad));
        });

        if (brokenSources.length > 0) {
          console.log(`Broken images on ${url}:`);
          for (const src of brokenSources) {
            console.log(`  - ${src}`);
          }
          brokenByPost.push({ url, images: brokenSources });
        }
      });
    }

    if (brokenByPost.length > 0) {
      const summaryLines: string[] = ['Broken images detected:'];
      for (const entry of brokenByPost) {
        summaryLines.push(`Post: ${entry.url}`);
        for (const src of entry.images) {
          summaryLines.push(`  - ${src}`);
        }
      }

      expect(
        brokenByPost.length,
        summaryLines.join('\n'),
      ).toBe(0);
    }
  });
});
