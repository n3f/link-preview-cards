/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

/* eslint-disable no-console */
// console.log("Hello World! (from link-preview-cards)");
/* eslint-enable no-console */

import { render, createElement } from '@wordpress/element';
import Card from './Card.js';

// Wait for DOMContentLoaded to ensure all blocks are present
document.addEventListener('DOMContentLoaded', () => {
    console.log('View script loaded, looking for blocks...');
    const blocks = document.querySelectorAll('.wp-block-link-preview-cards-card');
    console.log('Found blocks:', blocks.length);
    blocks.forEach((el) => {
        const url = el.getAttribute('data-url');
        const title = el.getAttribute('data-title');
        const description = el.getAttribute('data-description');
        const image = el.getAttribute('data-image');
        console.log('Rendering card for URL:', url);
        render(
            createElement(Card, { url, title, description, image }),
            el
        );
    });
});
