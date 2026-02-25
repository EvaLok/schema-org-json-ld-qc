<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ProfilePage;

$profilePage = new ProfilePage(
	mainEntity: new Person(
		name: 'Ada Lovelace',
		url: 'https://example.com/profiles/ada-lovelace',
		sameAs: [
			'https://twitter.com/example_ada',
			'https://www.linkedin.com/in/example-ada',
		],
	),
	dateCreated: '2024-01-15',
	dateModified: '2025-03-20',
);

echo JsonLdGenerator::SchemaToJson($profilePage) . "\n";
