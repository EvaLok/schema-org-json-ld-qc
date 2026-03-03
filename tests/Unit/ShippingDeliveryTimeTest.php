<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DefinedRegion;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferShippingDetails;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingDeliveryTime;
use PHPUnit\Framework\TestCase;

class ShippingDeliveryTimeTest extends TestCase
{
	public function testMinimalShippingDeliveryTime(): void
	{
		$schema = new ShippingDeliveryTime(
			handlingTime: new QuantitativeValue(minValue: 0, maxValue: 1, unitCode: 'DAY'),
			transitTime: new QuantitativeValue(minValue: 3, maxValue: 5, unitCode: 'DAY'),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('ShippingDeliveryTime', $data['@type']);
		$this->assertSame('QuantitativeValue', $data['handlingTime']['@type']);
		$this->assertSame('QuantitativeValue', $data['transitTime']['@type']);
	}

	public function testWithAllFields(): void
	{
		$schema = new ShippingDeliveryTime(
			handlingTime: new QuantitativeValue(
				value: 1,
				minValue: 0,
				maxValue: 2,
				unitCode: 'DAY',
			),
			transitTime: new QuantitativeValue(
				value: 4,
				minValue: 3,
				maxValue: 5,
				unitCode: 'DAY',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('ShippingDeliveryTime', $data['@type']);
		$this->assertEquals(1.0, $data['handlingTime']['value']);
		$this->assertEquals(0.0, $data['handlingTime']['minValue']);
		$this->assertEquals(2.0, $data['handlingTime']['maxValue']);
		$this->assertSame('DAY', $data['handlingTime']['unitCode']);
		$this->assertEquals(4.0, $data['transitTime']['value']);
		$this->assertEquals(3.0, $data['transitTime']['minValue']);
		$this->assertEquals(5.0, $data['transitTime']['maxValue']);
		$this->assertSame('DAY', $data['transitTime']['unitCode']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new ShippingDeliveryTime(
			handlingTime: new QuantitativeValue(minValue: 0, maxValue: 1, unitCode: 'DAY'),
			transitTime: new QuantitativeValue(minValue: 3, maxValue: 5, unitCode: 'DAY'),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('value', $data['handlingTime']);
		$this->assertArrayNotHasKey('value', $data['transitTime']);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new OfferShippingDetails(
			shippingDestination: new DefinedRegion(addressCountry: 'US'),
			deliveryTime: new ShippingDeliveryTime(
				handlingTime: new QuantitativeValue(minValue: 0, maxValue: 1, unitCode: 'DAY'),
				transitTime: new QuantitativeValue(minValue: 3, maxValue: 5, unitCode: 'DAY'),
			),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('ShippingDeliveryTime', $data['deliveryTime']['@type']);
		$this->assertEquals(0.0, $data['deliveryTime']['handlingTime']['minValue']);
		$this->assertEquals(5.0, $data['deliveryTime']['transitTime']['maxValue']);
	}
}
