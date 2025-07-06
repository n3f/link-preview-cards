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
// console.log("Hello World! (from wpogc-open-graph-card block)");
/* eslint-enable no-console */

import { render, createElement } from '@wordpress/element';
import OgCard from './OgCard';

// Wait for DOMContentLoaded to ensure all blocks are present
document.addEventListener('DOMContentLoaded', () => {
    const blocks = document.querySelectorAll('.wp-block-open-graph-card-og-card');
    blocks.forEach((el) => {
        const url = el.getAttribute('data-url');
        const ogTitle = el.getAttribute('data-og-title');
        const ogDescription = el.getAttribute('data-og-description');
        const ogImage = el.getAttribute('data-og-image');
        render(
            createElement(OgCard, { url, ogTitle, ogDescription, ogImage }),
            el
        );
    });
});
