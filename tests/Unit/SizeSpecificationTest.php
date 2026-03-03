<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SizeSpecification;
use PHPUnit\Framework\TestCase;

class SizeSpecificationTest extends TestCase
{
	public function testMinimal(): void
	{
		$schema = new SizeSpecification(name: 'Large');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('SizeSpecification', $data['@type']);
		$this->assertSame('Large', $data['name']);
	}

	public function testWithAllFields(): void
	{
		$schema = new SizeSpecification(
			name: 'Large',
			sizeGroup: 'regular',
			sizeSystem: 'US',
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertSame('SizeSpecification', $data['@type']);
		$this->assertSame('Large', $data['name']);
		$this->assertSame('regular', $data['sizeGroup']);
		$this->assertSame('US', $data['sizeSystem']);
	}

	public function testNullFieldsOmitted(): void
	{
		$schema = new SizeSpecification(name: 'Large');
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('sizeGroup', $data);
		$this->assertArrayNotHasKey('sizeSystem', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$schema = new Product(
			name: 'T-Shirt',
			image: ['https://example.com/t-shirt.jpg'],
			description: 'Soft cotton tee',
			sku: 'TSHIRT-001',
			offers: [
				new Offer(
					url: 'https://example.com/t-shirt',
					priceCurrency: 'USD',
					price: 19.99,
					availability: ItemAvailability::InStock,
				),
			],
			size: new SizeSpecification(
				name: 'Large',
				sizeGroup: 'regular',
				sizeSystem: 'US',
			),
		);
		$json = JsonLdGenerator::SchemaToJson($schema);
		$data = json_decode($json, true);

		$this->assertArrayHasKey('size', $data);
		$this->assertSame('SizeSpecification', $data['size']['@type']);
		$this->assertArrayNotHasKey('@context', $data['size']);
		$this->assertSame('Large', $data['size']['name']);
		$this->assertSame('regular', $data['size']['sizeGroup']);
		$this->assertSame('US', $data['size']['sizeSystem']);
	}
}
