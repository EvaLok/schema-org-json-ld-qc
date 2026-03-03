<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\CourseInstance;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Schedule;

$courseInstance = new CourseInstance(
	courseMode: 'online',
	instructor: new Person(name: 'Dr. Emily Zhang'),
	courseSchedule: new Schedule(
		repeatFrequency: 'P1W',
		startDate: '2025-09-01',
		endDate: '2025-12-15',
	),
	courseWorkload: 'PT40H',
);

echo JsonLdGenerator::SchemaToJson($courseInstance) . "\n";
