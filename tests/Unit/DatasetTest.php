<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DataCatalog;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DataDownload;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Dataset;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use PHPUnit\Framework\TestCase;

class DatasetTest extends TestCase
{
	public function testMinimalDataset(): void
	{
		$dataset = new Dataset(
			name: 'Global Temperature Anomalies 1880-2024',
			description: 'Monthly global surface temperature anomalies relative to the 1951-1980 base period.',
		);

		$json = JsonLdGenerator::SchemaToJson($dataset);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Dataset', $data['@type']);
		$this->assertSame('Global Temperature Anomalies 1880-2024', $data['name']);
		$this->assertStringContainsString('temperature anomalies', $data['description']);
	}

	public function testFullDataset(): void
	{
		$dataset = new Dataset(
			name: 'US Census Population Data 2020',
			description: 'Complete population counts by state and county from the 2020 US Census.',
			url: 'https://data.census.gov/datasets/population-2020',
			creator: new Organization(
				name: 'US Census Bureau',
				url: 'https://www.census.gov',
			),
			license: 'https://creativecommons.org/publicdomain/zero/1.0/',
			keywords: ['census', 'population', 'demographics', 'United States'],
			isAccessibleForFree: true,
			temporalCoverage: '2020',
			includedInDataCatalog: new DataCatalog(name: 'US Government Open Data'),
			distribution: [
				new DataDownload(
					contentUrl: 'https://data.census.gov/download/population-2020.csv',
					encodingFormat: 'text/csv',
				),
				new DataDownload(
					contentUrl: 'https://data.census.gov/download/population-2020.json',
					encodingFormat: 'application/json',
				),
			],
			version: '1.0',
		);

		$json = JsonLdGenerator::SchemaToJson($dataset);
		$data = json_decode($json, true);

		$this->assertSame('Dataset', $data['@type']);
		$this->assertSame('https://data.census.gov/datasets/population-2020', $data['url']);
		$this->assertSame('Organization', $data['creator']['@type']);
		$this->assertSame('https://creativecommons.org/publicdomain/zero/1.0/', $data['license']);
		$this->assertCount(4, $data['keywords']);
		$this->assertTrue($data['isAccessibleForFree']);
		$this->assertSame('2020', $data['temporalCoverage']);
		$this->assertSame('DataCatalog', $data['includedInDataCatalog']['@type']);
		$this->assertCount(2, $data['distribution']);
		$this->assertSame('DataDownload', $data['distribution'][0]['@type']);
		$this->assertSame('text/csv', $data['distribution'][0]['encodingFormat']);
		$this->assertSame('1.0', $data['version']);
	}

	public function testDatasetWithPersonCreator(): void
	{
		$dataset = new Dataset(
			name: 'Research Dataset',
			description: 'Data from a research study.',
			creator: new Person(name: 'Dr. Research'),
		);

		$json = JsonLdGenerator::SchemaToJson($dataset);
		$data = json_decode($json, true);

		$this->assertSame('Person', $data['creator']['@type']);
		$this->assertSame('Dr. Research', $data['creator']['name']);
	}

	public function testOptionalFieldsOmitted(): void
	{
		$dataset = new Dataset(
			name: 'Simple Dataset',
			description: 'A simple test dataset.',
		);

		$json = JsonLdGenerator::SchemaToJson($dataset);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('url', $data);
		$this->assertArrayNotHasKey('creator', $data);
		$this->assertArrayNotHasKey('license', $data);
		$this->assertArrayNotHasKey('keywords', $data);
		$this->assertArrayNotHasKey('distribution', $data);
	}
}
