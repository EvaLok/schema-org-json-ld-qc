<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\NewsArticle;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use PHPUnit\Framework\TestCase;

class NewsArticleTest extends TestCase
{
	public function testNewsArticleInheritsFromArticle(): void
	{
		$article = new NewsArticle(
			headline: 'City Council Approves New Park Development',
			author: new Person(name: 'Jane Reporter'),
			datePublished: '2025-03-10',
			publisher: new Organization(
				name: 'Daily News',
				logo: 'https://dailynews.example.com/logo.png',
			),
			image: ['https://example.com/park-development.jpg'],
		);

		$json = JsonLdGenerator::SchemaToJson($article);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('NewsArticle', $data['@type']);
		$this->assertSame('City Council Approves New Park Development', $data['headline']);
		$this->assertSame('Person', $data['author']['@type']);
		$this->assertSame('Jane Reporter', $data['author']['name']);
		$this->assertSame('Organization', $data['publisher']['@type']);
		$this->assertSame('Daily News', $data['publisher']['name']);
	}

	public function testMinimalNewsArticle(): void
	{
		$article = new NewsArticle(
			headline: 'Breaking News: Weather Alert',
		);

		$json = JsonLdGenerator::SchemaToJson($article);
		$data = json_decode($json, true);

		$this->assertSame('NewsArticle', $data['@type']);
		$this->assertSame('Breaking News: Weather Alert', $data['headline']);
		$this->assertArrayNotHasKey('author', $data);
		$this->assertArrayNotHasKey('publisher', $data);
	}

	public function testNewsArticleWithAllArticleFields(): void
	{
		$article = new NewsArticle(
			headline: 'Tech Giants Report Record Earnings',
			author: [
				new Person(name: 'Alice Finance'),
				new Person(name: 'Bob Markets'),
			],
			datePublished: '2025-04-15',
			dateModified: '2025-04-15T14:30:00Z',
			description: 'Major technology companies exceeded analyst expectations in Q1 2025.',
			image: [
				'https://example.com/tech-earnings-1x1.jpg',
				'https://example.com/tech-earnings-4x3.jpg',
			],
		);

		$json = JsonLdGenerator::SchemaToJson($article);
		$data = json_decode($json, true);

		$this->assertSame('NewsArticle', $data['@type']);
		$this->assertCount(2, $data['author']);
		$this->assertSame('2025-04-15T14:30:00Z', $data['dateModified']);
		$this->assertCount(2, $data['image']);
	}
}
