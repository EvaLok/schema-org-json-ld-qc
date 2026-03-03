<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Article;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SpeakableSpecification;
use PHPUnit\Framework\TestCase;

class SpeakableSpecificationTest extends TestCase
{
	public function testMinimalSpeakableSpecification(): void
	{
		$speakableSpecification = new SpeakableSpecification();

		$json = JsonLdGenerator::SchemaToJson($speakableSpecification);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('SpeakableSpecification', $data['@type']);
	}

	public function testWithAllFields(): void
	{
		$speakableSpecification = new SpeakableSpecification(
			cssSelector: ['.article-headline', '.article-summary'],
			xpath: ['//h1', '//p[@class="summary"]'],
		);

		$json = JsonLdGenerator::SchemaToJson($speakableSpecification);
		$data = json_decode($json, true);

		$this->assertSame('SpeakableSpecification', $data['@type']);
		$this->assertSame(['.article-headline', '.article-summary'], $data['cssSelector']);
		$this->assertSame(['//h1', '//p[@class="summary"]'], $data['xpath']);
	}

	public function testNullFieldsOmitted(): void
	{
		$speakableSpecification = new SpeakableSpecification();

		$json = JsonLdGenerator::SchemaToJson($speakableSpecification);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('cssSelector', $data);
		$this->assertArrayNotHasKey('xpath', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$article = new Article(
			headline: 'Understanding Tidal Patterns in the North Sea',
			speakable: new SpeakableSpecification(
				cssSelector: ['.article-headline', '.article-summary'],
			),
		);

		$json = JsonLdGenerator::SchemaToJson($article);
		$data = json_decode($json, true);

		$this->assertSame('Article', $data['@type']);
		$this->assertSame('SpeakableSpecification', $data['speakable']['@type']);
		$this->assertSame(['.article-headline', '.article-summary'], $data['speakable']['cssSelector']);
		$this->assertArrayNotHasKey('@context', $data['speakable']);
	}
}
