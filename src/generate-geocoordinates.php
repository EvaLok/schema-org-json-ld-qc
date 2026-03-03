<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;

$geo = new GeoCoordinates(
	latitude: 37.7749,
	longitude: -122.4194,
);

echo JsonLdGenerator::SchemaToJson($geo) . "\n";
