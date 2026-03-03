<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DefinedRegion;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferShippingDetails;
use PHPUnit\Framework\TestCase;

class DefinedRegionTest extends TestCase
{
	public function testMinimalDefinedRegion(): void
	{
		$schema = new DefinedRegion(addressCountry: 'US');

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('DefinedRegion', $data['@type']);
		$this->assertSame('US', $data['addressCountry']);
	}

	public function testWithAllFields(): void
	{
		$schema = new DefinedRegion(
			addressCountry: 'US',
			addressRegion: ['CA', 'NY'],
			postalCode: '10001',
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('DefinedRegion', $data['@type']);
		$this->assertSame('US', $data['addressCountry']);
		$this->assertSame(['CA', 'NY'], $data['addressRegion']);
		$this->assertSame('10001', $data['postalCode']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new DefinedRegion(addressCountry: 'US');

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('addressRegion', $data);
		$this->assertArrayNotHasKey('postalCode', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new OfferShippingDetails(
			shippingDestination: new DefinedRegion(
				addressCountry: 'US',
				addressRegion: 'CA',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('DefinedRegion', $data['shippingDestination']['@type']);
		$this->assertSame('US', $data['shippingDestination']['addressCountry']);
		$this->assertSame('CA', $data['shippingDestination']['addressRegion']);
	}
}
