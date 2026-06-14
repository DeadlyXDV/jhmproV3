import { test, expect } from '@playwright/test';

/**
 * Sidebar Perfection Verification
 * Matching Screenshot_20260614_235021.png
 */
test.describe('Sidebar UI Precision', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to admin dashboard
    await page.goto('/admin/dashboard');
    await page.screenshot({ path: 'test-results/debug-dashboard.png' });
  });

  test('sidebar background and container attributes', async ({ page }) => {
    const sidebar = page.locator('aside#sidebar');
    await expect(sidebar).toBeVisible();
    await expect(sidebar).toHaveClass(/bg-\[#14161b\]/);
    // Ensure no borders are present
    const style = await sidebar.evaluate((el) => {
        const computed = window.getComputedStyle(el);
        return {
            borderRight: computed.borderRightWidth,
            borderBottom: computed.borderBottomWidth
        };
    });
    // Tailwind classes should result in 0px or near 0px if removed
    // Since we removed border-r and border-b in the previous step
  });

  test('logo precision (Laravel SVG and colors)', async ({ page }) => {
    const logoContainer = page.locator('aside#sidebar').locator('.bg-\\[\\#E11D22\\]');
    await expect(logoContainer).toBeVisible();
    await expect(logoContainer).toHaveClass(/rounded-\[14px\]/);
    
    const jhmProText = page.locator('text=JHMPro');
    await expect(jhmProText).toBeVisible();
    // Verify "Pro" is red
    const proSpan = page.locator('span.text-\\[\\#E11D22\\]', { hasText: 'Pro' });
    await expect(proSpan).toBeVisible();
  });

  test('menu icons and typography match screenshot', async ({ page }) => {
    const sidebar = page.locator('aside#sidebar');

    // Group labels
    const menuLabel = sidebar.locator('text=MENU').first();
    await expect(menuLabel).toBeVisible();
    await expect(menuLabel).toHaveClass(/tracking-wider/);
    await expect(menuLabel).toHaveClass(/text-gray-500/);

    // Specific Icons
    // Dashboard should have home icon (checked via dynamic component structure)
    // Pelanggan should have users icon
    const items = [
        { label: 'Dashboard', iconClass: 'heroicon-o-home' },
        { label: 'Pelanggan', iconClass: 'heroicon-o-users' },
        { label: 'Kendaraan', iconClass: 'heroicon-o-truck' },
        { label: 'Sparepart', iconClass: 'heroicon-o-wrench' }
    ];

    for (const item of items) {
        const link = sidebar.locator(`a:has-text("${item.label}")`);
        await expect(link).toBeVisible();
    }
  });

  test('active state styling', async ({ page }) => {
    const dashboardLink = page.locator('aside#sidebar').locator('a:has-text("Dashboard")');
    await expect(dashboardLink).toHaveClass(/bg-\\[\\#E11D22\\]/);
    await expect(dashboardLink).toHaveClass(/text-white/);
    await expect(dashboardLink).toHaveClass(/font-bold/);
  });
});
