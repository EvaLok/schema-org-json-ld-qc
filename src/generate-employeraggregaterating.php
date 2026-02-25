<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\EmployerAggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;

$rating = new EmployerAggregateRating(
	itemReviewed: new Organization(name: 'TechCorp Industries'),
	ratingValue: 4.2,
	ratingCount: 1847,
	reviewCount: 523,
	bestRating: 5,
	worstRating: 1,
);

echo JsonLdGenerator::SchemaToJson($rating) . "\n";
