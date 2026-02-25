<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Clip;
use EvaLok\SchemaOrgJsonLd\v1\Schema\InteractionCounter;
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
		$this->assertArrayNotHasKey('interactionStatistic', $data);
		$this->assertArrayNotHasKey('hasPart', $data);
		$this->assertArrayNotHasKey('ineligibleRegion', $data);
	}

	public function testVideoObjectWithClips(): void
	{
		$video = new VideoObject(
			name: 'Cooking Tutorial',
			thumbnailUrl: ['https://example.com/thumb.jpg'],
			uploadDate: '2025-06-01',
			hasPart: [
				new Clip(
					name: 'Prep Work',
					startOffset: 0,
					url: 'https://example.com/video?t=0',
					endOffset: 120,
				),
				new Clip(
					name: 'Cooking',
					startOffset: 120,
					url: 'https://example.com/video?t=120',
					endOffset: 600,
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($video);
		$data = json_decode($json, true);

		$this->assertCount(2, $data['hasPart']);
		$this->assertSame('Clip', $data['hasPart'][0]['@type']);
		$this->assertSame('Prep Work', $data['hasPart'][0]['name']);
		$this->assertSame(0, $data['hasPart'][0]['startOffset']);
		$this->assertSame(120, $data['hasPart'][0]['endOffset']);
		$this->assertSame('Clip', $data['hasPart'][1]['@type']);
		$this->assertSame('Cooking', $data['hasPart'][1]['name']);
		$this->assertSame(120, $data['hasPart'][1]['startOffset']);
	}

	public function testVideoObjectWithInteractionStatistic(): void
	{
		$video = new VideoObject(
			name: 'Popular Video',
			thumbnailUrl: ['https://example.com/thumb.jpg'],
			uploadDate: '2025-03-15',
			interactionStatistic: new InteractionCounter(
				interactionType: 'WatchAction',
				userInteractionCount: 50000,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($video);
		$data = json_decode($json, true);

		$this->assertSame('InteractionCounter', $data['interactionStatistic']['@type']);
		$this->assertSame('WatchAction', $data['interactionStatistic']['interactionType']);
		$this->assertSame(50000, $data['interactionStatistic']['userInteractionCount']);
	}

	public function testVideoObjectWithIneligibleRegion(): void
	{
		$video = new VideoObject(
			name: 'Restricted Video',
			thumbnailUrl: ['https://example.com/thumb.jpg'],
			uploadDate: '2025-04-01',
			ineligibleRegion: 'CN,RU',
		);

		$json = JsonLdGenerator::SchemaToJson($video);
		$data = json_decode($json, true);

		$this->assertSame('CN,RU', $data['ineligibleRegion']);
	}
}
