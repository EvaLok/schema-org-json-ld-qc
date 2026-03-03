<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DefinedRegion;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MonetaryAmount;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferShippingDetails;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingDeliveryTime;

$schema = new OfferShippingDetails(
	shippingDestination: new DefinedRegion(addressCountry: 'US'),
	shippingRate: new MonetaryAmount(
		currency: 'USD',
		value: 5.99,
	),
	deliveryTime: new ShippingDeliveryTime(
		handlingTime: new QuantitativeValue(minValue: 0, maxValue: 1, unitCode: 'DAY'),
		transitTime: new QuantitativeValue(minValue: 3, maxValue: 5, unitCode: 'DAY'),
	),
);

echo JsonLdGenerator::SchemaToJson($schema) . "\n";
