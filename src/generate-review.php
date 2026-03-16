<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemList;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ListItem;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Thing;

$review = new Review(
	author: new Person(name: 'James Wilson'),
	reviewRating: new Rating(
		ratingValue: 4,
		bestRating: 5,
		worstRating: 1,
	),
	reviewBody: 'Excellent product with great build quality. Minor issues with the manual.',
	datePublished: '2025-03-15',
	name: 'Great quality, minor documentation issues',
	itemReviewed: new Thing(name: 'Acme Wireless Headphones'),
	positiveNotes: new ItemList(
		itemListElement: [
			new ListItem(position: 1, name: 'Excellent build quality'),
			new ListItem(position: 2, name: 'Great sound clarity'),
			new ListItem(position: 3, name: 'Comfortable fit'),
		],
	),
	negativeNotes: new ItemList(
		itemListElement: [
			new ListItem(position: 1, name: 'Manual could be clearer'),
			new ListItem(position: 2, name: 'No carrying case included'),
		],
	),
	contentReferenceTime: '2025-03-14T10:00:00Z',
);

echo JsonLdGenerator::SchemaToJson($review) . "\n";
