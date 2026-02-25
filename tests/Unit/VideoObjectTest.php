<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VideoObject;
use PHPUnit\Framework\TestCase;

class VideoObjectTest extends TestCase
{
	public function testMinimalVideoObject(): void
	{
		$video = new VideoObject(
			name: 'How to Make Sourdough Bread',
			thumbnailUrl: ['https://example.com/photos/sourdough-thumb.jpg'],
			uploadDate: '2025-02-05T08:00:00+08:00',
		);

		$json = JsonLdGenerator::SchemaToJson($video);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('VideoObject', $data['@type']);
		$this->assertSame('How to Make Sourdough Bread', $data['name']);
		$this->assertSame(['https://example.com/photos/sourdough-thumb.jpg'], $data['thumbnailUrl']);
		$this->assertSame('2025-02-05T08:00:00+08:00', $data['uploadDate']);
	}

	public function testFullVideoObject(): void
	{
		$video = new VideoObject(
			name: 'Advanced PHP Design Patterns',
			thumbnailUrl: [
				'https://example.com/video/thumb-1x1.jpg',
				'https://example.com/video/thumb-4x3.jpg',
				'https://example.com/video/thumb-16x9.jpg',
			],
			uploadDate: '2025-01-20T12:00:00Z',
			description: 'Learn advanced design patterns in PHP 8.x including Strategy, Observer, and Decorator.',
			contentUrl: 'https://example.com/video/php-patterns.mp4',
			embedUrl: 'https://example.com/embed/php-patterns',
			duration: 'PT45M30S',
			expires: '2026-01-20T12:00:00Z',
			regionsAllowed: 'US,CA,GB',
		);

		$json = JsonLdGenerator::SchemaToJson($video);
		$data = json_decode($json, true);

		$this->assertSame('VideoObject', $data['@type']);
		$this->assertCount(3, $data['thumbnailUrl']);
		$this->assertSame('Learn advanced design patterns in PHP 8.x including Strategy, Observer, and Decorator.', $data['description']);
		$this->assertSame('https://example.com/video/php-patterns.mp4', $data['contentUrl']);
		$this->assertSame('https://example.com/embed/php-patterns', $data['embedUrl']);
		$this->assertSame('PT45M30S', $data['duration']);
		$this->assertSame('2026-01-20T12:00:00Z', $data['expires']);
		$this->assertSame('US,CA,GB', $data['regionsAllowed']);
	}

	public function testOptionalFieldsOmitted(): void
	{
		$video = new VideoObject(
			name: 'Quick Tip',
			thumbnailUrl: ['https://example.com/thumb.jpg'],
			uploadDate: '2025-03-01',
		);

		$json = JsonLdGenerator::SchemaToJson($video);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('description', $data);
		$this->assertArrayNotHasKey('contentUrl', $data);
		$this->assertArrayNotHasKey('embedUrl', $data);
		$this->assertArrayNotHasKey('duration', $data);
		$this->assertArrayNotHasKey('expires', $data);
		$this->assertArrayNotHasKey('regionsAllowed', $data);
	}
}
