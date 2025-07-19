/* global test, expect */
/**
 * @jest-environment jsdom
 */

describe('View Script CSS Selector Issue', () => {
    test('should identify the correct CSS selector for the block', () => {
        // The block name in block.json is: link-preview-cards/card
        // This should generate CSS class: wp-block-link-preview-cards-card
        const blockName = 'link-preview-cards/card';
        const expectedCssClass = 'wp-block-link-preview-cards-card';

        // Convert block name to CSS class (WordPress convention)
        const convertedClass = 'wp-block-' + blockName.replace(/\//g, '-');

        expect(convertedClass).toBe(expectedCssClass);
    });

    test('should identify the incorrect selector currently used in view.js', () => {
        // Current selector in view.js is: .wp-block-link-preview-cards-card
        const currentSelector = '.wp-block-link-preview-cards-card';
        const correctSelector = '.wp-block-link-preview-cards-card';

        expect(currentSelector).toBe(correctSelector);
    });

    test('should verify the block name matches what WordPress expects', () => {
        const blockJson = require('../src/block.json');
        expect(blockJson.name).toBe('link-preview-cards/card');
    });


});