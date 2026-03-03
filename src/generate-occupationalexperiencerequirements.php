<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OccupationalExperienceRequirements;

$experienceRequirements = new OccupationalExperienceRequirements(
	monthsOfExperience: 24,
);

echo JsonLdGenerator::SchemaToJson($experienceRequirements) . "\n";
