const { test, expect } = require('@playwright/test');
const {
	loginToWordPress,
	navigateToNewPost,
	addOpenGraphCardBlock,
	getEditorFrame,
	waitForBlockLoaded,
	fillUrlAndWaitForPreview
} = require('./helpers');

test.describe('Open Graph Card Block', () => {
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

	test('should be able to add Open Graph Card block to a post', async ({ page }) => {
		const block = editorFrame.locator('.wp-block-wp-open-graph-card-og-card').first();
		await expect(block).toBeVisible({ timeout: 10000 });
	});

	test('should display URL input field when block is added', async ({ page }) => {
		const urlInput = editorFrame.locator('.wp-block-wp-open-graph-card-og-card input[type="text"]').first();
		await expect(urlInput).toBeVisible({ timeout: 10000 });
	});

	test('should show preview when URL is entered', async ({ page }) => {
		await fillUrlAndWaitForPreview(editorFrame, 'https://example.com');

		const preview = editorFrame.locator('.wpogc-card, .og-card, [class*="card"]').first();
		await expect(preview).toBeVisible({ timeout: 10000 });
	});
});