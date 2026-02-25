<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ContactPoint;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QuantitativeValue;

$org = new Organization(
	name: 'TechStart Inc.',
	url: 'https://techstart.example.com',
	logo: 'https://techstart.example.com/logo.png',
	description: 'Leading technology startup accelerator.',
	email: 'info@techstart.example.com',
	telephone: '+1-555-123-4567',
	address: new PostalAddress(
		streetAddress: '123 Innovation Drive',
		addressLocality: 'San Francisco',
		addressRegion: 'CA',
		postalCode: '94105',
		addressCountry: 'US',
	),
	contactPoint: new ContactPoint(
		telephone: '+1-555-987-6543',
		contactType: 'customer service',
	),
	sameAs: [
		'https://twitter.com/techstart',
		'https://linkedin.com/company/techstart',
	],
	foundingDate: '2020-03-15',
	legalName: 'TechStart Incorporated',
	numberOfEmployees: new QuantitativeValue(value: 150),
	taxID: '94-3456789',
	duns: '12-345-6789',
);

echo JsonLdGenerator::SchemaToJson($org);
