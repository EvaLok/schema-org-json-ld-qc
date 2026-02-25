<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToSection;
use EvaLok\SchemaOrgJsonLd\v1\Schema\HowToStep;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Recipe;

$recipe = new Recipe(
	name: 'Classic Tiramisu',
	image: [
		'https://example.com/photos/1x1/tiramisu.jpg',
		'https://example.com/photos/4x3/tiramisu.jpg',
		'https://example.com/photos/16x9/tiramisu.jpg',
	],
	author: new Person(name: 'Chef Marco'),
	datePublished: '2025-03-15',
	description: 'An authentic Italian tiramisu with espresso-soaked ladyfingers and mascarpone cream.',
	prepTime: 'PT30M',
	totalTime: 'PT4H30M',
	keywords: 'tiramisu, italian, dessert, no-bake',
	recipeYield: '8 servings',
	recipeCategory: 'Dessert',
	recipeCuisine: 'Italian',
	recipeIngredient: [
		'6 egg yolks',
		'3/4 cup sugar',
		'500g mascarpone cheese',
		'2 cups heavy cream',
		'2 cups strong espresso, cooled',
		'3 tbsp coffee liqueur',
		'300g ladyfinger biscuits',
		'Unsweetened cocoa powder',
	],
	recipeInstructions: [
		new HowToSection(
			name: 'Prepare the Mascarpone Cream',
			itemListElement: [
				new HowToStep(text: 'Whisk egg yolks and sugar until thick and pale yellow.'),
				new HowToStep(text: 'Add mascarpone cheese and mix until smooth.'),
				new HowToStep(text: 'In a separate bowl, whip heavy cream to stiff peaks.'),
				new HowToStep(text: 'Gently fold whipped cream into the mascarpone mixture.'),
			],
		),
		new HowToSection(
			name: 'Assemble the Tiramisu',
			itemListElement: [
				new HowToStep(text: 'Combine espresso and coffee liqueur in a shallow dish.'),
				new HowToStep(text: 'Quickly dip each ladyfinger into the espresso mixture.'),
				new HowToStep(text: 'Arrange a layer of soaked ladyfingers in a 9x13 dish.'),
				new HowToStep(text: 'Spread half the mascarpone cream over the ladyfingers.'),
				new HowToStep(text: 'Repeat with a second layer of ladyfingers and cream.'),
			],
		),
		new HowToSection(
			name: 'Chill and Serve',
			itemListElement: [
				new HowToStep(text: 'Cover with plastic wrap and refrigerate for at least 4 hours.'),
				new HowToStep(text: 'Dust generously with cocoa powder before serving.'),
			],
		),
	],
);

echo JsonLdGenerator::SchemaToJson($recipe) . "\n";
