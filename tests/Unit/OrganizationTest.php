<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ContactPoint;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ImageObject;
use EvaLok\SchemaOrgJsonLd\v1\Schema\InteractionCounter;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MemberProgram;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MemberProgramTier;
use EvaLok\SchemaOrgJsonLd\v1\Enum\MerchantReturnEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MerchantReturnPolicy;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MonetaryAmount;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ReturnFeesEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingConditions;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingService;
use EvaLok\SchemaOrgJsonLd\v1\Enum\TierBenefitEnumeration;
use PHPUnit\Framework\TestCase;

class OrganizationTest extends TestCase
{
	public function testMinimalOrganization(): void
	{
		$org = new Organization(
			name: 'Acme Corp',
		);

		$json = JsonLdGenerator::SchemaToJson($org);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Organization', $data['@type']);
		$this->assertSame('Acme Corp', $data['name']);
	}

	public function testOrganizationWithFullDetails(): void
	{
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
				email: 'support@techstart.example.com',
			),
			sameAs: [
				'https://twitter.com/techstart',
				'https://linkedin.com/company/techstart',
			],
			foundingDate: '2020-03-15',
			legalName: 'TechStart Incorporated',
			alternateName: 'TechStart',
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

		$json = JsonLdGenerator::SchemaToJson($org);
		$data = json_decode($json, true);

