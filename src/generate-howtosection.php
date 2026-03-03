<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToSection;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToStep;

$howToSection = new HowToSection(
	name: 'Prepare the Ingredients',
	itemListElement: [
		new HowToStep(text: 'Wash and chop the vegetables.', name: 'Prep vegetables'),
		new HowToStep(text: 'Measure the dry ingredients.', name: 'Measure dry'),
	],
);

echo JsonLdGenerator::SchemaToJson($howToSection) . "\n";
