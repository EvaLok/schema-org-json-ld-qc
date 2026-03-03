<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;

$aggregateRating = new AggregateRating(
	ratingValue: 4.5,
	bestRating: 5,
	worstRating: 1,
	ratingCount: 1250,
	reviewCount: 340,
);

echo JsonLdGenerator::SchemaToJson($aggregateRating) . "\n";
