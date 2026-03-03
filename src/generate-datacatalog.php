<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DataCatalog;

$dataCatalog = new DataCatalog(
	name: 'Open Government Data Catalog',
);

echo JsonLdGenerator::SchemaToJson($dataCatalog) . "\n";
