<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingDeliveryTime;

$schema = new ShippingDeliveryTime(
	handlingTime: new QuantitativeValue(minValue: 0, maxValue: 1, unitCode: 'DAY'),
	transitTime: new QuantitativeValue(minValue: 3, maxValue: 5, unitCode: 'DAY'),
);

echo JsonLdGenerator::SchemaToJson($schema) . "\n";
