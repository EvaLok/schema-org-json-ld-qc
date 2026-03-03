<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ListItem;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Thing;
use PHPUnit\Framework\TestCase;

class ThingTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new Thing(name: 'A Generic Thing');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Thing', $data['@type']);
		$this->assertSame('A Generic Thing', $data['name']);
	}

	public function testWithAllFields(): void
	{
		$schema = new Thing(name: 'A Generic Thing');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('Thing', $data['@type']);
		$this->assertSame('A Generic Thing', $data['name']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new Thing(name: 'A Generic Thing');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertCount(3, $data);
		$this->assertSame('A Generic Thing', $data['name']);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new ListItem(
			position: 1,
			name: 'Thing Entry',
			item: new Thing(name: 'A Generic Thing'),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('Thing', $data['item']['@type']);
		$this->assertArrayNotHasKey('@context', $data['item']);
		$this->assertSame('A Generic Thing', $data['item']['name']);
	}
}

