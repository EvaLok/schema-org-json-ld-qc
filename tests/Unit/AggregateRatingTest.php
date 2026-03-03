<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Movie;
use PHPUnit\Framework\TestCase;

class AggregateRatingTest extends TestCase
{
	public function testMinimalAggregateRating(): void
	{
		$aggregateRating = new AggregateRating(ratingValue: 4.5);
		$json = JsonLdGenerator::SchemaToJson($aggregateRating);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('AggregateRating', $data['@type']);
		$this->assertSame(4.5, $data['ratingValue']);
	}

	public function testAggregateRatingWithAllFields(): void
	{
		$aggregateRating = new AggregateRating(
			ratingValue: 4.5,
			bestRating: 5,
			worstRating: 1,
			ratingCount: 1250,
			reviewCount: 340,
			itemReviewed: new Movie(
				name: 'The Blueprint',
				image: 'https://example.com/the-blueprint.jpg',
			),
		);
		$json = JsonLdGenerator::SchemaToJson($aggregateRating);
		$data = json_decode($json, true);

		$this->assertSame(5, $data['bestRating']);
		$this->assertSame(1, $data['worstRating']);
		$this->assertSame(1250, $data['ratingCount']);
		$this->assertSame(340, $data['reviewCount']);
		$this->assertSame('Movie', $data['itemReviewed']['@type']);
		$this->assertSame('The Blueprint', $data['itemReviewed']['name']);
	}

	public function testAggregateRatingNullFieldsOmitted(): void
	{
		$aggregateRating = new AggregateRating(ratingValue: 4.5);
		$json = JsonLdGenerator::SchemaToJson($aggregateRating);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('bestRating', $data);
		$this->assertArrayNotHasKey('worstRating', $data);
		$this->assertArrayNotHasKey('ratingCount', $data);
		$this->assertArrayNotHasKey('reviewCount', $data);
		$this->assertArrayNotHasKey('itemReviewed', $data);
	}

	public function testAggregateRatingNestedInMovie(): void
	{
		$movie = new Movie(
			name: 'The Blueprint',
			image: 'https://example.com/images/the-blueprint.jpg',
			aggregateRating: new AggregateRating(ratingValue: 4.8, ratingCount: 520),
		);
		$json = JsonLdGenerator::SchemaToJson($movie);
		$data = json_decode($json, true);

		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
		$this->assertSame(4.8, $data['aggregateRating']['ratingValue']);
		$this->assertSame(520, $data['aggregateRating']['ratingCount']);
	}
}
