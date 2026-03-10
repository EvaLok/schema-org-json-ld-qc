<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Enum\DayOfWeek;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\FoodEstablishment;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;
use EvaLok\SchemaOrgJsonLd\v1\Schema\LocalBusiness;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OpeningHoursSpecification;
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
	openingHoursSpecification: [
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Tuesday, opens: '17:00', closes: '22:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Wednesday, opens: '17:00', closes: '22:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Thursday, opens: '17:00', closes: '22:00'),
	],
	aggregateRating: new AggregateRating(
		ratingValue: 4.7,
		bestRating: 5,
		worstRating: 1,
		reviewCount: 312,
	),
	review: new Review(
		author: 'Maria G.',
		reviewRating: new Rating(ratingValue: 5, bestRating: 5, worstRating: 1),
		reviewBody: 'The tasting menu was extraordinary. Every course was a masterpiece.',
		datePublished: '2025-11-15',
	),
	servesCuisine: 'Pacific Northwest',
	logo: 'https://example.com/golden-spoon-logo.png',
	email: 'reservations@goldenspoonbistro.example.com',
	sameAs: [
		'https://www.facebook.com/goldenspoonbistro',
		'https://www.instagram.com/goldenspoonbistro',
	],
	department: [
		new LocalBusiness(
			name: 'The Golden Spoon Bakery',
			address: new PostalAddress(
				streetAddress: '742 Evergreen Terrace, Bakery Counter',
				addressLocality: 'Portland',
				addressRegion: 'OR',
				postalCode: '97205',
				addressCountry: 'US',
			),
			telephone: '+1-503-555-0200',
		),
	],
	acceptsReservations: true,
);

echo JsonLdGenerator::SchemaToJson($food) . "\n";
