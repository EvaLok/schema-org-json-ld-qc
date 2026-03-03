<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\NutritionInformation;

$nutritionInformation = new NutritionInformation(
	calories: '250 calories',
	fatContent: '8 grams',
	proteinContent: '12 grams',
	carbohydrateContent: '35 grams',
);

echo JsonLdGenerator::SchemaToJson($nutritionInformation) . "\n";
