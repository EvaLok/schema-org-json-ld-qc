<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ContactPoint;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use PHPUnit\Framework\TestCase;

class ContactPointTest extends TestCase
{
	public function testMinimalContactPoint(): void
	{
		$contactPoint = new ContactPoint();

		$json = JsonLdGenerator::SchemaToJson($contactPoint);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('ContactPoint', $data['@type']);
	}

	public function testFullContactPoint(): void
	{
		$contactPoint = new ContactPoint(
			telephone: '+1-800-555-0199',
			email: 'support@example.com',
			contactType: 'customer service',
			areaServed: 'US',
			availableLanguage: 'English',
		);

		$json = JsonLdGenerator::SchemaToJson($contactPoint);
		$data = json_decode($json, true);

		$this->assertSame('ContactPoint', $data['@type']);
		$this->assertSame('+1-800-555-0199', $data['telephone']);
		$this->assertSame('support@example.com', $data['email']);
		$this->assertSame('customer service', $data['contactType']);
		$this->assertSame('US', $data['areaServed']);
		$this->assertSame('English', $data['availableLanguage']);
	}

	public function testContactPointNullFieldsOmitted(): void
	{
		$contactPoint = new ContactPoint(telephone: '+1-800-555-0100');

		$json = JsonLdGenerator::SchemaToJson($contactPoint);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('email', $data);
		$this->assertArrayNotHasKey('contactType', $data);
		$this->assertArrayNotHasKey('areaServed', $data);
		$this->assertArrayNotHasKey('availableLanguage', $data);
	}

	public function testContactPointNestedInOrganizationRendersCorrectly(): void
	{
		$organization = new Organization(
			name: 'Example Corp',
			contactPoint: new ContactPoint(
				telephone: '+1-800-555-0199',
				email: 'support@example.com',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($organization);
		$data = json_decode($json, true);

		$this->assertSame('Organization', $data['@type']);
		$this->assertSame('ContactPoint', $data['contactPoint']['@type']);
		$this->assertSame('+1-800-555-0199', $data['contactPoint']['telephone']);
		$this->assertSame('support@example.com', $data['contactPoint']['email']);
		$this->assertArrayNotHasKey('@context', $data['contactPoint']);
	}
}
