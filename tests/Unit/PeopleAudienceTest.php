<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PeopleAudience;
use PHPUnit\Framework\TestCase;

class PeopleAudienceTest extends TestCase
{
	public function testPeopleAudienceWithGender(): void
	{
		$audience = new PeopleAudience(suggestedGender: 'female');
		$json = JsonLdGenerator::SchemaToJson($audience);
		$data = json_decode($json, true);

		$this->assertSame('PeopleAudience', $data['@type']);
		$this->assertSame('female', $data['suggestedGender']);
		$this->assertArrayNotHasKey('suggestedMinAge', $data);
		$this->assertArrayNotHasKey('suggestedMaxAge', $data);
	}

	public function testPeopleAudienceWithAgeRange(): void
	{
		$audience = new PeopleAudience(
			suggestedGender: 'unisex',
			suggestedMinAge: 13,
			suggestedMaxAge: 65,
		);
		$json = JsonLdGenerator::SchemaToJson($audience);
		$data = json_decode($json, true);

		$this->assertSame('unisex', $data['suggestedGender']);
		$this->assertSame(13, $data['suggestedMinAge']);
		$this->assertSame(65, $data['suggestedMaxAge']);
	}

	public function testPeopleAudienceAllNull(): void
	{
		$audience = new PeopleAudience();
		$json = JsonLdGenerator::SchemaToJson($audience);
		$data = json_decode($json, true);

		$this->assertSame('PeopleAudience', $data['@type']);
		$this->assertArrayNotHasKey('suggestedGender', $data);
	}
}
