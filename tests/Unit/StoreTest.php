<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Enum\DayOfWeek;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;
use EvaLok\SchemaOrgJsonLd\v1\Schema\LocalBusiness;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OpeningHoursSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Store;
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase
{
	public function testMinimalStore(): void
	{
		$store = new Store(
			name: 'Corner Shop',
			address: new PostalAddress(
				streetAddress: '100 Commerce Blvd',
				addressLocality: 'Austin',
				addressRegion: 'TX',
				postalCode: '78701',
				addressCountry: 'US',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($store);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Store', $data['@type']);
		$this->assertSame('Corner Shop', $data['name']);
		$this->assertSame('PostalAddress', $data['address']['@type']);
	}

	public function testStoreInheritsFromLocalBusiness(): void
	{
		$store = new Store(
			name: 'GreenLeaf Garden Center',
			address: new PostalAddress(
				streetAddress: '500 Nursery Road',
				addressLocality: 'Austin',
				addressRegion: 'TX',
				postalCode: '78745',
				addressCountry: 'US',
			),
			url: 'https://greenleaf.example.com',
			telephone: '+1-512-555-0150',
			description: 'Garden center specializing in native Texas plants.',
			priceRange: '$$',
			geo: new GeoCoordinates(latitude: 30.2087, longitude: -97.7796),
			logo: 'https://example.com/greenleaf-logo.png',
		);

		$json = JsonLdGenerator::SchemaToJson($store);
		$data = json_decode($json, true);

		$this->assertSame('Store', $data['@type']);
		$this->assertSame('https://greenleaf.example.com', $data['url']);
		$this->assertSame('+1-512-555-0150', $data['telephone']);
		$this->assertSame('$$', $data['priceRange']);
		$this->assertSame('GeoCoordinates', $data['geo']['@type']);
		$this->assertSame('https://example.com/greenleaf-logo.png', $data['logo']);
	}

	public function testStoreWithOpeningHoursAndReview(): void
	{
		$store = new Store(
			name: 'Weekend Store',
			address: new PostalAddress(streetAddress: '1 Market St'),
			openingHoursSpecification: [
				new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Saturday, opens: '09:00', closes: '17:00'),
				new OpeningHoursSpecification(dayOfWeek: DayOfWeek::Sunday, opens: '10:00', closes: '16:00'),
			],
			aggregateRating: new AggregateRating(ratingValue: 4.8, bestRating: 5, worstRating: 1, reviewCount: 156),
			review: new Review(
				author: 'Carlos D.',
				reviewRating: new Rating(ratingValue: 5, bestRating: 5, worstRating: 1),
				reviewBody: 'Great selection.',
				datePublished: '2025-10-20',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($store);
		$data = json_decode($json, true);

		$this->assertCount(2, $data['openingHoursSpecification']);
		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
		$this->assertSame(4.8, $data['aggregateRating']['ratingValue']);
		$this->assertSame(5, $data['aggregateRating']['bestRating']);
		$this->assertSame(1, $data['aggregateRating']['worstRating']);
		$this->assertSame(1, $data['review']['reviewRating']['worstRating']);
		$this->assertSame('Review', $data['review']['@type']);
	}

	public function testStoreNullFieldsOmitted(): void
	{
		$store = new Store(
			name: 'Bare Store',
			address: new PostalAddress(streetAddress: '1 Empty St'),
		);

		$json = JsonLdGenerator::SchemaToJson($store);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('url', $data);
		$this->assertArrayNotHasKey('telephone', $data);
		$this->assertArrayNotHasKey('openingHoursSpecification', $data);
		$this->assertArrayNotHasKey('aggregateRating', $data);
		$this->assertArrayNotHasKey('review', $data);
		$this->assertArrayNotHasKey('logo', $data);
	}

	public function testStoreWithEmailAndSameAs(): void
	{
		$store = new Store(
			name: 'Connected Store',
			address: new PostalAddress(streetAddress: '44 Commerce Way'),
			email: 'contact@connectedstore.example.com',
			sameAs: [
				'https://www.facebook.com/connectedstore',
				'https://www.instagram.com/connectedstore',
			],
		);

		$json = JsonLdGenerator::SchemaToJson($store);
		$data = json_decode($json, true);

		$this->assertSame('contact@connectedstore.example.com', $data['email']);
		$this->assertCount(2, $data['sameAs']);
		$this->assertSame('https://www.facebook.com/connectedstore', $data['sameAs'][0]);
	}

	public function testStoreWithDepartment(): void
	{
		$store = new Store(
			name: 'Tech Plaza',
			address: new PostalAddress(streetAddress: '88 Retail Row'),
			department: [
				new LocalBusiness(
					name: 'Tech Plaza Electronics',
					address: new PostalAddress(streetAddress: '88 Retail Row, Electronics'),
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($store);
		$data = json_decode($json, true);

		$this->assertCount(1, $data['department']);
		$this->assertSame('LocalBusiness', $data['department'][0]['@type']);
		$this->assertSame('Tech Plaza Electronics', $data['department'][0]['name']);
	}
}
