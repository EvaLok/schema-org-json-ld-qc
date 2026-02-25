<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
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
);

echo JsonLdGenerator::SchemaToJson($video) . "\n";
