<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateOffer;

$aggregateOffer = new AggregateOffer(
	lowPrice: 9.99,
	highPrice: 49.99,
	priceCurrency: 'USD',
	offerCount: 12,
);

echo JsonLdGenerator::SchemaToJson($aggregateOffer) . "\n";
