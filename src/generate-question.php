<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Answer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Comment;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Question;

$question = new Question(
	name: 'How do I reset my password?',
	acceptedAnswer: new Answer(text: 'Go to Settings > Security > Reset Password'),
	suggestedAnswer: [
		new Answer(text: 'Try the forgot password link on the login page'),
		new Answer(text: 'Contact support for a manual reset'),
	],
	answerCount: 3,
	text: 'I cannot remember my password and need to regain access to my account.',
	upvoteCount: 42,
	author: new Person(name: 'Sarah Tech'),
	datePublished: '2025-01-15',
	dateModified: '2025-03-20',
	comment: [
		new Comment(text: 'Great question, I had the same issue!', author: new Person(name: 'Fellow User')),
	],
	eduQuestionType: null,
);

echo JsonLdGenerator::SchemaToJson($question) . "\n";
