<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ItemList;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ListItem;

$itemList = new ItemList(
	itemListElement: [
		new ListItem(
			position: 1,
			url: 'https://example.com/best-laptops/macbook-pro',
			name: 'MacBook Pro 16-inch',
		),
		new ListItem(
			position: 2,
			url: 'https://example.com/best-laptops/thinkpad-x1',
			name: 'ThinkPad X1 Carbon',
		),
		new ListItem(
			position: 3,
			url: 'https://example.com/best-laptops/dell-xps-15',
			name: 'Dell XPS 15',
		),
	],
	itemListOrder: 'https://schema.org/ItemListOrderDescending',
	numberOfItems: 3,
);

echo JsonLdGenerator::SchemaToJson($itemList) . "\n";
