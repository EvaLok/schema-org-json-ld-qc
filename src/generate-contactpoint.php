<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ContactPoint;

$contactPoint = new ContactPoint(
	telephone: '+1-800-555-0199',
	email: 'support@example.com',
	contactType: 'customer service',
	areaServed: 'US',
	availableLanguage: 'English',
);

echo JsonLdGenerator::SchemaToJson($contactPoint) . "\n";
