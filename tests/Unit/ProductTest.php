<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Brand;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
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

	public function testProductWithAggregateRating(): void
	{
		$product = new Product(
			name: 'Smart Widget',
			image: ['https://example.com/widget.jpg'],
			description: 'A highly rated widget.',
			sku: 'SWG-001',
			offers: [
				new Offer(
					url: 'https://example.com/widget',
					priceCurrency: 'USD',
					price: 49.99,
					itemCondition: OfferItemCondition::NewCondition,
					availability: ItemAvailability::InStock,
				),
			],
			aggregateRating: new AggregateRating(
				ratingValue: 4.6,
				bestRating: 5,
				worstRating: 1,
				ratingCount: 150,
				reviewCount: 42,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
		$this->assertSame(4.6, $data['aggregateRating']['ratingValue']);
		$this->assertSame(5, $data['aggregateRating']['bestRating']);
		$this->assertSame(1, $data['aggregateRating']['worstRating']);
		$this->assertSame(150, $data['aggregateRating']['ratingCount']);
		$this->assertSame(42, $data['aggregateRating']['reviewCount']);
	}

	public function testProductWithSingleReview(): void
	{
		$product = new Product(
			name: 'Deluxe Gadget',
			image: ['https://example.com/gadget.jpg'],
			description: 'A fine gadget.',
			sku: 'GAD-001',
			offers: [
				new Offer(
					url: 'https://example.com/gadget',
					priceCurrency: 'USD',
					price: 79.99,
					itemCondition: OfferItemCondition::NewCondition,
					availability: ItemAvailability::InStock,
				),
			],
			review: new Review(
				author: new Person(name: 'Jane Doe'),
				reviewRating: new Rating(
					ratingValue: 5,
					bestRating: 5,
					worstRating: 1,
				),
				reviewBody: 'Absolutely fantastic gadget.',
				datePublished: '2025-06-15',
				name: 'Love it!',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertSame('Review', $data['review']['@type']);
		$this->assertSame('Person', $data['review']['author']['@type']);
		$this->assertSame('Jane Doe', $data['review']['author']['name']);
		$this->assertSame(5, $data['review']['reviewRating']['ratingValue']);
		$this->assertSame('Absolutely fantastic gadget.', $data['review']['reviewBody']);
	}

	public function testProductWithMultipleReviews(): void
	{
		$product = new Product(
			name: 'Ultra Widget',
			image: ['https://example.com/ultra.jpg'],
			description: 'The ultimate widget.',
			sku: 'UWG-001',
			offers: [
				new Offer(
					url: 'https://example.com/ultra',
					priceCurrency: 'USD',
					price: 199.99,
					itemCondition: OfferItemCondition::NewCondition,
					availability: ItemAvailability::InStock,
				),
			],
			review: [
				new Review(
					author: new Person(name: 'Bob Smith'),
					reviewRating: new Rating(ratingValue: 4),
					reviewBody: 'Very good, minor issues.',
				),
				new Review(
					author: new Person(name: 'Carol Davis'),
					reviewRating: new Rating(ratingValue: 5),
					reviewBody: 'Perfect!',
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertIsArray($data['review']);
		$this->assertCount(2, $data['review']);
		$this->assertSame('Bob Smith', $data['review'][0]['author']['name']);
		$this->assertSame('Carol Davis', $data['review'][1]['author']['name']);
	}

	public function testOfferWithoutItemCondition(): void
	{
		$product = new Product(
			name: 'Simple Offer Product',
			image: ['https://example.com/simple.jpg'],
			description: 'Testing Offer without itemCondition.',
			sku: 'SOP-001',
			offers: [
				new Offer(
					url: 'https://example.com/simple',
					priceCurrency: 'USD',
					price: 29.99,
					availability: ItemAvailability::InStock,
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertSame('Offer', $data['offers'][0]['@type']);
		$this->assertEquals(29.99, $data['offers'][0]['price']);
		$this->assertSame('https://schema.org/InStock', $data['offers'][0]['availability']);
		$this->assertArrayNotHasKey('itemCondition', $data['offers'][0]);
	}

	public function testProductOptionalFieldsOmitted(): void
	{
		$product = new Product(
			name: 'Basic Item',
			image: ['https://example.com/basic.jpg'],
			description: 'A basic item.',
			sku: 'BAS-001',
			offers: [
				new Offer(
					url: 'https://example.com/basic',
					priceCurrency: 'USD',
					price: 9.99,
					itemCondition: OfferItemCondition::NewCondition,
					availability: ItemAvailability::InStock,
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('brand', $data);
		$this->assertArrayNotHasKey('mpn', $data);
		$this->assertArrayNotHasKey('weight', $data);
		$this->assertArrayNotHasKey('aggregateRating', $data);
		$this->assertArrayNotHasKey('review', $data);
	}
}
