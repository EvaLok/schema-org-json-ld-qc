<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\UnitPriceSpecification;
use PHPUnit\Framework\TestCase;

class UnitPriceSpecificationTest extends TestCase
{
	public function testBasicUnitPriceSpecification(): void
	{
		$ups = new UnitPriceSpecification(
			price: 4.99,
			priceCurrency: 'USD',
		);
		$json = JsonLdGenerator::SchemaToJson($ups);
		$data = json_decode($json, true);

		$this->assertSame('UnitPriceSpecification', $data['@type']);
		$this->assertEquals(4.99, $data['price']);
		$this->assertSame('USD', $data['priceCurrency']);
		$this->assertArrayNotHasKey('priceType', $data);
		$this->assertArrayNotHasKey('referenceQuantity', $data);
	}

	public function testUnitPriceSpecificationWithReferenceQuantity(): void
	{
		$ups = new UnitPriceSpecification(
			price: 2.49,
			priceCurrency: 'EUR',
			priceType: 'https://schema.org/SalePrice',
			referenceQuantity: new QuantitativeValue(
				value: 100,
				unitCode: 'GRM',
			),
		);
		$json = JsonLdGenerator::SchemaToJson($ups);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/SalePrice', $data['priceType']);
		$this->assertSame('QuantitativeValue', $data['referenceQuantity']['@type']);
		$this->assertSame(100, $data['referenceQuantity']['value']);
		$this->assertSame('GRM', $data['referenceQuantity']['unitCode']);
	}

	public function testUnitPriceSpecificationWithMembershipPoints(): void
	{
		$ups = new UnitPriceSpecification(
			price: 9.99,
			priceCurrency: 'USD',
			membershipPointsEarned: 100.0,
		);
		$json = JsonLdGenerator::SchemaToJson($ups);
		$data = json_decode($json, true);

		$this->assertEquals(100, $data['membershipPointsEarned']);
	}

	public function testOfferWithPriceSpecification(): void
	{
		$product = new Product(
			name: 'Bulk Coffee',
			image: ['https://example.com/coffee.jpg'],
			description: 'Premium bulk coffee by weight.',
			sku: 'COFFEE-BULK',
			offers: [
				new Offer(
					url: 'https://example.com/coffee',
					priceCurrency: 'USD',
					price: 12.99,
					availability: ItemAvailability::InStock,
					priceSpecification: new UnitPriceSpecification(
						price: 12.99,
						priceCurrency: 'USD',
						referenceQuantity: new QuantitativeValue(
							value: 1,
							unitCode: 'KGM',
						),
					),
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertSame('UnitPriceSpecification', $data['offers'][0]['priceSpecification']['@type']);
		$this->assertEquals(12.99, $data['offers'][0]['priceSpecification']['price']);
		$this->assertSame('QuantitativeValue', $data['offers'][0]['priceSpecification']['referenceQuantity']['@type']);
		$this->assertSame(1, $data['offers'][0]['priceSpecification']['referenceQuantity']['value']);
		$this->assertSame('KGM', $data['offers'][0]['priceSpecification']['referenceQuantity']['unitCode']);
	}

	public function testOfferPriceSpecificationOmittedWhenNull(): void
	{
		$product = new Product(
			name: 'Simple Product',
			image: ['https://example.com/simple.jpg'],
			description: 'A simple product without price specification.',
			sku: 'SMP-001',
			offers: [
				new Offer(
					url: 'https://example.com/simple',
					priceCurrency: 'USD',
					price: 9.99,
					availability: ItemAvailability::InStock,
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('priceSpecification', $data['offers'][0]);
	}
}
