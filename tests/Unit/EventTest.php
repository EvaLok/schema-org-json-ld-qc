<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Event;
use EvaLok\SchemaOrgJsonLd\v1\Schema\EventStatusType;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Place;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
	public function testMinimalEvent(): void
	{
		$event = new Event(
			name: 'The Rolling Stones Concert',
			startDate: '2025-07-21T19:00',
			location: new Place(name: 'Wembley Stadium'),
		);

		$json = JsonLdGenerator::SchemaToJson($event);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Event', $data['@type']);
		$this->assertSame('The Rolling Stones Concert', $data['name']);
		$this->assertSame('2025-07-21T19:00', $data['startDate']);
		$this->assertSame('Place', $data['location']['@type']);
		$this->assertSame('Wembley Stadium', $data['location']['name']);
	}

	public function testFullEvent(): void
	{
		$event = new Event(
			name: 'Tech Conference 2025',
			startDate: '2025-09-15T09:00',
			location: new Place(
				name: 'Convention Center',
				address: new PostalAddress(
					streetAddress: '789 Conference Blvd',
					addressLocality: 'San Francisco',
					addressRegion: 'CA',
					postalCode: '94102',
					addressCountry: 'US',
				),
			),
			description: 'Annual technology conference featuring AI and cloud computing.',
			endDate: '2025-09-17T18:00',
			eventStatus: EventStatusType::EventScheduled,
			image: ['https://example.com/techconf.jpg'],
			offers: new Offer(
				url: 'https://example.com/tickets',
				priceCurrency: 'USD',
				price: 299.00,
				itemCondition: OfferItemCondition::NewCondition,
				availability: ItemAvailability::InStock,
			),
			organizer: new Organization(
				name: 'Tech Events Inc.',
				url: 'https://techevents.example.com',
			),
			performer: new Person(name: 'Dr. Jane AI'),
		);

		$json = JsonLdGenerator::SchemaToJson($event);
		$data = json_decode($json, true);

		$this->assertSame('Event', $data['@type']);
		$this->assertSame('2025-09-17T18:00', $data['endDate']);
		$this->assertSame('PostalAddress', $data['location']['address']['@type']);
		$this->assertSame('Organization', $data['organizer']['@type']);
		$this->assertSame('Person', $data['performer']['@type']);

		// Offer nested
		$this->assertSame('Offer', $data['offers']['@type']);
		$this->assertEquals(299, $data['offers']['price']);
	}

	public function testEventStatusEnum(): void
	{
		$event = new Event(
			name: 'Postponed Concert',
			startDate: '2025-08-01T20:00',
			location: new Place(name: 'City Hall'),
			eventStatus: EventStatusType::EventPostponed,
			previousStartDate: '2025-06-15T20:00',
		);

		$json = JsonLdGenerator::SchemaToJson($event);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/EventPostponed', $data['eventStatus']);
		$this->assertSame('2025-06-15T20:00', $data['previousStartDate']);
	}
}
