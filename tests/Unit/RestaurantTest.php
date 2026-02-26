<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DayOfWeek;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OpeningHoursSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Restaurant;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use PHPUnit\Framework\TestCase;

class RestaurantTest extends TestCase
{
	public function testMinimalRestaurant(): void
	{
		$restaurant = new Restaurant(
			name: 'Pizza Palace',
			address: new PostalAddress(
				streetAddress: '88 Main St',
				addressLocality: 'New York',
				addressRegion: 'NY',
				postalCode: '10001',
				addressCountry: 'US',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($restaurant);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Restaurant', $data['@type']);
		$this->assertSame('Pizza Palace', $data['name']);
		$this->assertSame('PostalAddress', $data['address']['@type']);
	}

	public function testRestaurantInheritsFromFoodEstablishment(): void
	{
		$restaurant = new Restaurant(
			name: 'Bella Napoli',
			address: new PostalAddress(streetAddress: '10 Little Italy Lane'),
			servesCuisine: 'Italian',
			menu: 'https://bellanapoli.example.com/menu',
			acceptsReservations: 'https://bellanapoli.example.com/reserve',
		);

		$json = JsonLdGenerator::SchemaToJson($restaurant);
		$data = json_decode($json, true);

		$this->assertSame('Restaurant', $data['@type']);
		$this->assertSame('Italian', $data['servesCuisine']);
		$this->assertSame('https://bellanapoli.example.com/menu', $data['menu']);
		$this->assertSame('https://bellanapoli.example.com/reserve', $data['acceptsReservations']);
	}

	public function testRestaurantWithOpeningHours(): void
	{
		$restaurant = new Restaurant(
			name: 'Test Restaurant',
			address: new PostalAddress(streetAddress: '1 Test St'),
			openingHoursSpecification: [
				new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Monday, opens: '11:00', closes: '22:00'),
				new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Saturday, opens: '10:00', closes: '23:00'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($restaurant);
		$data = json_decode($json, true);

		$this->assertCount(2, $data['openingHoursSpecification']);
		$this->assertSame('OpeningHoursSpecification', $data['openingHoursSpecification'][0]['@type']);
		$this->assertSame('11:00', $data['openingHoursSpecification'][0]['opens']);
		$this->assertSame('22:00', $data['openingHoursSpecification'][0]['closes']);
	}

	public function testRestaurantWithMultipleReviews(): void
	{
		$restaurant = new Restaurant(
			name: 'Review Restaurant',
			address: new PostalAddress(streetAddress: '1 Review St'),
			review: [
				new Review(
					author: 'Alice',
					reviewRating: new Rating(ratingValue: 5, bestRating: 5, worstRating: 1),
					reviewBody: 'Amazing!',
					datePublished: '2025-12-01',
				),
				new Review(
					author: 'Bob',
					reviewRating: new Rating(ratingValue: 4, bestRating: 5, worstRating: 1),
					reviewBody: 'Good food.',
					datePublished: '2025-11-20',
				),
			],
			aggregateRating: new AggregateRating(ratingValue: 4.5, bestRating: 5, worstRating: 1, reviewCount: 100),
		);

		$json = JsonLdGenerator::SchemaToJson($restaurant);
		$data = json_decode($json, true);

		$this->assertCount(2, $data['review']);
		$this->assertSame('Review', $data['review'][0]['@type']);
		$this->assertSame('Alice', $data['review'][0]['author']);
		$this->assertSame('Review', $data['review'][1]['@type']);
		$this->assertSame('Bob', $data['review'][1]['author']);
		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
		$this->assertSame(5, $data['aggregateRating']['bestRating']);
		$this->assertSame(1, $data['aggregateRating']['worstRating']);
		$this->assertSame(1, $data['review'][0]['reviewRating']['worstRating']);
		$this->assertSame(1, $data['review'][1]['reviewRating']['worstRating']);
	}

	public function testRestaurantNullFieldsOmitted(): void
	{
		$restaurant = new Restaurant(
			name: 'Simple Restaurant',
			address: new PostalAddress(streetAddress: '1 Simple St'),
		);

		$json = JsonLdGenerator::SchemaToJson($restaurant);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('acceptsReservations', $data);
		$this->assertArrayNotHasKey('menu', $data);
		$this->assertArrayNotHasKey('servesCuisine', $data);
		$this->assertArrayNotHasKey('openingHoursSpecification', $data);
	}
}
