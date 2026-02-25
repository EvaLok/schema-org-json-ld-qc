<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemList;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ListItem;
use PHPUnit\Framework\TestCase;

class ItemListTest extends TestCase
{
	public function testMinimalItemList(): void
	{
		$list = new ItemList(
			itemListElement: [
				new ListItem(position: 1, name: 'First Item'),
				new ListItem(position: 2, name: 'Second Item'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($list);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('ItemList', $data['@type']);
		$this->assertCount(2, $data['itemListElement']);
		$this->assertSame('ListItem', $data['itemListElement'][0]['@type']);
		$this->assertSame(1, $data['itemListElement'][0]['position']);
		$this->assertSame('First Item', $data['itemListElement'][0]['name']);
		$this->assertSame(2, $data['itemListElement'][1]['position']);
	}

	public function testItemListWithUrls(): void
	{
		$list = new ItemList(
			itemListElement: [
				new ListItem(position: 1, name: 'Best Coffee Shops in Portland', url: 'https://example.com/portland-coffee'),
				new ListItem(position: 2, name: 'Best Coffee Shops in Seattle', url: 'https://example.com/seattle-coffee'),
				new ListItem(position: 3, name: 'Best Coffee Shops in San Francisco', url: 'https://example.com/sf-coffee'),
			],
			itemListOrder: 'https://schema.org/ItemListOrderDescending',
			numberOfItems: 3,
		);

		$json = JsonLdGenerator::SchemaToJson($list);
		$data = json_decode($json, true);

		$this->assertSame('ItemList', $data['@type']);
		$this->assertCount(3, $data['itemListElement']);
		$this->assertSame('https://example.com/portland-coffee', $data['itemListElement'][0]['url']);
		$this->assertSame('https://schema.org/ItemListOrderDescending', $data['itemListOrder']);
		$this->assertSame(3, $data['numberOfItems']);
	}

	public function testItemListNullFieldsOmitted(): void
	{
		$list = new ItemList(
			itemListElement: [
				new ListItem(position: 1),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($list);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('itemListOrder', $data);
		$this->assertArrayNotHasKey('numberOfItems', $data);
		$this->assertArrayNotHasKey('name', $data['itemListElement'][0]);
		$this->assertArrayNotHasKey('url', $data['itemListElement'][0]);
	}
}
