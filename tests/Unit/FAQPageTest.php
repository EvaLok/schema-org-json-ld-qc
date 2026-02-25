<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Answer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\FAQPage;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Question;
use PHPUnit\Framework\TestCase;

class FAQPageTest extends TestCase
{
	public function testSingleQuestion(): void
	{
		$faq = new FAQPage(
			mainEntity: [
				new Question(
					name: 'What is JSON-LD?',
					acceptedAnswer: new Answer(
						text: 'JSON-LD is a method of encoding Linked Data using JSON.',
					),
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($faq);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('FAQPage', $data['@type']);
		$this->assertCount(1, $data['mainEntity']);
		$this->assertSame('Question', $data['mainEntity'][0]['@type']);
		$this->assertSame('What is JSON-LD?', $data['mainEntity'][0]['name']);
		$this->assertSame('Answer', $data['mainEntity'][0]['acceptedAnswer']['@type']);
		$this->assertSame(
			'JSON-LD is a method of encoding Linked Data using JSON.',
			$data['mainEntity'][0]['acceptedAnswer']['text'],
		);
	}

	public function testMultipleQuestions(): void
	{
		$faq = new FAQPage(
			mainEntity: [
				new Question(
					name: 'What is structured data?',
					acceptedAnswer: new Answer(
						text: 'Structured data is a standardized format for providing information about a page.',
					),
				),
				new Question(
					name: 'Why is structured data important?',
					acceptedAnswer: new Answer(
						text: 'It helps search engines understand your content and display rich results.',
					),
				),
				new Question(
					name: 'How do I test my structured data?',
					acceptedAnswer: new Answer(
						text: 'Use Google Rich Results Test at search.google.com/test/rich-results.',
					),
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($faq);
		$data = json_decode($json, true);

		$this->assertSame('FAQPage', $data['@type']);
		$this->assertCount(3, $data['mainEntity']);

		foreach ($data['mainEntity'] as $question) {
			$this->assertSame('Question', $question['@type']);
			$this->assertArrayHasKey('name', $question);
			$this->assertSame('Answer', $question['acceptedAnswer']['@type']);
			$this->assertArrayHasKey('text', $question['acceptedAnswer']);
			$this->assertArrayNotHasKey('@context', $question, 'Nested should not have @context');
		}
	}

	public function testNoContextOnNestedTypes(): void
	{
		$faq = new FAQPage(
			mainEntity: [
				new Question(
					name: 'Test?',
					acceptedAnswer: new Answer(text: 'Yes.'),
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($faq);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('@context', $data['mainEntity'][0]);
		$this->assertArrayNotHasKey('@context', $data['mainEntity'][0]['acceptedAnswer']);
	}
}
