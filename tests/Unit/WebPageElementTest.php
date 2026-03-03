<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Article;
use EvaLok\SchemaOrgJsonLd\v1\Schema\WebPageElement;
use PHPUnit\Framework\TestCase;

class WebPageElementTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new WebPageElement(
			isAccessibleForFree: false,
			cssSelector: '.premium-content',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('WebPageElement', $data['@type']);
		$this->assertFalse($data['isAccessibleForFree']);
		$this->assertSame('.premium-content', $data['cssSelector']);
	}

	public function testWithAllFields(): void
	{
		$schema = new WebPageElement(
			isAccessibleForFree: false,
			cssSelector: '.premium-content',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('WebPageElement', $data['@type']);
		$this->assertSame('.premium-content', $data['cssSelector']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new WebPageElement(
			isAccessibleForFree: false,
			cssSelector: '.premium-content',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertCount(4, $data);
		$this->assertFalse($data['isAccessibleForFree']);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new Article(
			headline: 'Subscriber article',
			hasPart: [
				new WebPageElement(
					isAccessibleForFree: false,
					cssSelector: '.premium-content',
				),
			],
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('WebPageElement', $data['hasPart'][0]['@type']);
		$this->assertArrayNotHasKey('@context', $data['hasPart'][0]);
		$this->assertFalse($data['hasPart'][0]['isAccessibleForFree']);
	}
}
