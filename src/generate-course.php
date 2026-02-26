<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Course;
use EvaLok\SchemaOrgJsonLd\v1\Schema\CourseInstance;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Enum\OfferItemCondition;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;

$course = new Course(
	name: 'Introduction to Machine Learning',
	description: 'A comprehensive introduction to machine learning concepts, algorithms, and practical applications using Python.',
	provider: new Organization(name: 'DataScience Academy'),
	offers: [
		new Offer(
			url: 'https://example.com/courses/ml-intro',
			priceCurrency: 'USD',
			price: 199.99,
			itemCondition: OfferItemCondition::NewCondition,
			availability: ItemAvailability::InStock,
		),
	],
	hasCourseInstance: [
		new CourseInstance(
			courseMode: 'online',
			instructor: new \EvaLok\SchemaOrgJsonLd\v1\Schema\Person(name: 'Dr. Emily Zhang'),
		),
	],
	inLanguage: 'en',
	aggregateRating: new AggregateRating(
		ratingValue: 4.8,
		ratingCount: 1024,
		bestRating: 5,
		worstRating: 1,
	),
	image: 'https://example.com/photos/ml-course.jpg',
);

echo JsonLdGenerator::SchemaToJson($course) . "\n";
