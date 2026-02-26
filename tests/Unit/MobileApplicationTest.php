<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MobileApplication;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Enum\OfferItemCondition;
use PHPUnit\Framework\TestCase;

class MobileApplicationTest extends TestCase
{
	public function testMinimalMobileApplication(): void
	{
		$app = new MobileApplication(
			name: 'Duolingo',
			offers: new Offer(
				url: 'https://play.google.com/store/apps/details?id=com.duolingo',
				priceCurrency: 'USD',
				price: 0,
				itemCondition: OfferItemCondition::NewCondition,
				availability: ItemAvailability::InStock,
			),
			aggregateRating: new AggregateRating(
				ratingValue: 4.7,
				ratingCount: 12500000,
				bestRating: 5,
				worstRating: 1,
			),
			applicationCategory: 'EducationApplication',
			operatingSystem: 'ANDROID',
		);

		$json = JsonLdGenerator::SchemaToJson($app);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('MobileApplication', $data['@type']);
		$this->assertSame('Duolingo', $data['name']);
		$this->assertSame('EducationApplication', $data['applicationCategory']);
		$this->assertSame('ANDROID', $data['operatingSystem']);
		$this->assertSame('Offer', $data['offers']['@type']);
		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
		$this->assertSame(1, $data['aggregateRating']['worstRating']);
	}

	public function testMobileApplicationTypeNotSoftwareApplication(): void
	{
		$app = new MobileApplication(
			name: 'TestApp',
			offers: new Offer(
				url: 'https://example.com/app',
				priceCurrency: 'USD',
				price: 4.99,
				itemCondition: OfferItemCondition::NewCondition,
				availability: ItemAvailability::InStock,
			),
			aggregateRating: null,
		);

		$json = JsonLdGenerator::SchemaToJson($app);
		$data = json_decode($json, true);

		$this->assertSame('MobileApplication', $data['@type'], 'Should be MobileApplication, not SoftwareApplication');
	}
}
