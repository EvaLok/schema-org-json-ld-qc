<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SpeakableSpecification;

$speakableSpecification = new SpeakableSpecification(
	cssSelector: ['.article-headline', '.article-summary'],
);

echo JsonLdGenerator::SchemaToJson($speakableSpecification) . "\n";
