import { test, expect } from '@playwright/test';
import {
	navigateToNewPost,
	addOpenGraphCardBlock,
	getEditorFrame,
	waitForBlockLoaded,
	fillUrlAndWaitForPreview
} from './helpers';

test.describe('Link Preview Cards Block', () => {
	let editorFrame;

	test.beforeEach(async ({ page }) => {
		await navigateToNewPost(page);
		await addOpenGraphCardBlock(page);

		// Wait for block to be added and get editor frame
		await page.waitForTimeout(1000);
		editorFrame = getEditorFrame(page);
		await waitForBlockLoaded(editorFrame);
	});

	test('should be able to add Link Preview Cards block to a post', async ({ page }) => {
		const block = editorFrame.locator('[class*="wp-block-link-preview-cards"], [class*="link-preview-cards"]').first();
		await expect(block).toBeVisible({ timeout: 10000 });
	});

	test('should display URL input field when block is added', async ({ page }) => {
		const urlInput = editorFrame.locator('[class*="wp-block-link-preview-cards"] input[type="text"], [class*="link-preview-cards"] input[type="text"]').first();
		await expect(urlInput).toBeVisible({ timeout: 10000 });
	});

	test('should show preview when URL is entered', async ({ page }) => {
		await fillUrlAndWaitForPreview(editorFrame, 'https://example.com');

		const preview = editorFrame.locator('[class*="wp-block-link-preview-cards"], [class*="card"]').first();
		await expect(preview).toBeVisible({ timeout: 10000 });
	});
});
