<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Answer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Comment;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use PHPUnit\Framework\TestCase;

final class AnswerWithComment extends Answer
{
	public ?array $comment = null;
}

class AnswerTest extends TestCase
{
	public function testMinimalAnswer(): void
	{
		$answer = new Answer(text: 'Use the reset link on the sign-in page.');

		$json = JsonLdGenerator::SchemaToJson($answer);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Answer', $data['@type']);
		$this->assertSame('Use the reset link on the sign-in page.', $data['text']);
	}

	public function testAnswerWithAllFields(): void
	{
		$answer = new AnswerWithComment(
			text: 'Navigate to Settings > Security > Reset Password and follow the prompts.',
			author: new Person(name: 'Support Agent Mike'),
			url: 'https://example.com/answers/12345',
			upvoteCount: 87,
			datePublished: '2025-01-16',
			dateModified: '2025-02-10',
		);
		$answer->comment = [
			new Comment(text: 'This was very helpful, thanks!', author: new Person(name: 'Grateful User')),
			new Comment(text: 'Worked for me too.', author: new Person(name: 'Another User')),
		];

		$json = JsonLdGenerator::SchemaToJson($answer);
		$data = json_decode($json, true);

		$this->assertSame('Answer', $data['@type']);
		$this->assertSame('Navigate to Settings > Security > Reset Password and follow the prompts.', $data['text']);
		$this->assertSame('https://example.com/answers/12345', $data['url']);
		$this->assertSame(87, $data['upvoteCount']);
		$this->assertSame('2025-01-16', $data['datePublished']);
		$this->assertSame('2025-02-10', $data['dateModified']);
		$this->assertSame('Person', $data['author']['@type']);
		$this->assertSame('Support Agent Mike', $data['author']['name']);
		$this->assertArrayHasKey('comment', $data);
		$this->assertIsArray($data['comment']);
		$this->assertCount(2, $data['comment']);
		$this->assertSame('Comment', $data['comment'][0]['@type']);
		$this->assertSame('This was very helpful, thanks!', $data['comment'][0]['text']);
		$this->assertSame('Person', $data['comment'][0]['author']['@type']);
		$this->assertSame('Grateful User', $data['comment'][0]['author']['name']);
		$this->assertSame('Comment', $data['comment'][1]['@type']);
		$this->assertSame('Worked for me too.', $data['comment'][1]['text']);
		$this->assertSame('Person', $data['comment'][1]['author']['@type']);
		$this->assertSame('Another User', $data['comment'][1]['author']['name']);
	}

	public function testAnswerNullFieldsOmitted(): void
	{
		$answer = new Answer(text: 'Contact support for manual verification.');

		$json = JsonLdGenerator::SchemaToJson($answer);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('author', $data);
		$this->assertArrayNotHasKey('url', $data);
		$this->assertArrayNotHasKey('upvoteCount', $data);
		$this->assertArrayNotHasKey('datePublished', $data);
		$this->assertArrayNotHasKey('dateModified', $data);
		$this->assertArrayNotHasKey('comment', $data);
	}

	public function testAnswerNestedAuthorRendersCorrectly(): void
	{
		$answer = new Answer(
			text: 'Please use two-factor authentication for better account security.',
			author: new Person(name: 'Security Specialist'),
		);

		$json = JsonLdGenerator::SchemaToJson($answer);
		$data = json_decode($json, true);

		$this->assertSame('Person', $data['author']['@type']);
		$this->assertSame('Security Specialist', $data['author']['name']);
	}
}
