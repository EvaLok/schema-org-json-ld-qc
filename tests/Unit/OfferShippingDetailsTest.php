<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DefinedRegion;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MonetaryAmount;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferShippingDetails;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingDeliveryTime;
use PHPUnit\Framework\TestCase;

class OfferShippingDetailsTest extends TestCase
{
	public function testMinimalOfferShippingDetails(): void
	{
		$schema = new OfferShippingDetails(
			shippingDestination: new DefinedRegion(addressCountry: 'US'),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('OfferShippingDetails', $data['@type']);
		$this->assertSame('DefinedRegion', $data['shippingDestination']['@type']);
		$this->assertSame('US', $data['shippingDestination']['addressCountry']);
	}

	public function testWithAllFields(): void
	{
		$schema = new OfferShippingDetails(
			shippingDestination: new DefinedRegion(
				addressCountry: 'US',
				addressRegion: ['CA', 'NY'],
				postalCode: '10001',
			),
			shippingRate: new MonetaryAmount(
				currency: 'USD',
				value: 5.99,
				minValue: 3.99,
				maxValue: 9.99,
				unitText: 'per order',
			),
			deliveryTime: new ShippingDeliveryTime(
				handlingTime: new QuantitativeValue(minValue: 0, maxValue: 1, unitCode: 'DAY'),
				transitTime: new QuantitativeValue(minValue: 3, maxValue: 5, unitCode: 'DAY'),
			),
			doesNotShip: false,
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('OfferShippingDetails', $data['@type']);
		$this->assertSame(['CA', 'NY'], $data['shippingDestination']['addressRegion']);
		$this->assertSame('10001', $data['shippingDestination']['postalCode']);
		$this->assertSame('MonetaryAmount', $data['shippingRate']['@type']);
		$this->assertSame(5.99, $data['shippingRate']['value']);
		$this->assertSame(3.99, $data['shippingRate']['minValue']);
		$this->assertSame(9.99, $data['shippingRate']['maxValue']);
		$this->assertSame('per order', $data['shippingRate']['unitText']);
		$this->assertSame('ShippingDeliveryTime', $data['deliveryTime']['@type']);
		$this->assertEquals(0.0, $data['deliveryTime']['handlingTime']['minValue']);
		$this->assertEquals(1.0, $data['deliveryTime']['handlingTime']['maxValue']);
		$this->assertEquals(3.0, $data['deliveryTime']['transitTime']['minValue']);
		$this->assertEquals(5.0, $data['deliveryTime']['transitTime']['maxValue']);
		$this->assertFalse($data['doesNotShip']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new OfferShippingDetails(
			shippingDestination: new DefinedRegion(addressCountry: 'US'),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('shippingRate', $data);
		$this->assertArrayNotHasKey('deliveryTime', $data);
		$this->assertArrayNotHasKey('doesNotShip', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$offer = new Offer(
			url: 'https://example.com/products/widget-pro',
			priceCurrency: 'USD',
			price: 29.99,
			availability: ItemAvailability::InStock,
			shippingDetails: [
				new OfferShippingDetails(
				shippingDestination: new DefinedRegion(addressCountry: 'US'),
				shippingRate: new MonetaryAmount(currency: 'USD', value: 5.99),
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($offer);
		$data = json_decode($json, true);

		$this->assertSame('OfferShippingDetails', $data['shippingDetails'][0]['@type']);
		$this->assertSame('US', $data['shippingDetails'][0]['shippingDestination']['addressCountry']);
		$this->assertSame(5.99, $data['shippingDetails'][0]['shippingRate']['value']);
	}
}
