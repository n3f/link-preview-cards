/* global test, expect */
/**
 * @jest-environment jsdom
 */

import '@testing-library/jest-dom';
import { render, screen } from '@testing-library/react';
import OgCard from '../src/OgCard';

describe('OgCard Component', () => {
	test('should render null when no URL is provided', () => {
		const { container } = render(<OgCard />);
		expect(container.firstChild).toBeNull();
	});

	test('should render with URL only', () => {
		const url = 'https://example.com';
		render(<OgCard url={url} />);

		const link = screen.getByRole('link');
		expect(link).toHaveAttribute('href', url);
		expect(link).toHaveAttribute('target', '_blank');
		expect(link).toHaveAttribute('rel', 'noopener noreferrer');
		expect(link).toHaveClass('wpogc-card');
	});

	test('should render with title and description', () => {
		const url = 'https://example.com';
		const title = 'Test Title';
		const description = 'Test Description';

		render(<OgCard url={url} ogTitle={title} ogDescription={description} />);

		expect(screen.getByText(title)).toBeInTheDocument();
		expect(screen.getByText(description)).toBeInTheDocument();
	});

	test('should render image when provided', () => {
		const url = 'https://example.com';
		const imageUrl = 'https://example.com/image.jpg';

		render(<OgCard url={url} ogImage={imageUrl} />);
		// screen.debug();

		const image = screen.getByAltText('');
		expect(image).toHaveAttribute('src', imageUrl);
		expect(image).toHaveAttribute('alt', '');
	});

	test('should display domain from URL', () => {
		const url = 'https://example.com/page';
		render(<OgCard url={url} />);

		expect(screen.getByText('example.com')).toBeInTheDocument();
	});

	test('should fall back to URL as title when ogTitle is missing', () => {
		const url = 'https://example.com';
		render(<OgCard url={url} />);
		// The title div should display the URL
		expect(screen.getByText(url)).toBeInTheDocument();
	});
});