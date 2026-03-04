<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Answer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Comment;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Question;
use PHPUnit\Framework\TestCase;

final class QuestionWithComment extends Question
{
	public ?array $comment = null;
}

class QuestionTest extends TestCase
{
	public function testMinimalQuestion(): void
	{
		$question = new Question(name: 'How do I reset my password?');

		$json = JsonLdGenerator::SchemaToJson($question);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Question', $data['@type']);
		$this->assertSame('How do I reset my password?', $data['name']);
	}

	public function testQuestionWithAllFields(): void
	{
		$question = new QuestionWithComment(
			name: 'How do I reset my password?',
			acceptedAnswer: new Answer(text: 'Go to Settings > Security > Reset Password'),
			suggestedAnswer: [
				new Answer(text: 'Try the forgot password link on the login page'),
				new Answer(text: 'Contact support for a manual reset'),
			],
			answerCount: 3,
			text: 'I cannot remember my password and need to regain access to my account.',
			upvoteCount: 42,
			author: new Person(name: 'Sarah Tech'),
			datePublished: '2025-01-15',
			dateModified: '2025-03-20',
		);
		$question->comment = [
			new Comment(text: 'Great question, I had the same issue!', author: new Person(name: 'Fellow User')),
		];

		$json = JsonLdGenerator::SchemaToJson($question);
		$data = json_decode($json, true);

		$this->assertSame('Question', $data['@type']);
		$this->assertSame('How do I reset my password?', $data['name']);
		$this->assertSame(3, $data['answerCount']);
		$this->assertSame('I cannot remember my password and need to regain access to my account.', $data['text']);
		$this->assertSame(42, $data['upvoteCount']);
		$this->assertSame('2025-01-15', $data['datePublished']);
		$this->assertSame('2025-03-20', $data['dateModified']);
		$this->assertSame('Answer', $data['acceptedAnswer']['@type']);
		$this->assertCount(2, $data['suggestedAnswer']);
		$this->assertSame('Person', $data['author']['@type']);
		$this->assertSame('Sarah Tech', $data['author']['name']);
		$this->assertArrayHasKey('comment', $data);
		$this->assertIsArray($data['comment']);
		$this->assertCount(1, $data['comment']);
		$this->assertSame('Comment', $data['comment'][0]['@type']);
		$this->assertSame('Great question, I had the same issue!', $data['comment'][0]['text']);
		$this->assertSame('Person', $data['comment'][0]['author']['@type']);
		$this->assertSame('Fellow User', $data['comment'][0]['author']['name']);
	}

	public function testQuestionNullFieldsOmitted(): void
	{
		$question = new Question(name: 'Can I change my username?');

		$json = JsonLdGenerator::SchemaToJson($question);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('acceptedAnswer', $data);
		$this->assertArrayNotHasKey('suggestedAnswer', $data);
		$this->assertArrayNotHasKey('answerCount', $data);
		$this->assertArrayNotHasKey('text', $data);
		$this->assertArrayNotHasKey('upvoteCount', $data);
		$this->assertArrayNotHasKey('author', $data);
		$this->assertArrayNotHasKey('datePublished', $data);
		$this->assertArrayNotHasKey('dateModified', $data);
		$this->assertArrayNotHasKey('eduQuestionType', $data);
		$this->assertArrayNotHasKey('comment', $data);
	}

	public function testQuestionNestedAnswersRenderCorrectly(): void
	{
		$question = new Question(
			name: 'Where can I find the account security settings?',
			acceptedAnswer: new Answer(
				text: 'Open your profile menu, then choose Settings and Security.',
				author: new Person(name: 'Help Desk Team'),
			),
		);

		$json = JsonLdGenerator::SchemaToJson($question);
		$data = json_decode($json, true);

		$this->assertSame('Answer', $data['acceptedAnswer']['@type']);
		$this->assertSame('Person', $data['acceptedAnswer']['author']['@type']);
		$this->assertSame('Help Desk Team', $data['acceptedAnswer']['author']['name']);
	}
}
