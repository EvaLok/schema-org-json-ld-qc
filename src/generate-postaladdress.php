<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;

$address = new PostalAddress(
	streetAddress: '123 Main St',
	addressLocality: 'San Francisco',
	addressRegion: 'CA',
	postalCode: '94102',
	addressCountry: 'US',
	postOfficeBoxNumber: null,
);

echo JsonLdGenerator::SchemaToJson($address) . "\n";
