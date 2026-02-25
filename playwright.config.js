// @ts-check
const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
	testDir: './tests/E2E',
	timeout: 120000,
	retries: 1,
	use: {
		headless: true,
		browserName: 'chromium',
	},
	projects: [
		{
			name: 'chromium',
			use: { browserName: 'chromium' },
		},
	],
});
