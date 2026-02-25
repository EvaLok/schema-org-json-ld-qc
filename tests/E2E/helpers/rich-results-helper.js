// @ts-check

/**
 * Wraps JSON-LD in a minimal HTML document suitable for Rich Results Test.
 * @param {string} jsonLd - The JSON-LD string to wrap
 * @returns {string} - Complete HTML document
 */
function wrapInHtml(jsonLd) {
	return `<!DOCTYPE html>
<html>
<head>
<title>Schema Test Page</title>
<script type="application/ld+json">
${jsonLd}
</script>
</head>
<body>
<h1>Schema Test Page</h1>
<p>This page contains structured data for validation.</p>
</body>
</html>`;
}

/**
 * Submits HTML to Google Rich Results Test and extracts results.
 *
 * The page uses a CodeMirror editor with overlays that intercept pointer
 * events. We must use JavaScript evaluation to set the editor content
 * rather than clicking/typing.
 *
 * @param {import('@playwright/test').Page} page
 * @param {string} html - The HTML to test
 * @param {object} options
 * @param {number} [options.timeout=90000] - Max wait time in ms
 * @returns {Promise<{success: boolean, detectedTypes: string[], errors: string[], warnings: string[], infrastructure_failure: boolean, raw_text: string}>}
 */
async function submitToRichResultsTest(page, html, options = {}) {
	const timeout = options.timeout || 90000;
	const result = {
		success: false,
		detectedTypes: [],
		errors: [],
		warnings: [],
		infrastructure_failure: false,
		raw_text: '',
	};

	try {
		// Navigate to the Rich Results Test
		await page.goto('https://search.google.com/test/rich-results', {
			waitUntil: 'networkidle',
			timeout: 30000,
		});

		// Check for CAPTCHA, consent, or bot detection
		const bodyText = await page.textContent('body');
		if (bodyText.includes('unusual traffic') || bodyText.includes('robot')) {
			result.infrastructure_failure = true;
			result.raw_text = 'Bot detection or unusual traffic warning triggered';
			return result;
		}

		// Click the "code" tab to ensure it's active
		const codeTab = page.getByRole('tab', { name: 'code' });
		await codeTab.waitFor({ timeout: 10000 });
		await codeTab.click();
		await page.waitForTimeout(1000);

		// Set CodeMirror content via JavaScript (avoids overlay click interception)
		const cmSet = await page.evaluate((code) => {
			const cmEl = document.querySelector('.CodeMirror');
			if (cmEl && cmEl.CodeMirror) {
				cmEl.CodeMirror.setValue(code);
				return true;
			}
			return false;
		}, html);

		if (!cmSet) {
			// Fallback: try clicking the scroll area and typing
			const scrollArea = page.locator('.CodeMirror-scroll');
			await scrollArea.click({ force: true });
			await page.waitForTimeout(300);
			await page.keyboard.press('Control+a');
			await page.keyboard.type(html, { delay: 0 });
		}

		// Wait for "test code" button to become enabled
		const testButton = page.getByRole('button', { name: 'test code' });
		await testButton.waitFor({ state: 'visible', timeout: 5000 });
		await page.waitForTimeout(1500);

		// Check if button is enabled
		const isDisabled = await testButton.isDisabled();
		if (isDisabled) {
			// Trigger input event on CodeMirror in case change wasn't detected
			await page.evaluate(() => {
				const cmEl = document.querySelector('.CodeMirror');
				if (cmEl && cmEl.CodeMirror) {
					cmEl.CodeMirror.refresh();
					cmEl.CodeMirror.triggerOnKeyDown({
						type: 'keydown', keyCode: 13, preventDefault: () => {},
						stopPropagation: () => {},
					});
				}
			});
			await page.waitForTimeout(1000);
		}

		// Click test button (force in case of overlay)
		await testButton.click({ force: true });

		// Wait for results page to load
		await page.waitForFunction(
			() => {
				const text = document.body.innerText;
				return text.includes('detected') ||
					text.includes('items detected') ||
					text.includes('No rich results') ||
					text.includes('not eligible') ||
					text.includes('valid items');
			},
			{ timeout: timeout },
		);

		// Give results time to fully render
		await page.waitForTimeout(3000);

		// Capture full page text for analysis
		result.raw_text = await page.textContent('body');

		// Parse the results
		const text = result.raw_text;

		// Look for detected rich result types
		const typeMatches = text.match(/(?:FAQ|Article|Product|Recipe|Event|LocalBusiness|BreadcrumbList|JobPosting|VideoObject|Course|Dataset|Movie|SoftwareApplication|NewsArticle|QAPage|DiscussionForumPosting|ProfilePage|VacationRental|MathSolver)(?:\s*\(\d+ items? detected\))?/g);
		if (typeMatches) {
			result.detectedTypes = [...new Set(typeMatches.map(t => t.replace(/\s*\(\d+ items? detected\)/, '').trim()))];
		}

		// Check for errors
		if (text.includes('error') || text.includes('Error')) {
			const errorSection = text.match(/error[s]?[:\s]+([^\n]+)/gi);
			if (errorSection) {
				result.errors = errorSection.map(e => e.trim());
			}
		}

		// Check for warnings
		if (text.includes('warning') || text.includes('Warning')) {
			const warningSection = text.match(/warning[s]?[:\s]+([^\n]+)/gi);
			if (warningSection) {
				result.warnings = warningSection.map(w => w.trim());
			}
		}

		// Determine success
		if (text.includes('No rich results detected') || text.includes('not eligible')) {
			result.success = false;
		} else if (result.detectedTypes.length > 0) {
			result.success = result.errors.length === 0;
		}

	} catch (error) {
		const errorMessage = error.message || '';
		if (
			errorMessage.includes('timeout') ||
			errorMessage.includes('Timeout') ||
			errorMessage.includes('net::') ||
			errorMessage.includes('Navigation') ||
			errorMessage.includes('ERR_')
		) {
			result.infrastructure_failure = true;
		}
		result.raw_text = `Error: ${errorMessage}`;
	}

	return result;
}

module.exports = { wrapInHtml, submitToRichResultsTest };
