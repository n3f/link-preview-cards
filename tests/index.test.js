/* global test, expect */
/**
 * @jest-environment jsdom
 */

describe('Open Graph Card Block', () => {
	test('should have basic structure', () => {
		// Simple test to verify the test setup works
		expect(true).toBe(true);
	});

	test('should be able to import block metadata', () => {
		// Test that we can import the block.json file
		const metadata = require('../src/block.json');
		expect(metadata).toBeDefined();
		expect(metadata.name).toBe('wp-open-graph-card/og-card');
		expect(metadata.title).toBe('Open Graph Card');
	});
});