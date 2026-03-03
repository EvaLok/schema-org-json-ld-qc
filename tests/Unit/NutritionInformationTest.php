<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\NutritionInformation;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Recipe;
use PHPUnit\Framework\TestCase;

class NutritionInformationTest extends TestCase
{
	public function testMinimalNutritionInformation(): void
	{
		$nutrition = new NutritionInformation();

		$json = JsonLdGenerator::SchemaToJson($nutrition);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('NutritionInformation', $data['@type']);
	}

	public function testWithAllFields(): void
	{
		$nutrition = new NutritionInformation(
			calories: '250 calories',
			fatContent: '8 grams',
			saturatedFatContent: '2 grams',
			cholesterolContent: '30 milligrams',
			sodiumContent: '350 milligrams',
			carbohydrateContent: '35 grams',
			fiberContent: '4 grams',
			sugarContent: '6 grams',
			proteinContent: '12 grams',
			servingSize: '1 bowl',
		);

		$json = JsonLdGenerator::SchemaToJson($nutrition);
		$data = json_decode($json, true);

		$this->assertSame('NutritionInformation', $data['@type']);
		$this->assertSame('250 calories', $data['calories']);
		$this->assertSame('8 grams', $data['fatContent']);
		$this->assertSame('2 grams', $data['saturatedFatContent']);
		$this->assertSame('30 milligrams', $data['cholesterolContent']);
		$this->assertSame('350 milligrams', $data['sodiumContent']);
		$this->assertSame('35 grams', $data['carbohydrateContent']);
		$this->assertSame('4 grams', $data['fiberContent']);
		$this->assertSame('6 grams', $data['sugarContent']);
		$this->assertSame('12 grams', $data['proteinContent']);
		$this->assertSame('1 bowl', $data['servingSize']);
	}

	public function testNullFieldsOmitted(): void
	{
		$nutrition = new NutritionInformation();

		$json = JsonLdGenerator::SchemaToJson($nutrition);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('calories', $data);
		$this->assertArrayNotHasKey('fatContent', $data);
		$this->assertArrayNotHasKey('saturatedFatContent', $data);
		$this->assertArrayNotHasKey('cholesterolContent', $data);
		$this->assertArrayNotHasKey('sodiumContent', $data);
		$this->assertArrayNotHasKey('carbohydrateContent', $data);
		$this->assertArrayNotHasKey('fiberContent', $data);
		$this->assertArrayNotHasKey('sugarContent', $data);
		$this->assertArrayNotHasKey('proteinContent', $data);
		$this->assertArrayNotHasKey('servingSize', $data);
	}

	public function testNestedObjectRendersCorrectly(): void
	{
		$recipe = new Recipe(
			name: 'Hearty Vegetable Soup',
			image: ['https://example.com/soup.jpg'],
			nutrition: new NutritionInformation(
				calories: '250 calories',
				fatContent: '8 grams',
				proteinContent: '12 grams',
				carbohydrateContent: '35 grams',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($recipe);
		$data = json_decode($json, true);

		$this->assertSame('Recipe', $data['@type']);
		$this->assertSame('NutritionInformation', $data['nutrition']['@type']);
		$this->assertSame('250 calories', $data['nutrition']['calories']);
		$this->assertSame('8 grams', $data['nutrition']['fatContent']);
		$this->assertSame('12 grams', $data['nutrition']['proteinContent']);
		$this->assertSame('35 grams', $data['nutrition']['carbohydrateContent']);
		$this->assertArrayNotHasKey('@context', $data['nutrition']);
	}
}
