<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToStep;
use EvaLok\SchemaOrgJsonLd\v1\Schema\NutritionInformation;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Recipe;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
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
				new HowToStep(text: 'Preheat oven to 375°F (190°C).'),
				new HowToStep(text: 'Combine flour, baking soda, and salt in a bowl.'),
				new HowToStep(text: 'Beat butter and sugars until creamy. Add eggs and vanilla.'),
				new HowToStep(text: 'Gradually blend in flour mixture. Stir in chocolate chips.'),
				new HowToStep(text: 'Drop rounded tablespoons onto ungreased baking sheets.'),
				new HowToStep(text: 'Bake for 9 to 11 minutes or until golden brown.'),
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

		// Nutrition
		$this->assertSame('NutritionInformation', $data['nutrition']['@type']);
		$this->assertSame('210 calories', $data['nutrition']['calories']);
		$this->assertSame('1 cookie', $data['nutrition']['servingSize']);

		// Aggregate rating
		$this->assertSame('AggregateRating', $data['aggregateRating']['@type']);
		$this->assertSame(4.8, $data['aggregateRating']['ratingValue']);
		$this->assertSame(312, $data['aggregateRating']['ratingCount']);
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
