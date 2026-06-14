import { test, expect } from '@playwright/test';

test('check for layout shift on load', async ({ page }) => {
  // Record layout shifts
  let cumulativeLayoutShift = 0;
  await page.exposeFunction('onLayoutShift', (shift) => {
    cumulativeLayoutShift += shift;
  });

  await page.addInitScript(() => {
    new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        if (!entry.hadRecentInput) {
          window.onLayoutShift(entry.value);
        }
      }
    }).observe({type: 'layout-shift', buffered: true});
  });

  // Navigate and wait for idle
  await page.goto('/admin/dashboard', { waitUntil: 'networkidle' });
  
  console.log(`Cumulative Layout Shift: ${cumulativeLayoutShift}`);
  
  // A CLS under 0.1 is considered good.
  expect(cumulativeLayoutShift).toBeLessThan(0.1);
});

test('capture trace for visual analysis', async ({ page, context }) => {
  await page.context().tracing.start({ screenshots: true, snapshots: true });
  await page.goto('/admin/dashboard');
  await page.context().tracing.stop({ path: 'test-results/load-trace.zip' });
});
