import { defineConfig } from '@playwright/test';

export default defineConfig({
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
