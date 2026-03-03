<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Brand;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;
use PHPUnit\Framework\TestCase;

class BrandTest extends TestCase
{
	public function testMinimalBrand(): void
	{
		$brand = new Brand(name: 'Acme Corporation');
		$json = JsonLdGenerator::SchemaToJson($brand);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Brand', $data['@type']);
		$this->assertSame('Acme Corporation', $data['name']);
	}

	public function testBrandWithAllFields(): void
	{
		$brand = new Brand(
			name: 'Acme Corporation',
			description: 'Leading provider of quality goods since 1920',
		);
		$json = JsonLdGenerator::SchemaToJson($brand);
		$data = json_decode($json, true);

		$this->assertSame('Leading provider of quality goods since 1920', $data['description']);
	}

	public function testBrandNullFieldsOmitted(): void
	{
		$brand = new Brand(name: 'Acme Corporation');
		$json = JsonLdGenerator::SchemaToJson($brand);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('description', $data);
	}

	public function testBrandNestedInProduct(): void
	{
		$product = new Product(
			name: 'Acme Wireless Headphones',
			image: ['https://example.com/images/headphones-front.jpg'],
			description: 'Wireless over-ear headphones with active noise cancellation.',
			sku: 'ACME-WH-1000',
			offers: [
				new Offer(
					url: 'https://example.com/products/acme-wireless-headphones',
					priceCurrency: 'USD',
					price: 149.99,
					availability: ItemAvailability::InStock,
				),
			],
			brand: new Brand(name: 'Acme Corporation'),
		);
		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertSame('Brand', $data['brand']['@type']);
		$this->assertSame('Acme Corporation', $data['brand']['name']);
	}
}
