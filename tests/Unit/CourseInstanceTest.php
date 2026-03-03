<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Course;
use EvaLok\SchemaOrgJsonLd\v1\Schema\CourseInstance;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Schedule;
use PHPUnit\Framework\TestCase;

class CourseInstanceTest extends TestCase
{
	public function testMinimalCourseInstance(): void
	{
		$courseInstance = new CourseInstance();

		$json = JsonLdGenerator::SchemaToJson($courseInstance);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('CourseInstance', $data['@type']);
	}

	public function testWithAllFields(): void
	{
		$courseInstance = new CourseInstance(
			courseMode: 'online',
			instructor: new Person(name: 'Dr. Emily Zhang'),
			courseSchedule: new Schedule(
				repeatFrequency: 'P1W',
				startDate: '2025-09-01',
				endDate: '2025-12-15',
			),
			courseWorkload: 'PT40H',
		);

		$json = JsonLdGenerator::SchemaToJson($courseInstance);
		$data = json_decode($json, true);

		$this->assertSame('CourseInstance', $data['@type']);
		$this->assertSame('online', $data['courseMode']);
		$this->assertSame('Person', $data['instructor']['@type']);
		$this->assertSame('Dr. Emily Zhang', $data['instructor']['name']);
		$this->assertSame('Schedule', $data['courseSchedule']['@type']);
		$this->assertSame('P1W', $data['courseSchedule']['repeatFrequency']);
		$this->assertSame('2025-09-01', $data['courseSchedule']['startDate']);
		$this->assertSame('2025-12-15', $data['courseSchedule']['endDate']);
		$this->assertSame('PT40H', $data['courseWorkload']);
	}

	public function testNullFieldsOmitted(): void
	{
		$courseInstance = new CourseInstance();

		$json = JsonLdGenerator::SchemaToJson($courseInstance);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('courseMode', $data);
		$this->assertArrayNotHasKey('instructor', $data);
		$this->assertArrayNotHasKey('courseSchedule', $data);
		$this->assertArrayNotHasKey('courseWorkload', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$course = new Course(
			name: 'Introduction to Machine Learning',
			description: 'Learn machine learning fundamentals.',
			hasCourseInstance: [
				new CourseInstance(
					courseMode: 'online',
					instructor: new Person(name: 'Dr. Emily Zhang'),
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($course);
		$data = json_decode($json, true);

		$this->assertSame('Course', $data['@type']);
		$this->assertSame('CourseInstance', $data['hasCourseInstance'][0]['@type']);
		$this->assertSame('online', $data['hasCourseInstance'][0]['courseMode']);
		$this->assertSame('Person', $data['hasCourseInstance'][0]['instructor']['@type']);
		$this->assertSame('Dr. Emily Zhang', $data['hasCourseInstance'][0]['instructor']['name']);
		$this->assertArrayNotHasKey('@context', $data['hasCourseInstance'][0]);
	}
}
