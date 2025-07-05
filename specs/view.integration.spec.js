/**
 * Integration test for the frontend view.js functionality
 * Tests that blocks are properly rendered on the frontend
 */

import { test, expect } from '@playwright/test';

test.describe('Frontend Open Graph Card Rendering', () => {
    let editorFrame;

    test.beforeEach(async ({ page }) => {
        // Navigate to the WordPress admin
        await page.goto('http://localhost:8889/wp-admin');

        // Wait for login form to be ready
        await page.waitForSelector('#user_login', { timeout: 10000 });

        // Clear any existing values and login
        await page.fill('#user_login', 'admin');
        await page.fill('#user_pass', 'password');
        await page.click('#wp-submit');

        // Wait for login to complete and check if we're actually logged in
        await page.waitForURL('http://localhost:8889/wp-admin/', { timeout: 15000 });

        // Additional check to ensure we're logged in (look for admin bar or dashboard)
        await page.waitForSelector('#wpadminbar, .wp-admin', { timeout: 10000 });

        // Go to add new post
        await page.goto('http://localhost:8889/wp-admin/post-new.php');

        // Wait for the editor to be ready
        await page.waitForSelector('.block-editor-page', { timeout: 15000 });
        await page.click('.block-editor-page'); // Focus editor
        await page.keyboard.press('/'); // Open inline inserter
        await page.keyboard.press('o'); // Type 'o' to start searching for Open Graph Card

        // Wait for and click the block using the correct selector
        await page.waitForSelector('button[id="components-autocomplete-item-0-block-wp-open-graph-card/og-card"]', { timeout: 5000 });
        await page.click('button[id="components-autocomplete-item-0-block-wp-open-graph-card/og-card"]');

        // Wait for the block to be added and find the editor iframe
        await page.waitForTimeout(2000);

        // Find the frame with the blob: URL (editor iframe)
        const frames = page.frames();
        editorFrame = frames.find(f => f.url().startsWith('blob:'));
        if (!editorFrame) throw new Error('Editor iframe not found');
    });

    test('should render Open Graph Card blocks on the frontend', async ({ page }) => {
        // Fill in test data in the editor frame
        const urlInput = editorFrame.locator('input[type="text"]').first();
        await urlInput.fill('https://example.com');

        // Wait a moment for any API calls to complete
        await page.waitForTimeout(2000);

        // Publish the post
        await page.click('button.editor-post-publish-button__button');
        await page.waitForSelector('button.editor-post-publish-button__button:not([aria-disabled="true"])');
        // Click the second enabled publish button (the actual publish button)
        await page.locator('button.editor-post-publish-button__button:not([aria-disabled="true"])').nth(1).click();
        // Wait for the post to be published - look for any success indicator
        await page.waitForSelector('.editor-post-publish-panel');

        // Click "View Post" to go to the frontend
        await page.locator('.post-publish-panel__postpublish-buttons .is-primary').click();

        // Check that the block is present with correct CSS class
        const block = page.locator('.wp-block-wp-open-graph-card-og-card');

        await expect(block).toBeVisible();

        // Check that data attributes are present
        await expect(block).toHaveAttribute('data-url', 'https://example.com');

        // Check that the OgCard component rendered (should have specific content)
        await expect(block.locator('.og-card')).toBeVisible();
        await expect(block.locator('.og-card-title')).toBeVisible();
    });

    // test('should handle multiple blocks on the same page', async ({ page }) => {
    // test('should handle blocks with missing data gracefully', async ({ page }) => {
});