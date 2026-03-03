<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AdministrativeArea;
use EvaLok\SchemaOrgJsonLd\v1\Schema\JobPosting;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use PHPUnit\Framework\TestCase;

class AdministrativeAreaTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new AdministrativeArea(name: 'California');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('AdministrativeArea', $data['@type']);
		$this->assertSame('California', $data['name']);
	}

	public function testWithAllFields(): void
	{
		$schema = new AdministrativeArea(name: 'California');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('AdministrativeArea', $data['@type']);
		$this->assertSame('California', $data['name']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new AdministrativeArea(name: 'California');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertCount(3, $data);
		$this->assertSame('California', $data['name']);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new JobPosting(
			title: 'Backend Engineer',
			description: 'Build APIs',
			datePosted: '2025-09-01',
			hiringOrganization: new Organization(name: 'Example Inc'),
			applicantLocationRequirements: new AdministrativeArea(name: 'California'),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('AdministrativeArea', $data['applicantLocationRequirements']['@type']);
		$this->assertArrayNotHasKey('@context', $data['applicantLocationRequirements']);
		$this->assertSame('California', $data['applicantLocationRequirements']['name']);
	}
}

