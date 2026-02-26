<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MemberProgram;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MemberProgramTier;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\TierBenefitEnumeration;
use PHPUnit\Framework\TestCase;

class MemberProgramTest extends TestCase
{
	public function testBasicMemberProgram(): void
	{
		if (!class_exists(MemberProgram::class)) {
			$this->markTestSkipped('MemberProgram is not available in the installed schema-org-json-ld version.');
		}

		$program = new MemberProgram(
			name: 'Test Rewards',
			description: 'A test loyalty program.',
			hasTiers: [
				new MemberProgramTier(
					name: 'Basic',
					hasTierBenefit: TierBenefitEnumeration::TierBenefitLoyaltyPoints,
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($program);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('MemberProgram', $data['@type']);
		$this->assertSame('Test Rewards', $data['name']);
		$this->assertSame('A test loyalty program.', $data['description']);
		$this->assertCount(1, $data['hasTiers']);
		$this->assertSame('MemberProgramTier', $data['hasTiers'][0]['@type']);
		$this->assertSame('Basic', $data['hasTiers'][0]['name']);
		$this->assertSame('https://schema.org/TierBenefitLoyaltyPoints', $data['hasTiers'][0]['hasTierBenefit']);
	}

	public function testMemberProgramWithMultipleTiers(): void
	{
		if (!class_exists(MemberProgram::class)) {
			$this->markTestSkipped('MemberProgram is not available in the installed schema-org-json-ld version.');
		}

		$program = new MemberProgram(
			name: 'Premium Rewards',
			description: 'Earn more at higher tiers.',
			hasTiers: [
				new MemberProgramTier(
					name: 'Silver',
					hasTierBenefit: TierBenefitEnumeration::TierBenefitLoyaltyPoints,
				),
				new MemberProgramTier(
					name: 'Gold',
					hasTierBenefit: [
						TierBenefitEnumeration::TierBenefitLoyaltyPoints,
						TierBenefitEnumeration::TierBenefitLoyaltyPrice,
					],
					hasTierRequirement: 'Spend $500 per year',
					membershipPointsEarned: new QuantitativeValue(value: 2),
					url: 'https://example.com/gold',
				),
			],
			url: 'https://example.com/rewards',
		);

		$json = JsonLdGenerator::SchemaToJson($program);
		$data = json_decode($json, true);

		$this->assertSame('MemberProgram', $data['@type']);
		$this->assertCount(2, $data['hasTiers']);
		$this->assertSame('Silver', $data['hasTiers'][0]['name']);
		$this->assertSame('Gold', $data['hasTiers'][1]['name']);
		$this->assertCount(2, $data['hasTiers'][1]['hasTierBenefit']);
		$this->assertSame('Spend $500 per year', $data['hasTiers'][1]['hasTierRequirement']);
		$this->assertSame('QuantitativeValue', $data['hasTiers'][1]['membershipPointsEarned']['@type']);
		$this->assertSame('https://example.com/rewards', $data['url']);
	}

	public function testOptionalFieldsOmitted(): void
	{
		if (!class_exists(MemberProgram::class)) {
			$this->markTestSkipped('MemberProgram is not available in the installed schema-org-json-ld version.');
		}

		$program = new MemberProgram(
			name: 'Minimal',
			description: 'Minimal program.',
			hasTiers: [
				new MemberProgramTier(
					name: 'Basic',
					hasTierBenefit: TierBenefitEnumeration::TierBenefitLoyaltyPoints,
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($program);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('url', $data);
		$this->assertArrayNotHasKey('hasTierRequirement', $data['hasTiers'][0]);
		$this->assertArrayNotHasKey('membershipPointsEarned', $data['hasTiers'][0]);
		$this->assertArrayNotHasKey('url', $data['hasTiers'][0]);
	}
}
