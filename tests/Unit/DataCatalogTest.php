<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DataCatalog;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Dataset;
use PHPUnit\Framework\TestCase;

class DataCatalogTest extends TestCase
{
	public function testMinimalDataCatalog(): void
	{
		$dataCatalog = new DataCatalog(
			name: 'Open Government Data Catalog',
		);

		$json = JsonLdGenerator::SchemaToJson($dataCatalog);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('DataCatalog', $data['@type']);
		$this->assertSame('Open Government Data Catalog', $data['name']);
	}

	public function testWithAllFields(): void
	{
		$dataCatalog = new DataCatalog(
			name: 'Open Government Data Catalog',
		);

		$json = JsonLdGenerator::SchemaToJson($dataCatalog);
		$data = json_decode($json, true);

		$this->assertSame('DataCatalog', $data['@type']);
		$this->assertSame('Open Government Data Catalog', $data['name']);
	}

	public function testNullFieldsOmitted(): void
	{
		$dataCatalog = new DataCatalog(
			name: 'Open Government Data Catalog',
		);

		$json = JsonLdGenerator::SchemaToJson($dataCatalog);
		$data = json_decode($json, true);

		$this->assertCount(3, $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$dataset = new Dataset(
			name: 'Annual Climate Dataset',
			description: 'A yearly climate measurements dataset.',
			includedInDataCatalog: new DataCatalog(
				name: 'Open Government Data Catalog',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($dataset);
		$data = json_decode($json, true);

		$this->assertSame('Dataset', $data['@type']);
		$this->assertSame('DataCatalog', $data['includedInDataCatalog']['@type']);
		$this->assertSame('Open Government Data Catalog', $data['includedInDataCatalog']['name']);
		$this->assertArrayNotHasKey('@context', $data['includedInDataCatalog']);
	}
}
