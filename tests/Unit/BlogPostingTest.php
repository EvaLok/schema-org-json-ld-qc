<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\BlogPosting;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SpeakableSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\WebPageElement;
use PHPUnit\Framework\TestCase;

class BlogPostingTest extends TestCase
{
	public function testMinimalBlogPosting(): void
	{
		$post = new BlogPosting(
			headline: 'My First Blog Post',
		);

		$json = JsonLdGenerator::SchemaToJson($post);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('BlogPosting', $data['@type']);
		$this->assertSame('My First Blog Post', $data['headline']);
	}

	public function testBlogPostingWithAuthorAndDates(): void
	{
		$post = new BlogPosting(
			headline: 'Understanding Dependency Injection in PHP',
			author: new Person(name: 'Marcus Rivera'),
			datePublished: '2025-11-20',
			dateModified: '2025-12-01',
			description: 'A practical guide to dependency injection patterns in modern PHP applications.',
			publisher: new Organization(
				name: 'PHP Weekly',
				logo: 'https://example.com/phpweekly-logo.png',
			),
			image: ['https://example.com/di-php.jpg', 'https://example.com/di-diagram.png'],
		);

		$json = JsonLdGenerator::SchemaToJson($post);
		$data = json_decode($json, true);

		$this->assertSame('BlogPosting', $data['@type']);
		$this->assertSame('Understanding Dependency Injection in PHP', $data['headline']);
		$this->assertSame('Person', $data['author']['@type']);
		$this->assertSame('Marcus Rivera', $data['author']['name']);
		$this->assertSame('2025-11-20', $data['datePublished']);
		$this->assertSame('2025-12-01', $data['dateModified']);
		$this->assertSame('Organization', $data['publisher']['@type']);
		$this->assertSame('PHP Weekly', $data['publisher']['name']);
		$this->assertCount(2, $data['image']);
	}

	public function testBlogPostingNullFieldsOmitted(): void
	{
		$post = new BlogPosting(
			headline: 'Short Post',
		);

		$json = JsonLdGenerator::SchemaToJson($post);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('author', $data);
		$this->assertArrayNotHasKey('datePublished', $data);
		$this->assertArrayNotHasKey('image', $data);
		$this->assertArrayNotHasKey('publisher', $data);
	}

	public function testBlogPostingInheritsFromArticle(): void
	{
		$post = new BlogPosting(
			headline: 'Inheriting Article Features',
			author: [
				new Person(name: 'Author A'),
				new Person(name: 'Author B'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($post);
		$data = json_decode($json, true);

		$this->assertSame('BlogPosting', $data['@type'], 'Type should be BlogPosting, not Article');
		$this->assertCount(2, $data['author']);
		$this->assertSame('Author A', $data['author'][0]['name']);
	}

	public function testBlogPostingWithSpeakableAndHasPart(): void
	{
		$post = new BlogPosting(
			headline: 'Speakable Blog Post',
			speakable: new SpeakableSpecification(
				cssSelector: ['.post-title', '.post-excerpt'],
			),
			isAccessibleForFree: true,
			hasPart: [
				new WebPageElement(
					isAccessibleForFree: true,
					cssSelector: '.post-content',
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($post);
		$data = json_decode($json, true);

		$this->assertArrayHasKey('speakable', $data);
		$this->assertSame('SpeakableSpecification', $data['speakable']['@type']);
		$this->assertSame(['.post-title', '.post-excerpt'], $data['speakable']['cssSelector']);
		$this->assertTrue($data['isAccessibleForFree']);
		$this->assertArrayHasKey('hasPart', $data);
		$this->assertCount(1, $data['hasPart']);
		$this->assertSame('WebPageElement', $data['hasPart'][0]['@type']);
		$this->assertTrue($data['hasPart'][0]['isAccessibleForFree']);
		$this->assertSame('.post-content', $data['hasPart'][0]['cssSelector']);
	}
}
