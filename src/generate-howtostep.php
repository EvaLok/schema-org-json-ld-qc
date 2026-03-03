<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Clip;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToStep;

$howToStep = new HowToStep(
	text: 'Preheat the oven to 350 degrees F.',
	name: 'Preheat oven',
	url: 'https://example.com/recipe#step1',
	image: 'https://example.com/photos/step1.jpg',
	video: new Clip(
		name: 'Preheating',
		startOffset: 0,
		url: 'https://example.com/video.mp4?t=0',
		endOffset: 30,
	),
	itemListElement: [
		'Set temperature dial to 350F.',
		'Wait for preheat indicator light.',
	],
);

echo JsonLdGenerator::SchemaToJson($howToStep) . "\n";
