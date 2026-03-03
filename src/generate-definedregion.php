<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DefinedRegion;

$schema = new DefinedRegion(
	addressCountry: 'US',
	addressRegion: ['CA', 'NY'],
	postalCode: '10001',
);

echo JsonLdGenerator::SchemaToJson($schema) . "\n";
