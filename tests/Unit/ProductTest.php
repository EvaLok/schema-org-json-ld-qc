<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Brand;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
	public function testMinimalProduct(): void
	{
		$product = new Product(
			name: 'Executive Anvil',
			image: ['https://example.com/anvil.jpg'],
			description: 'Sleek and deadly.',
			sku: 'ANVIL-001',
			offers: [
				new Offer(
					url: 'https://example.com/anvil',
					priceCurrency: 'USD',
					price: 119.99,
					itemCondition: OfferItemCondition::NewCondition,
					availability: ItemAvailability::InStock,
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Product', $data['@type']);
		$this->assertSame('Executive Anvil', $data['name']);
		$this->assertSame(['https://example.com/anvil.jpg'], $data['image']);
		$this->assertSame('Sleek and deadly.', $data['description']);
		$this->assertSame('ANVIL-001', $data['sku']);
		$this->assertCount(1, $data['offers']);
		$this->assertSame('Offer', $data['offers'][0]['@type']);
		$this->assertSame(119.99, $data['offers'][0]['price']);
		$this->assertSame('USD', $data['offers'][0]['priceCurrency']);
		$this->assertSame('https://schema.org/NewCondition', $data['offers'][0]['itemCondition']);
		$this->assertSame('https://schema.org/InStock', $data['offers'][0]['availability']);
	}

	public function testProductWithBrand(): void
	{
		$product = new Product(
			name: 'Pro Hammer',
			image: ['https://example.com/hammer.jpg'],
			description: 'Professional grade hammer.',
			sku: 'HAM-100',
			offers: [
				new Offer(
					url: 'https://example.com/hammer',
					priceCurrency: 'EUR',
					price: 45.00,
					itemCondition: OfferItemCondition::NewCondition,
					availability: ItemAvailability::InStock,
				),
			],
			brand: new Brand(name: 'ACME Tools'),
			mpn: 'HAM100PRO',
		);

		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertSame('Brand', $data['brand']['@type']);
		$this->assertSame('ACME Tools', $data['brand']['name']);
		$this->assertSame('HAM100PRO', $data['mpn']);
	}

	public function testProductWithMultipleOffers(): void
	{
		$product = new Product(
			name: 'Widget',
			image: ['https://example.com/widget.jpg'],
			description: 'A fine widget.',
			sku: 'WDG-001',
			offers: [
				new Offer(
					url: 'https://shop-a.com/widget',
					priceCurrency: 'USD',
					price: 10.00,
					itemCondition: OfferItemCondition::NewCondition,
					availability: ItemAvailability::InStock,
				),
				new Offer(
					url: 'https://shop-b.com/widget',
					priceCurrency: 'USD',
					price: 12.50,
					itemCondition: OfferItemCondition::RefurbishedCondition,
					availability: ItemAvailability::InStock,
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertCount(2, $data['offers']);
		$this->assertSame('https://schema.org/RefurbishedCondition', $data['offers'][1]['itemCondition']);
	}

	public function testEnumValuesAreFullUrls(): void
	{
		$offer = new Offer(
			url: 'https://example.com/item',
			priceCurrency: 'GBP',
			price: 25.00,
			itemCondition: OfferItemCondition::UsedCondition,
			availability: ItemAvailability::OutOfStock,
		);

		$product = new Product(
			name: 'Used Book',
			image: ['https://example.com/book.jpg'],
			description: 'A used book.',
			sku: 'BOOK-001',
			offers: [$offer],
		);

		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/UsedCondition', $data['offers'][0]['itemCondition']);
		$this->assertSame('https://schema.org/OutOfStock', $data['offers'][0]['availability']);
	}
}
