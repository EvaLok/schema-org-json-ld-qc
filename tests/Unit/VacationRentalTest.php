<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Accommodation;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BedDetails;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Brand;
use EvaLok\SchemaOrgJsonLd\v1\Schema\LocationFeatureSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VacationRental;
use PHPUnit\Framework\TestCase;

class VacationRentalTest extends TestCase
{
	public function testMinimalVacationRental(): void
	{
		$rental = new VacationRental(
			name: 'Cozy Mountain Cabin',
			identifier: 'cabin-001',
			image: ['https://example.com/cabin1.jpg'],
			latitude: 45.5231,
			longitude: -122.6765,
			containsPlace: new Accommodation(
				occupancy: new QuantitativeValue(value: 4),
			),
		);

		$json = JsonLdGenerator::SchemaToJson($rental);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('VacationRental', $data['@type']);
		$this->assertSame('Cozy Mountain Cabin', $data['name']);
		$this->assertSame('cabin-001', $data['identifier']);
		$this->assertSame(['https://example.com/cabin1.jpg'], $data['image']);
		$this->assertSame(45.5231, $data['latitude']);
		$this->assertSame(-122.6765, $data['longitude']);
		$this->assertSame('Accommodation', $data['containsPlace']['@type']);
		$this->assertSame('QuantitativeValue', $data['containsPlace']['occupancy']['@type']);
	}

	public function testVacationRentalWithFullDetails(): void
	{
		$rental = new VacationRental(
			name: 'Seaside Villa Retreat',
			identifier: 'villa-seaside-42',
			image: [
				'https://example.com/villa-front.jpg',
				'https://example.com/villa-pool.jpg',
				'https://example.com/villa-interior.jpg',
			],
			latitude: 36.7783,
			longitude: -119.4179,
			containsPlace: new Accommodation(
				occupancy: new QuantitativeValue(value: 8),
				numberOfBedrooms: 4,
				numberOfBathroomsTotal: 3,
				numberOfRooms: 7,
				floorSize: new QuantitativeValue(value: 250, unitCode: 'MTK'),
			),
			address: new PostalAddress(
				streetAddress: '123 Ocean Boulevard',
				addressLocality: 'Malibu',
				addressRegion: 'CA',
				postalCode: '90265',
				addressCountry: 'US',
			),
			aggregateRating: new AggregateRating(
				ratingValue: 4.9,
				reviewCount: 87,
			),
			brand: new Brand(name: 'Luxury Stays'),
			checkinTime: '15:00',
			checkoutTime: '11:00',
			description: 'A stunning oceanfront villa with private pool and panoramic sea views.',
			knowsLanguage: ['en', 'es', 'fr'],
			review: [
				new Review(
					author: 'Traveler Kate',
					reviewRating: new Rating(ratingValue: 5, bestRating: 5),
					reviewBody: 'Absolutely perfect. The views are incredible.',
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($rental);
		$data = json_decode($json, true);

		$this->assertSame('VacationRental', $data['@type']);
		$this->assertSame('Seaside Villa Retreat', $data['name']);
		$this->assertCount(3, $data['image']);
		$this->assertSame(4, $data['containsPlace']['numberOfBedrooms']);
		$this->assertSame(3, $data['containsPlace']['numberOfBathroomsTotal']);
		$this->assertEquals(250, $data['containsPlace']['floorSize']['value']);
		$this->assertSame('MTK', $data['containsPlace']['floorSize']['unitCode']);
		$this->assertSame('PostalAddress', $data['address']['@type']);
		$this->assertSame('Malibu', $data['address']['addressLocality']);
		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
		$this->assertSame(4.9, $data['aggregateRating']['ratingValue']);
		$this->assertSame('Brand', $data['brand']['@type']);
		$this->assertSame('Luxury Stays', $data['brand']['name']);
		$this->assertSame('15:00', $data['checkinTime']);
		$this->assertSame('11:00', $data['checkoutTime']);
		$this->assertCount(3, $data['knowsLanguage']);
		$this->assertCount(1, $data['review']);
		$this->assertSame('Review', $data['review'][0]['@type']);
	}

	public function testVacationRentalNullFieldsOmitted(): void
	{
		$rental = new VacationRental(
			name: 'Simple Rental',
			identifier: 'r-001',
			image: ['https://example.com/photo.jpg'],
			latitude: 40.0,
			longitude: -74.0,
			containsPlace: new Accommodation(
				occupancy: new QuantitativeValue(value: 2),
			),
		);

		$json = JsonLdGenerator::SchemaToJson($rental);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('additionalType', $data);
		$this->assertArrayNotHasKey('address', $data);
		$this->assertArrayNotHasKey('aggregateRating', $data);
		$this->assertArrayNotHasKey('brand', $data);
		$this->assertArrayNotHasKey('checkinTime', $data);
		$this->assertArrayNotHasKey('checkoutTime', $data);
		$this->assertArrayNotHasKey('description', $data);
		$this->assertArrayNotHasKey('knowsLanguage', $data);
		$this->assertArrayNotHasKey('review', $data);
	}
}
