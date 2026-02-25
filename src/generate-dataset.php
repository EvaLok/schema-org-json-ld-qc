<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DataCatalog;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DataDownload;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Dataset;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;

$dataset = new Dataset(
	name: 'Global Ocean Temperature Records 1950-2025',
	description: 'Comprehensive dataset of ocean surface temperature measurements from 1950 to 2025, collected from buoys, satellites, and research vessels worldwide.',
	url: 'https://example.com/datasets/ocean-temp',
	creator: new Organization(name: 'National Oceanographic Institute'),
	license: 'https://creativecommons.org/licenses/by/4.0/',
	keywords: ['ocean temperature', 'climate data', 'marine science'],
	isAccessibleForFree: true,
	temporalCoverage: '1950/2025',
	includedInDataCatalog: new DataCatalog(name: 'World Climate Data Repository'),
	distribution: [
		new DataDownload(
			encodingFormat: 'text/csv',
			contentUrl: 'https://example.com/datasets/ocean-temp/download.csv',
		),
	],
);

echo JsonLdGenerator::SchemaToJson($dataset) . "\n";
