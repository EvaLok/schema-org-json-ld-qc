<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Thing;
use PHPUnit\Framework\TestCase;

class ReviewTest extends TestCase
{
	public function testMinimalReview(): void
	{
		$review = new Review(
			author: 'John Reviewer',
			reviewRating: new Rating(ratingValue: 4),
		);

		$json = JsonLdGenerator::SchemaToJson($review);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Review', $data['@type']);
		$this->assertSame('John Reviewer', $data['author']);
		$this->assertSame('Rating', $data['reviewRating']['@type']);
		$this->assertSame(4, $data['reviewRating']['ratingValue']);
	}

	public function testReviewWithAllFields(): void
	{
		$review = new Review(
			author: 'Alice Bookworm',
			reviewRating: new Rating(
				ratingValue: 4.5,
				bestRating: 5,
				worstRating: 1,
			),
			reviewBody: 'An excellent read with surprising depth. The author weaves together complex themes seamlessly.',
			datePublished: '2025-08-15',
			name: 'A Masterful Page-Turner',
		);

		$json = JsonLdGenerator::SchemaToJson($review);
		$data = json_decode($json, true);

		$this->assertSame('Review', $data['@type']);
		$this->assertSame('Alice Bookworm', $data['author']);
		$this->assertSame(4.5, $data['reviewRating']['ratingValue']);
		$this->assertSame(5, $data['reviewRating']['bestRating']);
		$this->assertSame(1, $data['reviewRating']['worstRating']);
		$this->assertSame('An excellent read with surprising depth. The author weaves together complex themes seamlessly.', $data['reviewBody']);
		$this->assertSame('2025-08-15', $data['datePublished']);
		$this->assertSame('A Masterful Page-Turner', $data['name']);
	}

	public function testReviewNullFieldsOmitted(): void
	{
		$review = new Review(
			author: 'Minimal Reviewer',
			reviewRating: new Rating(ratingValue: 3),
		);

		$json = JsonLdGenerator::SchemaToJson($review);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('reviewBody', $data);
		$this->assertArrayNotHasKey('datePublished', $data);
		$this->assertArrayNotHasKey('name', $data);
		$this->assertArrayNotHasKey('itemReviewed', $data);
		$this->assertArrayNotHasKey('bestRating', $data['reviewRating']);
		$this->assertArrayNotHasKey('worstRating', $data['reviewRating']);
	}

	public function testReviewWithItemReviewedThing(): void
	{
		$review = new Review(
			author: 'Jane Critic',
			reviewRating: new Rating(ratingValue: 5, bestRating: 5),
			name: 'Outstanding product',
			itemReviewed: new Thing(name: 'Acme Wireless Headphones'),
		);

		$json = JsonLdGenerator::SchemaToJson($review);
		$data = json_decode($json, true);

		$this->assertSame('Review', $data['@type']);
		$this->assertArrayHasKey('itemReviewed', $data);
		$this->assertSame('Thing', $data['itemReviewed']['@type']);
		$this->assertSame('Acme Wireless Headphones', $data['itemReviewed']['name']);
	}

	public function testReviewWithItemReviewedPerson(): void
	{
		$review = new Review(
			author: 'Tech Reviewer',
			reviewRating: new Rating(ratingValue: 4, bestRating: 5),
			itemReviewed: new Person(name: 'Dr. Smith'),
		);

		$json = JsonLdGenerator::SchemaToJson($review);
		$data = json_decode($json, true);

		$this->assertSame('Review', $data['@type']);
		$this->assertArrayHasKey('itemReviewed', $data);
		$this->assertSame('Person', $data['itemReviewed']['@type']);
		$this->assertSame('Dr. Smith', $data['itemReviewed']['name']);
	}
}
