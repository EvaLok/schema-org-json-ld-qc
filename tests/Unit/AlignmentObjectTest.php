<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AlignmentObject;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Question;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Quiz;
use PHPUnit\Framework\TestCase;

class AlignmentObjectTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new AlignmentObject(
			alignmentType: 'educationalSubject',
			targetName: 'Mathematics',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('AlignmentObject', $data['@type']);
		$this->assertSame('educationalSubject', $data['alignmentType']);
		$this->assertSame('Mathematics', $data['targetName']);
	}

	public function testWithAllFields(): void
	{
		$schema = new AlignmentObject(
			alignmentType: 'educationalSubject',
			targetName: 'Mathematics',
			educationalFramework: 'Common Core',
			targetUrl: 'https://example.com/standards/math',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('Common Core', $data['educationalFramework']);
		$this->assertSame('https://example.com/standards/math', $data['targetUrl']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new AlignmentObject(
			alignmentType: 'educationalSubject',
			targetName: 'Mathematics',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('educationalFramework', $data);
		$this->assertArrayNotHasKey('targetUrl', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new Quiz(
			hasPart: [new Question(name: 'What is 2+2?')],
			educationalAlignment: new AlignmentObject(
				alignmentType: 'educationalSubject',
				targetName: 'Mathematics',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('AlignmentObject', $data['educationalAlignment']['@type']);
		$this->assertArrayNotHasKey('@context', $data['educationalAlignment']);
		$this->assertSame('Mathematics', $data['educationalAlignment']['targetName']);
	}
}

