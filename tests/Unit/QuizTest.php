<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AlignmentObject;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Answer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Question;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Quiz;
use PHPUnit\Framework\TestCase;

class QuizTest extends TestCase
{
	public function testMinimalQuiz(): void
	{
		$quiz = new Quiz(
			hasPart: [
				new Question(
					name: 'What is the capital of France?',
					acceptedAnswer: new Answer(text: 'Paris'),
					eduQuestionType: 'Multiple choice',
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($quiz);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Quiz', $data['@type']);
		$this->assertCount(1, $data['hasPart']);
		$this->assertSame('Question', $data['hasPart'][0]['@type']);
		$this->assertSame('What is the capital of France?', $data['hasPart'][0]['name']);
		$this->assertSame('Answer', $data['hasPart'][0]['acceptedAnswer']['@type']);
		$this->assertSame('Paris', $data['hasPart'][0]['acceptedAnswer']['text']);
	}

	public function testQuizWithMultipleQuestions(): void
	{
		$quiz = new Quiz(
			hasPart: [
				new Question(
					name: 'What is 2 + 2?',
					acceptedAnswer: new Answer(text: '4'),
					eduQuestionType: 'Multiple choice',
				),
				new Question(
					name: 'What is the largest ocean?',
					acceptedAnswer: new Answer(text: 'Pacific Ocean'),
					eduQuestionType: 'Multiple choice',
				),
			],
			about: 'General Knowledge',
			name: 'Basic Knowledge Quiz',
			description: 'Test your general knowledge with these questions.',
			educationalAlignment: new AlignmentObject(
				alignmentType: 'educationalSubject',
				targetName: 'General Knowledge',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($quiz);
		$data = json_decode($json, true);

		$this->assertSame('Quiz', $data['@type']);
		$this->assertCount(2, $data['hasPart']);
		$this->assertSame('General Knowledge', $data['about']);
		$this->assertSame('Basic Knowledge Quiz', $data['name']);
		$this->assertSame('Test your general knowledge with these questions.', $data['description']);
		$this->assertSame('AlignmentObject', $data['educationalAlignment']['@type']);
		$this->assertSame('educationalSubject', $data['educationalAlignment']['alignmentType']);
		$this->assertSame('General Knowledge', $data['educationalAlignment']['targetName']);
	}

	public function testQuizNullFieldsOmitted(): void
	{
		$quiz = new Quiz(
			hasPart: [
				new Question(name: 'Sample question?'),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($quiz);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('about', $data);
		$this->assertArrayNotHasKey('name', $data);
		$this->assertArrayNotHasKey('description', $data);
		$this->assertArrayNotHasKey('educationalAlignment', $data);
	}
}
