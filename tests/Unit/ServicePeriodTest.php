<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\Enum\DayOfWeek;
use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ServicePeriod;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingConditions;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ShippingService;
use PHPUnit\Framework\TestCase;

class ServicePeriodTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new ServicePeriod();
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('ServicePeriod', $data['@type']);
	}

	public function testWithAllFields(): void
	{
		$schema = new ServicePeriod(
			duration: new QuantitativeValue(value: 5, unitCode: 'DAY'),
			businessDays: [
				DayOfWeek::Monday,
				DayOfWeek::Tuesday,
				DayOfWeek::Wednesday,
				DayOfWeek::Thursday,
				DayOfWeek::Friday,
			],
			cutoffTime: '12:00:00',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertEquals(5.0, $data['duration']['value']);
		$this->assertSame('https://schema.org/Monday', $data['businessDays'][0]);
		$this->assertSame('12:00:00', $data['cutoffTime']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new ServicePeriod();
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('duration', $data);
		$this->assertArrayNotHasKey('businessDays', $data);
		$this->assertArrayNotHasKey('cutoffTime', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new ShippingService(
			shippingConditions: new ShippingConditions(),
			handlingTime: new ServicePeriod(
				duration: new QuantitativeValue(value: 5, unitCode: 'DAY'),
			),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('ServicePeriod', $data['handlingTime']['@type']);
		$this->assertArrayNotHasKey('@context', $data['handlingTime']);
		$this->assertEquals(5.0, $data['handlingTime']['duration']['value']);
	}
}
