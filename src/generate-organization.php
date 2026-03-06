<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Enum\MerchantReturnEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Enum\TierBenefitEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ContactPoint;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ImageObject;
use EvaLok\SchemaOrgJsonLd\v1\Schema\InteractionCounter;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MemberProgram;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MemberProgramTier;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MerchantReturnPolicy;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;

$org = new Organization(
	name: 'TechStart Inc.',
	url: 'https://techstart.example.com',
	logo: 'https://techstart.example.com/logo.png',
	description: 'Leading technology startup accelerator.',
	email: 'info@techstart.example.com',
	telephone: '+1-555-123-4567',
	address: new PostalAddress(
		streetAddress: '123 Innovation Drive',
		addressLocality: 'San Francisco',
		addressRegion: 'CA',
		postalCode: '94105',
		addressCountry: 'US',
	),
	contactPoint: new ContactPoint(
		telephone: '+1-555-987-6543',
		contactType: 'customer service',
	),
	sameAs: [
		'https://twitter.com/techstart',
		'https://linkedin.com/company/techstart',
	],
	foundingDate: '2020-03-15',
	legalName: 'TechStart Incorporated',
	numberOfEmployees: new QuantitativeValue(value: 150),
	taxID: '94-3456789',
	duns: '12-345-6789',
	vatID: 'DE123456789',
	naics: '541512',
	identifier: 'techstart-2020',
	image: new ImageObject(
		contentUrl: 'https://techstart.example.com/office.jpg',
		url: 'https://techstart.example.com/office.jpg',
		width: '1200',
		height: '800',
	),
	interactionStatistic: new InteractionCounter(
		interactionType: 'https://schema.org/FollowAction',
		userInteractionCount: 15000,
	),
	agentInteractionStatistic: new InteractionCounter(
		interactionType: 'https://schema.org/WriteAction',
		userInteractionCount: 500,
	),
	leiCode: '529900T8BM49AURSDO55',
	iso6523Code: '0060:123456789',
	globalLocationNumber: '1234567890128',
	hasMerchantReturnPolicy: new MerchantReturnPolicy(
		applicableCountry: 'US',
		returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
		merchantReturnDays: 30,
	),
	hasMemberProgram: new MemberProgram(
		name: 'TechStart Partners',
		description: 'Partner membership program for strategic collaborators.',
		hasTiers: [
			new MemberProgramTier(
				name: 'Silver',
				hasTierBenefit: TierBenefitEnumeration::TierBenefitLoyaltyPoints,
			),
		],
	),
);

echo JsonLdGenerator::SchemaToJson($org);
