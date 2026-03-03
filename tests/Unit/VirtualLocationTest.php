<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Event;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VirtualLocation;
use PHPUnit\Framework\TestCase;

class VirtualLocationTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new VirtualLocation(url: 'https://zoom.us/j/123456789');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('VirtualLocation', $data['@type']);
		$this->assertSame('https://zoom.us/j/123456789', $data['url']);
	}

	public function testWithAllFields(): void
	{
		$schema = new VirtualLocation(
			url: 'https://zoom.us/j/123456789',
			name: 'Zoom Meeting',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('Zoom Meeting', $data['name']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new VirtualLocation(url: 'https://zoom.us/j/123456789');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('name', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new Event(
			name: 'Virtual Workshop',
			startDate: '2025-09-01T09:00:00Z',
			location: new VirtualLocation(
				url: 'https://zoom.us/j/123456789',
				name: 'Zoom Meeting',
			),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('VirtualLocation', $data['location']['@type']);
		$this->assertArrayNotHasKey('@context', $data['location']);
		$this->assertSame('Zoom Meeting', $data['location']['name']);
	}
}

