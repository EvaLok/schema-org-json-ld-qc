<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\CourseInstance;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Schedule;
use PHPUnit\Framework\TestCase;

class ScheduleTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new Schedule(repeatFrequency: 'P1W');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Schedule', $data['@type']);
		$this->assertSame('P1W', $data['repeatFrequency']);
	}

	public function testWithAllFields(): void
	{
		$schema = new Schedule(
			repeatFrequency: 'P1W',
			repeatCount: 15,
			startDate: '2025-09-01',
			endDate: '2025-12-15',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame(15, $data['repeatCount']);
		$this->assertSame('2025-09-01', $data['startDate']);
		$this->assertSame('2025-12-15', $data['endDate']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new Schedule(repeatFrequency: 'P1W');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('repeatCount', $data);
		$this->assertArrayNotHasKey('startDate', $data);
		$this->assertArrayNotHasKey('endDate', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new CourseInstance(
			courseSchedule: new Schedule(
				repeatFrequency: 'P1W',
				startDate: '2025-09-01',
				endDate: '2025-12-15',
			),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('Schedule', $data['courseSchedule']['@type']);
		$this->assertArrayNotHasKey('@context', $data['courseSchedule']);
		$this->assertSame('P1W', $data['courseSchedule']['repeatFrequency']);
	}
}

