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

    await page.waitForSelector('button[id="components-autocomplete-item-0-block-link-preview-cards-card"]');
    await page.click('button[id="components-autocomplete-item-0-block-link-preview-cards-card"]');
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
    await editorFrame.waitForSelector('.wp-block-link-preview-cards-card', { timeout: 10000 });
}

/**
 * Fill URL in the block and wait for preview
 * @param {import('@playwright/test').Frame} editorFrame
 * @param {string} url
 */
export async function fillUrlAndWaitForPreview(editorFrame, url = 'https://example.com') {
    const urlInput = editorFrame.locator('.wp-block-link-preview-cards-card input[type="text"]').first();
    await urlInput.fill(url);
    await editorFrame.waitForSelector('.wp-block-link-preview-cards-card, [class*="card"]', { timeout: 10000 });
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
    console.log(block);
    // await expect(block).toBeVisible({ timeout: 10000 });
    await expect(block).toHaveAttribute('data-url', expectedUrl, { timeout: 10000 });

    const cardContent = page.locator('.og-card');
    await expect(cardContent).toBeVisible({ timeout: 10000 });
}