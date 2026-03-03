<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingRateSettings;

$schema = new ShippingRateSettings(
	orderPercentage: 10.0,
	weightPercentage: 2.5,
);

echo JsonLdGenerator::SchemaToJson($schema) . "\n";
