<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Accommodation;
use EvaLok\SchemaOrgJsonLd\v1\Schema\LocationFeatureSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use PHPUnit\Framework\TestCase;

class LocationFeatureSpecificationTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new LocationFeatureSpecification(name: 'WiFi', value: true);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('LocationFeatureSpecification', $data['@type']);
		$this->assertSame('WiFi', $data['name']);
		$this->assertTrue($data['value']);
	}

	public function testWithAllFields(): void
	{
		$schema = new LocationFeatureSpecification(name: 'WiFi', value: true);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('LocationFeatureSpecification', $data['@type']);
		$this->assertSame('WiFi', $data['name']);
		$this->assertTrue($data['value']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new LocationFeatureSpecification(name: 'WiFi', value: true);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertCount(4, $data);
		$this->assertTrue($data['value']);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new Accommodation(
			occupancy: new QuantitativeValue(minValue: 1, maxValue: 4, unitCode: 'C62'),
			amenityFeature: [new LocationFeatureSpecification(name: 'WiFi', value: true)],
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('LocationFeatureSpecification', $data['amenityFeature'][0]['@type']);
		$this->assertArrayNotHasKey('@context', $data['amenityFeature'][0]);
		$this->assertTrue($data['amenityFeature'][0]['value']);
	}
}

