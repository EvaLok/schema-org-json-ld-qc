<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MathSolver;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SolveMathAction;
use PHPUnit\Framework\TestCase;

class SolveMathActionTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new SolveMathAction(
			target: 'https://example.com/solve',
			mathExpressionInput: 'required name=math_expression_string',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('SolveMathAction', $data['@type']);
		$this->assertSame('https://example.com/solve', $data['target']);
		$this->assertSame('required name=math_expression_string', $data['mathExpression-input']);
	}

	public function testWithAllFields(): void
	{
		$schema = new SolveMathAction(
			target: 'https://example.com/solve',
			mathExpressionInput: 'required name=math_expression_string',
			eduQuestionType: 'word-problem',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('word-problem', $data['eduQuestionType']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new SolveMathAction(
			target: 'https://example.com/solve',
			mathExpressionInput: 'required name=math_expression_string',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('eduQuestionType', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new MathSolver(
			url: 'https://example.com/math-solver',
			usageInfo: 'Use this for algebra',
			potentialAction: new SolveMathAction(
				target: 'https://example.com/solve',
				mathExpressionInput: 'required name=math_expression_string',
			),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('SolveMathAction', $data['potentialAction']['@type']);
		$this->assertArrayNotHasKey('@context', $data['potentialAction']);
		$this->assertSame('required name=math_expression_string', $data['potentialAction']['mathExpression-input']);
	}
}

