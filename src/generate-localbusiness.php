<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Enum\DayOfWeek;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;
use EvaLok\SchemaOrgJsonLd\v1\Schema\LocalBusiness;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OpeningHoursSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;

$biz = new LocalBusiness(
	name: "Dave's Steak House",
	address: new PostalAddress(
		streetAddress: '148 W 51st St',
		addressLocality: 'New York',
		addressRegion: 'NY',
		postalCode: '10019',
		addressCountry: 'US',
	),
	url: 'https://davessteakhouse.example.com',
	telephone: '+1-212-555-0100',
	description: 'Classic American steakhouse in the heart of Midtown Manhattan.',
	image: ['https://example.com/photos/daves-exterior.jpg'],
	priceRange: '$$$',
	geo: new GeoCoordinates(
		latitude: 40.7614,
		longitude: -73.9826,
	),
	openingHoursSpecification: [
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Monday, opens: '11:00', closes: '22:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Tuesday, opens: '11:00', closes: '22:00'),
		new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Wednesday, opens: '11:00', closes: '22:00'),
	],
	aggregateRating: new AggregateRating(
		ratingValue: 4.4,
		bestRating: 5,
		worstRating: 1,
		reviewCount: 267,
	),
	review: new Review(
		author: 'James T.',
		reviewRating: new Rating(ratingValue: 5, bestRating: 5, worstRating: 1),
		reviewBody: 'Best steak I have had in years. The service was impeccable.',
		datePublished: '2025-02-01',
	),
	servesCuisine: 'American',
	logo: 'https://example.com/daves-logo.png',
	email: 'info@davessteakhouse.example.com',
	sameAs: [
		'https://www.facebook.com/davessteakhouse',
		'https://twitter.com/davessteakhouse',
	],
	department: [
		new LocalBusiness(
			name: 'Dave\'s Steak House Bar',
			address: new PostalAddress(
				streetAddress: '148 W 51st St, Lower Level',
				addressLocality: 'New York',
				addressRegion: 'NY',
				postalCode: '10019',
				addressCountry: 'US',
			),
			telephone: '+1-212-555-0101',
		),
		new LocalBusiness(
			name: 'Dave\'s Steak House Private Dining',
			address: new PostalAddress(
				streetAddress: '148 W 51st St, 2nd Floor',
				addressLocality: 'New York',
				addressRegion: 'NY',
				postalCode: '10019',
				addressCountry: 'US',
			),
			telephone: '+1-212-555-0102',
		),
	],
);

echo JsonLdGenerator::SchemaToJson($biz) . "\n";
