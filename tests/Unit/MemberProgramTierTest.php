<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\Enum\TierBenefitEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MemberProgram;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MemberProgramTier;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use PHPUnit\Framework\TestCase;

class MemberProgramTierTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new MemberProgramTier(
			name: 'Gold Tier',
			hasTierBenefit: TierBenefitEnumeration::TierBenefitLoyaltyPrice,
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('MemberProgramTier', $data['@type']);
		$this->assertSame('Gold Tier', $data['name']);
		$this->assertSame('https://schema.org/TierBenefitLoyaltyPrice', $data['hasTierBenefit']);
	}

	public function testWithAllFields(): void
	{
		$schema = new MemberProgramTier(
			name: 'Gold Tier',
			hasTierBenefit: TierBenefitEnumeration::TierBenefitLoyaltyPrice,
			hasTierRequirement: 'Spend $500 in 12 months',
			membershipPointsEarned: new QuantitativeValue(value: 2, unitCode: 'C62'),
			url: 'https://example.com/membership/gold',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('Spend $500 in 12 months', $data['hasTierRequirement']);
		$this->assertEquals(2.0, $data['membershipPointsEarned']['value']);
		$this->assertSame('https://example.com/membership/gold', $data['url']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new MemberProgramTier(
			name: 'Gold Tier',
			hasTierBenefit: TierBenefitEnumeration::TierBenefitLoyaltyPrice,
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('hasTierRequirement', $data);
		$this->assertArrayNotHasKey('membershipPointsEarned', $data);
		$this->assertArrayNotHasKey('url', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new MemberProgram(
			name: 'Rewards Club',
			description: 'Loyalty program',
			hasTiers: [
				new MemberProgramTier(
					name: 'Gold Tier',
					hasTierBenefit: TierBenefitEnumeration::TierBenefitLoyaltyPrice,
				),
			],
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('MemberProgramTier', $data['hasTiers'][0]['@type']);
		$this->assertArrayNotHasKey('@context', $data['hasTiers'][0]);
		$this->assertSame('Gold Tier', $data['hasTiers'][0]['name']);
	}
}
