<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Comment;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ImageObject;
use EvaLok\SchemaOrgJsonLd\v1\Schema\InteractionCounter;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VideoObject;

$comment = new Comment(
	text: 'This is really helpful, thank you!',
	author: new Person(name: 'Jane Reader'),
	datePublished: '2025-03-01',
	url: 'https://example.com/comments/1',
	dateModified: '2025-03-02',
	image: new ImageObject(contentUrl: 'https://example.com/comment-image.jpg'),
	video: new VideoObject(
		name: 'Comment Walkthrough',
		thumbnailUrl: ['https://example.com/comment-thumb.jpg'],
		uploadDate: '2025-03-01',
	),
	comment: [
		new Comment(text: 'Reply comment'),
	],
	interactionStatistic: new InteractionCounter(
		interactionType: 'https://schema.org/LikeAction',
		userInteractionCount: 42,
	),
	sharedContent: 'https://example.com/posts/123',
	creativeWorkStatus: 'Published',
);

echo JsonLdGenerator::SchemaToJson($comment) . "\n";
