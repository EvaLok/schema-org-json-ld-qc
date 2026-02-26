<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Brand;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ProductGroup;

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
		),
	],
	color: 'Blue',
	size: 'M',
	gtin: '0012345678905',
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
		),
	],
	color: 'White',
	size: 'M',
	gtin: '0012345678912',
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
