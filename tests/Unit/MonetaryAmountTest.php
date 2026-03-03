<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DefinedRegion;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MonetaryAmount;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferShippingDetails;
use PHPUnit\Framework\TestCase;

class MonetaryAmountTest extends TestCase
{
	public function testMinimalMonetaryAmount(): void
	{
		$schema = new MonetaryAmount(currency: 'USD');

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('MonetaryAmount', $data['@type']);
		$this->assertSame('USD', $data['currency']);
	}

	public function testWithAllFields(): void
	{
		$schema = new MonetaryAmount(
			currency: 'USD',
			value: 29.99,
			minValue: 19.99,
			maxValue: 39.99,
			unitText: 'per order',
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('MonetaryAmount', $data['@type']);
		$this->assertSame('USD', $data['currency']);
		$this->assertSame(29.99, $data['value']);
		$this->assertSame(19.99, $data['minValue']);
		$this->assertSame(39.99, $data['maxValue']);
		$this->assertSame('per order', $data['unitText']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new MonetaryAmount(currency: 'USD');

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('value', $data);
		$this->assertArrayNotHasKey('minValue', $data);
		$this->assertArrayNotHasKey('maxValue', $data);
		$this->assertArrayNotHasKey('unitText', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new OfferShippingDetails(
			shippingDestination: new DefinedRegion(addressCountry: 'US'),
			shippingRate: new MonetaryAmount(currency: 'USD', value: 5.99),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('MonetaryAmount', $data['shippingRate']['@type']);
		$this->assertSame('USD', $data['shippingRate']['currency']);
		$this->assertSame(5.99, $data['shippingRate']['value']);
	}
}
