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

class DiscussionForumPostingTest extends TestCase
{
	public function testMinimalPosting(): void
	{
		$post = new DiscussionForumPosting(
			author: new Person(name: 'ForumUser42'),
			datePublished: '2025-03-01T10:30:00Z',
			text: 'Has anyone tried the new PHP 8.4 features? The property hooks look amazing!',
		);

		$json = JsonLdGenerator::SchemaToJson($post);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('DiscussionForumPosting', $data['@type']);
		$this->assertSame('Person', $data['author']['@type']);
		$this->assertSame('ForumUser42', $data['author']['name']);
		$this->assertSame('2025-03-01T10:30:00Z', $data['datePublished']);
		$this->assertStringContainsString('PHP 8.4', $data['text']);
	}

	public function testPostingWithHeadlineAndUrl(): void
	{
		$post = new DiscussionForumPosting(
			author: new Person(name: 'TechBlogger'),
			datePublished: '2025-04-10T08:00:00Z',
			text: 'I have been using Rust for 6 months now and here are my thoughts on the borrow checker...',
			headline: 'My experience with Rust after 6 months',
			url: 'https://forum.example.com/threads/rust-experience/123',
			dateModified: '2025-04-10T12:00:00Z',
		);

		$json = JsonLdGenerator::SchemaToJson($post);
		$data = json_decode($json, true);

		$this->assertSame('My experience with Rust after 6 months', $data['headline']);
		$this->assertSame('https://forum.example.com/threads/rust-experience/123', $data['url']);
		$this->assertSame('2025-04-10T12:00:00Z', $data['dateModified']);
	}

	public function testOptionalFieldsOmitted(): void
	{
		$post = new DiscussionForumPosting(
			author: new Person(name: 'User'),
			datePublished: '2025-01-01',
			text: 'Hello world',
		);

		$json = JsonLdGenerator::SchemaToJson($post);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('headline', $data);
		$this->assertArrayNotHasKey('url', $data);
		$this->assertArrayNotHasKey('dateModified', $data);
		$this->assertArrayNotHasKey('comment', $data);
		$this->assertArrayNotHasKey('image', $data);
	}

	public function testPostingWithAllProperties(): void
	{
		$post = new DiscussionForumPosting(
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

		$json = JsonLdGenerator::SchemaToJson($post);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('DiscussionForumPosting', $data['@type']);
		$this->assertSame('Person', $data['author']['@type']);
		$this->assertSame('Alex Thompson', $data['author']['name']);
		$this->assertSame('2025-03-10T14:30:00Z', $data['datePublished']);
		$this->assertSame('Has anyone managed to get the new schema.org VacationRental type working with Google Rich Results? I keep getting validation warnings about missing fields.', $data['text']);
		$this->assertSame('VacationRental schema validation issues', $data['headline']);
		$this->assertSame('https://example.com/forum/posts/vacationrental-schema', $data['url']);
		$this->assertSame('2025-03-10T16:30:00Z', $data['dateModified']);
		$this->assertSame('ImageObject', $data['image']['@type']);
		$this->assertSame('https://example.com/forum/posts/vacationrental-schema/screenshot.png', $data['image']['contentUrl']);
		$this->assertSame('800', $data['image']['width']);
		$this->assertSame('600', $data['image']['height']);
		$this->assertSame('VideoObject', $data['video']['@type']);
		$this->assertSame('VacationRental Schema Tutorial', $data['video']['name']);
		$this->assertSame(['https://example.com/videos/schema-tutorial-thumb.jpg'], $data['video']['thumbnailUrl']);
		$this->assertSame('https://example.com/videos/schema-tutorial.mp4', $data['video']['contentUrl']);
		$this->assertCount(2, $data['comment']);
		$this->assertSame('Comment', $data['comment'][0]['@type']);
		$this->assertSame('Sarah Dev', $data['comment'][0]['author']['name']);
		$this->assertSame('Comment', $data['comment'][1]['@type']);
		$this->assertSame('Mike Builder', $data['comment'][1]['author']['name']);
		$this->assertIsArray($data['interactionStatistic']);
		$this->assertCount(2, $data['interactionStatistic']);
		$this->assertSame('InteractionCounter', $data['interactionStatistic'][0]['@type']);
		$this->assertSame('https://schema.org/LikeAction', $data['interactionStatistic'][0]['interactionType']);
		$this->assertSame(42, $data['interactionStatistic'][0]['userInteractionCount']);
		$this->assertSame('InteractionCounter', $data['interactionStatistic'][1]['@type']);
		$this->assertSame('https://example.com/forum/schema-org-discussion', $data['isPartOf']);
		$this->assertSame('https://developers.google.com/search/docs/appearance/structured-data/vacation-rental', $data['sharedContent']);
		$this->assertSame('Published', $data['creativeWorkStatus']);
		$this->assertSame('https://example.com/forum/posts/vacationrental-schema', $data['mainEntityOfPage']);
	}
}
