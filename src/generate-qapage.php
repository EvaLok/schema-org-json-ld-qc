<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Answer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QAPage;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Question;

$qaPage = new QAPage(
	mainEntity: new Question(
		name: 'How do I validate JSON-LD structured data locally?',
		text: 'I want to validate my JSON-LD output against Google Rich Results requirements without using a browser. Is there a local tool?',
		answerCount: 2,
		acceptedAnswer: new Answer(
			text: 'Use @adobe/structured-data-validator — it validates against Google requirements locally with deterministic results.',
			upvoteCount: 42,
			author: new Person(name: 'DevHelper'),
			datePublished: '2025-02-20',
		),
		suggestedAnswer: [
			new Answer(
				text: 'You can also use structured-data-testing-tool for basic structural checks, though it does not validate against Google-specific requirements.',
				upvoteCount: 15,
				author: new Person(name: 'SchemaFan'),
				datePublished: '2025-02-21',
			),
		],
		author: new Person(name: 'NewDev123'),
		datePublished: '2025-02-19',
	),
);

echo JsonLdGenerator::SchemaToJson($qaPage) . "\n";
