<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\JobPosting;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Place;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PropertyValue;
use PHPUnit\Framework\TestCase;

class PropertyValueTest extends TestCase
{
	public function testMinimalPropertyValue(): void
	{
		$propertyValue = new PropertyValue(
			name: 'color',
			value: 'Midnight Blue',
		);

		$json = JsonLdGenerator::SchemaToJson($propertyValue);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('PropertyValue', $data['@type']);
		$this->assertSame('color', $data['name']);
		$this->assertSame('Midnight Blue', $data['value']);
	}

	public function testPropertyValueWithAllFields(): void
	{
		$propertyValue = new PropertyValue(
			name: 'sku',
			value: 'WB-1000',
		);

		$json = JsonLdGenerator::SchemaToJson($propertyValue);
		$data = json_decode($json, true);

		$this->assertSame('PropertyValue', $data['@type']);
		$this->assertSame('sku', $data['name']);
		$this->assertSame('WB-1000', $data['value']);
	}

	public function testPropertyValueNullFieldsOmitted(): void
	{
		$propertyValue = new PropertyValue(
			name: 'material',
			value: 'stainless steel',
		);

		$json = JsonLdGenerator::SchemaToJson($propertyValue);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('description', $data);
		$this->assertArrayNotHasKey('url', $data);
	}

	public function testPropertyValueNestedObjectRendersCorrectly(): void
	{
		$job = new JobPosting(
			title: 'Software Engineer',
			description: 'Build and maintain software systems.',
			datePosted: '2025-03-01',
			hiringOrganization: new Organization(name: 'ACME Corp'),
			jobLocation: new Place(
				name: 'ACME HQ',
				address: new PostalAddress(addressCountry: 'US'),
			),
			identifier: new PropertyValue(
				name: 'Internal Job ID',
				value: 'SE-2025-0042',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($job);
		$data = json_decode($json, true);

		$this->assertSame('PropertyValue', $data['identifier']['@type']);
		$this->assertSame('Internal Job ID', $data['identifier']['name']);
		$this->assertSame('SE-2025-0042', $data['identifier']['value']);
	}
}
