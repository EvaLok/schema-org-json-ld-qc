<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\JobPosting;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MonetaryAmount;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Place;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PropertyValue;

$job = new JobPosting(
	title: 'Senior Software Engineer',
	description: '<p>We are looking for a senior software engineer to lead our backend team. You will design and implement scalable APIs, mentor junior developers, and drive technical decisions.</p><p>Requirements: 5+ years experience with PHP or Python, experience with cloud infrastructure, strong communication skills.</p>',
	datePosted: '2025-03-01',
	hiringOrganization: new Organization(
		name: 'ACME Corp',
		url: 'https://acme.example.com',
		logo: 'https://acme.example.com/logo.png',
	),
	jobLocation: new Place(
		name: 'ACME Headquarters',
		address: new PostalAddress(
			streetAddress: '100 Innovation Way',
			addressLocality: 'Austin',
			addressRegion: 'TX',
			postalCode: '78701',
			addressCountry: 'US',
		),
	),
	baseSalary: new MonetaryAmount(
		currency: 'USD',
		minValue: 150000.00,
		maxValue: 200000.00,
	),
	employmentType: 'FULL_TIME',
	validThrough: '2025-06-01',
	directApply: true,
	identifier: new PropertyValue(
		name: 'Internal Job ID',
		value: 'SE-2025-0042',
	),
);

echo JsonLdGenerator::SchemaToJson($job) . "\n";
