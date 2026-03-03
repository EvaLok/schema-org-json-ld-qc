<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PropertyValue;

$propertyValue = new PropertyValue(
	name: 'color',
	value: 'Midnight Blue',
);

echo JsonLdGenerator::SchemaToJson($propertyValue) . "\n";
