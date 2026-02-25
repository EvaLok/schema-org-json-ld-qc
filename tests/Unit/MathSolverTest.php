<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MathSolver;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SolveMathAction;
use PHPUnit\Framework\TestCase;

class MathSolverTest extends TestCase
{
	public function testMinimalMathSolver(): void
	{
		$solver = new MathSolver(
			url: 'https://math.example.com/solver',
			usageInfo: 'https://math.example.com/terms',
			potentialAction: new SolveMathAction(
				target: 'https://math.example.com/solve',
				mathExpressionInput: 'text',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($solver);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertIsArray($data['@type']);
		$this->assertContains('MathSolver', $data['@type']);
		$this->assertContains('LearningResource', $data['@type']);
		$this->assertSame('https://math.example.com/solver', $data['url']);
		$this->assertSame('https://math.example.com/terms', $data['usageInfo']);
		$this->assertSame('SolveMathAction', $data['potentialAction']['@type']);
		$this->assertSame('https://math.example.com/solve', $data['potentialAction']['target']);
	}

	public function testMathSolverWithFullDetails(): void
	{
		$solver = new MathSolver(
			url: 'https://mathway.example.com',
			usageInfo: 'https://mathway.example.com/privacy',
			potentialAction: [
				new SolveMathAction(
					target: 'https://mathway.example.com/solve-algebra',
					mathExpressionInput: 'text',
					eduQuestionType: ['Polynomial', 'Linear Equation'],
				),
				new SolveMathAction(
					target: 'https://mathway.example.com/solve-calculus',
					mathExpressionInput: 'latex',
					eduQuestionType: 'Integral',
				),
			],
			name: 'MathWay Solver',
			inLanguage: 'en',
			learningResourceType: 'Math Solver',
			assesses: ['Algebra', 'Calculus'],
		);

		$json = JsonLdGenerator::SchemaToJson($solver);
		$data = json_decode($json, true);

		$this->assertSame('MathWay Solver', $data['name']);
		$this->assertSame('en', $data['inLanguage']);
		$this->assertSame('Math Solver', $data['learningResourceType']);
		$this->assertCount(2, $data['assesses']);
		$this->assertSame('Algebra', $data['assesses'][0]);
		$this->assertCount(2, $data['potentialAction']);
	}

	public function testSolveMathActionPropertyMap(): void
	{
		$action = new SolveMathAction(
			target: 'https://example.com/solve',
			mathExpressionInput: 'text',
		);

		$json = JsonLdGenerator::SchemaToJson($action);
		$data = json_decode($json, true);

		$this->assertSame('SolveMathAction', $data['@type']);
		$this->assertArrayHasKey('mathExpression-input', $data, 'PROPERTY_MAP should rename mathExpressionInput to mathExpression-input');
		$this->assertSame('text', $data['mathExpression-input']);
		$this->assertArrayNotHasKey('mathExpressionInput', $data, 'Original property name should not appear');
	}

	public function testMathSolverNullFieldsOmitted(): void
	{
		$solver = new MathSolver(
			url: 'https://example.com/solver',
			usageInfo: 'https://example.com/terms',
			potentialAction: new SolveMathAction(
				target: 'https://example.com/solve',
				mathExpressionInput: 'text',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($solver);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('name', $data);
		$this->assertArrayNotHasKey('inLanguage', $data);
		$this->assertArrayNotHasKey('learningResourceType', $data);
		$this->assertArrayNotHasKey('assesses', $data);
	}
}
