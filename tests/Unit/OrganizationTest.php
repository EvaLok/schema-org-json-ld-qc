<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ContactPoint;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use PHPUnit\Framework\TestCase;

class OrganizationTest extends TestCase
{
	public function testMinimalOrganization(): void
	{
		$org = new Organization(
			name: 'Acme Corp',
		);

		$json = JsonLdGenerator::SchemaToJson($org);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Organization', $data['@type']);
		$this->assertSame('Acme Corp', $data['name']);
	}

	public function testOrganizationWithFullDetails(): void
	{
		$org = new Organization(
			name: 'TechStart Inc.',
			url: 'https://techstart.example.com',
			logo: 'https://techstart.example.com/logo.png',
			description: 'Leading technology startup accelerator.',
			email: 'info@techstart.example.com',
			telephone: '+1-555-123-4567',
			address: new PostalAddress(
				streetAddress: '123 Innovation Drive',
				addressLocality: 'San Francisco',
				addressRegion: 'CA',
				postalCode: '94105',
				addressCountry: 'US',
			),
			contactPoint: new ContactPoint(
				telephone: '+1-555-987-6543',
				contactType: 'customer service',
				email: 'support@techstart.example.com',
			),
			sameAs: [
				'https://twitter.com/techstart',
				'https://linkedin.com/company/techstart',
			],
			foundingDate: '2020-03-15',
			legalName: 'TechStart Incorporated',
			alternateName: 'TechStart',
		);

		$json = JsonLdGenerator::SchemaToJson($org);
		$data = json_decode($json, true);

		$this->assertSame('Organization', $data['@type']);
		$this->assertSame('TechStart Inc.', $data['name']);
		$this->assertSame('https://techstart.example.com', $data['url']);
		$this->assertSame('https://techstart.example.com/logo.png', $data['logo']);
		$this->assertSame('Leading technology startup accelerator.', $data['description']);
		$this->assertSame('+1-555-123-4567', $data['telephone']);
		$this->assertSame('PostalAddress', $data['address']['@type']);
		$this->assertSame('123 Innovation Drive', $data['address']['streetAddress']);
		$this->assertSame('San Francisco', $data['address']['addressLocality']);
		$this->assertSame('CA', $data['address']['addressRegion']);
		$this->assertSame('94105', $data['address']['postalCode']);
		$this->assertSame('US', $data['address']['addressCountry']);
		$this->assertSame('ContactPoint', $data['contactPoint']['@type']);
		$this->assertSame('customer service', $data['contactPoint']['contactType']);
		$this->assertCount(2, $data['sameAs']);
		$this->assertSame('2020-03-15', $data['foundingDate']);
		$this->assertSame('TechStart Incorporated', $data['legalName']);
		$this->assertSame('TechStart', $data['alternateName']);
	}

	public function testOrganizationNullFieldsOmitted(): void
	{
		$org = new Organization(name: 'Minimal Co');

		$json = JsonLdGenerator::SchemaToJson($org);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('url', $data);
		$this->assertArrayNotHasKey('logo', $data);
		$this->assertArrayNotHasKey('email', $data);
		$this->assertArrayNotHasKey('telephone', $data);
		$this->assertArrayNotHasKey('address', $data);
		$this->assertArrayNotHasKey('contactPoint', $data);
		$this->assertArrayNotHasKey('sameAs', $data);
		$this->assertArrayNotHasKey('foundingDate', $data);
	}
}
