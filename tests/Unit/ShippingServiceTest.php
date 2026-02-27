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

class ShippingServiceTest extends TestCase
{
	public function testBasicShippingService(): void
	{
		if (!class_exists(ShippingService::class)) {
			$this->markTestSkipped('ShippingService is not available in the installed schema-org-json-ld version.');
		}

		$service = new ShippingService(
			shippingConditions: new ShippingConditions(
				shippingRate: new MonetaryAmount(
					value: 5.99,
					currency: 'USD',
				),
			),
		);

		$json = JsonLdGenerator::SchemaToJson($service);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('ShippingService', $data['@type']);
		$this->assertSame('ShippingConditions', $data['shippingConditions']['@type']);
		$this->assertSame('MonetaryAmount', $data['shippingConditions']['shippingRate']['@type']);
		$this->assertEquals(5.99, $data['shippingConditions']['shippingRate']['value']);
		$this->assertSame('USD', $data['shippingConditions']['shippingRate']['currency']);
	}

	public function testShippingServiceWithDestinationAndTransit(): void
	{
		if (!class_exists(ShippingService::class)) {
			$this->markTestSkipped('ShippingService is not available in the installed schema-org-json-ld version.');
		}

		$service = new ShippingService(
			shippingConditions: new ShippingConditions(
				shippingDestination: new DefinedRegion(
					addressCountry: 'US',
					addressRegion: ['CA', 'NY', 'TX'],
				),
				shippingRate: new MonetaryAmount(
					value: 0,
					currency: 'USD',
				),
				transitTime: new ServicePeriod(
					duration: new QuantitativeValue(value: 5),
				),
			),
			name: 'Free Shipping',
		);

		$json = JsonLdGenerator::SchemaToJson($service);
		$data = json_decode($json, true);

		$this->assertSame('ShippingService', $data['@type']);
		$this->assertSame('Free Shipping', $data['name']);
		$this->assertSame('DefinedRegion', $data['shippingConditions']['shippingDestination']['@type']);
		$this->assertSame(['CA', 'NY', 'TX'], $data['shippingConditions']['shippingDestination']['addressRegion']);
		$this->assertSame('ServicePeriod', $data['shippingConditions']['transitTime']['@type']);
	}

	public function testShippingServiceWithHandlingTime(): void
	{
		if (!class_exists(ShippingService::class)) {
			$this->markTestSkipped('ShippingService is not available in the installed schema-org-json-ld version.');
		}

		$service = new ShippingService(
			shippingConditions: new ShippingConditions(
				shippingRate: new MonetaryAmount(
					value: 9.99,
					currency: 'USD',
				),
			),
			name: 'Express',
			handlingTime: new ServicePeriod(
				duration: new QuantitativeValue(value: 1),
				cutoffTime: '14:00:00-05:00',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($service);
		$data = json_decode($json, true);

		$this->assertSame('ShippingService', $data['@type']);
		$this->assertSame('ServicePeriod', $data['handlingTime']['@type']);
		$this->assertSame('QuantitativeValue', $data['handlingTime']['duration']['@type']);
		$this->assertSame('14:00:00-05:00', $data['handlingTime']['cutoffTime']);
	}

	public function testOptionalFieldsOmitted(): void
	{
		if (!class_exists(ShippingService::class)) {
			$this->markTestSkipped('ShippingService is not available in the installed schema-org-json-ld version.');
		}

		$service = new ShippingService(
			shippingConditions: new ShippingConditions(),
		);

		$json = JsonLdGenerator::SchemaToJson($service);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('name', $data);
		$this->assertArrayNotHasKey('description', $data);
		$this->assertArrayNotHasKey('fulfillmentType', $data);
		$this->assertArrayNotHasKey('handlingTime', $data);
		$this->assertArrayNotHasKey('validForMemberTier', $data);
	}
}
