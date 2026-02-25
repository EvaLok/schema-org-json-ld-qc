<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BlogPosting;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;

$post = new BlogPosting(
	headline: 'Understanding Dependency Injection in PHP',
	author: new Person(name: 'Marcus Rivera'),
	datePublished: '2025-11-20',
	dateModified: '2025-12-01',
	description: 'A practical guide to dependency injection patterns in modern PHP applications.',
	publisher: new Organization(
		name: 'PHP Weekly',
		logo: 'https://example.com/phpweekly-logo.png',
	),
	image: ['https://example.com/di-php.jpg', 'https://example.com/di-diagram.png'],
);

echo JsonLdGenerator::SchemaToJson($post);
