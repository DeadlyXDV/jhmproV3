import { test, expect } from '@playwright/test';

test('has title', async ({ page }) => {
  await page.goto('/');

  // Expect a title "to contain" a substring.
  await expect(page).toHaveTitle(/JHMPro/);
});

test('dashboard link', async ({ page }) => {
  await page.goto('/');

  // Click the get started link.
  await page.getByRole('link', { name: 'Dashboard' }).click();

  // Expects page to have a heading with the name of Dashboard.
  await expect(page.getByRole('heading', { name: 'Dashboard' })).toBeVisible();
});
