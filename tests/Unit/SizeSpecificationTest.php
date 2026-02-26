<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SizeSpecification;
use PHPUnit\Framework\TestCase;

class SizeSpecificationTest extends TestCase
{
	public function testBasicSizeSpecification(): void
	{
		$size = new SizeSpecification(name: 'Large');
		$json = JsonLdGenerator::SchemaToJson($size);
		$data = json_decode($json, true);

		$this->assertSame('SizeSpecification', $data['@type']);
		$this->assertSame('Large', $data['name']);
		$this->assertArrayNotHasKey('sizeSystem', $data);
		$this->assertArrayNotHasKey('sizeGroup', $data);
	}

	public function testSizeSpecificationWithAllProperties(): void
	{
		$size = new SizeSpecification(
			name: '42',
			sizeSystem: 'EU',
			sizeGroup: 'regular',
		);
		$json = JsonLdGenerator::SchemaToJson($size);
		$data = json_decode($json, true);

		$this->assertSame('SizeSpecification', $data['@type']);
		$this->assertSame('42', $data['name']);
		$this->assertSame('EU', $data['sizeSystem']);
		$this->assertSame('regular', $data['sizeGroup']);
	}
}
