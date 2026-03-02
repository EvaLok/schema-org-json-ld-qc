<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateOffer;
use PHPUnit\Framework\TestCase;

class AggregateOfferTest extends TestCase
{
	public function testBasicAggregateOffer(): void
	{
		$offer = new AggregateOffer(
			lowPrice: 9.99,
			priceCurrency: 'USD',
		);

		$json = JsonLdGenerator::SchemaToJson($offer);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('AggregateOffer', $data['@type']);
		$this->assertEquals(9.99, $data['lowPrice']);
		$this->assertSame('USD', $data['priceCurrency']);
		$this->assertArrayNotHasKey('highPrice', $data);
		$this->assertArrayNotHasKey('offerCount', $data);
	}

	public function testFullAggregateOffer(): void
	{
		$offer = new AggregateOffer(
			lowPrice: 49.99,
			priceCurrency: 'EUR',
			highPrice: 199.99,
			offerCount: 12,
		);

		$json = JsonLdGenerator::SchemaToJson($offer);
		$data = json_decode($json, true);

		$this->assertSame('AggregateOffer', $data['@type']);
		$this->assertEquals(49.99, $data['lowPrice']);
		$this->assertSame('EUR', $data['priceCurrency']);
		$this->assertEquals(199.99, $data['highPrice']);
		$this->assertSame(12, $data['offerCount']);
	}

	public function testAggregateOfferNullFieldsOmitted(): void
	{
		$offer = new AggregateOffer(
			lowPrice: 19.99,
			priceCurrency: 'USD',
		);

		$json = JsonLdGenerator::SchemaToJson($offer);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('highPrice', $data);
		$this->assertArrayNotHasKey('offerCount', $data);
	}
}
