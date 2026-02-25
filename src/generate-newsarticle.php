<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\NewsArticle;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;

$article = new NewsArticle(
	headline: 'City Council Approves $50M Green Infrastructure Plan',
	author: new Person(name: 'Sarah Greenfield'),
	datePublished: '2025-03-10T09:00:00Z',
	dateModified: '2025-03-10T14:30:00Z',
	description: 'The city council voted unanimously to approve a $50 million plan for green infrastructure improvements including urban forests, rain gardens, and permeable pavements.',
	publisher: new Organization(
		name: 'Metro Daily News',
		logo: 'https://metrodaily.example.com/logo.png',
		url: 'https://metrodaily.example.com',
	),
	image: [
		'https://example.com/photos/green-plan-1x1.jpg',
		'https://example.com/photos/green-plan-4x3.jpg',
		'https://example.com/photos/green-plan-16x9.jpg',
	],
);

echo JsonLdGenerator::SchemaToJson($article) . "\n";
