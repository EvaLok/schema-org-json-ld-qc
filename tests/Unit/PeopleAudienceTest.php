<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PeopleAudience;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;
use PHPUnit\Framework\TestCase;

class PeopleAudienceTest extends TestCase
{
	public function testMinimalPeopleAudience(): void
	{
		$audience = new PeopleAudience(suggestedGender: 'unisex');
		$json = JsonLdGenerator::SchemaToJson($audience);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('PeopleAudience', $data['@type']);
		$this->assertSame('unisex', $data['suggestedGender']);
		$this->assertArrayNotHasKey('suggestedMinAge', $data);
		$this->assertArrayNotHasKey('suggestedMaxAge', $data);
	}

	public function testPeopleAudienceWithAllFields(): void
	{
		$audience = new PeopleAudience(
			suggestedGender: 'unisex',
			suggestedMinAge: 18,
			suggestedMaxAge: 65,
		);
		$json = JsonLdGenerator::SchemaToJson($audience);
		$data = json_decode($json, true);

		$this->assertSame('unisex', $data['suggestedGender']);
		$this->assertSame(18, $data['suggestedMinAge']);
		$this->assertSame(65, $data['suggestedMaxAge']);
	}

	public function testPeopleAudienceNullFieldsOmitted(): void
	{
		$audience = new PeopleAudience(suggestedGender: 'unisex');
		$json = JsonLdGenerator::SchemaToJson($audience);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('suggestedMinAge', $data);
		$this->assertArrayNotHasKey('suggestedMaxAge', $data);
	}

	public function testPeopleAudienceNestedInProduct(): void
	{
		$product = new Product(
			name: 'Acme Trekking Backpack 35L',
			image: ['https://example.com/images/trekking-backpack-35l.jpg'],
			description: 'Lightweight hiking backpack designed for day trips and travel.',
			sku: 'ACME-BP-35',
			offers: [
				new Offer(
					url: 'https://example.com/products/acme-trekking-backpack-35l',
					priceCurrency: 'USD',
					price: 89.99,
					availability: ItemAvailability::InStock,
				),
			],
			audience: new PeopleAudience(
				suggestedGender: 'unisex',
				suggestedMinAge: 18,
				suggestedMaxAge: 65,
			),
		);
		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertSame('PeopleAudience', $data['audience']['@type']);
		$this->assertSame('unisex', $data['audience']['suggestedGender']);
		$this->assertSame(18, $data['audience']['suggestedMinAge']);
		$this->assertSame(65, $data['audience']['suggestedMaxAge']);
	}
}
