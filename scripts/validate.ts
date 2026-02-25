#!/usr/bin/env bun
/**
 * Validates JSON-LD output from PHP scripts using @adobe/structured-data-validator.
 *
 * Usage: bun run scripts/validate.ts [php-script...]
 * Example: bun run scripts/validate.ts src/generate-article.php
 *          bun run scripts/validate.ts src/generate-*.php
 */

import Validator from '@adobe/structured-data-validator';
import WebAutoExtractor from '@marbec/web-auto-extractor';
import { execSync } from 'child_process';
import { mkdirSync, writeFileSync } from 'fs';

interface ValidationIssue {
	rootType: string;
	dataFormat: string;
	issueMessage: string;
	severity: 'ERROR' | 'WARNING';
	path?: Array<{ type?: string; property?: string; index?: number }>;
	fieldNames?: string[];
	location?: string;
	source?: string;
}

interface ScriptResult {
	script: string;
	schemaType: string;
	jsonLd: string;
	errors: ValidationIssue[];
	warnings: ValidationIssue[];
	success: boolean;
	timestamp: string;
}

// Fetch schema.org definitions once
let schemaOrgJson: unknown = null;

async function fetchSchemaOrg(): Promise<unknown> {
	if (schemaOrgJson) return schemaOrgJson;

	console.log('Fetching schema.org definitions...');
	const response = await fetch('https://schema.org/version/latest/schemaorg-all-https.jsonld');
	schemaOrgJson = await response.json();
	console.log('Schema.org definitions loaded.');
	return schemaOrgJson;
}

function wrapInHtml(jsonLd: string): string {
	return `<!DOCTYPE html>
<html>
<head>
<script type="application/ld+json">
${jsonLd}
</script>
</head>
<body></body>
</html>`;
}

async function validateScript(phpScript: string): Promise<ScriptResult> {
	const timestamp = new Date().toISOString();

	// Generate JSON-LD from PHP script
	let jsonLd: string;
	try {
		jsonLd = execSync(`php ${phpScript}`, { encoding: 'utf-8' }).trim();
	} catch (err) {
		return {
			script: phpScript,
			schemaType: 'unknown',
			jsonLd: '',
			errors: [{
				rootType: 'unknown',
				dataFormat: 'jsonld',
				issueMessage: `PHP execution failed: ${(err as Error).message}`,
				severity: 'ERROR',
			}],
			warnings: [],
			success: false,
			timestamp,
		};
	}

	const parsed = JSON.parse(jsonLd);
	const schemaType = Array.isArray(parsed['@type']) ? parsed['@type'].join(', ') : parsed['@type'];

	console.log(`\nValidating: ${phpScript} (@type: ${schemaType})`);
	console.log('---');

	// Wrap in HTML and extract structured data
	const html = wrapInHtml(jsonLd);
	const extractor = new WebAutoExtractor({ addLocation: true, embedSource: ['rdfa', 'microdata'] });
	const extractedData = extractor.parse(html);

	// Validate
	const schema = await fetchSchemaOrg();
	const validator = new Validator(schema);
	const issues = await validator.validate(extractedData) as ValidationIssue[];

	const errors = issues.filter(i => i.severity === 'ERROR');
	const warnings = issues.filter(i => i.severity === 'WARNING');

	console.log(`  Errors: ${errors.length}, Warnings: ${warnings.length}`);

	if (errors.length > 0) {
		console.log('  ERRORS:');
		for (const e of errors) {
			console.log(`    - [${e.rootType}] ${e.issueMessage}`);
			if (e.fieldNames?.length) {
				console.log(`      Fields: ${e.fieldNames.join(', ')}`);
			}
		}
	}

	if (warnings.length > 0) {
		console.log('  WARNINGS:');
		for (const w of warnings) {
			console.log(`    - [${w.rootType}] ${w.issueMessage}`);
			if (w.fieldNames?.length) {
				console.log(`      Fields: ${w.fieldNames.join(', ')}`);
			}
		}
	}

	if (errors.length === 0 && warnings.length === 0) {
		console.log('  PASS');
	}

	return {
		script: phpScript,
		schemaType,
		jsonLd,
		errors,
		warnings,
		success: errors.length === 0,
		timestamp,
	};
}

async function main() {
	const scripts = process.argv.slice(2);

	if (scripts.length === 0) {
		console.log('Usage: bun run scripts/validate.ts <php-script> [php-script...]');
		console.log('       bun run scripts/validate.ts src/generate-*.php');
		process.exit(1);
	}

	const results: ScriptResult[] = [];

	for (const script of scripts) {
		const result = await validateScript(script);
		results.push(result);
	}

	// Summary
	const passed = results.filter(r => r.success);
	const failed = results.filter(r => !r.success);
	const totalErrors = results.reduce((sum, r) => sum + r.errors.length, 0);
	const totalWarnings = results.reduce((sum, r) => sum + r.warnings.length, 0);

	console.log('\n===================================');
	console.log(`Results: ${passed.length} passed, ${failed.length} failed`);
	console.log(`Issues: ${totalErrors} errors, ${totalWarnings} warnings`);

	if (failed.length > 0) {
		console.log('\nFailed types:');
		for (const f of failed) {
			console.log(`  - ${f.schemaType} (${f.script})`);
			for (const e of f.errors) {
				console.log(`      ${e.issueMessage}`);
			}
		}
	}

	// Save results
	mkdirSync('results', { recursive: true });
	const packageCommit = execSync('composer show evabee/schema-org-json-ld 2>/dev/null | grep source | grep -oP "[a-f0-9]{40}"', { encoding: 'utf-8' }).trim();
	const resultFile = `results/validation-${new Date().toISOString().replace(/[:.]/g, '-')}.json`;
	writeFileSync(resultFile, JSON.stringify({
		timestamp: new Date().toISOString(),
		packageCommit,
		summary: {
			total: results.length,
			passed: passed.length,
			failed: failed.length,
			totalErrors,
			totalWarnings,
		},
		results: results.map(r => ({
			schemaType: r.schemaType,
			script: r.script,
			success: r.success,
			errorCount: r.errors.length,
			warningCount: r.warnings.length,
			errors: r.errors.map(e => ({ type: e.rootType, message: e.issueMessage, fields: e.fieldNames })),
			warnings: r.warnings.map(w => ({ type: w.rootType, message: w.issueMessage, fields: w.fieldNames })),
			jsonLd: r.jsonLd,
		})),
	}, null, 2));

	console.log(`\nResults saved to: ${resultFile}`);

	if (failed.length > 0) {
		process.exit(1);
	}
}

main();
