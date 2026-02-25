<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateOffer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Brand;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;

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
);

echo JsonLdGenerator::SchemaToJson($product) . "\n";
