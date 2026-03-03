<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToSection;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToStep;
use PHPUnit\Framework\TestCase;

class HowToSectionTest extends TestCase
{
	public function testMinimalHowToSection(): void
	{
		$section = new HowToSection(
			name: 'Prepare ingredients',
			itemListElement: [
				new HowToStep(text: 'Wash vegetables.'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($section);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('HowToSection', $data['@type']);
		$this->assertSame('Prepare ingredients', $data['name']);
		$this->assertSame('HowToStep', $data['itemListElement'][0]['@type']);
	}

	public function testHowToSectionWithAllFields(): void
	{
		$section = new HowToSection(
			name: 'Prepare the Ingredients',
			itemListElement: [
				new HowToStep(text: 'Wash and chop the vegetables.', name: 'Prep vegetables'),
				new HowToStep(text: 'Measure the dry ingredients.', name: 'Measure dry'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($section);
		$data = json_decode($json, true);

		$this->assertSame('HowToSection', $data['@type']);
		$this->assertCount(2, $data['itemListElement']);
		$this->assertSame('Prep vegetables', $data['itemListElement'][0]['name']);
		$this->assertSame('Measure dry', $data['itemListElement'][1]['name']);
	}

	public function testHowToSectionNullFieldsOmitted(): void
	{
		$section = new HowToSection(
			name: 'Cook',
			itemListElement: [
				new HowToStep(text: 'Bake for 20 minutes.'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($section);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('url', $data);
		$this->assertArrayNotHasKey('image', $data);
		$this->assertArrayNotHasKey('description', $data);
	}

	public function testHowToSectionNestedObjectRendersCorrectly(): void
	{
		$section = new HowToSection(
			name: 'Final steps',
			itemListElement: [
				new HowToStep(text: 'Serve warm.', name: 'Serve'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($section);
		$data = json_decode($json, true);

		$this->assertSame('HowToStep', $data['itemListElement'][0]['@type']);
		$this->assertSame('Serve warm.', $data['itemListElement'][0]['text']);
	}
}
