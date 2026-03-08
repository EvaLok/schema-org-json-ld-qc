<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Enum\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SoftwareApplication;

$app = new SoftwareApplication(
	name: 'TaskFlow Pro',
	offers: new Offer(
		url: 'https://example.com/taskflow-pro',
		priceCurrency: 'USD',
		price: 4.99,
		itemCondition: OfferItemCondition::NewCondition,
		availability: ItemAvailability::InStock,
	),
	aggregateRating: new AggregateRating(
		ratingValue: 4.6,
		ratingCount: 8250,
		bestRating: 5,
		worstRating: 1,
	),
	applicationCategory: 'BusinessApplication',
	operatingSystem: 'Android, iOS',
	datePublished: '2024-06-15',
	review: new Review(
		author: 'App Reviewer Weekly',
		reviewRating: new Rating(
			ratingValue: 5,
			bestRating: 5,
		),
		reviewBody: 'An indispensable productivity tool for managing complex projects.',
	),
	description: 'A powerful task management app for professionals.',
	screenshot: 'https://example.com/taskflow-pro/screenshot.png',
);

echo JsonLdGenerator::SchemaToJson($app) . "\n";
