<?php

require_once __DIR__ . '/../vendor/autoload.php';

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

$event = new Event(
	name: 'The Rolling Stones - Hackney Diamonds Tour',
	startDate: '2025-07-21T19:00-05:00',
	location: new Place(
		name: 'Soldier Field',
		address: new PostalAddress(
			streetAddress: '1410 Special Olympics Dr',
			addressLocality: 'Chicago',
			addressRegion: 'IL',
			postalCode: '60605',
			addressCountry: 'US',
		),
	),
	description: 'The Rolling Stones return to Chicago for one night only.',
	endDate: '2025-07-21T23:00-05:00',
	eventStatus: EventStatusType::EventScheduled,
	image: ['https://example.com/rolling-stones-tour.jpg'],
	offers: new Offer(
		url: 'https://example.com/tickets/rolling-stones',
		priceCurrency: 'USD',
		price: 125.00,
		itemCondition: OfferItemCondition::NewCondition,
		availability: ItemAvailability::InStock,
	),
	organizer: new Organization(
		name: 'Live Nation',
		url: 'https://www.livenation.com',
	),
	performer: new Person(name: 'The Rolling Stones'),
);

echo JsonLdGenerator::SchemaToJson($event) . "\n";
