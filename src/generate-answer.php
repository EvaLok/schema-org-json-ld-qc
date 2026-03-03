<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Answer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;

$answer = new Answer(
	text: 'Navigate to Settings > Security > Reset Password and follow the prompts.',
	author: new Person(name: 'Support Agent Mike'),
	url: 'https://example.com/answers/12345',
	upvoteCount: 87,
	datePublished: '2025-01-16',
	dateModified: '2025-02-10',
);

echo JsonLdGenerator::SchemaToJson($answer) . "\n";
