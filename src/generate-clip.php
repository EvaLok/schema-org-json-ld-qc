<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Clip;

$clip = new Clip(
	name: 'Introduction to the Topic',
	startOffset: 0,
	url: 'https://example.com/video/tutorial?t=0',
	endOffset: 120,
);

echo JsonLdGenerator::SchemaToJson($clip) . "\n";
