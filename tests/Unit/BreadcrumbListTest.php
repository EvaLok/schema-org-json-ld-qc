<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BreadcrumbList;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ListItem;
use PHPUnit\Framework\TestCase;

class BreadcrumbListTest extends TestCase
{
	public function testSimpleBreadcrumb(): void
	{
		$breadcrumb = new BreadcrumbList(
			itemListElement: [
				new ListItem(position: 1, name: 'Home', item: 'https://example.com/'),
				new ListItem(position: 2, name: 'Books', item: 'https://example.com/books'),
				new ListItem(position: 3, name: 'Science Fiction', item: 'https://example.com/books/sci-fi'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($breadcrumb);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('BreadcrumbList', $data['@type']);
		$this->assertCount(3, $data['itemListElement']);

		$this->assertSame('ListItem', $data['itemListElement'][0]['@type']);
		$this->assertSame(1, $data['itemListElement'][0]['position']);
		$this->assertSame('Home', $data['itemListElement'][0]['name']);
		$this->assertSame('https://example.com/', $data['itemListElement'][0]['item']);

		$this->assertSame(2, $data['itemListElement'][1]['position']);
		$this->assertSame('Books', $data['itemListElement'][1]['name']);

		$this->assertSame(3, $data['itemListElement'][2]['position']);
		$this->assertSame('Science Fiction', $data['itemListElement'][2]['name']);
	}

	public function testSingleItemBreadcrumb(): void
	{
		$breadcrumb = new BreadcrumbList(
			itemListElement: [
				new ListItem(position: 1, name: 'Home', item: 'https://example.com/'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($breadcrumb);
		$data = json_decode($json, true);

		$this->assertCount(1, $data['itemListElement']);
	}

	public function testNestedItemsHaveNoContext(): void
	{
		$breadcrumb = new BreadcrumbList(
			itemListElement: [
				new ListItem(position: 1, name: 'Home', item: 'https://example.com/'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($breadcrumb);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('@context', $data['itemListElement'][0]);
	}

	public function testDeepBreadcrumb(): void
	{
		$breadcrumb = new BreadcrumbList(
			itemListElement: [
				new ListItem(position: 1, name: 'Home', item: 'https://example.com/'),
				new ListItem(position: 2, name: 'Electronics', item: 'https://example.com/electronics'),
				new ListItem(position: 3, name: 'Phones', item: 'https://example.com/electronics/phones'),
				new ListItem(position: 4, name: 'Android', item: 'https://example.com/electronics/phones/android'),
				new ListItem(position: 5, name: 'Pixel 9', item: 'https://example.com/electronics/phones/android/pixel-9'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($breadcrumb);
		$data = json_decode($json, true);

		$this->assertCount(5, $data['itemListElement']);
		$this->assertSame(5, $data['itemListElement'][4]['position']);
		$this->assertSame('Pixel 9', $data['itemListElement'][4]['name']);
	}
}
