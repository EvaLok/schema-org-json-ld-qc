<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PeopleAudience;

$peopleAudience = new PeopleAudience(
	suggestedGender: 'unisex',
	suggestedMinAge: 18,
	suggestedMaxAge: 65,
);

echo JsonLdGenerator::SchemaToJson($peopleAudience) . "\n";
