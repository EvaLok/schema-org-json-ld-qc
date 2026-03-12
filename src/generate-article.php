<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Article;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SpeakableSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\WebPageElement;

$article = new Article(
	headline: 'Understanding Tidal Patterns in the North Sea',
	author: new Person(name: 'Dr. Sarah Chen'),
	datePublished: '2025-01-15',
	dateModified: '2025-02-01',
	description: 'A comprehensive guide to tidal patterns and their impact on coastal ecosystems.',
	publisher: new Organization(
		name: 'Nature Weekly',
		logo: 'https://example.com/logo.png',
	),
	speakable: new SpeakableSpecification(
		cssSelector: ['.article-headline', '.article-summary'],
		xpath: ['/html/head/title'],
	),
	isAccessibleForFree: true,
	hasPart: [
		new WebPageElement(
			isAccessibleForFree: true,
			cssSelector: '.article-body',
		),
		new WebPageElement(
			isAccessibleForFree: false,
			cssSelector: '.premium-content',
		),
	],
	image: [
		'https://example.com/tidal-1x1.jpg',
		'https://example.com/tidal-4x3.jpg',
		'https://example.com/tidal-16x9.jpg',
	],
);

echo JsonLdGenerator::SchemaToJson($article) . "\n";
