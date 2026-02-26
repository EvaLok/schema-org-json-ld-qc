<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\AggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToStep;
use EvaLok\SchemaOrgJsonLd\v1\Schema\NutritionInformation;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Recipe;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Review;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\VideoObject;

$recipe = new Recipe(
	name: 'Classic Banana Bread',
	image: [
		'https://example.com/photos/1x1/banana-bread.jpg',
		'https://example.com/photos/4x3/banana-bread.jpg',
		'https://example.com/photos/16x9/banana-bread.jpg',
	],
	author: new Person(name: 'Mary Baker'),
	datePublished: '2025-01-10',
	description: 'This classic banana bread recipe is moist, delicious, and easy to make.',
	prepTime: 'PT15M',
	cookTime: 'PT60M',
	totalTime: 'PT75M',
	keywords: 'banana bread, baking, dessert, snack',
	recipeYield: '1 loaf (10 slices)',
	recipeCategory: 'Dessert',
	recipeCuisine: 'American',
	recipeIngredient: [
		'3 ripe bananas',
		'1/3 cup melted butter',
		'3/4 cup sugar',
		'1 egg, beaten',
		'1 tsp vanilla extract',
		'1 tsp baking soda',
		'Pinch of salt',
		'1 1/2 cups all-purpose flour',
	],
	recipeInstructions: [
		new HowToStep(
			text: 'Preheat oven to 350°F (175°C). Grease a 4x8 inch loaf pan.',
			name: 'Preheat the oven',
			url: 'https://example.com/banana-bread#step1',
			image: 'https://example.com/photos/banana-bread/step1.jpg',
		),
		new HowToStep(
			text: 'Mash the bananas in a mixing bowl with a fork.',
			name: 'Mash bananas',
			url: 'https://example.com/banana-bread#step2',
			image: 'https://example.com/photos/banana-bread/step2.jpg',
		),
		new HowToStep(
			text: 'Mix in the melted butter, sugar, egg, and vanilla.',
			name: 'Mix wet ingredients',
			url: 'https://example.com/banana-bread#step3',
			image: 'https://example.com/photos/banana-bread/step3.jpg',
		),
		new HowToStep(
			text: 'Stir in the baking soda and salt. Mix in the flour.',
			name: 'Add dry ingredients',
			url: 'https://example.com/banana-bread#step4',
			image: 'https://example.com/photos/banana-bread/step4.jpg',
		),
		new HowToStep(
			text: 'Pour batter into prepared loaf pan.',
			name: 'Fill loaf pan',
			url: 'https://example.com/banana-bread#step5',
			image: 'https://example.com/photos/banana-bread/step5.jpg',
		),
		new HowToStep(
			text: 'Bake for 60 minutes or until a toothpick inserted comes out clean.',
			name: 'Bake banana bread',
			url: 'https://example.com/banana-bread#step6',
			image: 'https://example.com/photos/banana-bread/step6.jpg',
		),
	],
	nutrition: new NutritionInformation(
		calories: '240 calories',
		fatContent: '8 g',
		carbohydrateContent: '40 g',
		proteinContent: '3 g',
		servingSize: '1 slice',
	),
	aggregateRating: new AggregateRating(
		ratingValue: 4.7,
		ratingCount: 256,
		bestRating: 5,
		worstRating: 1,
	),
	video: new VideoObject(
		name: 'How to Make Banana Bread',
		thumbnailUrl: ['https://example.com/photos/banana-bread/video-thumbnail.jpg'],
		uploadDate: '2025-02-05',
		description: 'Watch our step-by-step guide to making the best banana bread.',
		contentUrl: 'https://example.com/videos/banana-bread.mp4',
		embedUrl: 'https://example.com/embed/banana-bread',
		duration: 'PT8M30S',
	),
);

echo JsonLdGenerator::SchemaToJson($recipe) . "\n";
