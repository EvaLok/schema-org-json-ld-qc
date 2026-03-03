<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MonetaryAmount;

$schema = new MonetaryAmount(
	currency: 'USD',
	value: 29.99,
	minValue: 19.99,
	maxValue: 39.99,
	unitText: 'per order',
);

echo JsonLdGenerator::SchemaToJson($schema) . "\n";
