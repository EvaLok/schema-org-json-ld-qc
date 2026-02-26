<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Course;
use EvaLok\SchemaOrgJsonLd\v1\Schema\CourseInstance;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use PHPUnit\Framework\TestCase;

class CourseTest extends TestCase
{
	public function testMinimalCourse(): void
	{
		$course = new Course(
			name: 'Introduction to Machine Learning',
			description: 'Learn the fundamentals of machine learning, including supervised and unsupervised learning algorithms.',
		);

		$json = JsonLdGenerator::SchemaToJson($course);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Course', $data['@type']);
		$this->assertSame('Introduction to Machine Learning', $data['name']);
		$this->assertStringContainsString('fundamentals', $data['description']);
	}

	public function testFullCourse(): void
	{
		$course = new Course(
			name: 'Advanced PHP Development',
			description: 'Master modern PHP development with design patterns, testing, and frameworks.',
			provider: new Organization(
				name: 'Code Academy Pro',
				url: 'https://codeacademypro.example.com',
			),
			offers: [
				new Offer(
					url: 'https://codeacademypro.example.com/courses/php-advanced',
					priceCurrency: 'USD',
					price: 199.99,
					itemCondition: OfferItemCondition::NewCondition,
					availability: ItemAvailability::InStock,
				),
			],
			hasCourseInstance: [
				new CourseInstance(
					courseMode: 'online',
					instructor: new Person(name: 'Dr. PHP Expert'),
					courseWorkload: 'PT40H',
				),
			],
			courseCode: 'PHP-ADV-101',
			inLanguage: 'en',
			totalHistoricalEnrollment: 5420,
			aggregateRating: new AggregateRating(
				ratingValue: 4.8,
				ratingCount: 1024,
				bestRating: 5,
				worstRating: 1,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($course);
		$data = json_decode($json, true);

		$this->assertSame('Course', $data['@type']);
		$this->assertSame('Organization', $data['provider']['@type']);
		$this->assertSame('Code Academy Pro', $data['provider']['name']);
		$this->assertCount(1, $data['offers']);
		$this->assertCount(1, $data['hasCourseInstance']);
		$this->assertSame('CourseInstance', $data['hasCourseInstance'][0]['@type']);
		$this->assertSame('online', $data['hasCourseInstance'][0]['courseMode']);
		$this->assertSame('Person', $data['hasCourseInstance'][0]['instructor']['@type']);
		$this->assertSame('PHP-ADV-101', $data['courseCode']);
		$this->assertSame('en', $data['inLanguage']);
		$this->assertSame(5420, $data['totalHistoricalEnrollment']);
		$this->assertSame(5, $data['aggregateRating']['bestRating']);
		$this->assertSame(1, $data['aggregateRating']['worstRating']);
	}

	public function testCourseInstanceWithoutCourseMode(): void
	{
		$course = new Course(
			name: 'Data Science Fundamentals',
			description: 'Learn the basics of data science.',
			hasCourseInstance: [
				new CourseInstance(
					instructor: new Person(name: 'Prof. Data'),
					courseWorkload: 'PT20H',
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($course);
		$data = json_decode($json, true);

		$this->assertSame('CourseInstance', $data['hasCourseInstance'][0]['@type']);
		$this->assertSame('Person', $data['hasCourseInstance'][0]['instructor']['@type']);
		$this->assertSame('PT20H', $data['hasCourseInstance'][0]['courseWorkload']);
		$this->assertArrayNotHasKey('courseMode', $data['hasCourseInstance'][0]);
	}

	public function testCourseWithOfferWithoutItemCondition(): void
	{
		$course = new Course(
			name: 'Web Development Bootcamp',
			description: 'Full-stack web development.',
			offers: [
				new Offer(
					url: 'https://example.com/courses/webdev',
					priceCurrency: 'USD',
					price: 499.00,
					availability: ItemAvailability::InStock,
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($course);
		$data = json_decode($json, true);

		$this->assertSame('Offer', $data['offers'][0]['@type']);
		$this->assertEquals(499, $data['offers'][0]['price']);
		$this->assertArrayNotHasKey('itemCondition', $data['offers'][0]);
	}

	public function testOptionalFieldsOmitted(): void
	{
		$course = new Course(
			name: 'Test Course',
			description: 'A test.',
		);

		$json = JsonLdGenerator::SchemaToJson($course);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('provider', $data);
		$this->assertArrayNotHasKey('offers', $data);
		$this->assertArrayNotHasKey('hasCourseInstance', $data);
		$this->assertArrayNotHasKey('courseCode', $data);
	}
}
