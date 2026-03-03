<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Certification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;

$certification = new Certification(
	name: 'ISO 9001:2015 Quality Management',
	issuedBy: new Organization(name: 'International Organization for Standardization'),
	certificationIdentification: 'ISO-9001-2025-00042',
);

echo JsonLdGenerator::SchemaToJson($certification) . "\n";
