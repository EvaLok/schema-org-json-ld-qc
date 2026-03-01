#!/usr/bin/env bun
/**
 * TypeScript parity check tool.
 *
 * Generates JSON-LD from TS types using the same data as our PHP generate scripts,
 * compares the output to PHP, and validates through Adobe's structured data validator.
 *
 * Usage: bun run tools/ts-parity-check.ts
 */

import Validator from '@adobe/structured-data-validator';
import WebAutoExtractor from '@marbec/web-auto-extractor';
import { execSync } from 'child_process';
import { resolve, dirname } from 'path';

// Import TS types from the vendor package source
import { JsonLdGenerator } from '../vendor/evabee/schema-org-json-ld/ts/src/JsonLdGenerator';
import { Article } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Article';
import { Answer } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Answer';
import { BreadcrumbList } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/BreadcrumbList';
import { Event } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Event';
import { FAQPage } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/FAQPage';
import { ListItem } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/ListItem';
import { Offer } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Offer';
import { Organization } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Organization';
import { Person } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Person';
import { Place } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Place';
import { PostalAddress } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/PostalAddress';
import { Question } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Question';
import { VirtualLocation } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/VirtualLocation';
import { EventAttendanceModeEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/EventAttendanceModeEnumeration';
import { EventStatusType } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/EventStatusType';
import { ItemAvailability } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/ItemAvailability';
import { OfferItemCondition } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/OfferItemCondition';

interface ValidationIssue {
	rootType: string;
	dataFormat: string;
	issueMessage: string;
	severity: 'ERROR' | 'WARNING';
	fieldNames?: string[];
}

interface ParityResult {
	type: string;
	phpScript: string;
	tsJsonLd: string;
	phpJsonLd: string;
	parity: boolean;
	parityDiffs: string[];
	e2eErrors: number;
	e2eWarnings: number;
	e2eIssues: ValidationIssue[];
}

// Generate JSON-LD for the same data as each PHP script, but using TS types
function generateTsJsonLd(): Map<string, { type: string; phpScript: string; json: string }> {
	const results = new Map<string, { type: string; phpScript: string; json: string }>();

	// Article — matches src/generate-article.php
	const article = new Article({
		headline: 'Understanding Tidal Patterns in the North Sea',
		author: new Person({ name: 'Dr. Sarah Chen' }),
		datePublished: '2025-01-15',
		dateModified: '2025-02-01',
		description: 'A comprehensive guide to tidal patterns and their impact on coastal ecosystems.',
		publisher: new Organization({
			name: 'Nature Weekly',
			logo: 'https://example.com/logo.png',
		}),
		image: [
			'https://example.com/tidal-1x1.jpg',
			'https://example.com/tidal-4x3.jpg',
			'https://example.com/tidal-16x9.jpg',
		],
	});
	results.set('Article', {
		type: 'Article',
		phpScript: 'src/generate-article.php',
		json: JsonLdGenerator.schemaToJson(article),
	});

	// BreadcrumbList — matches src/generate-breadcrumblist.php
	const breadcrumb = new BreadcrumbList([
		new ListItem(1, 'Home', 'https://example.com/'),
		new ListItem(2, 'Electronics', 'https://example.com/electronics'),
		new ListItem(3, 'Phones', 'https://example.com/electronics/phones'),
		new ListItem(4, 'Pixel 9 Pro'),
	]);
	results.set('BreadcrumbList', {
		type: 'BreadcrumbList',
		phpScript: 'src/generate-breadcrumblist.php',
		json: JsonLdGenerator.schemaToJson(breadcrumb),
	});

	// FAQPage — matches src/generate-faqpage.php
	const faq = new FAQPage([
		new Question({
			name: 'What is JSON-LD?',
			acceptedAnswer: new Answer(
				'JSON-LD is a method of encoding Linked Data using JSON. It allows data to be serialized in a way that is familiar to developers.',
			),
		}),
		new Question({
			name: 'Why should I use structured data on my website?',
			acceptedAnswer: new Answer(
				'Structured data helps search engines understand your content better and can enable rich results in search, such as FAQ snippets, recipe cards, and product listings.',
			),
		}),
		new Question({
			name: 'How do I validate my structured data?',
			acceptedAnswer: new Answer(
				'You can use the Google Rich Results Test at search.google.com/test/rich-results to validate your structured data and see which rich result types it supports.',
			),
		}),
	]);
	results.set('FAQPage', {
		type: 'FAQPage',
		phpScript: 'src/generate-faqpage.php',
		json: JsonLdGenerator.schemaToJson(faq),
	});

	// Event — matches src/generate-event.php
	const event = new Event({
		name: 'The Rolling Stones - Hackney Diamonds Tour',
		startDate: '2025-07-21T19:00-05:00',
		location: [
			new Place(
				'Soldier Field',
				new PostalAddress({
					streetAddress: '1410 Special Olympics Dr',
					addressLocality: 'Chicago',
					addressRegion: 'IL',
					postalCode: '60605',
					addressCountry: 'US',
				}),
			),
			new VirtualLocation(
				'https://livestream.example.com/rolling-stones',
				'Official Livestream',
			),
		],
		description: 'The Rolling Stones return to Chicago for one night only. Available in-person and via livestream.',
		endDate: '2025-07-21T23:00-05:00',
		eventAttendanceMode: EventAttendanceModeEnumeration.MixedEventAttendanceMode,
		eventStatus: EventStatusType.EventScheduled,
		image: ['https://example.com/rolling-stones-tour.jpg'],
		offers: new Offer({
			url: 'https://example.com/tickets/rolling-stones',
			priceCurrency: 'USD',
			price: 125.00,
			itemCondition: OfferItemCondition.NewCondition,
			availability: ItemAvailability.InStock,
		}),
		organizer: new Organization({
			name: 'Live Nation',
			url: 'https://www.livenation.com',
		}),
		performer: new Person({ name: 'The Rolling Stones' }),
	});
	results.set('Event', {
		type: 'Event',
		phpScript: 'src/generate-event.php',
		json: JsonLdGenerator.schemaToJson(event),
	});

	return results;
}

function compareJsonLd(tsJson: string, phpJson: string): string[] {
	const diffs: string[] = [];
	const tsObj = JSON.parse(tsJson) as Record<string, unknown>;
	const phpObj = JSON.parse(phpJson) as Record<string, unknown>;

	// Compare recursively, report key differences
	function compare(ts: unknown, php: unknown, path: string): void {
		if (ts === php) return;

		if (typeof ts !== typeof php) {
			diffs.push(`${path}: type mismatch (TS: ${typeof ts}, PHP: ${typeof php})`);
			return;
		}

		if (ts === null || php === null) {
			if (ts !== php) diffs.push(`${path}: null mismatch (TS: ${ts}, PHP: ${php})`);
			return;
		}

		if (Array.isArray(ts) && Array.isArray(php)) {
			if (ts.length !== php.length) {
				diffs.push(`${path}: array length (TS: ${ts.length}, PHP: ${php.length})`);
			}
			const minLen = Math.min(ts.length, php.length);
			for (let i = 0; i < minLen; i++) {
				compare(ts[i], php[i], `${path}[${i}]`);
			}
			return;
		}

		if (typeof ts === 'object' && typeof php === 'object') {
			const tsKeys = Object.keys(ts as Record<string, unknown>).sort();
			const phpKeys = Object.keys(php as Record<string, unknown>).sort();

			// Keys in TS but not PHP
			for (const k of tsKeys) {
				if (!phpKeys.includes(k)) {
					diffs.push(`${path}.${k}: present in TS but not PHP`);
				}
			}
			// Keys in PHP but not TS
			for (const k of phpKeys) {
				if (!tsKeys.includes(k)) {
					diffs.push(`${path}.${k}: present in PHP but not TS`);
				}
			}
			// Compare shared keys
			for (const k of tsKeys) {
				if (phpKeys.includes(k)) {
					compare(
						(ts as Record<string, unknown>)[k],
						(php as Record<string, unknown>)[k],
						`${path}.${k}`,
					);
				}
			}
			return;
		}

		if (ts !== php) {
			diffs.push(`${path}: value mismatch (TS: ${JSON.stringify(ts)}, PHP: ${JSON.stringify(php)})`);
		}
	}

	compare(tsObj, phpObj, '$');
	return diffs;
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

let schemaOrgJson: unknown = null;

async function fetchSchemaOrg(): Promise<unknown> {
	if (schemaOrgJson) return schemaOrgJson;
	console.log('Fetching schema.org definitions...');
	const response = await fetch('https://schema.org/version/latest/schemaorg-all-https.jsonld');
	schemaOrgJson = await response.json();
	console.log('Schema.org definitions loaded.');
	return schemaOrgJson;
}

async function validateJsonLd(jsonLd: string): Promise<{ errors: ValidationIssue[]; warnings: ValidationIssue[] }> {
	const html = wrapInHtml(jsonLd);
	const extractor = new WebAutoExtractor({ addLocation: true, embedSource: ['rdfa', 'microdata'] });
	const extractedData = extractor.parse(html);
	const schema = await fetchSchemaOrg();
	const validator = new Validator(schema);
	const issues = await validator.validate(extractedData) as ValidationIssue[];

	return {
		errors: issues.filter(i => i.severity === 'ERROR'),
		warnings: issues.filter(i => i.severity === 'WARNING'),
	};
}

async function main() {
	// Resolve project root (parent of tools/)
	const projectRoot = resolve(dirname(new URL(import.meta.url).pathname), '..');

	console.log('=== TypeScript Parity Check ===\n');

	const tsResults = generateTsJsonLd();
	const results: ParityResult[] = [];
	let totalParityPass = 0;
	let totalParityFail = 0;
	let totalE2eErrors = 0;
	let totalE2eWarnings = 0;

	for (const [typeName, tsData] of tsResults) {
		console.log(`\n--- ${typeName} ---`);

		// Generate PHP output for comparison
		let phpJsonLd: string;
		const phpPath = resolve(projectRoot, tsData.phpScript);
		try {
			phpJsonLd = execSync(`php ${phpPath}`, { encoding: 'utf-8', cwd: projectRoot }).trim();
		} catch (err) {
			console.log(`  PHP ERROR: ${(err as Error).message}`);
			continue;
		}

		// Compare TS and PHP output
		const diffs = compareJsonLd(tsData.json, phpJsonLd);
		const parity = diffs.length === 0;

		if (parity) {
			console.log('  Parity: MATCH');
			totalParityPass++;
		} else {
			console.log('  Parity: MISMATCH');
			for (const d of diffs) {
				console.log(`    ${d}`);
			}
			totalParityFail++;
		}

		// Validate TS output through Adobe validator
		const { errors, warnings } = await validateJsonLd(tsData.json);
		console.log(`  E2E: ${errors.length} errors, ${warnings.length} warnings`);

		if (errors.length > 0) {
			for (const e of errors) {
				console.log(`    ERROR: [${e.rootType}] ${e.issueMessage}`);
			}
		}
		if (warnings.length > 0) {
			for (const w of warnings) {
				console.log(`    WARN: [${w.rootType}] ${w.issueMessage}`);
			}
		}

		totalE2eErrors += errors.length;
		totalE2eWarnings += warnings.length;

		results.push({
			type: typeName,
			phpScript: tsData.phpScript,
			tsJsonLd: tsData.json,
			phpJsonLd,
			parity,
			parityDiffs: diffs,
			e2eErrors: errors.length,
			e2eWarnings: warnings.length,
			e2eIssues: [...errors, ...warnings],
		});
	}

	console.log('\n=== Summary ===');
	console.log(`Parity: ${totalParityPass} match, ${totalParityFail} mismatch (${tsResults.size} types tested)`);
	console.log(`E2E: ${totalE2eErrors} errors, ${totalE2eWarnings} warnings`);
	console.log(`Result: ${totalParityFail === 0 && totalE2eErrors === 0 ? 'PASS' : 'ISSUES FOUND'}`);

	// Output TS JSON-LD for inspection if there are mismatches
	if (totalParityFail > 0) {
		console.log('\n=== Parity Mismatches Detail ===');
		for (const r of results) {
			if (!r.parity) {
				console.log(`\n--- ${r.type} ---`);
				console.log('TS output:');
				console.log(r.tsJsonLd);
				console.log('PHP output:');
				console.log(r.phpJsonLd);
			}
		}
	}

	process.exit(totalParityFail > 0 || totalE2eErrors > 0 ? 1 : 0);
}

main();
