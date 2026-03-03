<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\InteractionCounter;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VideoObject;
use PHPUnit\Framework\TestCase;

class InteractionCounterTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new InteractionCounter(
			interactionType: 'https://schema.org/WatchAction',
			userInteractionCount: 15000,
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('InteractionCounter', $data['@type']);
		$this->assertSame('https://schema.org/WatchAction', $data['interactionType']);
		$this->assertSame(15000, $data['userInteractionCount']);
	}

	public function testWithAllFields(): void
	{
		$schema = new InteractionCounter(
			interactionType: 'https://schema.org/WatchAction',
			userInteractionCount: 15000,
			interactionService: 'https://youtube.com',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://youtube.com', $data['interactionService']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new InteractionCounter(
			interactionType: 'https://schema.org/WatchAction',
			userInteractionCount: 15000,
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('interactionService', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new VideoObject(
			name: 'Demo Video',
			thumbnailUrl: ['https://example.com/thumb.jpg'],
			uploadDate: '2025-09-01',
			interactionStatistic: new InteractionCounter(
				interactionType: 'https://schema.org/WatchAction',
				userInteractionCount: 15000,
			),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('InteractionCounter', $data['interactionStatistic']['@type']);
		$this->assertArrayNotHasKey('@context', $data['interactionStatistic']);
		$this->assertSame(15000, $data['interactionStatistic']['userInteractionCount']);
	}
}

