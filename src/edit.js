/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';
import { useEffect, useRef } from 'react';
import debounce from 'lodash.debounce';
import apiFetch from '@wordpress/api-fetch';
import Card from './Card.js';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const { url, title, description, image } = attributes;

	const debouncedFetch = useRef(
		debounce(async (url, setAttributes) => {
			try {
				const data = await apiFetch({
					path: `/linkpreviewcards/v1/fetch?url=${encodeURIComponent(url)}&nonce=${linkpreviewcards.nonce}`,
				});
				setAttributes({
					title: data.title || '',
					description: data.description || '',
					image: data.image || ''
				});
			} catch (e) {
				setAttributes({
					title: '',
					description: '',
					image: ''
				});
			}
		}, 600)
	).current;

	useEffect(() => {
		if (!url) return;
		debouncedFetch(url, setAttributes);
		// Cleanup on unmount
		return () => debouncedFetch.cancel();
	}, [url]);

	return (
		<div {...useBlockProps()}>
			<TextControl
				label={__("Paste a URL to preview", "link-preview-cards")}
				value={url}
				onChange={(url) => setAttributes({ url })}
				placeholder="https://example.com"
			/>
			{url && <Card url={url} title={title} description={description} image={image} />}
		</div>
	);
}
