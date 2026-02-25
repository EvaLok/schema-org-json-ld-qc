#!/usr/bin/env node

/**
 * Validates JSON-LD output from PHP scripts using structured-data-testing-tool.
 *
 * Usage: node scripts/validate-jsonld.js <php-script>
 * Example: node scripts/validate-jsonld.js src/generate-article.php
 */

const { structuredDataTest } = require('structured-data-testing-tool');
const { execSync } = require('child_process');
const path = require('path');

async function validate(phpScript) {
    // Generate JSON-LD from PHP script
    const jsonLd = execSync(`php ${phpScript}`, { encoding: 'utf-8' }).trim();

    // Wrap in minimal HTML
    const html = `<!DOCTYPE html>
<html>
<head>
<script type="application/ld+json">
${jsonLd}
</script>
</head>
<body></body>
</html>`;

    console.log(`\nValidating: ${phpScript}`);
    console.log(`JSON-LD @type: ${JSON.parse(jsonLd)['@type']}`);
    console.log('---');

    try {
        const result = await structuredDataTest(html);
        console.log(`Schemas found: ${result.schemas.map(s => s.schema).join(', ')}`);
        console.log(`Tests passed: ${result.passed.length}`);
        console.log(`Tests failed: ${result.failed.length}`);
        console.log(`Warnings: ${result.warnings.length}`);

        if (result.failed.length > 0) {
            console.log('\nFailures:');
            result.failed.forEach(f => {
                console.log(`  - ${f.test}: ${f.error || 'failed'}`);
            });
        }
        if (result.warnings.length > 0) {
            console.log('\nWarnings:');
            result.warnings.forEach(w => {
                console.log(`  - ${w.test}: ${w.description || ''}`);
            });
        }

        return { success: true, passed: result.passed.length, failed: result.failed.length, warnings: result.warnings.length };
    } catch (err) {
        if (err.type === 'VALIDATION_FAILED') {
            console.log(`Tests passed: ${err.res.passed.length}`);
            console.log(`Tests failed: ${err.res.failed.length}`);
            console.log(`Warnings: ${err.res.warnings.length}`);

            if (err.res.failed.length > 0) {
                console.log('\nFailures:');
                err.res.failed.forEach(f => {
                    console.log(`  - ${f.test}: ${f.error || 'failed'}`);
                });
            }
            return { success: false, passed: err.res.passed.length, failed: err.res.failed.length, warnings: err.res.warnings.length };
        }
        console.error(`Error: ${err.message}`);
        return { success: false, error: err.message };
    }
}

async function main() {
    const scripts = process.argv.slice(2);

    if (scripts.length === 0) {
        console.log('Usage: node scripts/validate-jsonld.js <php-script> [php-script...]');
        console.log('       node scripts/validate-jsonld.js src/generate-*.php');
        process.exit(1);
    }

    let totalPassed = 0;
    let totalFailed = 0;

    for (const script of scripts) {
        const result = await validate(script);
        if (result.passed) totalPassed += result.passed;
        if (result.failed) totalFailed += result.failed;
    }

    console.log('\n===================================');
    console.log(`Total: ${totalPassed} passed, ${totalFailed} failed`);

    if (totalFailed > 0) {
        process.exit(1);
    }
}

main();
