<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemList;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ListItem;
use PHPUnit\Framework\TestCase;

class ListItemTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new ListItem(position: 1);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('ListItem', $data['@type']);
		$this->assertSame(1, $data['position']);
	}

	public function testWithAllFields(): void
	{
		$schema = new ListItem(
			position: 1,
			name: 'First Item',
			item: 'https://example.com/item-1',
			url: 'https://example.com/list-item-1',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('First Item', $data['name']);
		$this->assertSame('https://example.com/item-1', $data['item']);
		$this->assertSame('https://example.com/list-item-1', $data['url']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new ListItem(position: 1);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('name', $data);
		$this->assertArrayNotHasKey('item', $data);
		$this->assertArrayNotHasKey('url', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new ItemList(
			itemListElement: [new ListItem(position: 1, name: 'First Item', item: 'https://example.com/item-1')],
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('ListItem', $data['itemListElement'][0]['@type']);
		$this->assertArrayNotHasKey('@context', $data['itemListElement'][0]);
		$this->assertSame(1, $data['itemListElement'][0]['position']);
	}
}

