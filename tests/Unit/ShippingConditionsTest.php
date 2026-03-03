<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DefinedRegion;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MonetaryAmount;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ServicePeriod;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingConditions;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingService;
use PHPUnit\Framework\TestCase;

class ShippingConditionsTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new ShippingConditions();
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('ShippingConditions', $data['@type']);
	}

	public function testWithAllFields(): void
	{
		$schema = new ShippingConditions(
			doesNotShip: false,
			numItems: new QuantitativeValue(value: 1, unitCode: 'C62'),
			orderValue: new MonetaryAmount(currency: 'USD', value: 50.0),
			shippingDestination: new DefinedRegion(addressCountry: 'US'),
			shippingRate: new MonetaryAmount(currency: 'USD', value: 7.99),
			transitTime: new ServicePeriod(
				duration: new QuantitativeValue(value: 5, unitCode: 'DAY'),
			),
			weight: new QuantitativeValue(value: 2.0, unitCode: 'KGM'),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertFalse($data['doesNotShip']);
		$this->assertSame('US', $data['shippingDestination']['addressCountry']);
		$this->assertEquals(7.99, $data['shippingRate']['value']);
		$this->assertEquals(5.0, $data['transitTime']['duration']['value']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new ShippingConditions();
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('doesNotShip', $data);
		$this->assertArrayNotHasKey('numItems', $data);
		$this->assertArrayNotHasKey('orderValue', $data);
		$this->assertArrayNotHasKey('shippingDestination', $data);
		$this->assertArrayNotHasKey('shippingOrigin', $data);
		$this->assertArrayNotHasKey('seasonalOverride', $data);
		$this->assertArrayNotHasKey('shippingRate', $data);
		$this->assertArrayNotHasKey('transitTime', $data);
		$this->assertArrayNotHasKey('weight', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new ShippingService(
			shippingConditions: new ShippingConditions(
				shippingDestination: new DefinedRegion(addressCountry: 'US'),
				shippingRate: new MonetaryAmount(currency: 'USD', value: 7.99),
				transitTime: new ServicePeriod(
					duration: new QuantitativeValue(value: 5, unitCode: 'DAY'),
				),
			),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('ShippingConditions', $data['shippingConditions']['@type']);
		$this->assertArrayNotHasKey('@context', $data['shippingConditions']);
		$this->assertSame('US', $data['shippingConditions']['shippingDestination']['addressCountry']);
	}
}

