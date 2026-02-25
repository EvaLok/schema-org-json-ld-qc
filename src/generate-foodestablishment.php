<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\FoodEstablishment;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;

$food = new FoodEstablishment(
	name: 'The Golden Spoon Bistro',
	address: new PostalAddress(
		streetAddress: '742 Evergreen Terrace',
		addressLocality: 'Portland',
		addressRegion: 'OR',
		postalCode: '97205',
		addressCountry: 'US',
	),
	url: 'https://goldenspoonbistro.example.com',
	telephone: '+1-503-555-0199',
	description: 'Farm-to-table bistro featuring seasonal Pacific Northwest cuisine.',
	image: ['https://example.com/photos/golden-spoon-exterior.jpg', 'https://example.com/photos/golden-spoon-interior.jpg'],
	priceRange: '$$$',
	geo: new GeoCoordinates(
		latitude: 45.5231,
		longitude: -122.6765,
	),
	aggregateRating: new AggregateRating(
		ratingValue: 4.7,
		reviewCount: 312,
	),
	review: new Review(
		author: 'Maria G.',
		reviewRating: new Rating(ratingValue: 5, bestRating: 5),
		reviewBody: 'The tasting menu was extraordinary. Every course was a masterpiece.',
		datePublished: '2025-11-15',
	),
	servesCuisine: 'Pacific Northwest',
	logo: 'https://example.com/golden-spoon-logo.png',
	acceptsReservations: true,
);

echo JsonLdGenerator::SchemaToJson($food) . "\n";
