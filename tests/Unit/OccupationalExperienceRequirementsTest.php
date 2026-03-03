<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\JobPosting;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OccupationalExperienceRequirements;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use PHPUnit\Framework\TestCase;

class OccupationalExperienceRequirementsTest extends TestCase
{
	public function testMinimalOccupationalExperienceRequirements(): void
	{
		$requirements = new OccupationalExperienceRequirements(
			monthsOfExperience: 24,
		);

		$json = JsonLdGenerator::SchemaToJson($requirements);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('OccupationalExperienceRequirements', $data['@type']);
		$this->assertSame(24, $data['monthsOfExperience']);
	}

	public function testOccupationalExperienceRequirementsWithRequiredFieldsOnly(): void
	{
		$requirements = new OccupationalExperienceRequirements(
			monthsOfExperience: 60,
		);

		$json = JsonLdGenerator::SchemaToJson($requirements);
		$data = json_decode($json, true);

		$this->assertSame('OccupationalExperienceRequirements', $data['@type']);
		$this->assertSame(60, $data['monthsOfExperience']);
	}

	public function testOccupationalExperienceRequirementsNullFieldsOmitted(): void
	{
		$requirements = new OccupationalExperienceRequirements(
			monthsOfExperience: 12,
		);

		$json = JsonLdGenerator::SchemaToJson($requirements);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('name', $data);
		$this->assertArrayNotHasKey('description', $data);
	}

	public function testOccupationalExperienceRequirementsNestedObjectRendersCorrectly(): void
	{
		$job = new JobPosting(
			title: 'Senior Backend Engineer',
			description: 'Build resilient API services and mentor engineering teams.',
			datePosted: '2025-03-10',
			hiringOrganization: new Organization(name: 'Acme Platform Inc.'),
			experienceRequirements: new OccupationalExperienceRequirements(
				monthsOfExperience: 48,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($job);
		$data = json_decode($json, true);

		$this->assertSame('OccupationalExperienceRequirements', $data['experienceRequirements']['@type']);
		$this->assertSame(48, $data['experienceRequirements']['monthsOfExperience']);
	}
}
