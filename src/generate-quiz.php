<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Answer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Question;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Quiz;

$quiz = new Quiz(
	hasPart: [
		new Question(
			name: 'What is the chemical symbol for water?',
			acceptedAnswer: new Answer(text: 'H2O'),
			eduQuestionType: 'Multiple choice',
		),
		new Question(
			name: 'What planet is closest to the Sun?',
			acceptedAnswer: new Answer(text: 'Mercury'),
			eduQuestionType: 'Multiple choice',
		),
	],
	about: 'General Science',
	name: 'Basic Science Quiz',
	description: 'Test your knowledge of basic science concepts.',
);

echo JsonLdGenerator::SchemaToJson($quiz) . "\n";
