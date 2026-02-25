<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BreadcrumbList;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ListItem;

$breadcrumb = new BreadcrumbList(
	itemListElement: [
		new ListItem(position: 1, name: 'Home', item: 'https://example.com/'),
		new ListItem(position: 2, name: 'Electronics', item: 'https://example.com/electronics'),
		new ListItem(position: 3, name: 'Phones', item: 'https://example.com/electronics/phones'),
		new ListItem(position: 4, name: 'Pixel 9 Pro'),
	],
);

echo JsonLdGenerator::SchemaToJson($breadcrumb) . "\n";
