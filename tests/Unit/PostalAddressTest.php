<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Place;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use PHPUnit\Framework\TestCase;

class PostalAddressTest extends TestCase
{
	public function testMinimalPostalAddress(): void
	{
		$address = new PostalAddress();

		$json = JsonLdGenerator::SchemaToJson($address);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('PostalAddress', $data['@type']);
	}

	public function testFullPostalAddress(): void
	{
		$address = new PostalAddress(
			streetAddress: '123 Main St',
			addressLocality: 'San Francisco',
			addressRegion: 'CA',
			postalCode: '94102',
			addressCountry: 'US',
			postOfficeBoxNumber: 'PO Box 123',
		);

		$json = JsonLdGenerator::SchemaToJson($address);
		$data = json_decode($json, true);

		$this->assertSame('PostalAddress', $data['@type']);
		$this->assertSame('123 Main St', $data['streetAddress']);
		$this->assertSame('San Francisco', $data['addressLocality']);
		$this->assertSame('CA', $data['addressRegion']);
		$this->assertSame('94102', $data['postalCode']);
		$this->assertSame('US', $data['addressCountry']);
		$this->assertSame('PO Box 123', $data['postOfficeBoxNumber']);
	}

	public function testPostalAddressNullFieldsOmitted(): void
	{
		$address = new PostalAddress(streetAddress: '123 Main St');

		$json = JsonLdGenerator::SchemaToJson($address);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('addressLocality', $data);
		$this->assertArrayNotHasKey('addressRegion', $data);
		$this->assertArrayNotHasKey('postalCode', $data);
		$this->assertArrayNotHasKey('addressCountry', $data);
		$this->assertArrayNotHasKey('postOfficeBoxNumber', $data);
	}

	public function testPostalAddressNestedInPlaceRendersCorrectly(): void
	{
		$place = new Place(
			name: 'City Hall',
			address: new PostalAddress(streetAddress: '1 Dr Carlton B Goodlett Pl'),
		);

		$json = JsonLdGenerator::SchemaToJson($place);
		$data = json_decode($json, true);

		$this->assertSame('Place', $data['@type']);
		$this->assertSame('PostalAddress', $data['address']['@type']);
		$this->assertSame('1 Dr Carlton B Goodlett Pl', $data['address']['streetAddress']);
		$this->assertArrayNotHasKey('@context', $data['address']);
	}
}
