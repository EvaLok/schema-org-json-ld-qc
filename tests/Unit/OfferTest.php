<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Enum\MerchantReturnEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Enum\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MerchantReturnPolicy;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use PHPUnit\Framework\TestCase;

class OfferTest extends TestCase
{
	public function testMinimalOffer(): void
	{
		$offer = new Offer(
			url: 'https://example.com/products/widget-pro',
			priceCurrency: 'USD',
			price: 29.99,
			availability: ItemAvailability::InStock,
		);

		$json = JsonLdGenerator::SchemaToJson($offer);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Offer', $data['@type']);
		$this->assertSame('https://example.com/products/widget-pro', $data['url']);
		$this->assertSame('USD', $data['priceCurrency']);
		$this->assertSame(29.99, $data['price']);
		$this->assertSame('https://schema.org/InStock', $data['availability']);
	}

	public function testOfferWithAllFields(): void
	{
		$offer = new Offer(
			url: 'https://example.com/products/widget-pro',
			priceCurrency: 'USD',
			price: 29.99,
			availability: ItemAvailability::InStock,
			itemCondition: OfferItemCondition::NewCondition,
			validFrom: '2025-01-01',
			priceValidUntil: '2025-12-31',
			hasMerchantReturnPolicy: new MerchantReturnPolicy(
				applicableCountry: 'US',
				returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
				merchantReturnDays: 30,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($offer);
		$data = json_decode($json, true);

		$this->assertSame('Offer', $data['@type']);
		$this->assertSame('https://schema.org/NewCondition', $data['itemCondition']);
		$this->assertSame('2025-01-01', $data['validFrom']);
		$this->assertSame('2025-12-31', $data['priceValidUntil']);
		$this->assertSame('MerchantReturnPolicy', $data['hasMerchantReturnPolicy']['@type']);
		$this->assertSame('https://schema.org/MerchantReturnFiniteReturnWindow', $data['hasMerchantReturnPolicy']['returnPolicyCategory']);
		$this->assertSame(30, $data['hasMerchantReturnPolicy']['merchantReturnDays']);
	}

	public function testOfferNullFieldsOmitted(): void
	{
		$offer = new Offer(
			url: 'https://example.com/products/widget-pro',
			priceCurrency: 'USD',
			price: 29.99,
			availability: ItemAvailability::InStock,
		);

		$json = JsonLdGenerator::SchemaToJson($offer);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('itemCondition', $data);
		$this->assertArrayNotHasKey('shippingDetails', $data);
		$this->assertArrayNotHasKey('validFrom', $data);
		$this->assertArrayNotHasKey('priceValidUntil', $data);
		$this->assertArrayNotHasKey('priceSpecification', $data);
		$this->assertArrayNotHasKey('hasMerchantReturnPolicy', $data);
	}

	public function testOfferNestedReturnPolicyRendersCorrectly(): void
	{
		$offer = new Offer(
			url: 'https://example.com/products/widget-pro',
			priceCurrency: 'USD',
			price: 29.99,
			availability: ItemAvailability::InStock,
			hasMerchantReturnPolicy: new MerchantReturnPolicy(
				applicableCountry: 'US',
				returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnNotPermitted,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($offer);
		$data = json_decode($json, true);

		$this->assertSame('MerchantReturnPolicy', $data['hasMerchantReturnPolicy']['@type']);
		$this->assertSame('US', $data['hasMerchantReturnPolicy']['applicableCountry']);
		$this->assertSame('https://schema.org/MerchantReturnNotPermitted', $data['hasMerchantReturnPolicy']['returnPolicyCategory']);
	}
}
