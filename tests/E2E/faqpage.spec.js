// @ts-check
const { test, expect } = require('@playwright/test');
const { execSync } = require('child_process');
const { wrapInHtml, submitToRichResultsTest } = require('./helpers/rich-results-helper');
const fs = require('fs');
const path = require('path');

test.describe('FAQPage Rich Results Validation', () => {
	test('FAQPage JSON-LD passes Google Rich Results Test', async ({ page }) => {
		// Generate JSON-LD from PHP
		const jsonLd = execSync('php src/generate-faqpage.php', {
			cwd: path.resolve(__dirname, '../../'),
			encoding: 'utf-8',
		}).trim();

		// Validate it's valid JSON first
		const parsed = JSON.parse(jsonLd);
		expect(parsed['@type']).toBe('FAQPage');

		// Wrap in HTML and submit to Rich Results Test
		const html = wrapInHtml(jsonLd);
		const result = await submitToRichResultsTest(page, html);

		// Store results
		const resultData = {
			schema_type: 'FAQPage',
			timestamp: new Date().toISOString(),
			package_commit: 'c98da7c',
			json_ld: jsonLd,
			result: result,
		};

		const resultsDir = path.resolve(__dirname, '../../results');
		fs.writeFileSync(
			path.join(resultsDir, 'faqpage-latest.json'),
			JSON.stringify(resultData, null, '\t'),
		);

		// If infrastructure failure, skip assertion but log it
		if (result.infrastructure_failure) {
			console.log('INFRASTRUCTURE FAILURE:', result.raw_text);
			test.skip(true, 'Google Rich Results Test infrastructure failure — not a package bug');
			return;
		}

		// Assert success
		expect(result.errors).toEqual([]);
		expect(result.detectedTypes.length).toBeGreaterThan(0);
	});
});
