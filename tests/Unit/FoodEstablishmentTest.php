<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Enum\DayOfWeek;
use EvaLok\SchemaOrgJsonLd\v1\Schema\FoodEstablishment;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;
use EvaLok\SchemaOrgJsonLd\v1\Schema\LocalBusiness;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OpeningHoursSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use PHPUnit\Framework\TestCase;

class FoodEstablishmentTest extends TestCase
{
	public function testMinimalFoodEstablishment(): void
	{
		$food = new FoodEstablishment(
			name: 'Corner Cafe',
			address: new PostalAddress(
				streetAddress: '1 Main St',
				addressLocality: 'Portland',
				addressRegion: 'OR',
				postalCode: '97201',
				addressCountry: 'US',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($food);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('FoodEstablishment', $data['@type']);
		$this->assertSame('Corner Cafe', $data['name']);
		$this->assertSame('PostalAddress', $data['address']['@type']);
		$this->assertSame('1 Main St', $data['address']['streetAddress']);
	}

	public function testFoodEstablishmentExtendsLocalBusiness(): void
	{
		$food = new FoodEstablishment(
			name: 'The Bistro',
			address: new PostalAddress(streetAddress: '10 Oak St'),
			url: 'https://thebistro.example.com',
			telephone: '+1-555-0100',
			priceRange: '$$$',
			servesCuisine: 'French',
			logo: 'https://example.com/logo.png',
		);

		$json = JsonLdGenerator::SchemaToJson($food);
		$data = json_decode($json, true);

		$this->assertSame('FoodEstablishment', $data['@type']);
		$this->assertSame('https://thebistro.example.com', $data['url']);
		$this->assertSame('+1-555-0100', $data['telephone']);
		$this->assertSame('$$$', $data['priceRange']);
		$this->assertSame('French', $data['servesCuisine']);
		$this->assertSame('https://example.com/logo.png', $data['logo']);
	}

	public function testFoodEstablishmentAcceptsReservationsBool(): void
	{
		$food = new FoodEstablishment(
			name: 'Reservable Place',
			address: new PostalAddress(streetAddress: '5 Elm St'),
			acceptsReservations: true,
		);

		$json = JsonLdGenerator::SchemaToJson($food);
		$data = json_decode($json, true);

		$this->assertTrue($data['acceptsReservations']);
	}

	public function testFoodEstablishmentAcceptsReservationsUrl(): void
	{
		$food = new FoodEstablishment(
			name: 'Reservable Place',
			address: new PostalAddress(streetAddress: '5 Elm St'),
			acceptsReservations: 'https://example.com/reserve',
		);

		$json = JsonLdGenerator::SchemaToJson($food);
		$data = json_decode($json, true);

		$this->assertSame('https://example.com/reserve', $data['acceptsReservations']);
	}

	public function testFoodEstablishmentNullFieldsOmitted(): void
	{
		$food = new FoodEstablishment(
			name: 'Simple Cafe',
			address: new PostalAddress(streetAddress: '1 Test St'),
		);

		$json = JsonLdGenerator::SchemaToJson($food);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('acceptsReservations', $data);
		$this->assertArrayNotHasKey('menu', $data);
		$this->assertArrayNotHasKey('servesCuisine', $data);
		$this->assertArrayNotHasKey('aggregateRating', $data);
		$this->assertArrayNotHasKey('review', $data);
		$this->assertArrayNotHasKey('geo', $data);
	}

	public function testFoodEstablishmentWithFullDetails(): void
	{
		$food = new FoodEstablishment(
			name: 'The Golden Spoon',
			address: new PostalAddress(
				streetAddress: '742 Evergreen Terrace',
				addressLocality: 'Portland',
				addressRegion: 'OR',
				postalCode: '97205',
				addressCountry: 'US',
			),
			url: 'https://goldenspoon.example.com',
			telephone: '+1-503-555-0199',
			description: 'Farm-to-table bistro.',
			image: ['https://example.com/photo.jpg'],
			priceRange: '$$$',
			geo: new GeoCoordinates(latitude: 45.5231, longitude: -122.6765),
			aggregateRating: new AggregateRating(ratingValue: 4.7, bestRating: 5, worstRating: 1, reviewCount: 312),
			review: new Review(
				author: 'Maria G.',
				reviewRating: new Rating(ratingValue: 5, bestRating: 5, worstRating: 1),
				reviewBody: 'Excellent.',
				datePublished: '2025-11-15',
			),
			menu: 'https://goldenspoon.example.com/menu',
			servesCuisine: 'Pacific Northwest',
			logo: 'https://example.com/logo.png',
			acceptsReservations: true,
		);

		$json = JsonLdGenerator::SchemaToJson($food);
		$data = json_decode($json, true);

		$this->assertSame('FoodEstablishment', $data['@type']);
		$this->assertSame('The Golden Spoon', $data['name']);
		$this->assertSame('https://goldenspoon.example.com/menu', $data['menu']);
		$this->assertSame('Pacific Northwest', $data['servesCuisine']);
		$this->assertTrue($data['acceptsReservations']);
		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
		$this->assertSame(5, $data['aggregateRating']['bestRating']);
		$this->assertSame(1, $data['aggregateRating']['worstRating']);
		$this->assertSame(1, $data['review']['reviewRating']['worstRating']);
		$this->assertSame('Review', $data['review']['@type']);
		$this->assertSame('GeoCoordinates', $data['geo']['@type']);
	}

	public function testFoodEstablishmentWithOpeningHoursSpecification(): void
	{
		$food = new FoodEstablishment(
			name: 'Early Bird Cafe',
			address: new PostalAddress(streetAddress: '25 Sunrise Way'),
			openingHoursSpecification: [
				new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Monday, opens: '07:00', closes: '15:00'),
				new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Tuesday, opens: '07:00', closes: '15:00'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($food);
		$data = json_decode($json, true);

		$this->assertCount(2, $data['openingHoursSpecification']);
		$this->assertSame('OpeningHoursSpecification', $data['openingHoursSpecification'][0]['@type']);
		$this->assertSame('https://schema.org/Monday', $data['openingHoursSpecification'][0]['dayOfWeek']);
		$this->assertSame('15:00', $data['openingHoursSpecification'][1]['closes']);
	}

	public function testFoodEstablishmentWithEmailAndSameAs(): void
	{
		$food = new FoodEstablishment(
			name: 'Connected Cafe',
			address: new PostalAddress(streetAddress: '9 Social Plaza'),
			email: 'reservations@connectedcafe.example.com',
			sameAs: [
				'https://www.facebook.com/connectedcafe',
				'https://www.instagram.com/connectedcafe',
			],
		);

		$json = JsonLdGenerator::SchemaToJson($food);
		$data = json_decode($json, true);

		$this->assertSame('reservations@connectedcafe.example.com', $data['email']);
		$this->assertCount(2, $data['sameAs']);
		$this->assertSame('https://www.facebook.com/connectedcafe', $data['sameAs'][0]);
	}

	public function testFoodEstablishmentWithDepartment(): void
	{
		$food = new FoodEstablishment(
			name: 'Market Hall',
			address: new PostalAddress(streetAddress: '12 Market Square'),
			department: [
				new LocalBusiness(
					name: 'Market Hall Bakery',
					address: new PostalAddress(streetAddress: '12 Market Square, Bakery'),
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($food);
		$data = json_decode($json, true);

		$this->assertCount(1, $data['department']);
		$this->assertSame('LocalBusiness', $data['department'][0]['@type']);
		$this->assertSame('Market Hall Bakery', $data['department'][0]['name']);
	}
}
