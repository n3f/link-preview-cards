export default {
	testPathIgnorePatterns: ['/node_modules/', '/specs/'],
	testMatch: ['**/tests/**/*.test.js', '**/tests/**/*.spec.js'],
	testEnvironment: 'jsdom',
};
