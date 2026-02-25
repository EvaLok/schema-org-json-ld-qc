<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DayOfWeek;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;
use EvaLok\SchemaOrgJsonLd\v1\Schema\LocalBusiness;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OpeningHoursSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use PHPUnit\Framework\TestCase;

class LocalBusinessTest extends TestCase
{
	public function testMinimalLocalBusiness(): void
	{
		$biz = new LocalBusiness(
			name: 'Joe\'s Pizza',
			address: new PostalAddress(
				streetAddress: '123 Main St',
				addressLocality: 'Springfield',
				addressRegion: 'IL',
				postalCode: '62701',
				addressCountry: 'US',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($biz);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('LocalBusiness', $data['@type']);
		$this->assertSame('Joe\'s Pizza', $data['name']);
		$this->assertSame('PostalAddress', $data['address']['@type']);
		$this->assertSame('123 Main St', $data['address']['streetAddress']);
		$this->assertSame('Springfield', $data['address']['addressLocality']);
		$this->assertSame('IL', $data['address']['addressRegion']);
		$this->assertSame('62701', $data['address']['postalCode']);
		$this->assertSame('US', $data['address']['addressCountry']);
	}

	public function testFullLocalBusiness(): void
	{
		$biz = new LocalBusiness(
			name: 'Dave\'s Repair Shop',
			address: new PostalAddress(
				streetAddress: '456 Oak Avenue',
				addressLocality: 'Portland',
				addressRegion: 'OR',
				postalCode: '97201',
				addressCountry: 'US',
			),
			url: 'https://davesrepair.example.com',
			telephone: '+1-503-555-0100',
			description: 'Expert electronics and appliance repair since 1985.',
			image: ['https://example.com/daves-shop.jpg'],
			priceRange: '$$',
			geo: new GeoCoordinates(
				latitude: 45.5152,
				longitude: -122.6784,
			),
			aggregateRating: new AggregateRating(
				ratingValue: 4.6,
				reviewCount: 89,
			),
			review: new Review(
				author: 'Sarah K.',
				reviewRating: new Rating(ratingValue: 5, bestRating: 5),
				reviewBody: 'Dave fixed my laptop in 2 hours. Highly recommend!',
				datePublished: '2025-01-10',
			),
			servesCuisine: null,
			logo: 'https://example.com/daves-logo.png',
		);

		$json = JsonLdGenerator::SchemaToJson($biz);
		$data = json_decode($json, true);

		$this->assertSame('LocalBusiness', $data['@type']);
		$this->assertSame('https://davesrepair.example.com', $data['url']);
		$this->assertSame('+1-503-555-0100', $data['telephone']);
		$this->assertSame('$$', $data['priceRange']);

		$this->assertSame('GeoCoordinates', $data['geo']['@type']);
		$this->assertSame(45.5152, $data['geo']['latitude']);
		$this->assertSame(-122.6784, $data['geo']['longitude']);

		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
		$this->assertSame(4.6, $data['aggregateRating']['ratingValue']);

		$this->assertSame('Review', $data['review']['@type']);
	}

	public function testNestedAddressHasNoContext(): void
	{
		$biz = new LocalBusiness(
			name: 'Test Biz',
			address: new PostalAddress(
				streetAddress: '1 Test Lane',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($biz);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('@context', $data['address']);
	}

	public function testLocalBusinessWithDepartment(): void
	{
		$biz = new LocalBusiness(
			name: 'MegaMart',
			address: new PostalAddress(
				streetAddress: '100 Retail Blvd',
				addressLocality: 'Chicago',
				addressRegion: 'IL',
				postalCode: '60601',
				addressCountry: 'US',
			),
			department: [
				new LocalBusiness(
					name: 'MegaMart Electronics',
					address: new PostalAddress(streetAddress: '100 Retail Blvd, Dept E'),
					telephone: '+1-312-555-0101',
				),
				new LocalBusiness(
					name: 'MegaMart Grocery',
					address: new PostalAddress(streetAddress: '100 Retail Blvd, Dept G'),
					telephone: '+1-312-555-0102',
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($biz);
		$data = json_decode($json, true);

		$this->assertSame('LocalBusiness', $data['@type']);
		$this->assertSame('MegaMart', $data['name']);
		$this->assertCount(2, $data['department']);
		$this->assertSame('LocalBusiness', $data['department'][0]['@type']);
		$this->assertSame('MegaMart Electronics', $data['department'][0]['name']);
		$this->assertSame('LocalBusiness', $data['department'][1]['@type']);
		$this->assertSame('MegaMart Grocery', $data['department'][1]['name']);
	}

	public function testLocalBusinessWithSingleDepartment(): void
	{
		$biz = new LocalBusiness(
			name: 'Small Store',
			address: new PostalAddress(streetAddress: '1 Main St'),
			department: new LocalBusiness(
				name: 'Small Store Pharmacy',
				address: new PostalAddress(streetAddress: '1 Main St, Pharmacy'),
			),
		);

		$json = JsonLdGenerator::SchemaToJson($biz);
		$data = json_decode($json, true);

		$this->assertSame('LocalBusiness', $data['department']['@type']);
		$this->assertSame('Small Store Pharmacy', $data['department']['name']);
	}

	public function testLocalBusinessWithEmailAndSameAs(): void
	{
		$biz = new LocalBusiness(
			name: 'Connected Biz',
			address: new PostalAddress(streetAddress: '10 Social St'),
			email: 'info@connected.example.com',
			sameAs: [
				'https://facebook.com/connectedbiz',
				'https://twitter.com/connectedbiz',
			],
		);

		$json = JsonLdGenerator::SchemaToJson($biz);
		$data = json_decode($json, true);

		$this->assertSame('info@connected.example.com', $data['email']);
		$this->assertCount(2, $data['sameAs']);
		$this->assertSame('https://facebook.com/connectedbiz', $data['sameAs'][0]);
	}
}
