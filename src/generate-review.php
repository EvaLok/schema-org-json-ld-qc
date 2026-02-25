<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;

$review = new Review(
	author: new Person(name: 'James Wilson'),
	reviewRating: new Rating(
		ratingValue: 4,
		bestRating: 5,
		worstRating: 1,
	),
	reviewBody: 'Excellent product with great build quality. Minor issues with the manual.',
	datePublished: '2025-03-15',
	name: 'Great quality, minor documentation issues',
);

echo JsonLdGenerator::SchemaToJson($review) . "\n";
