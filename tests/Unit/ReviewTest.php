<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemList;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ListItem;
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
			author: new Person(name: 'James Wilson'),
			reviewRating: new Rating(
				ratingValue: 4,
				bestRating: 5,
				worstRating: 1,
			),
			reviewBody: 'Excellent product with great build quality. Minor issues with the manual.',
			datePublished: '2025-03-15',
			name: 'Great quality, minor documentation issues',
			itemReviewed: new Thing(name: 'Acme Wireless Headphones'),
			positiveNotes: new ItemList(
				itemListElement: [
					new ListItem(position: 1, name: 'Excellent build quality'),
					new ListItem(position: 2, name: 'Great sound clarity'),
					new ListItem(position: 3, name: 'Comfortable fit'),
				],
			),
			negativeNotes: new ItemList(
				itemListElement: [
					new ListItem(position: 1, name: 'Manual could be clearer'),
					new ListItem(position: 2, name: 'No carrying case included'),
				],
			),
			contentReferenceTime: '2025-03-14T10:00:00Z',
		);

		$json = JsonLdGenerator::SchemaToJson($review);
		$data = json_decode($json, true);

		$this->assertSame('Review', $data['@type']);
		$this->assertSame('Person', $data['author']['@type']);
		$this->assertSame('James Wilson', $data['author']['name']);
		$this->assertSame(4, $data['reviewRating']['ratingValue']);
		$this->assertSame(5, $data['reviewRating']['bestRating']);
		$this->assertSame(1, $data['reviewRating']['worstRating']);
		$this->assertSame('Excellent product with great build quality. Minor issues with the manual.', $data['reviewBody']);
		$this->assertSame('2025-03-15', $data['datePublished']);
		$this->assertSame('Great quality, minor documentation issues', $data['name']);
		$this->assertSame('Thing', $data['itemReviewed']['@type']);
		$this->assertSame('Acme Wireless Headphones', $data['itemReviewed']['name']);
		$this->assertSame('ItemList', $data['positiveNotes']['@type']);
		$this->assertCount(3, $data['positiveNotes']['itemListElement']);
		$this->assertSame('ListItem', $data['positiveNotes']['itemListElement'][0]['@type']);
		$this->assertSame('Excellent build quality', $data['positiveNotes']['itemListElement'][0]['name']);
		$this->assertSame('ItemList', $data['negativeNotes']['@type']);
		$this->assertCount(2, $data['negativeNotes']['itemListElement']);
		$this->assertSame('ListItem', $data['negativeNotes']['itemListElement'][0]['@type']);
		$this->assertSame('Manual could be clearer', $data['negativeNotes']['itemListElement'][0]['name']);
		$this->assertSame('2025-03-14T10:00:00Z', $data['contentReferenceTime']);
	}

	public function testReviewWithPositiveAndNegativeNotes(): void
	{
		$review = new Review(
			author: 'Notes Reviewer',
			reviewRating: new Rating(ratingValue: 4),
			positiveNotes: new ItemList(
				itemListElement: [
					new ListItem(position: 1, name: 'Excellent build quality'),
					new ListItem(position: 2, name: 'Great sound clarity'),
				],
			),
			negativeNotes: new ItemList(
				itemListElement: [
					new ListItem(position: 1, name: 'Manual could be clearer'),
				],
			),
		);

		$json = JsonLdGenerator::SchemaToJson($review);
		$data = json_decode($json, true);

		$this->assertSame('ItemList', $data['positiveNotes']['@type']);
		$this->assertCount(2, $data['positiveNotes']['itemListElement']);
		$this->assertSame('ListItem', $data['positiveNotes']['itemListElement'][1]['@type']);
		$this->assertSame(2, $data['positiveNotes']['itemListElement'][1]['position']);
		$this->assertSame('Great sound clarity', $data['positiveNotes']['itemListElement'][1]['name']);
		$this->assertSame('ItemList', $data['negativeNotes']['@type']);
		$this->assertCount(1, $data['negativeNotes']['itemListElement']);
		$this->assertSame('ListItem', $data['negativeNotes']['itemListElement'][0]['@type']);
		$this->assertSame(1, $data['negativeNotes']['itemListElement'][0]['position']);
		$this->assertSame('Manual could be clearer', $data['negativeNotes']['itemListElement'][0]['name']);
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
		$this->assertArrayNotHasKey('positiveNotes', $data);
		$this->assertArrayNotHasKey('negativeNotes', $data);
		$this->assertArrayNotHasKey('contentReferenceTime', $data);
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
