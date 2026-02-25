<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;

$person = new Person(
	name: 'Dr. Emily Zhang',
	url: 'https://emilyzhang.example.com',
	image: 'https://emilyzhang.example.com/photo.jpg',
	jobTitle: 'Senior Research Scientist',
	worksFor: new Organization(name: 'BioGen Labs'),
	sameAs: [
		'https://twitter.com/emilyzhang',
		'https://linkedin.com/in/emilyzhang',
	],
	description: 'Genomics researcher specializing in CRISPR applications.',
	givenName: 'Emily',
	familyName: 'Zhang',
	address: new PostalAddress(
		addressLocality: 'Boston',
		addressRegion: 'MA',
		addressCountry: 'US',
	),
);

echo JsonLdGenerator::SchemaToJson($person);
