<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;

$schema = new QuantitativeValue(
	minValue: 1,
	maxValue: 10,
	unitCode: 'DAY',
	value: 5,
);

echo JsonLdGenerator::SchemaToJson($schema) . "\n";
