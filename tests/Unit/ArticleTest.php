<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Article;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\SpeakableSpecification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\WebPageElement;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
	public function testMinimalArticle(): void
	{
		$article = new Article(
			headline: 'How to Tie a Bowline Knot',
		);

		$json = JsonLdGenerator::SchemaToJson($article);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Article', $data['@type']);
		$this->assertSame('How to Tie a Bowline Knot', $data['headline']);
	}

	public function testArticleWithAuthorAndDates(): void
	{
		$article = new Article(
			headline: 'Understanding Tidal Patterns in the North Sea',
			author: new Person(name: 'Dr. Sarah Chen'),
			datePublished: '2025-01-15',
			dateModified: '2025-02-01',
			description: 'A comprehensive guide to tidal patterns and their impact on coastal ecosystems.',
		);

		$json = JsonLdGenerator::SchemaToJson($article);
		$data = json_decode($json, true);

		$this->assertSame('Article', $data['@type']);
		$this->assertSame('Understanding Tidal Patterns in the North Sea', $data['headline']);
		$this->assertSame('Person', $data['author']['@type']);
		$this->assertSame('Dr. Sarah Chen', $data['author']['name']);
		$this->assertSame('2025-01-15', $data['datePublished']);
		$this->assertSame('2025-02-01', $data['dateModified']);
		$this->assertSame('A comprehensive guide to tidal patterns and their impact on coastal ecosystems.', $data['description']);
		$this->assertArrayNotHasKey('@context', $data['author'], 'Nested schemas should not have @context');
	}

	public function testArticleWithPublisher(): void
	{
		$article = new Article(
			headline: 'Breaking: New Species Discovered',
			author: new Person(name: 'Jane Reporter'),
			publisher: new Organization(
				name: 'Nature Weekly',
				logo: 'https://example.com/logo.png',
			),
			datePublished: '2025-03-01',
			image: ['https://example.com/species.jpg'],
		);

		$json = JsonLdGenerator::SchemaToJson($article);
		$data = json_decode($json, true);

		$this->assertSame('Organization', $data['publisher']['@type']);
		$this->assertSame('Nature Weekly', $data['publisher']['name']);
		$this->assertSame('https://example.com/logo.png', $data['publisher']['logo']);
		$this->assertSame(['https://example.com/species.jpg'], $data['image']);
	}

	public function testArticleWithMultipleAuthors(): void
	{
		$article = new Article(
			headline: 'Collaborative Research on Coral Reefs',
			author: [
				new Person(name: 'Dr. Alice Wong'),
				new Person(name: 'Dr. Bob Smith'),
			],
			datePublished: '2025-06-15',
		);

		$json = JsonLdGenerator::SchemaToJson($article);
		$data = json_decode($json, true);

		$this->assertIsArray($data['author']);
		$this->assertCount(2, $data['author']);
		$this->assertSame('Dr. Alice Wong', $data['author'][0]['name']);
		$this->assertSame('Dr. Bob Smith', $data['author'][1]['name']);
	}

	public function testNullFieldsAreOmitted(): void
	{
		$article = new Article(
			headline: 'Simple Article',
		);

		$json = JsonLdGenerator::SchemaToJson($article);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('author', $data);
		$this->assertArrayNotHasKey('datePublished', $data);
		$this->assertArrayNotHasKey('dateModified', $data);
		$this->assertArrayNotHasKey('image', $data);
		$this->assertArrayNotHasKey('description', $data);
		$this->assertArrayNotHasKey('publisher', $data);
	}

	public function testJsonIsValidJson(): void
	{
		$article = new Article(
			headline: 'Test Article with "quotes" and special chars: <>&',
			description: "Multi-line\ndescription",
		);

		$json = JsonLdGenerator::SchemaToJson($article);
		$data = json_decode($json, true);

		$this->assertNotNull($data, 'Generated JSON should be valid');
		$this->assertJson($json);
	}

	public function testArticleWithSpeakableAndPaywall(): void
	{
		$article = new Article(
			headline: 'Speakable Article',
			speakable: new SpeakableSpecification(
				cssSelector: ['.headline', '.summary'],
				xpath: ['/html/head/title'],
			),
			isAccessibleForFree: false,
			hasPart: [
				new WebPageElement(
					isAccessibleForFree: true,
					cssSelector: '.free-section',
				),
				new WebPageElement(
					isAccessibleForFree: false,
					cssSelector: '.paywalled-section',
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($article);
		$data = json_decode($json, true);

		$this->assertArrayHasKey('speakable', $data);
		$this->assertSame('SpeakableSpecification', $data['speakable']['@type']);
		$this->assertSame(['.headline', '.summary'], $data['speakable']['cssSelector']);
		$this->assertSame(['/html/head/title'], $data['speakable']['xpath']);
		$this->assertFalse($data['isAccessibleForFree']);
		$this->assertArrayHasKey('hasPart', $data);
		$this->assertCount(2, $data['hasPart']);
		$this->assertSame('WebPageElement', $data['hasPart'][0]['@type']);
		$this->assertTrue($data['hasPart'][0]['isAccessibleForFree']);
		$this->assertSame('.free-section', $data['hasPart'][0]['cssSelector']);
	}
}
