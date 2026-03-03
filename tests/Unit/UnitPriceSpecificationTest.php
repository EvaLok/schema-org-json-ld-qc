<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Enum\TierBenefitEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MemberProgramTier;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\UnitPriceSpecification;
use PHPUnit\Framework\TestCase;

class UnitPriceSpecificationTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new UnitPriceSpecification(
			price: 29.99,
			priceCurrency: 'USD',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('UnitPriceSpecification', $data['@type']);
		$this->assertEquals(29.99, $data['price']);
		$this->assertSame('USD', $data['priceCurrency']);
	}

	public function testWithAllFields(): void
	{
		$schema = new UnitPriceSpecification(
			price: 29.99,
			priceCurrency: 'USD',
			priceType: 'https://schema.org/SalePrice',
			membershipPointsEarned: 150.0,
			validForMemberTier: new MemberProgramTier(
				name: 'Gold',
				hasTierBenefit: TierBenefitEnumeration::TierBenefitLoyaltyPrice,
			),
			referenceQuantity: new QuantitativeValue(
				value: 1,
				unitCode: 'C62',
			),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('UnitPriceSpecification', $data['@type']);
		$this->assertEquals(29.99, $data['price']);
		$this->assertSame('USD', $data['priceCurrency']);
		$this->assertSame('https://schema.org/SalePrice', $data['priceType']);
		$this->assertEquals(150.0, $data['membershipPointsEarned']);
		$this->assertSame('MemberProgramTier', $data['validForMemberTier']['@type']);
		$this->assertSame('Gold', $data['validForMemberTier']['name']);
		$this->assertSame('QuantitativeValue', $data['referenceQuantity']['@type']);
		$this->assertSame(1, $data['referenceQuantity']['value']);
		$this->assertSame('C62', $data['referenceQuantity']['unitCode']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new UnitPriceSpecification(
			price: 29.99,
			priceCurrency: 'USD',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('priceType', $data);
		$this->assertArrayNotHasKey('membershipPointsEarned', $data);
		$this->assertArrayNotHasKey('validForMemberTier', $data);
		$this->assertArrayNotHasKey('referenceQuantity', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new Offer(
			url: 'https://example.com/product',
			priceCurrency: 'USD',
			price: 29.99,
			availability: ItemAvailability::InStock,
			priceSpecification: new UnitPriceSpecification(
				price: 29.99,
				priceCurrency: 'USD',
				priceType: 'https://schema.org/SalePrice',
				membershipPointsEarned: 150.0,
				validForMemberTier: new MemberProgramTier(
					name: 'Gold',
					hasTierBenefit: TierBenefitEnumeration::TierBenefitLoyaltyPrice,
				),
				referenceQuantity: new QuantitativeValue(
					value: 1,
					unitCode: 'C62',
				),
			),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayHasKey('priceSpecification', $data);
		$this->assertSame('UnitPriceSpecification', $data['priceSpecification']['@type']);
		$this->assertArrayNotHasKey('@context', $data['priceSpecification']);
		$this->assertEquals(29.99, $data['priceSpecification']['price']);
		$this->assertSame('USD', $data['priceSpecification']['priceCurrency']);
	}
}
