<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Accommodation;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BedDetails;
use EvaLok\SchemaOrgJsonLd\v1\Schema\LocationFeatureSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;

$schema = new Accommodation(
	occupancy: new QuantitativeValue(
		minValue: 1,
		maxValue: 4,
		unitCode: 'C62',
	),
	additionalType: 'https://schema.org/Hotel',
	numberOfBedrooms: 2,
	numberOfBathroomsTotal: 1,
	numberOfRooms: 3,
	floorSize: new QuantitativeValue(
		value: 85,
		unitCode: 'MTK',
	),
	bed: [
		new BedDetails(numberOfBeds: 1, typeOfBed: 'King'),
		new BedDetails(numberOfBeds: 2, typeOfBed: 'Twin'),
	],
	amenityFeature: [
		new LocationFeatureSpecification(name: 'WiFi', value: true),
		new LocationFeatureSpecification(name: 'Pool', value: true),
	],
);

echo JsonLdGenerator::SchemaToJson($schema) . "\n";
