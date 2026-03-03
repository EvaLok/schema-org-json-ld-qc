<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use PHPUnit\Framework\TestCase;

class RatingTest extends TestCase
{
	public function testMinimalRating(): void
	{
		$rating = new Rating(ratingValue: 4);
		$json = JsonLdGenerator::SchemaToJson($rating);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Rating', $data['@type']);
		$this->assertSame(4, $data['ratingValue']);
	}

	public function testRatingWithAllFields(): void
	{
		$rating = new Rating(
			ratingValue: 4,
			bestRating: 5,
			worstRating: 1,
		);
		$json = JsonLdGenerator::SchemaToJson($rating);
		$data = json_decode($json, true);

		$this->assertSame(5, $data['bestRating']);
		$this->assertSame(1, $data['worstRating']);
	}

	public function testRatingNullFieldsOmitted(): void
	{
		$rating = new Rating(ratingValue: 4);
		$json = JsonLdGenerator::SchemaToJson($rating);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('bestRating', $data);
		$this->assertArrayNotHasKey('worstRating', $data);
	}

	public function testRatingNestedInReview(): void
	{
		$review = new Review(
			author: 'Movie Fan',
			reviewRating: new Rating(ratingValue: 5, bestRating: 5, worstRating: 1),
		);
		$json = JsonLdGenerator::SchemaToJson($review);
		$data = json_decode($json, true);

		$this->assertSame('Rating', $data['reviewRating']['@type']);
		$this->assertSame(5, $data['reviewRating']['ratingValue']);
		$this->assertSame(5, $data['reviewRating']['bestRating']);
		$this->assertSame(1, $data['reviewRating']['worstRating']);
	}
}
