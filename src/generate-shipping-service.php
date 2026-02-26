<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\Enum\FulfillmentTypeEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DefinedRegion;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MonetaryAmount;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ServicePeriod;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingConditions;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingService;

$service = new ShippingService(
	shippingConditions: new ShippingConditions(
		shippingDestination: new DefinedRegion(
			addressCountry: 'US',
			addressRegion: [],
		),
		shippingRate: new MonetaryAmount(
			value: 5.99,
			currency: 'USD',
		),
		transitTime: new ServicePeriod(
			duration: new QuantitativeValue(
				minValue: 3,
				maxValue: 7,
				unitCode: 'DAY',
			),
		),
	),
	name: 'Standard Shipping',
	description: 'Standard ground shipping within the US.',
	fulfillmentType: FulfillmentTypeEnumeration::FulfillmentTypeDelivery,
	handlingTime: new ServicePeriod(
		duration: new QuantitativeValue(
			minValue: 0,
			maxValue: 1,
			unitCode: 'DAY',
		),
		cutoffTime: '14:00:00-05:00',
	),
);

echo JsonLdGenerator::SchemaToJson($service) . "\n";
