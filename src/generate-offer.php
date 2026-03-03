<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Enum\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;

$offer = new Offer(
	url: 'https://example.com/products/widget-pro',
	priceCurrency: 'USD',
	price: 29.99,
	availability: ItemAvailability::InStock,
	itemCondition: OfferItemCondition::NewCondition,
	shippingDetails: null,
	validFrom: '2025-01-01',
	priceValidUntil: '2025-12-31',
	priceSpecification: null,
	hasMerchantReturnPolicy: null,
);

echo JsonLdGenerator::SchemaToJson($offer) . "\n";
