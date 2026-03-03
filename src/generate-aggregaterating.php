<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Movie;

$aggregateRating = new AggregateRating(
	ratingValue: 4.5,
	bestRating: 5,
	worstRating: 1,
	ratingCount: 1250,
	reviewCount: 340,
	itemReviewed: new Movie(
		name: 'The Blueprint',
		image: 'https://example.com/the-blueprint.jpg',
	),
);

echo JsonLdGenerator::SchemaToJson($aggregateRating) . "\n";
