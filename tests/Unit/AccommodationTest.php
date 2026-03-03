<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Accommodation;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BedDetails;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ListItem;
use EvaLok\SchemaOrgJsonLd\v1\Schema\LocationFeatureSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use PHPUnit\Framework\TestCase;

class AccommodationTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new Accommodation(
			occupancy: new QuantitativeValue(minValue: 1, maxValue: 4, unitCode: 'C62'),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Accommodation', $data['@type']);
		$this->assertSame('QuantitativeValue', $data['occupancy']['@type']);
	}

	public function testWithAllFields(): void
	{
		$schema = new Accommodation(
			occupancy: new QuantitativeValue(minValue: 1, maxValue: 4, unitCode: 'C62'),
			additionalType: 'https://schema.org/Hotel',
			numberOfBedrooms: 2,
			numberOfBathroomsTotal: 1,
			numberOfRooms: 3,
			floorSize: new QuantitativeValue(value: 85, unitCode: 'MTK'),
			bed: [
				new BedDetails(numberOfBeds: 1, typeOfBed: 'King'),
				new BedDetails(numberOfBeds: 2, typeOfBed: 'Twin'),
			],
			amenityFeature: [
				new LocationFeatureSpecification(name: 'WiFi', value: true),
				new LocationFeatureSpecification(name: 'Pool', value: true),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('Accommodation', $data['@type']);
		$this->assertSame('https://schema.org/Hotel', $data['additionalType']);
		$this->assertSame(2, $data['numberOfBedrooms']);
		$this->assertSame(1, $data['numberOfBathroomsTotal']);
		$this->assertSame(3, $data['numberOfRooms']);
		$this->assertEquals(85.0, $data['floorSize']['value']);
		$this->assertSame('BedDetails', $data['bed'][0]['@type']);
		$this->assertSame('LocationFeatureSpecification', $data['amenityFeature'][0]['@type']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new Accommodation(
			occupancy: new QuantitativeValue(minValue: 1, maxValue: 4, unitCode: 'C62'),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('additionalType', $data);
		$this->assertArrayNotHasKey('numberOfBedrooms', $data);
		$this->assertArrayNotHasKey('numberOfBathroomsTotal', $data);
		$this->assertArrayNotHasKey('numberOfRooms', $data);
		$this->assertArrayNotHasKey('floorSize', $data);
		$this->assertArrayNotHasKey('bed', $data);
		$this->assertArrayNotHasKey('amenityFeature', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new ListItem(
			position: 1,
			name: 'Suite',
			item: new Accommodation(
				occupancy: new QuantitativeValue(minValue: 1, maxValue: 4, unitCode: 'C62'),
			),
		);

		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('Accommodation', $data['item']['@type']);
		$this->assertArrayNotHasKey('@context', $data['item']);
		$this->assertSame('C62', $data['item']['occupancy']['unitCode']);
	}
}
