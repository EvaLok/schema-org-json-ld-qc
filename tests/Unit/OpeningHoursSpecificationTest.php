<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\Enum\DayOfWeek;
use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OpeningHoursSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Store;
use PHPUnit\Framework\TestCase;

class OpeningHoursSpecificationTest extends TestCase
{
	public function testMinimalOpeningHoursSpecification(): void
	{
		$openingHours = new OpeningHoursSpecification(
			dayOfWeek: DayOfWeek::Monday,
			opens: '09:00',
			closes: '17:00',
		);

		$json = JsonLdGenerator::SchemaToJson($openingHours);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('OpeningHoursSpecification', $data['@type']);
		$this->assertSame('https://schema.org/Monday', $data['dayOfWeek']);
		$this->assertSame('09:00', $data['opens']);
		$this->assertSame('17:00', $data['closes']);
	}

	public function testOpeningHoursSpecificationWithAllFields(): void
	{
		$openingHours = new OpeningHoursSpecification(
			dayOfWeek: DayOfWeek::Monday,
			opens: '09:00',
			closes: '17:00',
			validFrom: '2025-01-01',
			validThrough: '2025-12-31',
		);

		$json = JsonLdGenerator::SchemaToJson($openingHours);
		$data = json_decode($json, true);

		$this->assertSame('OpeningHoursSpecification', $data['@type']);
		$this->assertSame('2025-01-01', $data['validFrom']);
		$this->assertSame('2025-12-31', $data['validThrough']);
	}

	public function testOpeningHoursSpecificationNullFieldsOmitted(): void
	{
		$openingHours = new OpeningHoursSpecification(
			dayOfWeek: DayOfWeek::Tuesday,
		);

		$json = JsonLdGenerator::SchemaToJson($openingHours);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('opens', $data);
		$this->assertArrayNotHasKey('closes', $data);
		$this->assertArrayNotHasKey('validFrom', $data);
		$this->assertArrayNotHasKey('validThrough', $data);
	}

	public function testOpeningHoursSpecificationNestedObjectRendersCorrectly(): void
	{
		$store = new Store(
			name: 'Weekend Store',
			address: new PostalAddress(streetAddress: '1 Market St'),
			openingHoursSpecification: [
				new OpeningHoursSpecification(
					dayOfWeek: DayOfWeek::Sunday,
					opens: '10:00',
					closes: '16:00',
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($store);
		$data = json_decode($json, true);

		$this->assertSame('OpeningHoursSpecification', $data['openingHoursSpecification'][0]['@type']);
		$this->assertSame('https://schema.org/Sunday', $data['openingHoursSpecification'][0]['dayOfWeek']);
	}
}
