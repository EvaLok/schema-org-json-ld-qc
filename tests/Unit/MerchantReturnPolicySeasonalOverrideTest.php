<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\Enum\MerchantReturnEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MerchantReturnPolicy;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MerchantReturnPolicySeasonalOverride;
use PHPUnit\Framework\TestCase;

class MerchantReturnPolicySeasonalOverrideTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new MerchantReturnPolicySeasonalOverride(
			startDate: '2025-11-28',
			endDate: '2025-12-31',
			returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('MerchantReturnPolicySeasonalOverride', $data['@type']);
		$this->assertSame('2025-11-28', $data['startDate']);
		$this->assertSame('2025-12-31', $data['endDate']);
		$this->assertSame('https://schema.org/MerchantReturnFiniteReturnWindow', $data['returnPolicyCategory']);
	}

	public function testWithAllFields(): void
	{
		$schema = new MerchantReturnPolicySeasonalOverride(
			startDate: '2025-11-28',
			endDate: '2025-12-31',
			returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
			merchantReturnDays: 30,
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame(30, $data['merchantReturnDays']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new MerchantReturnPolicySeasonalOverride(
			startDate: '2025-11-28',
			endDate: '2025-12-31',
			returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('merchantReturnDays', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new MerchantReturnPolicy(
			applicableCountry: 'US',
			returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
			returnPolicySeasonalOverride: new MerchantReturnPolicySeasonalOverride(
				startDate: '2025-11-28',
				endDate: '2025-12-31',
				returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
				merchantReturnDays: 30,
			),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('MerchantReturnPolicySeasonalOverride', $data['returnPolicySeasonalOverride']['@type']);
		$this->assertArrayNotHasKey('@context', $data['returnPolicySeasonalOverride']);
		$this->assertSame(30, $data['returnPolicySeasonalOverride']['merchantReturnDays']);
	}
}

