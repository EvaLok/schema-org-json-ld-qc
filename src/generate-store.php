<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OpeningHoursSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DayOfWeek;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Store;

$store = new Store(
	name: 'GreenLeaf Garden Center',
	address: new PostalAddress(
		streetAddress: '500 Nursery Road',
		addressLocality: 'Austin',
		addressRegion: 'TX',
		postalCode: '78745',
		addressCountry: 'US',
	),
	url: 'https://greenleafgarden.example.com',
	telephone: '+1-512-555-0150',
	description: 'Family-owned garden center specializing in native Texas plants and organic gardening supplies.',
	image: ['https://example.com/photos/greenleaf-storefront.jpg'],
	priceRange: '$$',
	geo: new GeoCoordinates(
		latitude: 30.2087,
		longitude: -97.7796,
	),
	openingHoursSpecification: [
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Monday, opens: '08:00', closes: '18:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Tuesday, opens: '08:00', closes: '18:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Wednesday, opens: '08:00', closes: '18:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Thursday, opens: '08:00', closes: '18:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Friday, opens: '08:00', closes: '18:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Saturday, opens: '08:00', closes: '18:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Sunday, opens: '10:00', closes: '16:00'),
	],
	aggregateRating: new AggregateRating(
		ratingValue: 4.8,
		reviewCount: 156,
	),
	review: new Review(
		author: 'Carlos D.',
		reviewRating: new Rating(ratingValue: 5, bestRating: 5),
		reviewBody: 'Incredible selection of native plants. The staff really knows their stuff.',
		datePublished: '2025-10-20',
	),
	logo: 'https://example.com/greenleaf-logo.png',
);

echo JsonLdGenerator::SchemaToJson($store) . "\n";
