<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingConditions;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingRateSettings;
use PHPUnit\Framework\TestCase;

class ShippingRateSettingsTest extends TestCase
{
	public function testMinimalShippingRateSettings(): void
	{
		$schema = new ShippingRateSettings();

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('ShippingRateSettings', $data['@type']);
	}

	public function testWithAllFields(): void
	{
		$schema = new ShippingRateSettings(
			orderPercentage: 10.0,
			weightPercentage: 2.5,
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('ShippingRateSettings', $data['@type']);
		$this->assertEquals(10.0, $data['orderPercentage']);
		$this->assertEquals(2.5, $data['weightPercentage']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new ShippingRateSettings();

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('orderPercentage', $data);
		$this->assertArrayNotHasKey('weightPercentage', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new ShippingConditions(
			shippingRate: new ShippingRateSettings(
				orderPercentage: 15.0,
				weightPercentage: 3.5,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('ShippingRateSettings', $data['shippingRate']['@type']);
		$this->assertEquals(15.0, $data['shippingRate']['orderPercentage']);
		$this->assertEquals(3.5, $data['shippingRate']['weightPercentage']);
	}
}
