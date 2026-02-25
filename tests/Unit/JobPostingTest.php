<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AdministrativeArea;
use EvaLok\SchemaOrgJsonLd\v1\Schema\JobPosting;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MonetaryAmount;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Place;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use PHPUnit\Framework\TestCase;

class JobPostingTest extends TestCase
{
	public function testMinimalJobPosting(): void
	{
		$job = new JobPosting(
			title: 'Software Engineer',
			description: '<p>We are looking for a skilled software engineer to join our team.</p>',
			datePosted: '2025-03-01',
			hiringOrganization: new Organization(name: 'ACME Corp'),
			jobLocation: new Place(
				name: 'ACME HQ',
				address: new PostalAddress(
					streetAddress: '100 Innovation Way',
					addressLocality: 'Austin',
					addressRegion: 'TX',
					postalCode: '78701',
					addressCountry: 'US',
				),
			),
		);

		$json = JsonLdGenerator::SchemaToJson($job);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('JobPosting', $data['@type']);
		$this->assertSame('Software Engineer', $data['title']);
		$this->assertStringContainsString('skilled software engineer', $data['description']);
		$this->assertSame('2025-03-01', $data['datePosted']);
		$this->assertSame('Organization', $data['hiringOrganization']['@type']);
		$this->assertSame('ACME Corp', $data['hiringOrganization']['name']);
		$this->assertSame('Place', $data['jobLocation']['@type']);
	}

	public function testJobPostingWithSalary(): void
	{
		$job = new JobPosting(
			title: 'Senior Backend Developer',
			description: 'Lead backend architecture for our SaaS platform.',
			datePosted: '2025-02-15',
			hiringOrganization: new Organization(
				name: 'Tech Startup Inc.',
				url: 'https://techstartup.example.com',
				logo: 'https://techstartup.example.com/logo.png',
			),
			jobLocation: new Place(
				name: 'Remote',
				address: new PostalAddress(addressCountry: 'US'),
			),
			baseSalary: new MonetaryAmount(
				currency: 'USD',
				minValue: 120000.00,
				maxValue: 180000.00,
			),
			employmentType: 'FULL_TIME',
			validThrough: '2025-06-01',
			directApply: true,
		);

		$json = JsonLdGenerator::SchemaToJson($job);
		$data = json_decode($json, true);

		$this->assertSame('MonetaryAmount', $data['baseSalary']['@type']);
		$this->assertSame('USD', $data['baseSalary']['currency']);
		$this->assertEquals(120000, $data['baseSalary']['minValue']);
		$this->assertEquals(180000, $data['baseSalary']['maxValue']);
		$this->assertSame('FULL_TIME', $data['employmentType']);
		$this->assertSame('2025-06-01', $data['validThrough']);
		$this->assertTrue($data['directApply']);
	}

	public function testRemoteJobPosting(): void
	{
		$job = new JobPosting(
			title: 'DevOps Engineer',
			description: 'Manage cloud infrastructure and CI/CD pipelines.',
			datePosted: '2025-04-01',
			hiringOrganization: new Organization(name: 'CloudCo'),
			jobLocation: new Place(
				name: 'Remote',
				address: new PostalAddress(addressCountry: 'US'),
			),
			jobLocationType: 'TELECOMMUTE',
			applicantLocationRequirements: new AdministrativeArea(name: 'United States'),
		);

		$json = JsonLdGenerator::SchemaToJson($job);
		$data = json_decode($json, true);

		$this->assertSame('TELECOMMUTE', $data['jobLocationType']);
		$this->assertSame('AdministrativeArea', $data['applicantLocationRequirements']['@type']);
		$this->assertSame('United States', $data['applicantLocationRequirements']['name']);
	}

	public function testOptionalFieldsOmitted(): void
	{
		$job = new JobPosting(
			title: 'Intern',
			description: 'Summer internship.',
			datePosted: '2025-05-01',
			hiringOrganization: new Organization(name: 'Test Co'),
			jobLocation: new Place(name: 'Office'),
		);

		$json = JsonLdGenerator::SchemaToJson($job);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('baseSalary', $data);
		$this->assertArrayNotHasKey('employmentType', $data);
		$this->assertArrayNotHasKey('validThrough', $data);
		$this->assertArrayNotHasKey('directApply', $data);
		$this->assertArrayNotHasKey('jobLocationType', $data);
	}
}
