<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BroadcastEvent;

$broadcastEvent = new BroadcastEvent(
	isLiveBroadcast: true,
	startDate: '2025-06-15T20:00:00+00:00',
	endDate: '2025-06-15T22:00:00+00:00',
);

echo JsonLdGenerator::SchemaToJson($broadcastEvent) . "\n";
