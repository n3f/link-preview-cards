const { test, expect } = require('@playwright/test');

test.describe('Open Graph Card Block', () => {
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

	test('should be able to add Open Graph Card block to a post', async ({ page }) => {
		// Verify the block is added by checking for it in the editor frame
		const block = editorFrame.locator('.wp-block-wp-open-graph-card-og-card').first();
		await expect(block).toBeVisible();
	});

	test('should display URL input field when block is added', async ({ page }) => {
		// Verify the URL input field is present in the editor frame
		const urlInput = editorFrame.locator('input[placeholder="https://example.com"]');
		await expect(urlInput).toBeVisible();
	});

	test('should show preview when URL is entered', async ({ page }) => {
		// Enter a URL in the input field
		const urlInput = editorFrame.locator('input[placeholder="https://example.com"]');
		await urlInput.fill('https://example.com');

		// Wait for the preview to appear (this might take a moment due to API call)
		await page.waitForTimeout(2000);

		// Verify the preview is shown
		const preview = editorFrame.locator('.wpogc-card');
		await expect(preview).toBeVisible();
	});
});