<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Comment;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DiscussionForumPosting;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ImageObject;
use EvaLok\SchemaOrgJsonLd\v1\Schema\InteractionCounter;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VideoObject;

$posting = new DiscussionForumPosting(
	author: new Person(name: 'Alex Thompson'),
	datePublished: '2025-03-10T14:30:00Z',
	text: 'Has anyone managed to get the new schema.org VacationRental type working with Google Rich Results? I keep getting validation warnings about missing fields.',
	headline: 'VacationRental schema validation issues',
	url: 'https://example.com/forum/posts/vacationrental-schema',
	dateModified: '2025-03-10T16:30:00Z',
	image: new ImageObject(
		contentUrl: 'https://example.com/forum/posts/vacationrental-schema/screenshot.png',
		width: '800',
		height: '600',
	),
	video: new VideoObject(
		name: 'VacationRental Schema Tutorial',
		thumbnailUrl: ['https://example.com/videos/schema-tutorial-thumb.jpg'],
		uploadDate: '2025-03-09T10:00:00Z',
		description: 'Step-by-step guide to implementing VacationRental schema',
		contentUrl: 'https://example.com/videos/schema-tutorial.mp4',
	),
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
	interactionStatistic: [
		new InteractionCounter(
			interactionType: 'https://schema.org/LikeAction',
			userInteractionCount: 42,
		),
		new InteractionCounter(
			interactionType: 'https://schema.org/CommentAction',
			userInteractionCount: 2,
		),
	],
	isPartOf: 'https://example.com/forum/schema-org-discussion',
	sharedContent: 'https://developers.google.com/search/docs/appearance/structured-data/vacation-rental',
	creativeWorkStatus: 'Published',
	mainEntityOfPage: 'https://example.com/forum/posts/vacationrental-schema',
);

echo JsonLdGenerator::SchemaToJson($posting) . "\n";
