<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateOffer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Brand;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Certification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PeopleAudience;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;

$product = new Product(
	name: 'Wireless Bluetooth Headphones',
	image: [
		'https://example.com/photos/headphones-front.jpg',
		'https://example.com/photos/headphones-side.jpg',
	],
	description: 'Premium noise-cancelling wireless headphones with 30-hour battery life.',
	sku: 'WBH-PRO-2025',
	offers: new AggregateOffer(
		lowPrice: 149.99,
		priceCurrency: 'USD',
		highPrice: 249.99,
		offerCount: 8,
	),
	brand: new Brand(name: 'AudioTech'),
	aggregateRating: new AggregateRating(
		ratingValue: 4.6,
		bestRating: 5,
		worstRating: 1,
		ratingCount: 234,
		reviewCount: 45,
	),
	mpn: 'AT-WBH-PRO',
	color: 'Matte Black',
	material: 'Premium ABS Plastic',
	pattern: 'Solid',
	size: 'One Size',
	gtin: '0098765432101',
	audience: new PeopleAudience(suggestedGender: 'unisex', suggestedMinAge: 13),
	review: [
		new Review(
			author: new Person(name: 'Alex Chen'),
			reviewRating: new Rating(ratingValue: 5, bestRating: 5, worstRating: 1),
			reviewBody: 'Incredible noise cancellation and battery life.',
			datePublished: '2025-06-15',
			name: 'Best headphones ever',
		),
	],
	hasCertification: [
		new Certification(
			name: 'Bluetooth 5.3 Certified',
			issuedBy: new Organization(name: 'Bluetooth SIG'),
			certificationIdentification: 'BT53-WBH-2025',
		),
	],
);

echo JsonLdGenerator::SchemaToJson($product) . "\n";
