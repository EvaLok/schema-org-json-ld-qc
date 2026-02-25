<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MathSolver;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SolveMathAction;

$solver = new MathSolver(
	url: 'https://math.example.com/solver',
	usageInfo: 'https://math.example.com/terms',
	potentialAction: [
		new SolveMathAction(
			target: 'https://math.example.com/solve-algebra',
			mathExpressionInput: 'text',
			eduQuestionType: ['Polynomial', 'Linear Equation'],
		),
		new SolveMathAction(
			target: 'https://math.example.com/solve-calculus',
			mathExpressionInput: 'latex',
			eduQuestionType: 'Integral',
		),
	],
	name: 'MathWay Solver',
	inLanguage: 'en',
	learningResourceType: 'Math Solver',
	assesses: ['Algebra', 'Calculus'],
);

echo JsonLdGenerator::SchemaToJson($solver);
