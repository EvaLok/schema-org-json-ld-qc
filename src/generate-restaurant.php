<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OpeningHoursSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DayOfWeek;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Restaurant;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;

$restaurant = new Restaurant(
	name: 'Bella Napoli Trattoria',
	address: new PostalAddress(
		streetAddress: '88 Little Italy Lane',
		addressLocality: 'New York',
		addressRegion: 'NY',
		postalCode: '10013',
		addressCountry: 'US',
	),
	url: 'https://bellanapoli.example.com',
	telephone: '+1-212-555-0188',
	description: 'Authentic Neapolitan pizza and pasta in the heart of Little Italy.',
	image: ['https://example.com/photos/bella-napoli.jpg'],
	priceRange: '$$',
	geo: new GeoCoordinates(
		latitude: 40.7191,
		longitude: -73.9973,
	),
	openingHoursSpecification: [
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Monday, opens: '11:00', closes: '22:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Tuesday, opens: '11:00', closes: '22:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Wednesday, opens: '11:00', closes: '22:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Thursday, opens: '11:00', closes: '22:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Friday, opens: '11:00', closes: '22:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Saturday, opens: '10:00', closes: '23:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Sunday, opens: '10:00', closes: '23:00'),
	],
	aggregateRating: new AggregateRating(
		ratingValue: 4.5,
		bestRating: 5,
		worstRating: 1,
		reviewCount: 487,
	),
	review: [
		new Review(
			author: 'Anthony R.',
			reviewRating: new Rating(ratingValue: 5, bestRating: 5, worstRating: 1),
			reviewBody: 'Best margherita pizza outside of Naples. The crust is perfection.',
			datePublished: '2025-12-01',
		),
		new Review(
			author: 'Lisa M.',
			reviewRating: new Rating(ratingValue: 4, bestRating: 5, worstRating: 1),
			reviewBody: 'Great food and atmosphere, but can get crowded on weekends.',
			datePublished: '2025-11-20',
		),
	],
	menu: 'https://bellanapoli.example.com/menu',
	servesCuisine: 'Italian',
	logo: 'https://example.com/bella-napoli-logo.png',
	acceptsReservations: 'https://bellanapoli.example.com/reservations',
);

echo JsonLdGenerator::SchemaToJson($restaurant) . "\n";
