<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoShape;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Place;
use PHPUnit\Framework\TestCase;

class GeoShapeTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new GeoShape();
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('GeoShape', $data['@type']);
	}

	public function testWithAllFields(): void
	{
		$schema = new GeoShape(box: '36.8 -122.0 38.0 -120.5');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('GeoShape', $data['@type']);
		$this->assertSame('36.8 -122.0 38.0 -120.5', $data['box']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new GeoShape();
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('box', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new Place(
			name: 'Mountain Lodge',
			geo: new GeoShape(box: '36.8 -122.0 38.0 -120.5'),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('GeoShape', $data['geo']['@type']);
		$this->assertArrayNotHasKey('@context', $data['geo']);
		$this->assertSame('36.8 -122.0 38.0 -120.5', $data['geo']['box']);
	}
}