		$this->assertSame('Organization', $data['@type']);
		$this->assertSame('TechStart Inc.', $data['name']);
		$this->assertSame('https://techstart.example.com', $data['url']);
		$this->assertSame('https://techstart.example.com/logo.png', $data['logo']);
		$this->assertSame('Leading technology startup accelerator.', $data['description']);
		$this->assertSame('+1-555-123-4567', $data['telephone']);
		$this->assertSame('PostalAddress', $data['address']['@type']);
		$this->assertSame('123 Innovation Drive', $data['address']['streetAddress']);
		$this->assertSame('San Francisco', $data['address']['addressLocality']);
		$this->assertSame('CA', $data['address']['addressRegion']);
		$this->assertSame('94105', $data['address']['postalCode']);
		$this->assertSame('US', $data['address']['addressCountry']);
		$this->assertSame('ContactPoint', $data['contactPoint']['@type']);
		$this->assertSame('customer service', $data['contactPoint']['contactType']);
		$this->assertCount(2, $data['sameAs']);
		$this->assertSame('2020-03-15', $data['foundingDate']);
		$this->assertSame('TechStart Incorporated', $data['legalName']);
		$this->assertSame('TechStart', $data['alternateName']);
		$this->assertSame('DE123456789', $data['vatID']);
		$this->assertSame('541512', $data['naics']);
		$this->assertSame('techstart-2020', $data['identifier']);
		$this->assertSame('ImageObject', $data['image']['@type']);
		$this->assertSame('https://techstart.example.com/office.jpg', $data['image']['url']);
		$this->assertSame('1200', $data['image']['width']);
		$this->assertSame('800', $data['image']['height']);
		$this->assertSame('InteractionCounter', $data['interactionStatistic']['@type']);
		$this->assertSame('https://schema.org/FollowAction', $data['interactionStatistic']['interactionType']);
		$this->assertSame(15000, $data['interactionStatistic']['userInteractionCount']);
		$this->assertSame('InteractionCounter', $data['agentInteractionStatistic']['@type']);
		$this->assertSame('https://schema.org/WriteAction', $data['agentInteractionStatistic']['interactionType']);
		$this->assertSame(500, $data['agentInteractionStatistic']['userInteractionCount']);
		$this->assertSame('529900T8BM49AURSDO55', $data['leiCode']);
		$this->assertSame('0060:123456789', $data['iso6523Code']);
		$this->assertSame('1234567890128', $data['globalLocationNumber']);
		$this->assertSame('MerchantReturnPolicy', $data['hasMerchantReturnPolicy']['@type']);
		$this->assertSame(30, $data['hasMerchantReturnPolicy']['merchantReturnDays']);
		$this->assertSame('MemberProgram', $data['hasMemberProgram']['@type']);
		$this->assertSame('TechStart Partners', $data['hasMemberProgram']['name']);
		$this->assertCount(1, $data['hasMemberProgram']['hasTiers']);
		$this->assertSame('Silver', $data['hasMemberProgram']['hasTiers'][0]['name']);
	}

	public function testOrganizationNullFieldsOmitted(): void
	{
		$org = new Organization(name: 'Minimal Co');

		$json = JsonLdGenerator::SchemaToJson($org);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('url', $data);
		$this->assertArrayNotHasKey('logo', $data);
		$this->assertArrayNotHasKey('email', $data);
		$this->assertArrayNotHasKey('telephone', $data);
		$this->assertArrayNotHasKey('address', $data);
		$this->assertArrayNotHasKey('contactPoint', $data);
		$this->assertArrayNotHasKey('sameAs', $data);
		$this->assertArrayNotHasKey('foundingDate', $data);
		$this->assertArrayNotHasKey('numberOfEmployees', $data);
		$this->assertArrayNotHasKey('taxID', $data);
		$this->assertArrayNotHasKey('vatID', $data);
		$this->assertArrayNotHasKey('naics', $data);
		$this->assertArrayNotHasKey('duns', $data);
		$this->assertArrayNotHasKey('leiCode', $data);
		$this->assertArrayNotHasKey('iso6523Code', $data);
		$this->assertArrayNotHasKey('globalLocationNumber', $data);
		$this->assertArrayNotHasKey('hasMerchantReturnPolicy', $data);
		$this->assertArrayNotHasKey('hasMemberProgram', $data);
		$this->assertArrayNotHasKey('hasShippingService', $data);
	}

	public function testOrganizationWithMerchantReturnPolicy(): void
	{
		$org = new Organization(
			name: 'RetailCo',
			hasMerchantReturnPolicy: new MerchantReturnPolicy(
				applicableCountry: 'US',
				returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
				merchantReturnDays: 30,
				returnFees: ReturnFeesEnumeration::FreeReturn,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($org);
		$data = json_decode($json, true);

		$this->assertSame('Organization', $data['@type']);
		$this->assertSame('MerchantReturnPolicy', $data['hasMerchantReturnPolicy']['@type']);
		$this->assertSame(30, $data['hasMerchantReturnPolicy']['merchantReturnDays']);
		$this->assertSame('https://schema.org/FreeReturn', $data['hasMerchantReturnPolicy']['returnFees']);
	}

	public function testOrganizationWithBusinessIdentifiers(): void
	{
		$org = new Organization(
			name: 'Acme Corporation',
			taxID: '12-3456789',
			vatID: 'DE123456789',
			naics: '511210',
			duns: '12-345-6789',
			leiCode: '5493006MHB84DD3ZDB09',
			iso6523Code: '0060:123456789',
			globalLocationNumber: '1234567890128',
		);

		$json = JsonLdGenerator::SchemaToJson($org);
		$data = json_decode($json, true);

		$this->assertSame('Organization', $data['@type']);
		$this->assertSame('12-3456789', $data['taxID']);
		$this->assertSame('DE123456789', $data['vatID']);
		$this->assertSame('511210', $data['naics']);
		$this->assertSame('12-345-6789', $data['duns']);
		$this->assertSame('5493006MHB84DD3ZDB09', $data['leiCode']);
		$this->assertSame('0060:123456789', $data['iso6523Code']);
		$this->assertSame('1234567890128', $data['globalLocationNumber']);
	}

	public function testOrganizationWithNumberOfEmployees(): void
	{
		$org = new Organization(
			name: 'Big Corp',
			numberOfEmployees: new \EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue(
				value: 5000,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($org);
		$data = json_decode($json, true);

		$this->assertSame('Organization', $data['@type']);
		$this->assertSame('QuantitativeValue', $data['numberOfEmployees']['@type']);
		$this->assertEquals(5000, $data['numberOfEmployees']['value']);
	}

	public function testOrganizationWithMemberProgram(): void
	{
		if (!class_exists(MemberProgram::class)) {
			$this->markTestSkipped('MemberProgram is not available in the installed schema-org-json-ld version.');
		}

		$org = new Organization(
			name: 'ShopMart',
			hasMemberProgram: new MemberProgram(
				name: 'ShopMart Rewards',
				description: 'Earn points on purchases.',
				hasTiers: [
					new MemberProgramTier(
						name: 'Basic',
						hasTierBenefit: TierBenefitEnumeration::TierBenefitLoyaltyPoints,
					),
				],
			),
		);

		$json = JsonLdGenerator::SchemaToJson($org);
		$data = json_decode($json, true);

		$this->assertSame('Organization', $data['@type']);
		$this->assertSame('MemberProgram', $data['hasMemberProgram']['@type']);
		$this->assertSame('ShopMart Rewards', $data['hasMemberProgram']['name']);
		$this->assertCount(1, $data['hasMemberProgram']['hasTiers']);
	}

	public function testOrganizationWithShippingService(): void
	{
		if (!class_exists(ShippingService::class)) {
			$this->markTestSkipped('ShippingService is not available in the installed schema-org-json-ld version.');
		}

		$org = new Organization(
			name: 'ShipCo',
			hasShippingService: new ShippingService(
				shippingConditions: new ShippingConditions(
					shippingRate: new MonetaryAmount(
						value: 4.99,
						currency: 'USD',
					),
				),
				name: 'Standard',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($org);
		$data = json_decode($json, true);

		$this->assertSame('Organization', $data['@type']);
		$this->assertSame('ShippingService', $data['hasShippingService']['@type']);
		$this->assertSame('Standard', $data['hasShippingService']['name']);
	}
}
