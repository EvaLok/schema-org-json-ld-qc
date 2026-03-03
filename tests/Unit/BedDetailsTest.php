<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Accommodation;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BedDetails;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use PHPUnit\Framework\TestCase;

class BedDetailsTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new BedDetails(numberOfBeds: 1);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('BedDetails', $data['@type']);
		$this->assertSame(1, $data['numberOfBeds']);
	}

	public function testWithAllFields(): void
	{
		$schema = new BedDetails(numberOfBeds: 1, typeOfBed: 'King');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('BedDetails', $data['@type']);
		$this->assertSame(1, $data['numberOfBeds']);
		$this->assertSame('King', $data['typeOfBed']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new BedDetails(numberOfBeds: 1);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('typeOfBed', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new Accommodation(
			occupancy: new QuantitativeValue(minValue: 1, maxValue: 4, unitCode: 'C62'),
			bed: [new BedDetails(numberOfBeds: 1, typeOfBed: 'King')],
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('BedDetails', $data['bed'][0]['@type']);
		$this->assertArrayNotHasKey('@context', $data['bed'][0]);
		$this->assertSame('King', $data['bed'][0]['typeOfBed']);
	}
}

