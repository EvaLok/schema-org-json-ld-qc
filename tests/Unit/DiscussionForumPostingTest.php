<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Comment;
use EvaLok\SchemaOrgJsonLd\v1\Schema\DiscussionForumPosting;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
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
}
