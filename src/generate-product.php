<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Brand;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Certification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PeopleAudience;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;

$product = new Product(
	name: 'Executive Anvil',
	image: [
		'https://example.com/photos/1x1/anvil.jpg',
		'https://example.com/photos/4x3/anvil.jpg',
		'https://example.com/photos/16x9/anvil.jpg',
	],
	description: 'Sleek and deadly, this anvil is the epitome of executive style.',
	sku: '0446310786',
	offers: [
		new Offer(
			url: 'https://example.com/anvil',
			priceCurrency: 'USD',
			price: 119.99,
			itemCondition: OfferItemCondition::NewCondition,
			availability: ItemAvailability::InStock,
			priceValidUntil: '2026-12-31',
		),
	],
	brand: new Brand(name: 'ACME'),
	mpn: '925872',
	color: 'Slate Gray',
	material: 'Hardened Steel',
	pattern: 'Solid',
	size: 'Standard',
	gtin: '0012345678905',
	inProductGroupWithID: 'pg-executive-tools',
	audience: new PeopleAudience(
		suggestedGender: 'unisex',
		suggestedMinAge: 18,
	),
	hasCertification: [
		new Certification(
			name: 'ACME Safety Certified',
			issuedBy: new Organization(name: 'ACME Safety Council'),
			certificationIdentification: 'ASC-2025-0042',
		),
	],
	aggregateRating: new AggregateRating(
		ratingValue: 4.4,
		bestRating: 5,
		worstRating: 1,
		ratingCount: 89,
		reviewCount: 12,
	),
	review: [
		new Review(
			author: new Person(name: 'Fred Benson'),
			reviewRating: new Rating(
				ratingValue: 5,
				bestRating: 5,
				worstRating: 1,
			),
			reviewBody: 'This is the best anvil I have ever used. Heavy duty and well built.',
			datePublished: '2025-04-01',
			name: 'Best anvil ever',
		),
		new Review(
			author: new Person(name: 'Sara Mitchell'),
			reviewRating: new Rating(
				ratingValue: 4,
				bestRating: 5,
				worstRating: 1,
			),
			reviewBody: 'Great quality but a bit pricey for what you get.',
			datePublished: '2025-05-10',
			name: 'Good but expensive',
		),
	],
);

echo JsonLdGenerator::SchemaToJson($product) . "\n";
