<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DataCatalog;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DataDownload;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Dataset;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Place;

$dataset = new Dataset(
	name: 'Global Ocean Temperature Records 1950-2025',
	description: 'Comprehensive dataset of ocean surface temperature measurements from 1950 to 2025, collected from buoys, satellites, and research vessels worldwide.',
	url: 'https://example.com/datasets/ocean-temp',
	sameAs: 'https://doi.org/10.1234/ocean-temp',
	creator: new Organization(name: 'National Oceanographic Institute'),
	funder: new Organization(name: 'National Science Foundation'),
	license: 'https://creativecommons.org/licenses/by/4.0/',
	keywords: ['ocean temperature', 'climate data', 'marine science'],
	identifier: ['doi:10.1234/ocean-temp', 'NOAA-OT-2025'],
	isAccessibleForFree: true,
	temporalCoverage: '1950/2025',
	spatialCoverage: new Place(name: 'Global Oceans'),
	includedInDataCatalog: new DataCatalog(name: 'World Climate Data Repository'),
	distribution: [
		new DataDownload(
			encodingFormat: 'text/csv',
			contentUrl: 'https://example.com/datasets/ocean-temp/download.csv',
		),
	],
	variableMeasured: 'Sea Surface Temperature',
	measurementTechnique: 'Satellite remote sensing and in-situ buoy measurements',
	version: '3.2',
	alternateName: 'GOTR 1950-2025',
	citation: 'Zhang et al. (2024). Global Ocean Temperature Trends. Nature Climate Change.',
	hasPart: [
		new Dataset(
			name: 'Pacific Ocean Subset',
			description: 'Temperature data for the Pacific Ocean basin',
		),
	],
);

echo JsonLdGenerator::SchemaToJson($dataset) . "\n";
