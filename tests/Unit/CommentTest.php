<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Comment;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DiscussionForumPosting;
use EvaLok\SchemaOrgJsonLd\v1\Schema\ImageObject;
use EvaLok\SchemaOrgJsonLd\v1\Schema\InteractionCounter;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VideoObject;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
	public function testMinimalComment(): void
	{
		$comment = new Comment(
			text: 'This is really helpful, thank you!',
		);

		$json = JsonLdGenerator::SchemaToJson($comment);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Comment', $data['@type']);
		$this->assertSame('This is really helpful, thank you!', $data['text']);
	}

	public function testWithAllFields(): void
	{
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

		$json = JsonLdGenerator::SchemaToJson($comment);
		$data = json_decode($json, true);

		$this->assertSame('Comment', $data['@type']);
		$this->assertSame('This is really helpful, thank you!', $data['text']);
		$this->assertSame('Person', $data['author']['@type']);
		$this->assertSame('Jane Reader', $data['author']['name']);
		$this->assertSame('2025-03-01', $data['datePublished']);
		$this->assertSame('https://example.com/comments/1', $data['url']);
		$this->assertSame('2025-03-02', $data['dateModified']);
		$this->assertSame('ImageObject', $data['image']['@type']);
		$this->assertSame('https://example.com/comment-image.jpg', $data['image']['contentUrl']);
		$this->assertSame('VideoObject', $data['video']['@type']);
		$this->assertSame('Comment Walkthrough', $data['video']['name']);
		$this->assertSame('Comment', $data['comment'][0]['@type']);
		$this->assertSame('Reply comment', $data['comment'][0]['text']);
		$this->assertSame('InteractionCounter', $data['interactionStatistic']['@type']);
		$this->assertSame('https://schema.org/LikeAction', $data['interactionStatistic']['interactionType']);
		$this->assertSame(42, $data['interactionStatistic']['userInteractionCount']);
		$this->assertSame('https://example.com/posts/123', $data['sharedContent']);
		$this->assertSame('Published', $data['creativeWorkStatus']);
	}

	public function testNullFieldsOmitted(): void
	{
		$comment = new Comment(
			text: 'This is really helpful, thank you!',
		);

		$json = JsonLdGenerator::SchemaToJson($comment);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('author', $data);
		$this->assertArrayNotHasKey('datePublished', $data);
		$this->assertArrayNotHasKey('url', $data);
		$this->assertArrayNotHasKey('dateModified', $data);
		$this->assertArrayNotHasKey('image', $data);
		$this->assertArrayNotHasKey('video', $data);
		$this->assertArrayNotHasKey('comment', $data);
		$this->assertArrayNotHasKey('interactionStatistic', $data);
		$this->assertArrayNotHasKey('sharedContent', $data);
		$this->assertArrayNotHasKey('creativeWorkStatus', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$discussionPost = new DiscussionForumPosting(
			author: new Person(name: 'Forum Author'),
			datePublished: '2025-03-01',
			text: 'Original discussion post.',
			comment: [
				new Comment(
					text: 'This is really helpful, thank you!',
					author: new Person(name: 'Jane Reader'),
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($discussionPost);
		$data = json_decode($json, true);

		$this->assertSame('DiscussionForumPosting', $data['@type']);
		$this->assertSame('Comment', $data['comment'][0]['@type']);
		$this->assertSame('This is really helpful, thank you!', $data['comment'][0]['text']);
		$this->assertSame('Person', $data['comment'][0]['author']['@type']);
		$this->assertSame('Jane Reader', $data['comment'][0]['author']['name']);
		$this->assertArrayNotHasKey('@context', $data['comment'][0]);
	}
}
