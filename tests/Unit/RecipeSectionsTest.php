<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Clip;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToSection;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToStep;
use EvaLok\SchemaOrgJsonLd\v1\Schema\NutritionInformation;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Recipe;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VideoObject;
use PHPUnit\Framework\TestCase;

class RecipeSectionsTest extends TestCase
{
	public function testRecipeSectionsIncludeStepMetadata(): void
	{
		$recipe = new Recipe(
			name: 'Classic Tiramisu',
			image: ['https://example.com/photos/1x1/tiramisu.jpg'],
			author: new Person(name: 'Chef Marco'),
			cookTime: 'PT0M',
			recipeInstructions: [
				new HowToSection(
					name: 'Prepare the Mascarpone Cream',
					itemListElement: [
						new HowToStep(
							text: 'Whisk egg yolks and sugar until thick and pale yellow.',
							name: 'Whisk yolks and sugar',
							url: 'https://example.com/tiramisu#cream-step1',
							image: 'https://example.com/photos/tiramisu/step-1.jpg',
							video: new Clip(
								name: 'Whisking Yolks and Sugar',
								startOffset: 0,
								url: 'https://example.com/videos/tiramisu.mp4?t=0',
								endOffset: 30,
							),
							itemListElement: [
								'Add yolks and sugar to a bowl.',
								'Whisk until pale and thick.',
							],
						),
						new HowToStep(
							text: 'Add mascarpone cheese and mix until smooth.',
							name: 'Blend in mascarpone',
							url: 'https://example.com/tiramisu#cream-step2',
							image: 'https://example.com/photos/tiramisu/step-2.jpg',
						),
						new HowToStep(
							text: 'In a separate bowl, whip heavy cream to stiff peaks.',
							name: 'Whip heavy cream',
							url: 'https://example.com/tiramisu#cream-step3',
							image: 'https://example.com/photos/tiramisu/step-3.jpg',
						),
						new HowToStep(
							text: 'Gently fold whipped cream into the mascarpone mixture.',
							name: 'Fold cream mixture',
							url: 'https://example.com/tiramisu#cream-step4',
							image: 'https://example.com/photos/tiramisu/step-4.jpg',
						),
					],
				),
				new HowToSection(
					name: 'Assemble the Tiramisu',
					itemListElement: [
						new HowToStep(
							text: 'Combine espresso and coffee liqueur in a shallow dish.',
							name: 'Prepare espresso dip',
							url: 'https://example.com/tiramisu#assemble-step1',
							image: 'https://example.com/photos/tiramisu/step-5.jpg',
						),
						new HowToStep(
							text: 'Quickly dip each ladyfinger into the espresso mixture.',
							name: 'Dip ladyfingers',
							url: 'https://example.com/tiramisu#assemble-step2',
							image: 'https://example.com/photos/tiramisu/step-6.jpg',
						),
						new HowToStep(
							text: 'Arrange a layer of soaked ladyfingers in a 9x13 dish.',
							name: 'Layer ladyfingers',
							url: 'https://example.com/tiramisu#assemble-step3',
							image: 'https://example.com/photos/tiramisu/step-7.jpg',
						),
						new HowToStep(
							text: 'Spread half the mascarpone cream over the ladyfingers.',
							name: 'Add first cream layer',
							url: 'https://example.com/tiramisu#assemble-step4',
							image: 'https://example.com/photos/tiramisu/step-8.jpg',
						),
						new HowToStep(
							text: 'Repeat with a second layer of ladyfingers and cream.',
							name: 'Repeat layers',
							url: 'https://example.com/tiramisu#assemble-step5',
							image: 'https://example.com/photos/tiramisu/step-9.jpg',
						),
					],
				),
				new HowToSection(
					name: 'Chill and Serve',
					itemListElement: [
						new HowToStep(
							text: 'Cover with plastic wrap and refrigerate for at least 4 hours.',
							name: 'Chill tiramisu',
							url: 'https://example.com/tiramisu#serve-step1',
							image: 'https://example.com/photos/tiramisu/step-10.jpg',
						),
						new HowToStep(
							text: 'Dust generously with cocoa powder before serving.',
							name: 'Finish with cocoa',
							url: 'https://example.com/tiramisu#serve-step2',
							image: 'https://example.com/photos/tiramisu/step-11.jpg',
						),
					],
				),
			],
			aggregateRating: new AggregateRating(
				ratingValue: 4.9,
				ratingCount: 184,
				bestRating: 5,
				worstRating: 1,
			),
			nutrition: new NutritionInformation(calories: '450 calories'),
			video: new VideoObject(
				name: 'Classic Tiramisu Tutorial',
				thumbnailUrl: ['https://example.com/photos/tiramisu/video-thumbnail.jpg'],
				uploadDate: '2025-03-15',
				contentUrl: 'https://example.com/videos/tiramisu.mp4',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($recipe);
		$data = json_decode($json, true);

		$this->assertSame('PT0M', $data['cookTime']);
		$this->assertSame(1, $data['aggregateRating']['worstRating']);
		$this->assertSame('450 calories', $data['nutrition']['calories']);
		$this->assertSame('VideoObject', $data['video']['@type']);
		$this->assertSame('Clip', $data['recipeInstructions'][0]['itemListElement'][0]['video']['@type']);
		$this->assertIsArray($data['recipeInstructions'][0]['itemListElement'][0]['itemListElement']);

		foreach ($data['recipeInstructions'] as $section) {
			foreach ($section['itemListElement'] as $step) {
				$this->assertArrayHasKey('name', $step);
				$this->assertArrayHasKey('url', $step);
				$this->assertArrayHasKey('image', $step);
			}
		}
	}
}
