/* Local rendered-template UX checks. Server handler is tested separately in PHP. */
const { chromium } = require('playwright');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');
const root = path.resolve(__dirname, '..');
const artifacts = path.join(root, 'artifacts/support-form');
const base = process.env.SUPPORT_FIXTURE_URL || 'http://127.0.0.1:8774';
assert(['127.0.0.1', 'localhost'].includes(new URL(base).hostname), 'Local fixture only; never submit to production');
fs.mkdirSync(artifacts, { recursive: true });
for (const locale of ['en_US', 'zh_TW', 'zh_CN']) {
    fs.writeFileSync(path.join(artifacts, locale + '.html'), execFileSync('php', ['tests/fixtures/assert-support-form.php', '--render', locale], { cwd: root }));
}
(async () => {
    const browser = await chromium.launch({ headless: true, ...(process.env.CHROME_PATH ? { executablePath: process.env.CHROME_PATH } : {}) });
    let cases = 0;
    try {
        for (const locale of ['en_US', 'zh_TW', 'zh_CN']) {
            for (const width of [390, 1280]) {
                const page = await browser.newPage({ viewport: { width, height: 900 } });
                const errors = [], submissions = [];
                page.on('pageerror', error => errors.push(error.message));
                await page.route('**/*', route => {
                    const req = route.request();
                    if (req.method() === 'POST') {
                        submissions.push(new URLSearchParams(req.postData()));
                        return route.fulfill({ status: 204 });
                    }
                    return new URL(req.url()).origin === base ? route.continue() : route.abort();
                });
                await page.goto(base + '/artifacts/support-form/' + locale + '.html');
                assert.equal(await page.locator('#support-order').evaluate(e => e.required), true);
                assert.equal(await page.locator('#support-device option[value="15p"]').count(), 1);
                assert.equal(await page.locator('#support-issue option[value="shipping"]').isDisabled(), true);
                assert.equal(await page.locator('#support-website').getAttribute('tabindex'), '-1');
                await page.locator('#support-name').fill('Fixture buyer');
                await page.locator('#support-email').fill('buyer@example.test');
                await page.locator('#support-message').fill('A fixture support question.');
                await page.locator('#support-issue').selectOption('apps');
                await page.locator('[type="submit"]').click();
                assert.equal(submissions.length, 0, 'missing order stops browser submission');
                assert.equal(await page.locator('#support-order').evaluate(e => e.validity.valueMissing), true);
                await page.locator('#support-kind').selectOption('presales');
                assert.equal(await page.locator('#support-order').isVisible(), false);
                assert.equal(await page.locator('#support-order').isDisabled(), true);
                assert.equal(await page.locator('#support-issue').inputValue(), '');
                assert.equal(await page.locator('#support-issue option[value="apps"]').isDisabled(), true);
                await page.locator('#support-issue').selectOption('shipping');
                await page.locator('[type="submit"]').click();
                await page.waitForRequest(req => req.method() === 'POST', { timeout: 1000 }).catch(() => {});
                assert.equal(submissions.length, 1);
                assert.equal(submissions[0].get('support_kind'), 'presales');
                assert.equal(submissions[0].has('support_order'), false);
                await page.locator('#support-kind').selectOption('order-help');
                assert.equal(await page.locator('[data-support-help="order-help"]').isVisible(), true);
                assert.equal(await page.locator('[type="submit"]').isVisible(), false);
                assert.equal(await page.locator('#support-message').isDisabled(), true);
                await page.locator('#support-kind').selectOption('other-seller');
                assert.equal(await page.locator('[data-support-help="other-seller"]').isVisible(), true);
                assert.equal(await page.locator('[type="submit"]').isVisible(), false);
                await page.locator('#support-kind').selectOption('aftersales');
                await page.locator('#support-order').fill('42');
                await page.locator('#support-issue').selectOption('apps');
                assert.equal(await page.locator('[type="submit"]').isDisabled(), false);
                await page.locator('#support-form').evaluate(e => window.scrollTo(0, e.getBoundingClientRect().top + window.scrollY - 16));
                const overflow = await page.evaluate(() => document.documentElement.scrollWidth > innerWidth + 1);
                assert.equal(overflow, false, 'no horizontal overflow');
                await page.screenshot({ path: path.join(artifacts, locale + '-' + width + '.png') });
                await page.locator('[type="submit"]').click();
                await page.waitForRequest(req => req.method() === 'POST', { timeout: 1000 }).catch(() => {});
                assert.equal(submissions.length, 2);
                assert.equal(submissions[1].get('support_order'), '42');
                assert.equal(submissions[1].get('support_kind'), 'aftersales');
                assert.deepEqual(errors, []);
                console.log('PASS', locale, width, 'routing, validation, presales/aftersales serialization, no overflow/JS errors');
                ++cases;
                await page.close();
            }
            const page = await browser.newPage({ javaScriptEnabled: false });
            await page.goto(base + '/artifacts/support-form/' + locale + '.html');
            assert.equal(await page.locator('[data-support-help="order-help"]').isVisible(), true);
            assert.equal(await page.locator('#support-kind').isVisible(), true);
            assert.equal(await page.locator('[type="submit"]').isVisible(), true);
            console.log('PASS', locale, 'no-JS help and native form available; server enforces verification');
            ++cases;
            await page.close();
        }
        console.log('TOTAL', cases, 'browser cases passed; all submissions intercepted locally.');
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exit(1); });
