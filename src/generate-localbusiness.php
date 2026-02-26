<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;
use EvaLok\SchemaOrgJsonLd\v1\Schema\LocalBusiness;
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
);

echo JsonLdGenerator::SchemaToJson($biz) . "\n";
