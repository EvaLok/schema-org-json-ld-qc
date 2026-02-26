<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Article;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BreadcrumbList;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ListItem;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;
use PHPUnit\Framework\TestCase;

class GraphTest extends TestCase
{
	public function testSchemasToJsonBasic(): void
	{
		$org = new Organization(name: 'Acme Corp');
		$person = new Person(name: 'Jane Smith', jobTitle: 'CTO');

		$json = JsonLdGenerator::SchemasToJson($org, $person);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertArrayHasKey('@graph', $data);
		$this->assertCount(2, $data['@graph']);
		$this->assertSame('Organization', $data['@graph'][0]['@type']);
		$this->assertSame('Acme Corp', $data['@graph'][0]['name']);
		$this->assertSame('Person', $data['@graph'][1]['@type']);
		$this->assertSame('Jane Smith', $data['@graph'][1]['name']);
	}

	public function testSchemasToJsonNoIndividualContext(): void
	{
		$org = new Organization(name: 'Test Corp');
		$person = new Person(name: 'Test Person');

		$json = JsonLdGenerator::SchemasToJson($org, $person);
		$data = json_decode($json, true);

		$this->assertArrayHasKey('@context', $data);
		$this->assertArrayNotHasKey('@context', $data['@graph'][0]);
		$this->assertArrayNotHasKey('@context', $data['@graph'][1]);
	}

	public function testSchemasToObjectStructure(): void
	{
		$org = new Organization(name: 'Obj Corp');
		$person = new Person(name: 'Obj Person');

		$result = JsonLdGenerator::SchemasToObject($org, $person);

		$this->assertIsArray($result);
		$this->assertSame('https://schema.org/', $result['@context']);
		$this->assertIsArray($result['@graph']);
		$this->assertCount(2, $result['@graph']);
		$this->assertSame('Organization', $result['@graph'][0]['@type']);
		$this->assertSame('Person', $result['@graph'][1]['@type']);
	}

	public function testSchemasToJsonSingleSchema(): void
	{
		$org = new Organization(name: 'Solo Corp');

		$json = JsonLdGenerator::SchemasToJson($org);
		$data = json_decode($json, true);

		$this->assertArrayHasKey('@graph', $data);
		$this->assertCount(1, $data['@graph']);
		$this->assertSame('Organization', $data['@graph'][0]['@type']);
	}

	public function testSchemasToJsonPreservesOrder(): void
	{
		$article = new Article(
			headline: 'Test Article',
			image: ['https://example.com/img.jpg'],
			author: [new Person(name: 'Author')],
			datePublished: '2026-01-01',
		);
		$breadcrumb = new BreadcrumbList(
			itemListElement: [
				new ListItem(position: 1, name: 'Home', item: 'https://example.com/'),
			],
		);
		$org = new Organization(name: 'Publisher Inc');

		$json = JsonLdGenerator::SchemasToJson($article, $breadcrumb, $org);
		$data = json_decode($json, true);

		$this->assertCount(3, $data['@graph']);
		$this->assertSame('Article', $data['@graph'][0]['@type']);
		$this->assertSame('BreadcrumbList', $data['@graph'][1]['@type']);
		$this->assertSame('Organization', $data['@graph'][2]['@type']);
	}

	public function testSchemasToJsonWithNestedTypes(): void
	{
		$product = new Product(
			name: 'Widget',
			image: ['https://example.com/widget.jpg'],
			description: 'A test widget.',
			sku: 'WDG-001',
			offers: [
				new Offer(
					url: 'https://example.com/widget',
					priceCurrency: 'USD',
					price: 9.99,
					availability: ItemAvailability::InStock,
				),
			],
		);
		$org = new Organization(name: 'Widget Co');

		$json = JsonLdGenerator::SchemasToJson($product, $org);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('@context', $data['@graph'][0]['offers'][0]);
		$this->assertSame('Product', $data['@graph'][0]['@type']);
		$this->assertSame('Organization', $data['@graph'][1]['@type']);
	}
}
