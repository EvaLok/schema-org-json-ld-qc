<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Movie;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;

$movie = new Movie(
	name: 'The Algorithmic Garden',
	image: 'https://example.com/photos/algorithmic-garden.jpg',
	aggregateRating: new AggregateRating(
		ratingValue: 8.1,
		ratingCount: 45230,
		bestRating: 10,
		worstRating: 1,
	),
	dateCreated: '2025-06-15',
	datePublished: '2025-11-21',
	director: new Person(name: 'Sofia Castellano'),
	review: new Review(
		author: new Person(name: 'Roger Chen'),
		reviewRating: new Rating(
			ratingValue: 9,
			bestRating: 10,
			worstRating: 1,
		),
		reviewBody: 'A stunning visual exploration of mathematics in nature.',
	),
	description: 'A mathematician discovers that the patterns in an ancient garden hold the key to a revolutionary algorithm.',
	actor: [
		new Person(name: 'Elena Voss'),
		new Person(name: 'Marcus Reid'),
	],
);

echo JsonLdGenerator::SchemaToJson($movie) . "\n";
