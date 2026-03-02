<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Place;
use PHPUnit\Framework\TestCase;

class GeoCoordinatesTest extends TestCase
{
	public function testMinimalGeoCoordinates(): void
	{
		$geo = new GeoCoordinates(
			latitude: 37.7749,
			longitude: -122.4194,
		);

		$json = JsonLdGenerator::SchemaToJson($geo);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('GeoCoordinates', $data['@type']);
		$this->assertSame(37.7749, $data['latitude']);
		$this->assertSame(-122.4194, $data['longitude']);
	}

	public function testFullGeoCoordinates(): void
	{
		$geo = new GeoCoordinates(
			latitude: 48.8584,
			longitude: 2.2945,
		);

		$json = JsonLdGenerator::SchemaToJson($geo);
		$data = json_decode($json, true);

		$this->assertSame('GeoCoordinates', $data['@type']);
		$this->assertSame(48.8584, $data['latitude']);
		$this->assertSame(2.2945, $data['longitude']);
	}

	public function testGeoCoordinatesOmitUnspecifiedFields(): void
	{
		$geo = new GeoCoordinates(
			latitude: 40.6892,
			longitude: -74.0445,
		);

		$json = JsonLdGenerator::SchemaToJson($geo);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('name', $data);
		$this->assertArrayNotHasKey('address', $data);
	}

	public function testGeoCoordinatesNestedInPlaceRenderCorrectly(): void
	{
		$place = new Place(
			name: 'Coit Tower',
			geo: new GeoCoordinates(
				latitude: 37.8024,
				longitude: -122.4058,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($place);
		$data = json_decode($json, true);

		$this->assertSame('Place', $data['@type']);
		$this->assertSame('GeoCoordinates', $data['geo']['@type']);
		$this->assertSame(37.8024, $data['geo']['latitude']);
		$this->assertSame(-122.4058, $data['geo']['longitude']);
		$this->assertArrayNotHasKey('@context', $data['geo']);
	}
}
