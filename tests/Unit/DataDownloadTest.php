<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DataDownload;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Dataset;
use PHPUnit\Framework\TestCase;

class DataDownloadTest extends TestCase
{
	public function testMinimalDataDownload(): void
	{
		$dataDownload = new DataDownload(
			contentUrl: 'https://example.com/data/climate-2025.csv',
		);

		$json = JsonLdGenerator::SchemaToJson($dataDownload);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('DataDownload', $data['@type']);
		$this->assertSame('https://example.com/data/climate-2025.csv', $data['contentUrl']);
	}

	public function testWithAllFields(): void
	{
		$dataDownload = new DataDownload(
			contentUrl: 'https://example.com/data/climate-2025.csv',
			encodingFormat: 'text/csv',
		);

		$json = JsonLdGenerator::SchemaToJson($dataDownload);
		$data = json_decode($json, true);

		$this->assertSame('DataDownload', $data['@type']);
		$this->assertSame('https://example.com/data/climate-2025.csv', $data['contentUrl']);
		$this->assertSame('text/csv', $data['encodingFormat']);
	}

	public function testNullFieldsOmitted(): void
	{
		$dataDownload = new DataDownload(
			contentUrl: 'https://example.com/data/climate-2025.csv',
		);

		$json = JsonLdGenerator::SchemaToJson($dataDownload);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('encodingFormat', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$dataset = new Dataset(
			name: 'Annual Climate Dataset',
			description: 'A yearly climate measurements dataset.',
			distribution: [
				new DataDownload(
					contentUrl: 'https://example.com/data/climate-2025.csv',
					encodingFormat: 'text/csv',
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($dataset);
		$data = json_decode($json, true);

		$this->assertSame('Dataset', $data['@type']);
		$this->assertSame('DataDownload', $data['distribution'][0]['@type']);
		$this->assertSame('https://example.com/data/climate-2025.csv', $data['distribution'][0]['contentUrl']);
		$this->assertSame('text/csv', $data['distribution'][0]['encodingFormat']);
		$this->assertArrayNotHasKey('@context', $data['distribution'][0]);
	}
}
