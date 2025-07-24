/* global test, expect */
/**
 * @jest-environment jsdom
 */

import React from 'react';
import '@testing-library/jest-dom';
import { render, screen } from '@testing-library/react';
import Card from '../src/Card';

describe('Card Component', () => {
	test('should render null when no URL is provided', () => {
		const { container } = render(<Card />);
		expect(container.firstChild).toBeNull();
	});

	test('should render with URL only', () => {
		const url = 'https://example.com';
		render(<Card url={url} />);

		const link = screen.getByRole('link');
		expect(link).toHaveAttribute('href', url);
		expect(link).toHaveAttribute('target', '_blank');
		expect(link).toHaveAttribute('rel', 'noopener noreferrer');
		expect(link).toHaveClass('link-preview-card');
	});

	test('should render with title and description', () => {
		const url = 'https://example.com';
		const title = 'Test Title';
		const description = 'Test Description';

		render(<Card url={url} title={title} description={description} />);

		expect(screen.getByText(title)).toBeInTheDocument();
		expect(screen.getByText(description)).toBeInTheDocument();
	});

	test('should render image when provided', () => {
		const url = 'https://example.com';
		const imageUrl = 'https://example.com/image.jpg';

		render(<Card url={url} image={imageUrl} />);
		// screen.debug();

		const image = screen.getByAltText('');
		expect(image).toHaveAttribute('src', imageUrl);
		expect(image).toHaveAttribute('alt', '');
	});

	test('should display domain from URL', () => {
		const url = 'https://example.com/page';
		render(<Card url={url} />);

		expect(screen.getByText('example.com')).toBeInTheDocument();
	});

	test('should fall back to URL as title when title is missing', () => {
		const url = 'https://example.com';
		render(<Card url={url} />);
		// The title div should display the URL
		expect(screen.getByText(url)).toBeInTheDocument();
	});

	test('should handle malformed URLs gracefully', () => {
		render(<Card url="not-a-url" />);
		expect(screen.getByRole('link')).toHaveAttribute('href', 'not-a-url');
		// Test that the component renders without crashing
		expect(screen.getByRole('link')).toBeInTheDocument();
	});

	test('should handle very long titles', () => {
		const longTitle = 'a'.repeat(200);
		render(<Card url="https://example.com" title={longTitle} />);
		expect(screen.getByText(longTitle)).toBeInTheDocument();
	});

	test('should handle missing images gracefully', () => {
		render(<Card url="https://example.com" image="https://invalid-image.jpg" />);
		// Should not crash when image fails to load
		expect(screen.getByRole('link')).toBeInTheDocument();
	});

	test('should handle XSS in content', () => {
		const maliciousTitle = '<script>alert("xss")</script>';
		render(<Card url="https://example.com" title={maliciousTitle} />);
		expect(screen.getByText(maliciousTitle)).toBeInTheDocument();
		// Verify the script tag is not executed (rendered as text)
		expect(screen.queryByText('xss')).not.toBeInTheDocument();
	});

	test('should handle very long URLs', () => {
		const longUrl = 'https://example.com/' + 'a'.repeat(1000);
		render(<Card url={longUrl} />);
		expect(screen.getByRole('link')).toHaveAttribute('href', longUrl);
	});

	test('should handle special characters in URLs', () => {
		const urlWithSpecialChars = 'https://example.com/path?param=value&another=test#fragment';
		render(<Card url={urlWithSpecialChars} />);
		expect(screen.getByRole('link')).toHaveAttribute('href', urlWithSpecialChars);
	});
});
