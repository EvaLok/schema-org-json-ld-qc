<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Clip;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToStep;
use PHPUnit\Framework\TestCase;

class HowToStepTest extends TestCase
{
	public function testMinimalHowToStep(): void
	{
		$step = new HowToStep(
			text: 'Mix flour and water.',
		);

		$json = JsonLdGenerator::SchemaToJson($step);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('HowToStep', $data['@type']);
		$this->assertSame('Mix flour and water.', $data['text']);
	}

	public function testHowToStepWithAllFields(): void
	{
		$step = new HowToStep(
			text: 'Preheat the oven to 350 degrees F.',
			name: 'Preheat oven',
			url: 'https://example.com/recipe#step1',
			image: 'https://example.com/photos/step1.jpg',
			video: new Clip(
				name: 'Preheating',
				startOffset: 0,
				url: 'https://example.com/video.mp4?t=0',
				endOffset: 30,
			),
			itemListElement: [
				'Set temperature dial to 350F.',
				'Wait for preheat indicator light.',
			],
		);

		$json = JsonLdGenerator::SchemaToJson($step);
		$data = json_decode($json, true);

		$this->assertSame('HowToStep', $data['@type']);
		$this->assertSame('Preheat oven', $data['name']);
		$this->assertSame('https://example.com/recipe#step1', $data['url']);
		$this->assertSame('https://example.com/photos/step1.jpg', $data['image']);
		$this->assertSame('Clip', $data['video']['@type']);
		$this->assertSame('Preheating', $data['video']['name']);
		$this->assertSame(2, count($data['itemListElement']));
	}

	public function testHowToStepNullFieldsOmitted(): void
	{
		$step = new HowToStep(
			text: 'Boil water.',
		);

		$json = JsonLdGenerator::SchemaToJson($step);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('name', $data);
		$this->assertArrayNotHasKey('url', $data);
		$this->assertArrayNotHasKey('image', $data);
		$this->assertArrayNotHasKey('video', $data);
		$this->assertArrayNotHasKey('itemListElement', $data);
	}

	public function testHowToStepNestedObjectRendersCorrectly(): void
	{
		$step = new HowToStep(
			text: 'Let the dough rise.',
			video: new Clip(
				name: 'Rising dough',
				startOffset: 45,
				url: 'https://example.com/video.mp4?t=45',
				endOffset: 120,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($step);
		$data = json_decode($json, true);

		$this->assertSame('Clip', $data['video']['@type']);
		$this->assertSame(45, $data['video']['startOffset']);
		$this->assertSame(120, $data['video']['endOffset']);
	}
}
