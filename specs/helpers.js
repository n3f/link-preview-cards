/**
 * Common test helpers for Link Preview Cards tests
 */

import { expect } from '@playwright/test';

/**
 * Login to WordPress admin
 * @param {import('@playwright/test').Page} page
 */
export async function loginToWordPress(page) {
    await page.goto('http://localhost:8889/wp-admin');
    await page.waitForSelector('#user_login', { timeout: 10000 });
    await page.waitForTimeout(3000);
    await page.fill('#user_login', 'admin');
    await page.fill('#user_pass', 'password');
    await page.click('#wp-submit');
    await page.waitForURL('http://localhost:8889/wp-admin/', { timeout: 15000 });
    await page.waitForSelector('#wpadminbar, .wp-admin', { timeout: 10000 });
}

/**
 * Navigate to new post and wait for editor to be ready
 * @param {import('@playwright/test').Page} page
 */
export async function navigateToNewPost(page) {
    await page.goto('http://localhost:8889/wp-admin/post-new.php');
    await page.waitForSelector('.block-editor-page', { timeout: 15000 });
}

/**
 * Add Link Preview Cards block to the editor
 * @param {import('@playwright/test').Page} page
 */
export async function addOpenGraphCardBlock(page) {
    await page.click('.block-editor-page');
    await page.keyboard.press('/');
    await page.keyboard.press('l');

    // Wait for the block to appear in the autocomplete
    await page.waitForSelector('button[id*="link-preview-cards"]', { timeout: 10000 });
    await page.click('button[id*="link-preview-cards"]');
}

/**
 * Find and return the editor iframe
 * @param {import('@playwright/test').Page} page
 * @returns {import('@playwright/test').Frame}
 */
export function getEditorFrame(page) {
    const frames = page.frames();
    const editorFrame = frames.find(f => f.url().startsWith('blob:'));
    if (!editorFrame) throw new Error('Editor iframe not found');
    return editorFrame;
}

/**
 * Wait for block to be loaded in editor frame
 * @param {import('@playwright/test').Frame} editorFrame
 */
export async function waitForBlockLoaded(editorFrame) {
    // Try multiple possible selectors for the block
    await editorFrame.waitForSelector('[class*="wp-block-link-preview-cards"], [class*="link-preview-cards"]', { timeout: 10000 });
}

/**
 * Fill URL in the block and wait for preview
 * @param {import('@playwright/test').Frame} editorFrame
 * @param {string} url
 */
export async function fillUrlAndWaitForPreview(editorFrame, url = 'https://example.com') {
    const urlInput = editorFrame.locator('[class*="wp-block-link-preview-cards"] input[type="text"], [class*="link-preview-cards"] input[type="text"]').first();
    await urlInput.fill(url);
    await editorFrame.waitForSelector('[class*="wp-block-link-preview-cards"], [class*="card"]', { timeout: 10000 });
}

/**
 * Publish the post and navigate to frontend
 * @param {import('@playwright/test').Page} page
 */
export async function publishPostAndView(page) {
    await page.click('button.editor-post-publish-button__button');
    await page.waitForSelector('button.editor-post-publish-button__button:not([aria-disabled="true"])', { timeout: 15000 });

    const publishButtons = page.locator('button.editor-post-publish-button__button:not([aria-disabled="true"])');
    // await expect(publishButtons).toHaveCount(2, { timeout: 10000 });
    await publishButtons.nth(1).click();

    await page.waitForSelector('.editor-post-publish-panel', { timeout: 15000 });

    const viewPostButton = page.locator('.post-publish-panel__postpublish-buttons .is-primary');
    // await expect(viewPostButton).toBeVisible({ timeout: 10000 });
    await viewPostButton.click();

    await page.waitForLoadState('networkidle', { timeout: 15000 });
}

/**
 * Verify block is rendered on frontend
 * @param {import('@playwright/test').Page} page
 * @param {string} expectedUrl
 */
export async function verifyFrontendBlock(page, expectedUrl = 'https://example.com') {
    const block = page.locator('.wp-block-link-preview-cards-card');
    // Add this before the failing line to see what's actually on the page
    // const pageContent = await page.content();
    await expect(block).toHaveAttribute('data-url', expectedUrl, { timeout: 10000 });

    // The Card component is rendered inside the block wrapper, so we look for the block wrapper
    // which should be visible and contain the card content
    await expect(block).toBeVisible({ timeout: 10000 });

    // Wait for the Card component to be rendered inside the block
    await page.waitForSelector('.wp-block-link-preview-cards-card .card', { timeout: 30000 });
}