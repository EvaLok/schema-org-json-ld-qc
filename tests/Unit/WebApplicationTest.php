<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\Schema\WebApplication;
use PHPUnit\Framework\TestCase;

class WebApplicationTest extends TestCase
{
	public function testMinimalWebApplication(): void
	{
		$app = new WebApplication(
			name: 'Google Docs',
			offers: new Offer(
				url: 'https://docs.google.com',
				priceCurrency: 'USD',
				price: 0,
				itemCondition: OfferItemCondition::NewCondition,
				availability: ItemAvailability::InStock,
			),
			aggregateRating: new AggregateRating(
				ratingValue: 4.5,
				reviewCount: 50000,
			),
			applicationCategory: 'BusinessApplication',
		);

		$json = JsonLdGenerator::SchemaToJson($app);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('WebApplication', $data['@type']);
		$this->assertSame('Google Docs', $data['name']);
		$this->assertSame('BusinessApplication', $data['applicationCategory']);
		$this->assertSame('Offer', $data['offers']['@type']);
		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
	}

	public function testWebApplicationWithDescription(): void
	{
		$app = new WebApplication(
			name: 'Figma',
			offers: new Offer(
				url: 'https://figma.com/pricing',
				priceCurrency: 'USD',
				price: 12.00,
				itemCondition: OfferItemCondition::NewCondition,
				availability: ItemAvailability::InStock,
			),
			aggregateRating: new AggregateRating(
				ratingValue: 4.8,
				ratingCount: 9500,
			),
			applicationCategory: 'DesignApplication',
			description: 'Collaborative interface design tool for teams.',
			screenshot: 'https://figma.com/screenshot.png',
		);

		$json = JsonLdGenerator::SchemaToJson($app);
		$data = json_decode($json, true);

		$this->assertSame('WebApplication', $data['@type']);
		$this->assertSame('Collaborative interface design tool for teams.', $data['description']);
		$this->assertSame('https://figma.com/screenshot.png', $data['screenshot']);
	}

	public function testWebApplicationTypeNotSoftwareApplication(): void
	{
		$app = new WebApplication(
			name: 'TestWebApp',
			offers: new Offer(
				url: 'https://example.com/app',
				priceCurrency: 'USD',
				price: 0,
				itemCondition: OfferItemCondition::NewCondition,
				availability: ItemAvailability::InStock,
			),
			aggregateRating: null,
		);

		$json = JsonLdGenerator::SchemaToJson($app);
		$data = json_decode($json, true);

		$this->assertSame('WebApplication', $data['@type'], 'Should be WebApplication, not SoftwareApplication');
	}
}
