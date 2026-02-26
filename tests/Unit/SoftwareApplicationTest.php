<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Enum\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SoftwareApplication;
use PHPUnit\Framework\TestCase;

class SoftwareApplicationTest extends TestCase
{
	public function testMinimalSoftwareApplication(): void
	{
		$app = new SoftwareApplication(
			name: 'Angry Birds',
			offers: new Offer(
				url: 'https://play.google.com/store/apps/details?id=com.rovio.angrybirds',
				priceCurrency: 'USD',
				price: 0,
				itemCondition: OfferItemCondition::NewCondition,
				availability: ItemAvailability::InStock,
			),
			aggregateRating: new AggregateRating(
				ratingValue: 4.6,
				ratingCount: 8864,
				bestRating: 5,
				worstRating: 1,
			),
			applicationCategory: 'GameApplication',
			operatingSystem: 'ANDROID',
		);

		$json = JsonLdGenerator::SchemaToJson($app);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('SoftwareApplication', $data['@type']);
		$this->assertSame('Angry Birds', $data['name']);
		$this->assertSame('GameApplication', $data['applicationCategory']);
		$this->assertSame('ANDROID', $data['operatingSystem']);
		$this->assertSame('Offer', $data['offers']['@type']);
		$this->assertEquals(0, $data['offers']['price']);
		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
		$this->assertSame(4.6, $data['aggregateRating']['ratingValue']);
		$this->assertSame(1, $data['aggregateRating']['worstRating']);
	}

	public function testSoftwareApplicationWithReview(): void
	{
		$app = new SoftwareApplication(
			name: 'VSCode',
			offers: new Offer(
				url: 'https://code.visualstudio.com/',
				priceCurrency: 'USD',
				price: 0,
				itemCondition: OfferItemCondition::NewCondition,
				availability: ItemAvailability::InStock,
			),
			aggregateRating: new AggregateRating(
				ratingValue: 4.8,
				reviewCount: 15000,
				bestRating: 5,
				worstRating: 1,
			),
			applicationCategory: 'DeveloperApplication',
			description: 'Free source-code editor by Microsoft.',
			review: new Review(
				author: 'Dev Daily',
				reviewRating: new Rating(ratingValue: 5, bestRating: 5),
				reviewBody: 'The best free code editor available.',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($app);
		$data = json_decode($json, true);

		$this->assertSame('Free source-code editor by Microsoft.', $data['description']);
		$this->assertSame('Review', $data['review']['@type']);
		$this->assertSame('Dev Daily', $data['review']['author']);
	}
}
