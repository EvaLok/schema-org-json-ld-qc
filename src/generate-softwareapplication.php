<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferItemCondition;
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
	description: 'A powerful task management app for professionals.',
);

echo JsonLdGenerator::SchemaToJson($app) . "\n";
