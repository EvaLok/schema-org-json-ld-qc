<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToSection;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToStep;
use EvaLok\SchemaOrgJsonLd\v1\Schema\NutritionInformation;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Recipe;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VideoObject;
use PHPUnit\Framework\TestCase;

class RecipeTest extends TestCase
{
	public function testMinimalRecipe(): void
	{
		$recipe = new Recipe(
			name: 'Classic Banana Bread',
			image: ['https://example.com/banana-bread.jpg'],
		);

		$json = JsonLdGenerator::SchemaToJson($recipe);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Recipe', $data['@type']);
		$this->assertSame('Classic Banana Bread', $data['name']);
		$this->assertSame(['https://example.com/banana-bread.jpg'], $data['image']);
	}

	public function testFullRecipe(): void
	{
		$recipe = new Recipe(
			name: 'Grandma\'s Chocolate Chip Cookies',
			image: [
				'https://example.com/cookies-1x1.jpg',
				'https://example.com/cookies-4x3.jpg',
				'https://example.com/cookies-16x9.jpg',
			],
			author: new Person(name: 'Grandma Betty'),
			datePublished: '2024-12-15',
			description: 'The best chocolate chip cookies you will ever taste.',
			prepTime: 'PT20M',
			cookTime: 'PT15M',
			totalTime: 'PT35M',
			keywords: 'cookies, chocolate, baking, dessert',
			recipeYield: '24 cookies',
			recipeCategory: 'Dessert',
			recipeCuisine: 'American',
			recipeIngredient: [
				'2 1/4 cups all-purpose flour',
				'1 tsp baking soda',
				'1 tsp salt',
				'1 cup butter, softened',
				'3/4 cup granulated sugar',
				'3/4 cup packed brown sugar',
				'2 large eggs',
				'2 tsp vanilla extract',
				'2 cups chocolate chips',
			],
			recipeInstructions: [
				new HowToStep(
					text: 'Preheat oven to 375°F (190°C).',
					name: 'Preheat oven',
					url: 'https://example.com/cookies#step1',
					image: 'https://example.com/photos/cookies/step1.jpg',
				),
				new HowToStep(
					text: 'Combine flour, baking soda, and salt in a bowl.',
					name: 'Mix dry ingredients',
					url: 'https://example.com/cookies#step2',
					image: 'https://example.com/photos/cookies/step2.jpg',
				),
				new HowToStep(
					text: 'Beat butter and sugars until creamy. Add eggs and vanilla.',
					name: 'Beat wet ingredients',
					url: 'https://example.com/cookies#step3',
					image: 'https://example.com/photos/cookies/step3.jpg',
				),
				new HowToStep(
					text: 'Gradually blend in flour mixture. Stir in chocolate chips.',
					name: 'Combine dough',
					url: 'https://example.com/cookies#step4',
					image: 'https://example.com/photos/cookies/step4.jpg',
				),
				new HowToStep(
					text: 'Drop rounded tablespoons onto ungreased baking sheets.',
					name: 'Portion dough',
					url: 'https://example.com/cookies#step5',
					image: 'https://example.com/photos/cookies/step5.jpg',
				),
				new HowToStep(
					text: 'Bake for 9 to 11 minutes or until golden brown.',
					name: 'Bake cookies',
					url: 'https://example.com/cookies#step6',
					image: 'https://example.com/photos/cookies/step6.jpg',
				),
			],
			nutrition: new NutritionInformation(
				calories: '210 calories',
				fatContent: '11 g',
				carbohydrateContent: '27 g',
				proteinContent: '2 g',
				servingSize: '1 cookie',
			),
			aggregateRating: new AggregateRating(
				ratingValue: 4.8,
				ratingCount: 312,
				bestRating: 5,
			),
			video: new VideoObject(
				name: 'Cookie Baking Tutorial',
				thumbnailUrl: ['https://example.com/cookies-video-thumb.jpg'],
				uploadDate: '2025-01-01',
				contentUrl: 'https://example.com/cookies-video.mp4',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($recipe);
		$data = json_decode($json, true);

		$this->assertSame('Recipe', $data['@type']);
		$this->assertSame('Grandma\'s Chocolate Chip Cookies', $data['name']);
		$this->assertCount(3, $data['image']);
		$this->assertSame('Person', $data['author']['@type']);
		$this->assertSame('PT20M', $data['prepTime']);
		$this->assertSame('PT15M', $data['cookTime']);
		$this->assertSame('PT35M', $data['totalTime']);
		$this->assertSame('24 cookies', $data['recipeYield']);
		$this->assertSame('Dessert', $data['recipeCategory']);
		$this->assertSame('American', $data['recipeCuisine']);
		$this->assertCount(9, $data['recipeIngredient']);
		$this->assertCount(6, $data['recipeInstructions']);
		$this->assertSame('HowToStep', $data['recipeInstructions'][0]['@type']);
		$this->assertSame('Preheat oven', $data['recipeInstructions'][0]['name']);
		$this->assertSame('https://example.com/cookies#step1', $data['recipeInstructions'][0]['url']);
		$this->assertSame('https://example.com/photos/cookies/step1.jpg', $data['recipeInstructions'][0]['image']);
		$this->assertSame('Mix dry ingredients', $data['recipeInstructions'][1]['name']);
		$this->assertSame('https://example.com/cookies#step2', $data['recipeInstructions'][1]['url']);
		$this->assertSame('https://example.com/photos/cookies/step2.jpg', $data['recipeInstructions'][1]['image']);
		$this->assertSame('Beat wet ingredients', $data['recipeInstructions'][2]['name']);
		$this->assertSame('https://example.com/cookies#step3', $data['recipeInstructions'][2]['url']);
		$this->assertSame('https://example.com/photos/cookies/step3.jpg', $data['recipeInstructions'][2]['image']);
		$this->assertSame('Combine dough', $data['recipeInstructions'][3]['name']);
		$this->assertSame('https://example.com/cookies#step4', $data['recipeInstructions'][3]['url']);
		$this->assertSame('https://example.com/photos/cookies/step4.jpg', $data['recipeInstructions'][3]['image']);
		$this->assertSame('Portion dough', $data['recipeInstructions'][4]['name']);
		$this->assertSame('https://example.com/cookies#step5', $data['recipeInstructions'][4]['url']);
		$this->assertSame('https://example.com/photos/cookies/step5.jpg', $data['recipeInstructions'][4]['image']);
		$this->assertSame('Bake cookies', $data['recipeInstructions'][5]['name']);
		$this->assertSame('https://example.com/cookies#step6', $data['recipeInstructions'][5]['url']);
		$this->assertSame('https://example.com/photos/cookies/step6.jpg', $data['recipeInstructions'][5]['image']);

		// Nutrition
		$this->assertSame('NutritionInformation', $data['nutrition']['@type']);
		$this->assertSame('210 calories', $data['nutrition']['calories']);
		$this->assertSame('1 cookie', $data['nutrition']['servingSize']);

		// Aggregate rating
		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
		$this->assertSame(4.8, $data['aggregateRating']['ratingValue']);
		$this->assertSame(312, $data['aggregateRating']['ratingCount']);
		$this->assertSame('VideoObject', $data['video']['@type']);
	}

	public function testRecipeWithReview(): void
	{
		$recipe = new Recipe(
			name: 'Simple Pancakes',
			image: ['https://example.com/pancakes.jpg'],
			review: new Review(
				author: 'Mike Johnson',
				reviewRating: new Rating(ratingValue: 5, bestRating: 5),
				reviewBody: 'These pancakes are amazing! Light and fluffy.',
				datePublished: '2025-01-20',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($recipe);
		$data = json_decode($json, true);

		$this->assertSame('Review', $data['review']['@type']);
		$this->assertSame('Mike Johnson', $data['review']['author']);
		$this->assertSame('Rating', $data['review']['reviewRating']['@type']);
		$this->assertSame(5, $data['review']['reviewRating']['ratingValue']);
	}

	public function testRecipeWithHowToSections(): void
	{
		$recipe = new Recipe(
			name: 'Two-Layer Cake',
			image: ['https://example.com/cake.jpg'],
			recipeInstructions: [
				new HowToSection(
					name: 'Make the Cake Batter',
					itemListElement: [
						new HowToStep(text: 'Preheat oven to 350°F.'),
						new HowToStep(text: 'Mix flour, sugar, and baking powder.'),
						new HowToStep(text: 'Add eggs, milk, and vanilla. Beat until smooth.'),
						new HowToStep(text: 'Pour into two greased 9-inch round pans.'),
						new HowToStep(text: 'Bake for 30-35 minutes.'),
					],
				),
				new HowToSection(
					name: 'Make the Frosting',
					itemListElement: [
						new HowToStep(text: 'Beat butter until fluffy.'),
						new HowToStep(text: 'Gradually add powdered sugar and cocoa powder.'),
						new HowToStep(text: 'Add milk and vanilla. Beat until smooth.'),
					],
				),
				new HowToSection(
					name: 'Assemble',
					itemListElement: [
						new HowToStep(text: 'Place one cake layer on a plate.'),
						new HowToStep(text: 'Spread frosting on top, then add second layer.'),
						new HowToStep(text: 'Frost the top and sides of the cake.'),
					],
				),
			],
		);

		$json = JsonLdGenerator::SchemaToJson($recipe);
		$data = json_decode($json, true);

		$this->assertSame('Recipe', $data['@type']);
		$this->assertCount(3, $data['recipeInstructions']);

		// First section
		$this->assertSame('HowToSection', $data['recipeInstructions'][0]['@type']);
		$this->assertSame('Make the Cake Batter', $data['recipeInstructions'][0]['name']);
		$this->assertCount(5, $data['recipeInstructions'][0]['itemListElement']);
		$this->assertSame('HowToStep', $data['recipeInstructions'][0]['itemListElement'][0]['@type']);

		// Second section
		$this->assertSame('HowToSection', $data['recipeInstructions'][1]['@type']);
		$this->assertSame('Make the Frosting', $data['recipeInstructions'][1]['name']);
		$this->assertCount(3, $data['recipeInstructions'][1]['itemListElement']);

		// Third section
		$this->assertSame('Assemble', $data['recipeInstructions'][2]['name']);
		$this->assertCount(3, $data['recipeInstructions'][2]['itemListElement']);
	}

	public function testOptionalFieldsOmittedWhenNull(): void
	{
		$recipe = new Recipe(
			name: 'Basic Toast',
			image: ['https://example.com/toast.jpg'],
		);

		$json = JsonLdGenerator::SchemaToJson($recipe);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('author', $data);
		$this->assertArrayNotHasKey('prepTime', $data);
		$this->assertArrayNotHasKey('cookTime', $data);
		$this->assertArrayNotHasKey('nutrition', $data);
		$this->assertArrayNotHasKey('aggregateRating', $data);
		$this->assertArrayNotHasKey('review', $data);
		$this->assertArrayNotHasKey('recipeIngredient', $data);
		$this->assertArrayNotHasKey('recipeInstructions', $data);
	}
}
