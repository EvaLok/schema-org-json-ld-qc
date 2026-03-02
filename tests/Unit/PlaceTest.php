<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Place;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use PHPUnit\Framework\TestCase;

class PlaceTest extends TestCase
{
	public function testMinimalPlace(): void
	{
		$place = new Place(name: 'Golden Gate Park');

		$json = JsonLdGenerator::SchemaToJson($place);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Place', $data['@type']);
		$this->assertSame('Golden Gate Park', $data['name']);
	}

	public function testFullPlace(): void
	{
		$place = new Place(
			name: 'Golden Gate Park',
			address: new PostalAddress(
				streetAddress: '501 Stanyan St',
				addressLocality: 'San Francisco',
				addressRegion: 'CA',
				postalCode: '94117',
				addressCountry: 'US',
			),
			geo: new GeoCoordinates(
				latitude: 37.7694,
				longitude: -122.4862,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($place);
		$data = json_decode($json, true);

		$this->assertSame('Place', $data['@type']);
		$this->assertSame('PostalAddress', $data['address']['@type']);
		$this->assertSame('501 Stanyan St', $data['address']['streetAddress']);
		$this->assertSame('GeoCoordinates', $data['geo']['@type']);
		$this->assertSame(37.7694, $data['geo']['latitude']);
		$this->assertSame(-122.4862, $data['geo']['longitude']);
	}

	public function testPlaceNullFieldsOmitted(): void
	{
		$place = new Place(
			name: 'Simple Park',
			address: null,
			geo: null,
		);

		$json = JsonLdGenerator::SchemaToJson($place);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('address', $data);
		$this->assertArrayNotHasKey('geo', $data);
	}

	public function testPlaceNestedObjectsRenderCorrectly(): void
	{
		$place = new Place(
			name: 'Lands End',
			address: new PostalAddress(streetAddress: '680 Point Lobos Ave'),
			geo: new GeoCoordinates(latitude: 37.7802, longitude: -122.5133),
		);

		$json = JsonLdGenerator::SchemaToJson($place);
		$data = json_decode($json, true);

		$this->assertSame('PostalAddress', $data['address']['@type']);
		$this->assertSame('680 Point Lobos Ave', $data['address']['streetAddress']);
		$this->assertArrayNotHasKey('@context', $data['address']);
		$this->assertSame('GeoCoordinates', $data['geo']['@type']);
		$this->assertArrayNotHasKey('@context', $data['geo']);
	}
}
