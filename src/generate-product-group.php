<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Brand;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Certification;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PeopleAudience;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ProductGroup;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;

$groupRef = new ProductGroup(
	name: 'Classic Oxford Shirt Collection',
	productGroupID: 'oxford-shirts',
	variesBy: ['https://schema.org/color', 'https://schema.org/size'],
	url: 'https://example.com/shirts/oxford',
);

$blueShirt = new Product(
	name: 'Classic Oxford Shirt - Blue',
	image: ['https://example.com/shirts/blue.jpg'],
	description: 'Classic oxford button-down shirt in blue.',
	sku: 'SHIRT-OX-BLUE-M',
	offers: [
		new Offer(
			url: 'https://example.com/shirts/blue',
			priceCurrency: 'USD',
			price: 89.00,
			availability: ItemAvailability::InStock,
			priceValidUntil: '2026-12-31',
		),
	],
	brand: new Brand(name: 'ClassicWear'),
	mpn: 'OX-BLUE-M',
	material: '100% Premium Cotton',
	pattern: 'Solid',
	inProductGroupWithID: 'oxford-shirts',
	subjectOf: 'https://example.com/shirt-review',
	audience: new PeopleAudience(suggestedGender: 'unisex', suggestedMinAge: 16),
	hasCertification: [
		new Certification(
			name: 'OEKO-TEX Standard 100',
			issuedBy: new Organization(name: 'OEKO-TEX Association'),
			certificationIdentification: 'OT-12345',
		),
	],
	aggregateRating: new AggregateRating(
		ratingValue: 4.6,
		bestRating: 5,
		worstRating: 1,
		ratingCount: 156,
	),
	review: [
		new Review(
			author: new Person(name: 'Sam T.'),
			reviewRating: new Rating(ratingValue: 5, bestRating: 5, worstRating: 1),
			reviewBody: 'Perfect fit and great quality cotton.',
			datePublished: '2025-08-15',
		),
	],
	color: 'Blue',
	size: 'M',
	gtin: '0012345678905',
	isVariantOf: $groupRef,
);

$whiteShirt = new Product(
	name: 'Classic Oxford Shirt - White',
	image: ['https://example.com/shirts/white.jpg'],
	description: 'Classic oxford button-down shirt in white.',
	sku: 'SHIRT-OX-WHITE-M',
	offers: [
		new Offer(
			url: 'https://example.com/shirts/white',
			priceCurrency: 'USD',
			price: 89.00,
			availability: ItemAvailability::InStock,
			priceValidUntil: '2026-12-31',
		),
	],
	brand: new Brand(name: 'ClassicWear'),
	mpn: 'OX-WHITE-M',
	material: '100% Premium Cotton',
	pattern: 'Solid',
	inProductGroupWithID: 'oxford-shirts',
	subjectOf: 'https://example.com/shirt-review',
	audience: new PeopleAudience(suggestedGender: 'unisex', suggestedMinAge: 16),
	hasCertification: [
		new Certification(
			name: 'OEKO-TEX Standard 100',
			issuedBy: new Organization(name: 'OEKO-TEX Association'),
			certificationIdentification: 'OT-12345',
		),
	],
	aggregateRating: new AggregateRating(
		ratingValue: 4.5,
		bestRating: 5,
		worstRating: 1,
		ratingCount: 142,
	),
	review: [
		new Review(
			author: new Person(name: 'Jordan K.'),
			reviewRating: new Rating(ratingValue: 5, bestRating: 5, worstRating: 1),
			reviewBody: 'Crisp look and comfortable all day.',
			datePublished: '2025-09-03',
		),
	],
	color: 'White',
	size: 'M',
	gtin: '0012345678912',
	isVariantOf: $groupRef,
);

$productGroup = new ProductGroup(
	name: 'Classic Oxford Shirt Collection',
	productGroupID: 'oxford-shirts',
	variesBy: ['https://schema.org/color', 'https://schema.org/size'],
	hasVariant: [$blueShirt, $whiteShirt],
	url: 'https://example.com/shirts/oxford',
	description: 'Our classic oxford button-down shirts, available in multiple colors and sizes.',
	brand: new Brand(name: 'ClassicWear'),
	aggregateRating: new AggregateRating(
		ratingValue: 4.7,
		bestRating: 5,
		worstRating: 1,
		ratingCount: 312,
		reviewCount: 89,
	),
);

echo JsonLdGenerator::SchemaToJson($productGroup) . "\n";
