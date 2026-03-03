<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\EducationalOccupationalCredential;
use EvaLok\SchemaOrgJsonLd\v1\Schema\JobPosting;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use PHPUnit\Framework\TestCase;

class EducationalOccupationalCredentialTest extends TestCase
{
	public function testMinimalEducationalOccupationalCredential(): void
	{
		$credential = new EducationalOccupationalCredential(
			credentialCategory: 'bachelor degree',
		);

		$json = JsonLdGenerator::SchemaToJson($credential);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('EducationalOccupationalCredential', $data['@type']);
		$this->assertSame('bachelor degree', $data['credentialCategory']);
	}

	public function testEducationalOccupationalCredentialWithAllFields(): void
	{
		$credential = new EducationalOccupationalCredential(
			credentialCategory: 'state registered nurse license',
		);

		$json = JsonLdGenerator::SchemaToJson($credential);
		$data = json_decode($json, true);

		$this->assertSame('EducationalOccupationalCredential', $data['@type']);
		$this->assertSame('state registered nurse license', $data['credentialCategory']);
	}

	public function testEducationalOccupationalCredentialNullFieldsOmitted(): void
	{
		$credential = new EducationalOccupationalCredential(
			credentialCategory: 'professional certification',
		);

		$json = JsonLdGenerator::SchemaToJson($credential);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('name', $data);
		$this->assertArrayNotHasKey('description', $data);
	}

	public function testEducationalOccupationalCredentialNestedObjectRendersCorrectly(): void
	{
		$job = new JobPosting(
			title: 'Registered Nurse',
			description: 'Provide direct patient care in a hospital setting.',
			datePosted: '2025-03-01',
			hiringOrganization: new Organization(name: 'City General Hospital'),
			educationRequirements: new EducationalOccupationalCredential(
				credentialCategory: 'state registered nurse license',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($job);
		$data = json_decode($json, true);

		$this->assertSame('EducationalOccupationalCredential', $data['educationRequirements']['@type']);
		$this->assertSame('state registered nurse license', $data['educationRequirements']['credentialCategory']);
	}
}
