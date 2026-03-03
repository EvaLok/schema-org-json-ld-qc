<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;

$rating = new Rating(
	ratingValue: 4,
	bestRating: 5,
	worstRating: 1,
);

echo JsonLdGenerator::SchemaToJson($rating) . "\n";
