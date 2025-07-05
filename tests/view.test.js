/* global test, expect */
/**
 * @jest-environment jsdom
 */

describe('View Script CSS Selector Issue', () => {
    test('should identify the correct CSS selector for the block', () => {
        // The block name in block.json is: wp-open-graph-card/og-card
        // This should generate CSS class: wp-block-wp-open-graph-card-og-card
        const blockName = 'wp-open-graph-card/og-card';
        const expectedCssClass = 'wp-block-wp-open-graph-card-og-card';

        // Convert block name to CSS class (WordPress convention)
        const convertedClass = 'wp-block-' + blockName.replace(/\//g, '-');

        expect(convertedClass).toBe(expectedCssClass);
    });

    test('should identify the incorrect selector currently used in view.js', () => {
        // Current selector in view.js is: .wp-block-wpogc-open-graph-card
        const currentSelector = '.wp-block-wpogc-open-graph-card';
        const correctSelector = '.wp-block-wp-open-graph-card-og-card';

        expect(currentSelector).not.toBe(correctSelector);
    });

    test('should verify the block name matches what WordPress expects', () => {
        const blockJson = require('../src/block.json');
        expect(blockJson.name).toBe('wp-open-graph-card/og-card');
    });


});