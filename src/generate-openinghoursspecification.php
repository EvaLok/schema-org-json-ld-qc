<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\Enum\DayOfWeek;
use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\OpeningHoursSpecification;

$openingHours = new OpeningHoursSpecification(
	dayOfWeek: DayOfWeek::Monday,
	opens: '09:00',
	closes: '17:00',
	validFrom: '2025-01-01',
	validThrough: '2025-12-31',
);

echo JsonLdGenerator::SchemaToJson($openingHours) . "\n";
