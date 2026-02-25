<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Answer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\QAPage;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Question;
use PHPUnit\Framework\TestCase;

class QAPageTest extends TestCase
{
	public function testQAPageWithAcceptedAnswer(): void
	{
		$qa = new QAPage(
			mainEntity: new Question(
				name: 'How do I sort a list in Python?',
				acceptedAnswer: new Answer(
					text: 'Use the sorted() function or the .sort() method on lists.',
					upvoteCount: 42,
					author: new Person(name: 'Python Expert'),
				),
				answerCount: 5,
				upvoteCount: 15,
				author: new Person(name: 'Beginner Dev'),
				datePublished: '2025-01-05',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($qa);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('QAPage', $data['@type']);
		$this->assertSame('Question', $data['mainEntity']['@type']);
		$this->assertSame('How do I sort a list in Python?', $data['mainEntity']['name']);
		$this->assertSame(5, $data['mainEntity']['answerCount']);
		$this->assertSame(15, $data['mainEntity']['upvoteCount']);
		$this->assertSame('Answer', $data['mainEntity']['acceptedAnswer']['@type']);
		$this->assertSame(42, $data['mainEntity']['acceptedAnswer']['upvoteCount']);
	}

	public function testQAPageWithSuggestedAnswers(): void
	{
		$qa = new QAPage(
			mainEntity: new Question(
				name: 'What is the best PHP framework?',
				acceptedAnswer: new Answer(
					text: 'It depends on your needs. Laravel is most popular, Symfony is most enterprise-grade.',
					upvoteCount: 100,
				),
				suggestedAnswer: [
					new Answer(
						text: 'Laravel is great for rapid development.',
						upvoteCount: 50,
					),
					new Answer(
						text: 'Symfony provides the most flexibility.',
						upvoteCount: 30,
					),
				],
				answerCount: 3,
			),
		);

		$json = JsonLdGenerator::SchemaToJson($qa);
		$data = json_decode($json, true);

		$this->assertArrayHasKey('suggestedAnswer', $data['mainEntity']);
		$this->assertCount(2, $data['mainEntity']['suggestedAnswer']);
		$this->assertSame('Answer', $data['mainEntity']['suggestedAnswer'][0]['@type']);
	}

	public function testNestedTypesHaveNoContext(): void
	{
		$qa = new QAPage(
			mainEntity: new Question(
				name: 'Test question?',
				acceptedAnswer: new Answer(text: 'Test answer.'),
			),
		);

		$json = JsonLdGenerator::SchemaToJson($qa);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('@context', $data['mainEntity']);
		$this->assertArrayNotHasKey('@context', $data['mainEntity']['acceptedAnswer']);
	}
}
