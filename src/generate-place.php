<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\GeoCoordinates;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Place;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;

$place = new Place(
	name: 'Golden Gate Park',
	address: new PostalAddress(
		streetAddress: '501 Stanyan St',
		addressLocality: 'San Francisco',
		addressRegion: 'CA',
		postalCode: '94117',
		addressCountry: 'US',
	),
	geo: new GeoCoordinates(
		latitude: 37.7694,
		longitude: -122.4862,
	),
);

echo JsonLdGenerator::SchemaToJson($place) . "\n";
