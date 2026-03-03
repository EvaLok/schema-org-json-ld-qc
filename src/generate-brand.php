<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Brand;

$brand = new Brand(
	name: 'Acme Corporation',
	description: 'Leading provider of quality goods since 1920',
);

echo JsonLdGenerator::SchemaToJson($brand) . "\n";
