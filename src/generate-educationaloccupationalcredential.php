<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\EducationalOccupationalCredential;

$credential = new EducationalOccupationalCredential(
	credentialCategory: 'bachelor degree',
);

echo JsonLdGenerator::SchemaToJson($credential) . "\n";
