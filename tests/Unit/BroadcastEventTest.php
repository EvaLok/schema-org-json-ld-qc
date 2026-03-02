<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BroadcastEvent;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VideoObject;
use PHPUnit\Framework\TestCase;

class BroadcastEventTest extends TestCase
{
	public function testMinimalBroadcastEvent(): void
	{
		$broadcastEvent = new BroadcastEvent(isLiveBroadcast: true);

		$json = JsonLdGenerator::SchemaToJson($broadcastEvent);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('BroadcastEvent', $data['@type']);
		$this->assertTrue($data['isLiveBroadcast']);
	}

	public function testBroadcastEventWithAllFields(): void
	{
		$broadcastEvent = new BroadcastEvent(
			isLiveBroadcast: true,
			startDate: '2025-06-15T20:00:00+00:00',
			endDate: '2025-06-15T22:00:00+00:00',
		);

		$json = JsonLdGenerator::SchemaToJson($broadcastEvent);
		$data = json_decode($json, true);

		$this->assertSame('BroadcastEvent', $data['@type']);
		$this->assertSame('2025-06-15T20:00:00+00:00', $data['startDate']);
		$this->assertSame('2025-06-15T22:00:00+00:00', $data['endDate']);
	}

	public function testBroadcastEventNullFieldsOmitted(): void
	{
		$broadcastEvent = new BroadcastEvent(isLiveBroadcast: false);

		$json = JsonLdGenerator::SchemaToJson($broadcastEvent);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('startDate', $data);
		$this->assertArrayNotHasKey('endDate', $data);
	}

	public function testBroadcastEventNestedInVideoObjectRendersCorrectly(): void
	{
		$video = new VideoObject(
			name: 'Weekly Product Update',
			thumbnailUrl: ['https://example.com/photos/update-thumb.jpg'],
			uploadDate: '2025-06-15T18:00:00+00:00',
			publication: new BroadcastEvent(
				isLiveBroadcast: true,
				startDate: '2025-06-15T20:00:00+00:00',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($video);
		$data = json_decode($json, true);

		$this->assertSame('BroadcastEvent', $data['publication']['@type']);
		$this->assertTrue($data['publication']['isLiveBroadcast']);
		$this->assertSame('2025-06-15T20:00:00+00:00', $data['publication']['startDate']);
	}
}
