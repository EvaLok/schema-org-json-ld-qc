<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SeekToAction;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VideoObject;
use PHPUnit\Framework\TestCase;

class SeekToActionTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new SeekToAction(
			target: 'https://example.com/video?t={seek_to_second_number}',
			startOffsetInput: 'required name=seek_to_second_number',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('SeekToAction', $data['@type']);
		$this->assertSame('https://example.com/video?t={seek_to_second_number}', $data['target']);
		$this->assertSame('required name=seek_to_second_number', $data['startOffset-input']);
	}

	public function testWithAllFields(): void
	{
		$schema = new SeekToAction(
			target: 'https://example.com/video?t={seek_to_second_number}',
			startOffsetInput: 'required name=seek_to_second_number',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('SeekToAction', $data['@type']);
		$this->assertSame('required name=seek_to_second_number', $data['startOffset-input']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new SeekToAction(
			target: 'https://example.com/video?t={seek_to_second_number}',
			startOffsetInput: 'required name=seek_to_second_number',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertCount(4, $data);
		$this->assertSame('required name=seek_to_second_number', $data['startOffset-input']);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new VideoObject(
			name: 'Demo Video',
			thumbnailUrl: ['https://example.com/thumb.jpg'],
			uploadDate: '2025-09-01',
			potentialAction: new SeekToAction(
				target: 'https://example.com/video?t={seek_to_second_number}',
				startOffsetInput: 'required name=seek_to_second_number',
			),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('SeekToAction', $data['potentialAction']['@type']);
		$this->assertArrayNotHasKey('@context', $data['potentialAction']);
		$this->assertSame('required name=seek_to_second_number', $data['potentialAction']['startOffset-input']);
	}
}

