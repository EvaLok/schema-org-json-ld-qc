<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Comment;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;

$comment = new Comment(
	text: 'This is really helpful, thank you!',
	author: new Person(name: 'Jane Reader'),
	datePublished: '2025-03-01',
);

echo JsonLdGenerator::SchemaToJson($comment) . "\n";
