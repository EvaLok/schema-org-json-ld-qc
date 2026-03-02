<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ImageObject;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;

$image = new ImageObject(
	contentUrl: 'https://example.com/photos/sunset.jpg',
	url: 'https://example.com/gallery/sunset',
	name: 'Sunset over the Pacific',
	caption: 'A vivid sunset captured from Ocean Beach',
	description: 'High-resolution photograph of a sunset over the Pacific Ocean',
	width: '1920',
	height: '1080',
	license: 'https://creativecommons.org/licenses/by/4.0/',
	acquireLicensePage: 'https://example.com/licensing',
	creditText: 'Photo by Jane Doe',
	copyrightNotice: 'Copyright 2025 Jane Doe',
	creator: new Organization(name: 'Example Photography'),
	datePublished: '2025-06-15',
	uploadDate: '2025-06-16',
);

echo JsonLdGenerator::SchemaToJson($image) . "\n";
