<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BroadcastEvent;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Clip;
use EvaLok\SchemaOrgJsonLd\v1\Schema\InteractionCounter;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VideoObject;

$video = new VideoObject(
	name: 'How to Make Sourdough Bread from Scratch',
	thumbnailUrl: [
		'https://example.com/photos/sourdough-1x1.jpg',
		'https://example.com/photos/sourdough-4x3.jpg',
		'https://example.com/photos/sourdough-16x9.jpg',
	],
	uploadDate: '2025-02-05T08:00:00+00:00',
	description: 'A step-by-step guide to making artisan sourdough bread at home, from creating your starter to baking the perfect loaf.',
	contentUrl: 'https://example.com/video/sourdough-guide.mp4',
	embedUrl: 'https://example.com/embed/sourdough-guide',
	duration: 'PT23M15S',
	expires: '2027-02-05T08:00:00+00:00',
	regionsAllowed: 'US,CA,GB,AU',
	interactionStatistic: new InteractionCounter(
		interactionType: 'WatchAction',
		userInteractionCount: 14503,
	),
	publication: new BroadcastEvent(
		isLiveBroadcast: false,
		startDate: '2025-02-05T08:00:00+00:00',
		endDate: '2025-02-05T09:00:00+00:00',
	),
	hasPart: [
		new Clip(
			name: 'Creating the Starter',
			startOffset: 0,
			url: 'https://example.com/video/sourdough-guide?t=0',
			endOffset: 180,
		),
		new Clip(
			name: 'Mixing the Dough',
			startOffset: 180,
			url: 'https://example.com/video/sourdough-guide?t=180',
			endOffset: 420,
		),
		new Clip(
			name: 'Shaping and Proofing',
			startOffset: 420,
			url: 'https://example.com/video/sourdough-guide?t=420',
			endOffset: 840,
		),
		new Clip(
			name: 'Baking the Loaf',
			startOffset: 840,
			url: 'https://example.com/video/sourdough-guide?t=840',
			endOffset: 1395,
		),
	],
);

echo JsonLdGenerator::SchemaToJson($video) . "\n";
