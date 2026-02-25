<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\Schema\WebApplication;

$app = new WebApplication(
	name: 'CloudNote Editor',
	offers: new Offer(
		url: 'https://example.com/cloudnote',
		priceCurrency: 'USD',
		price: 9.99,
		itemCondition: OfferItemCondition::NewCondition,
		availability: ItemAvailability::InStock,
	),
	aggregateRating: new AggregateRating(
		ratingValue: 4.3,
		ratingCount: 5670,
		bestRating: 5,
	),
	applicationCategory: 'ProductivityApplication',
	operatingSystem: 'All',
	description: 'A collaborative note-taking and document editing web application.',
	screenshot: 'https://example.com/screenshots/cloudnote-editor.png',
);

echo JsonLdGenerator::SchemaToJson($app) . "\n";
