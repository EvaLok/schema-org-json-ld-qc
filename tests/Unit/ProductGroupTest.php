<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Brand;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ProductGroup;
use PHPUnit\Framework\TestCase;

class ProductGroupTest extends TestCase
{
	public function testMinimalProductGroup(): void
	{
		$group = new ProductGroup(name: 'T-Shirt Collection');
		$json = JsonLdGenerator::SchemaToJson($group);
		$data = json_decode($json, true);

		$this->assertSame('ProductGroup', $data['@type']);
		$this->assertSame('T-Shirt Collection', $data['name']);
		$this->assertArrayNotHasKey('productGroupID', $data);
		$this->assertArrayNotHasKey('variesBy', $data);
		$this->assertArrayNotHasKey('hasVariant', $data);
	}

	public function testProductGroupWithVariants(): void
	{
		$variant = new Product(
			name: 'Blue T-Shirt',
			image: ['https://example.com/blue.jpg'],
			description: 'A blue t-shirt.',
			sku: 'TS-BLUE-M',
			offers: [
				new Offer(
					url: 'https://example.com/blue',
					priceCurrency: 'USD',
					price: 29.99,
					availability: ItemAvailability::InStock,
				),
			],
			color: 'Blue',
			size: 'M',
		);

		$group = new ProductGroup(
			name: 'T-Shirt Collection',
			productGroupID: 'tshirt-collection',
			variesBy: ['https://schema.org/color', 'https://schema.org/size'],
			hasVariant: [$variant],
			brand: new Brand(name: 'FashionCo'),
		);

		$json = JsonLdGenerator::SchemaToJson($group);
		$data = json_decode($json, true);

		$this->assertSame('tshirt-collection', $data['productGroupID']);
		$this->assertCount(2, $data['variesBy']);
		$this->assertIsArray($data['hasVariant']);
		$this->assertCount(1, $data['hasVariant']);
		$this->assertSame('Product', $data['hasVariant'][0]['@type']);
		$this->assertSame('Blue T-Shirt', $data['hasVariant'][0]['name']);
		$this->assertSame('Brand', $data['brand']['@type']);
	}

	public function testProductGroupWithSingleVariant(): void
	{
		$variant = new Product(
			name: 'Single Variant',
			image: ['https://example.com/single.jpg'],
			description: 'A single variant product.',
			sku: 'SV-001',
			offers: [
				new Offer(
					url: 'https://example.com/single',
					priceCurrency: 'USD',
					price: 15.00,
					availability: ItemAvailability::InStock,
				),
			],
		);

		$group = new ProductGroup(
			name: 'Single Group',
			hasVariant: $variant,
		);

		$json = JsonLdGenerator::SchemaToJson($group);
		$data = json_decode($json, true);

		$this->assertSame('ProductGroup', $data['@type']);
		$this->assertArrayHasKey('hasVariant', $data);
	}

	public function testProductGroupWithAllProperties(): void
	{
		$group = new ProductGroup(
			name: 'Full Group',
			productGroupID: 'full-group',
			variesBy: 'https://schema.org/color',
			url: 'https://example.com/full-group',
			description: 'A fully specified product group.',
			brand: new Brand(name: 'BrandX'),
			aggregateRating: new AggregateRating(
				ratingValue: 4.5,
				bestRating: 5,
				ratingCount: 100,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($group);
		$data = json_decode($json, true);

		$this->assertSame('full-group', $data['productGroupID']);
		$this->assertSame('https://schema.org/color', $data['variesBy']);
		$this->assertSame('https://example.com/full-group', $data['url']);
		$this->assertSame('A fully specified product group.', $data['description']);
		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
	}

	public function testProductWithIsVariantOf(): void
	{
		$group = new ProductGroup(
			name: 'Shoe Collection',
			productGroupID: 'shoe-collection',
			variesBy: ['https://schema.org/size', 'https://schema.org/color'],
		);

		$product = new Product(
			name: 'Running Shoe - Red Size 10',
			image: ['https://example.com/shoe-red.jpg'],
			description: 'A red running shoe in size 10.',
			sku: 'SHOE-RED-10',
			offers: [
				new Offer(
					url: 'https://example.com/shoe-red-10',
					priceCurrency: 'USD',
					price: 129.99,
					availability: ItemAvailability::InStock,
				),
			],
			isVariantOf: $group,
		);

		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertSame('Product', $data['@type']);
		$this->assertSame('ProductGroup', $data['isVariantOf']['@type']);
		$this->assertSame('Shoe Collection', $data['isVariantOf']['name']);
		$this->assertSame('shoe-collection', $data['isVariantOf']['productGroupID']);
	}
}
