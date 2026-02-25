<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Brand;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;

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
		),
	],
	brand: new Brand(name: 'ACME'),
	mpn: '925872',
);

echo JsonLdGenerator::SchemaToJson($product) . "\n";
