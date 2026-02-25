<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Comment;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DiscussionForumPosting;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;

$posting = new DiscussionForumPosting(
	author: new Person(name: 'Alex Thompson'),
	datePublished: '2025-03-10T14:30:00Z',
	text: 'Has anyone managed to get the new schema.org VacationRental type working with Google Rich Results? I keep getting validation warnings about missing fields.',
	headline: 'VacationRental schema validation issues',
	url: 'https://example.com/forum/posts/vacationrental-schema',
	comment: [
		new Comment(
			text: 'Yes! Make sure you include the address and aggregateRating fields. Those are recommended by Google.',
			author: new Person(name: 'Sarah Dev'),
			datePublished: '2025-03-10T15:45:00Z',
		),
		new Comment(
			text: 'I found that the bestRating and worstRating fields on nested ratings also trigger warnings if omitted.',
			author: new Person(name: 'Mike Builder'),
			datePublished: '2025-03-10T16:20:00Z',
		),
	],
);

echo JsonLdGenerator::SchemaToJson($posting) . "\n";
