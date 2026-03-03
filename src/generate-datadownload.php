<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DataDownload;

$dataDownload = new DataDownload(
	contentUrl: 'https://example.com/data/climate-2025.csv',
	encodingFormat: 'text/csv',
);

echo JsonLdGenerator::SchemaToJson($dataDownload) . "\n";
