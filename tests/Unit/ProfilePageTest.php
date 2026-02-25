<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\InteractionCounter;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ProfilePage;
use PHPUnit\Framework\TestCase;

class ProfilePageTest extends TestCase
{
	public function testProfilePageWithPerson(): void
	{
		$profile = new ProfilePage(
			mainEntity: new Person(
				name: 'Jane Developer',
				url: 'https://janedeveloper.example.com',
				image: 'https://example.com/jane-photo.jpg',
				jobTitle: 'Senior Software Engineer',
				worksFor: new Organization(name: 'Tech Corp'),
				sameAs: [
					'https://twitter.com/janedeveloper',
					'https://github.com/janedeveloper',
					'https://linkedin.com/in/janedeveloper',
				],
			),
			dateCreated: '2020-06-15',
			dateModified: '2025-02-20',
		);

		$json = JsonLdGenerator::SchemaToJson($profile);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('ProfilePage', $data['@type']);
		$this->assertSame('Person', $data['mainEntity']['@type']);
		$this->assertSame('Jane Developer', $data['mainEntity']['name']);
		$this->assertSame('Senior Software Engineer', $data['mainEntity']['jobTitle']);
		$this->assertSame('Organization', $data['mainEntity']['worksFor']['@type']);
		$this->assertCount(3, $data['mainEntity']['sameAs']);
		$this->assertSame('2020-06-15', $data['dateCreated']);
		$this->assertSame('2025-02-20', $data['dateModified']);
	}

	public function testProfilePageWithOrganization(): void
	{
		$profile = new ProfilePage(
			mainEntity: new Organization(
				name: 'Open Source Foundation',
				url: 'https://opensourcefoundation.example.com',
				description: 'A non-profit supporting open source software development.',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($profile);
		$data = json_decode($json, true);

		$this->assertSame('ProfilePage', $data['@type']);
		$this->assertSame('Organization', $data['mainEntity']['@type']);
		$this->assertSame('Open Source Foundation', $data['mainEntity']['name']);
	}

	public function testOptionalFieldsOmitted(): void
	{
		$profile = new ProfilePage(
			mainEntity: new Person(name: 'Test User'),
		);

		$json = JsonLdGenerator::SchemaToJson($profile);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('dateCreated', $data);
		$this->assertArrayNotHasKey('dateModified', $data);
	}
}
