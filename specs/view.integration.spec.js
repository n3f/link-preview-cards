/**
 * Integration test for the frontend view.js functionality
 * Tests that blocks are properly rendered on the frontend
 */

import { test, expect } from '@playwright/test';
import {
    loginToWordPress,
    navigateToNewPost,
    addOpenGraphCardBlock,
    getEditorFrame,
    waitForBlockLoaded,
    fillUrlAndWaitForPreview,
    publishPostAndView,
    verifyFrontendBlock
} from './helpers';

test.describe('Frontend Open Graph Card Rendering', () => {
    let editorFrame;

    test.beforeEach(async ({ page }) => {
        await loginToWordPress(page);
        await navigateToNewPost(page);
        await addOpenGraphCardBlock(page);

        // Wait for block to be added and get editor frame
        await page.waitForTimeout(1000);
        editorFrame = getEditorFrame(page);
        await waitForBlockLoaded(editorFrame);
    });

    test('should render Open Graph Card blocks on the frontend', async ({ page }) => {
        await fillUrlAndWaitForPreview(editorFrame, 'https://example.com');
        await publishPostAndView(page);
        await verifyFrontendBlock(page, 'https://example.com');

        // Additional check for title element if it exists
        const titleElement = page.locator('.og-card-title');
        if (await titleElement.count() > 0) {
            await expect(titleElement).toBeVisible({ timeout: 5000 });
        }
    });

    // test('should handle multiple blocks on the same page', async ({ page }) => {
    // test('should handle blocks with missing data gracefully', async ({ page }) => {
});