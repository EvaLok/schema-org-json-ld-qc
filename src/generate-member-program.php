<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MemberProgram;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MemberProgramTier;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Enum\TierBenefitEnumeration;

$program = new MemberProgram(
	name: 'ShopRewards Loyalty Program',
	description: 'Earn points on every purchase and unlock exclusive member benefits.',
	hasTiers: [
		new MemberProgramTier(
			name: 'Silver',
			hasTierBenefit: TierBenefitEnumeration::TierBenefitLoyaltyPoints,
			hasTierRequirement: 'No minimum spend required',
			membershipPointsEarned: new QuantitativeValue(value: 1),
		),
		new MemberProgramTier(
			name: 'Gold',
			hasTierBenefit: [
				TierBenefitEnumeration::TierBenefitLoyaltyPoints,
				TierBenefitEnumeration::TierBenefitLoyaltyPrice,
			],
			hasTierRequirement: 'Spend $500 or more per year',
			membershipPointsEarned: new QuantitativeValue(value: 2),
			url: 'https://www.example.com/rewards/gold',
		),
	],
	url: 'https://www.example.com/rewards',
);

echo JsonLdGenerator::SchemaToJson($program) . "\n";
