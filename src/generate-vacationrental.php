<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Accommodation;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VacationRental;

$rental = new VacationRental(
	name: 'Seaside Villa Retreat',
	identifier: 'villa-seaside-42',
	image: [
		'https://example.com/villa-front.jpg',
		'https://example.com/villa-pool.jpg',
		'https://example.com/villa-interior.jpg',
	],
	latitude: 36.7783,
	longitude: -119.4179,
	containsPlace: new Accommodation(
		occupancy: new QuantitativeValue(value: 8),
		numberOfBedrooms: 4,
		numberOfBathroomsTotal: 3,
		floorSize: new QuantitativeValue(value: 250, unitCode: 'MTK'),
	),
	address: new PostalAddress(
		streetAddress: '123 Ocean Boulevard',
		addressLocality: 'Malibu',
		addressRegion: 'CA',
		postalCode: '90265',
		addressCountry: 'US',
	),
	aggregateRating: new AggregateRating(
		ratingValue: 4.9,
		bestRating: 5,
		worstRating: 1,
		reviewCount: 87,
	),
	datePublished: '2025-06-01',
	checkinTime: '15:00',
	checkoutTime: '11:00',
	description: 'A stunning oceanfront villa with private pool and panoramic sea views.',
	review: [
		new Review(
			author: 'Traveler Kate',
			reviewRating: new Rating(ratingValue: 5, bestRating: 5, worstRating: 1),
			reviewBody: 'Absolutely perfect. The views are incredible.',
		),
	],
);

echo JsonLdGenerator::SchemaToJson($rental);
