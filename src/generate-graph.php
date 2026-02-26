<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Article;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BreadcrumbList;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ListItem;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;

$article = new Article(
	headline: 'Understanding JSON-LD and Structured Data',
	image: [
		'https://example.com/images/jsonld-guide.jpg',
		'https://example.com/images/jsonld-guide-wide.jpg',
	],
	author: [
		new Person(
			name: 'Sarah Chen',
			url: 'https://example.com/authors/sarah-chen',
		),
	],
	publisher: new Organization(
		name: 'TechBlog Inc',
		logo: 'https://example.com/logo.png',
	),
	datePublished: '2026-02-20',
	dateModified: '2026-02-25',
);

$breadcrumbs = new BreadcrumbList(
	itemListElement: [
		new ListItem(position: 1, name: 'Home', item: 'https://example.com/'),
		new ListItem(position: 2, name: 'Blog', item: 'https://example.com/blog/'),
		new ListItem(position: 3, name: 'Understanding JSON-LD', item: 'https://example.com/blog/jsonld-guide'),
	],
);

$publisher = new Organization(
	name: 'TechBlog Inc',
	url: 'https://example.com',
	logo: 'https://example.com/logo.png',
);

echo JsonLdGenerator::SchemasToJson($article, $breadcrumbs, $publisher) . "\n";
