<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ImageObject;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use PHPUnit\Framework\TestCase;

class ImageObjectTest extends TestCase
{
	public function testMinimalImageObject(): void
	{
		$image = new ImageObject(contentUrl: 'https://example.com/photos/sunset.jpg');

		$json = JsonLdGenerator::SchemaToJson($image);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('ImageObject', $data['@type']);
		$this->assertSame('https://example.com/photos/sunset.jpg', $data['contentUrl']);
	}

	public function testFullImageObject(): void
	{
		$image = new ImageObject(
			contentUrl: 'https://example.com/photos/sunset.jpg',
			url: 'https://example.com/gallery/sunset',
			name: 'Sunset over the Pacific',
			caption: 'A vivid sunset captured from Ocean Beach',
			description: 'High-resolution photograph of a sunset over the Pacific Ocean',
			width: '1920',
			height: '1080',
			license: 'https://creativecommons.org/licenses/by/4.0/',
			acquireLicensePage: 'https://example.com/licensing',
			creditText: 'Photo by Jane Doe',
			copyrightNotice: 'Copyright 2025 Jane Doe',
			creator: new Organization(name: 'Example Photography'),
			datePublished: '2025-06-15',
			uploadDate: '2025-06-16',
		);

		$json = JsonLdGenerator::SchemaToJson($image);
		$data = json_decode($json, true);

		$this->assertSame('ImageObject', $data['@type']);
		$this->assertSame('https://example.com/gallery/sunset', $data['url']);
		$this->assertSame('Sunset over the Pacific', $data['name']);
		$this->assertSame('Photo by Jane Doe', $data['creditText']);
		$this->assertSame('Organization', $data['creator']['@type']);
		$this->assertSame('Example Photography', $data['creator']['name']);
	}

	public function testImageObjectNullFieldsOmitted(): void
	{
		$image = new ImageObject(contentUrl: 'https://example.com/photos/minimal.jpg');

		$json = JsonLdGenerator::SchemaToJson($image);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('url', $data);
		$this->assertArrayNotHasKey('name', $data);
		$this->assertArrayNotHasKey('caption', $data);
		$this->assertArrayNotHasKey('creator', $data);
		$this->assertArrayNotHasKey('uploadDate', $data);
	}

	public function testImageObjectNestedCreatorRendersCorrectly(): void
	{
		$image = new ImageObject(
			contentUrl: 'https://example.com/photos/bridge.jpg',
			creator: new Organization(name: 'Bay Photo Collective'),
		);

		$json = JsonLdGenerator::SchemaToJson($image);
		$data = json_decode($json, true);

		$this->assertSame('ImageObject', $data['@type']);
		$this->assertSame('Organization', $data['creator']['@type']);
		$this->assertSame('Bay Photo Collective', $data['creator']['name']);
		$this->assertArrayNotHasKey('@context', $data['creator']);
	}
}
