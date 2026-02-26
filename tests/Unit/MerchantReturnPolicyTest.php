<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MerchantReturnEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MerchantReturnPolicy;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MerchantReturnPolicySeasonalOverride;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MonetaryAmount;
use EvaLok\SchemaOrgJsonLd\v1\Schema\RefundTypeEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ReturnFeesEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ReturnLabelSourceEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ReturnMethodEnumeration;
use PHPUnit\Framework\TestCase;

class MerchantReturnPolicyTest extends TestCase
{
	public function testBasicReturnPolicy(): void
	{
		$policy = new MerchantReturnPolicy(
			applicableCountry: 'US',
			returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
			merchantReturnDays: 30,
		);

		$json = JsonLdGenerator::SchemaToJson($policy);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('MerchantReturnPolicy', $data['@type']);
		$this->assertSame('US', $data['applicableCountry']);
		$this->assertSame('https://schema.org/MerchantReturnFiniteReturnWindow', $data['returnPolicyCategory']);
		$this->assertSame(30, $data['merchantReturnDays']);
	}

	public function testReturnPolicyNotPermitted(): void
	{
		$policy = new MerchantReturnPolicy(
			applicableCountry: 'DE',
			returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnNotPermitted,
		);

		$json = JsonLdGenerator::SchemaToJson($policy);
		$data = json_decode($json, true);

		$this->assertSame('MerchantReturnPolicy', $data['@type']);
		$this->assertSame('https://schema.org/MerchantReturnNotPermitted', $data['returnPolicyCategory']);
		$this->assertArrayNotHasKey('merchantReturnDays', $data);
		$this->assertArrayNotHasKey('returnMethod', $data);
		$this->assertArrayNotHasKey('returnFees', $data);
	}

	public function testFullReturnPolicy(): void
	{
		$policy = new MerchantReturnPolicy(
			applicableCountry: ['US', 'CA'],
			returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
			merchantReturnDays: 30,
			merchantReturnLink: 'https://www.example.com/returns',
			returnMethod: ReturnMethodEnumeration::ReturnByMail,
			returnFees: ReturnFeesEnumeration::FreeReturn,
			refundType: RefundTypeEnumeration::FullRefund,
			returnLabelSource: ReturnLabelSourceEnumeration::ReturnLabelDownloadAndPrint,
		);

		$json = JsonLdGenerator::SchemaToJson($policy);
		$data = json_decode($json, true);

		$this->assertSame('MerchantReturnPolicy', $data['@type']);
		$this->assertCount(2, $data['applicableCountry']);
		$this->assertContains('US', $data['applicableCountry']);
		$this->assertContains('CA', $data['applicableCountry']);
		$this->assertSame(30, $data['merchantReturnDays']);
		$this->assertSame('https://www.example.com/returns', $data['merchantReturnLink']);
		$this->assertSame('https://schema.org/ReturnByMail', $data['returnMethod']);
		$this->assertSame('https://schema.org/FreeReturn', $data['returnFees']);
		$this->assertSame('https://schema.org/FullRefund', $data['refundType']);
		$this->assertSame('https://schema.org/ReturnLabelDownloadAndPrint', $data['returnLabelSource']);
	}

	public function testReturnPolicyWithSeasonalOverride(): void
	{
		$policy = new MerchantReturnPolicy(
			applicableCountry: 'US',
			returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
			merchantReturnDays: 30,
			returnPolicySeasonalOverride: new MerchantReturnPolicySeasonalOverride(
				startDate: '2026-11-29',
				endDate: '2027-01-31',
				returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
				merchantReturnDays: 60,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($policy);
		$data = json_decode($json, true);

		$this->assertSame('MerchantReturnPolicy', $data['@type']);
		$this->assertSame('MerchantReturnPolicySeasonalOverride', $data['returnPolicySeasonalOverride']['@type']);
		$this->assertSame('2026-11-29', $data['returnPolicySeasonalOverride']['startDate']);
		$this->assertSame('2027-01-31', $data['returnPolicySeasonalOverride']['endDate']);
		$this->assertSame(60, $data['returnPolicySeasonalOverride']['merchantReturnDays']);
	}

	public function testReturnPolicyWithReturnShippingFees(): void
	{
		$policy = new MerchantReturnPolicy(
			applicableCountry: 'US',
			returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
			merchantReturnDays: 14,
			returnFees: ReturnFeesEnumeration::ReturnShippingFees,
			returnShippingFeesAmount: new MonetaryAmount(
				value: 7.99,
				currency: 'USD',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($policy);
		$data = json_decode($json, true);

		$this->assertSame('MerchantReturnPolicy', $data['@type']);
		$this->assertSame('https://schema.org/ReturnShippingFees', $data['returnFees']);
		$this->assertSame('MonetaryAmount', $data['returnShippingFeesAmount']['@type']);
		$this->assertEquals(7.99, $data['returnShippingFeesAmount']['value']);
		$this->assertSame('USD', $data['returnShippingFeesAmount']['currency']);
	}

	public function testOptionalFieldsOmitted(): void
	{
		$policy = new MerchantReturnPolicy(
			applicableCountry: 'US',
			returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
		);

		$json = JsonLdGenerator::SchemaToJson($policy);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('merchantReturnDays', $data);
		$this->assertArrayNotHasKey('merchantReturnLink', $data);
		$this->assertArrayNotHasKey('returnMethod', $data);
		$this->assertArrayNotHasKey('returnFees', $data);
		$this->assertArrayNotHasKey('returnShippingFeesAmount', $data);
		$this->assertArrayNotHasKey('refundType', $data);
		$this->assertArrayNotHasKey('returnLabelSource', $data);
		$this->assertArrayNotHasKey('returnPolicySeasonalOverride', $data);
		$this->assertArrayNotHasKey('customerRemorseReturnFees', $data);
		$this->assertArrayNotHasKey('itemDefectReturnFees', $data);
	}
}
