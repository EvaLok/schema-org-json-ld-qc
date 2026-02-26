<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Movie;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use PHPUnit\Framework\TestCase;

class MovieTest extends TestCase
{
	public function testMinimalMovie(): void
	{
		$movie = new Movie(
			name: 'The Matrix',
			image: 'https://example.com/matrix-poster.jpg',
		);

		$json = JsonLdGenerator::SchemaToJson($movie);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Movie', $data['@type']);
		$this->assertSame('The Matrix', $data['name']);
		$this->assertSame('https://example.com/matrix-poster.jpg', $data['image']);
	}

	public function testFullMovie(): void
	{
		$movie = new Movie(
			name: 'Inception',
			image: 'https://example.com/inception-poster.jpg',
			aggregateRating: new AggregateRating(
				ratingValue: 8.8,
				bestRating: 10,
				worstRating: 1,
				ratingCount: 2150000,
			),
			dateCreated: '2010-07-16',
			director: new Person(name: 'Christopher Nolan'),
			review: new Review(
				author: 'Movie Critic',
				reviewRating: new Rating(ratingValue: 9, bestRating: 10),
				reviewBody: 'A mind-bending thriller that rewards multiple viewings.',
			),
			description: 'A thief who steals corporate secrets through dream-sharing technology is given the task of planting an idea in a CEO\'s mind.',
			actor: [
				new Person(name: 'Leonardo DiCaprio'),
				new Person(name: 'Ellen Page'),
				new Person(name: 'Tom Hardy'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($movie);
		$data = json_decode($json, true);

		$this->assertSame('Movie', $data['@type']);
		$this->assertSame('2010-07-16', $data['dateCreated']);
		$this->assertSame('Person', $data['director']['@type']);
		$this->assertSame('Christopher Nolan', $data['director']['name']);
		$this->assertSame('Review', $data['review']['@type']);
		$this->assertCount(3, $data['actor']);
		$this->assertSame('Leonardo DiCaprio', $data['actor'][0]['name']);
		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
		$this->assertSame(8.8, $data['aggregateRating']['ratingValue']);
		$this->assertSame(1, $data['aggregateRating']['worstRating']);
	}

	public function testOptionalFieldsOmitted(): void
	{
		$movie = new Movie(
			name: 'Simple Movie',
			image: 'https://example.com/poster.jpg',
		);

		$json = JsonLdGenerator::SchemaToJson($movie);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('aggregateRating', $data);
		$this->assertArrayNotHasKey('dateCreated', $data);
		$this->assertArrayNotHasKey('director', $data);
		$this->assertArrayNotHasKey('review', $data);
		$this->assertArrayNotHasKey('actor', $data);
	}
}
