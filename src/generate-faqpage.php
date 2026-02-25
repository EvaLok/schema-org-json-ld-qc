<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Answer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\FAQPage;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Question;

$faq = new FAQPage(
	mainEntity: [
		new Question(
			name: 'What is JSON-LD?',
			acceptedAnswer: new Answer(
				text: 'JSON-LD is a method of encoding Linked Data using JSON. It allows data to be serialized in a way that is familiar to developers.',
			),
		),
		new Question(
			name: 'Why should I use structured data on my website?',
			acceptedAnswer: new Answer(
				text: 'Structured data helps search engines understand your content better and can enable rich results in search, such as FAQ snippets, recipe cards, and product listings.',
			),
		),
		new Question(
			name: 'How do I validate my structured data?',
			acceptedAnswer: new Answer(
				text: 'You can use the Google Rich Results Test at search.google.com/test/rich-results to validate your structured data and see which rich result types it supports.',
			),
		),
	],
);

echo JsonLdGenerator::SchemaToJson($faq) . "\n";
