<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Clip;
use PHPUnit\Framework\TestCase;

class ClipTest extends TestCase
{
	public function testMinimalClip(): void
	{
		$clip = new Clip(
			name: 'Chapter 1',
			startOffset: 0,
			url: 'https://example.com/video?t=0',
		);

		$json = JsonLdGenerator::SchemaToJson($clip);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Clip', $data['@type']);
		$this->assertSame('Chapter 1', $data['name']);
		$this->assertSame(0, $data['startOffset']);
		$this->assertSame('https://example.com/video?t=0', $data['url']);
		$this->assertArrayNotHasKey('endOffset', $data);
	}

	public function testClipWithEndOffset(): void
	{
		$clip = new Clip(
			name: 'Mixing the Ingredients',
			startOffset: 180,
			url: 'https://example.com/video/recipe?t=180',
			endOffset: 420,
		);

		$json = JsonLdGenerator::SchemaToJson($clip);
		$data = json_decode($json, true);

		$this->assertSame('Clip', $data['@type']);
		$this->assertSame('Mixing the Ingredients', $data['name']);
		$this->assertSame(180, $data['startOffset']);
		$this->assertSame('https://example.com/video/recipe?t=180', $data['url']);
		$this->assertSame(420, $data['endOffset']);
	}

	public function testClipNullEndOffsetOmitted(): void
	{
		$clip = new Clip(
			name: 'Final Chapter',
			startOffset: 3600,
			url: 'https://example.com/video?t=3600',
		);

		$json = JsonLdGenerator::SchemaToJson($clip);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('endOffset', $data);
	}
}
