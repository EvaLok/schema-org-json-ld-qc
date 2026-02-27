<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MobileApplication;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Enum\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;

$app = new MobileApplication(
	name: 'FitTracker',
	offers: new Offer(
		url: 'https://play.google.com/store/apps/details?id=com.example.fittracker',
		priceCurrency: 'USD',
		price: 0,
		itemCondition: OfferItemCondition::NewCondition,
		availability: ItemAvailability::InStock,
	),
	aggregateRating: new AggregateRating(
		ratingValue: 4.5,
		ratingCount: 32100,
		bestRating: 5,
		worstRating: 1,
	),
	applicationCategory: 'HealthApplication',
	operatingSystem: 'Android 10+',
	datePublished: '2025-03-15',
	review: new Review(
		author: new Person(name: 'FitnessGuru'),
		reviewRating: new Rating(
			ratingValue: 5,
			bestRating: 5,
			worstRating: 1,
		),
		reviewBody: 'Best fitness tracking app I have ever used. Accurate heart rate monitoring.',
	),
	description: 'Track your workouts, heart rate, and daily steps with precision.',
);

echo JsonLdGenerator::SchemaToJson($app) . "\n";
