<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingDeliveryTime;
use PHPUnit\Framework\TestCase;

class QuantitativeValueTest extends TestCase
{
	public function testMinimalQuantitativeValue(): void
	{
		$schema = new QuantitativeValue();

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('QuantitativeValue', $data['@type']);
	}

	public function testWithAllFields(): void
	{
		$schema = new QuantitativeValue(
			value: 5,
			unitCode: 'DAY',
			minValue: 1,
			maxValue: 10,
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('QuantitativeValue', $data['@type']);
		$this->assertEquals(5.0, $data['value']);
		$this->assertSame('DAY', $data['unitCode']);
		$this->assertEquals(1.0, $data['minValue']);
		$this->assertEquals(10.0, $data['maxValue']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new QuantitativeValue();

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('value', $data);
		$this->assertArrayNotHasKey('unitCode', $data);
		$this->assertArrayNotHasKey('minValue', $data);
		$this->assertArrayNotHasKey('maxValue', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new ShippingDeliveryTime(
			handlingTime: new QuantitativeValue(
				minValue: 0,
				maxValue: 1,
				unitCode: 'DAY',
			),
			transitTime: new QuantitativeValue(
				minValue: 3,
				maxValue: 5,
				unitCode: 'DAY',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('QuantitativeValue', $data['handlingTime']['@type']);
		$this->assertEquals(0.0, $data['handlingTime']['minValue']);
		$this->assertEquals(5.0, $data['transitTime']['maxValue']);
	}
}
