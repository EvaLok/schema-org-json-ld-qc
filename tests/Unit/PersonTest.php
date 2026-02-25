<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\InteractionCounter;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Person;
use EvaLok\SchemaOrgJsonLd\v1\Schema\PostalAddress;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
	public function testMinimalPerson(): void
	{
		$person = new Person(
			name: 'Jane Doe',
		);

		$json = JsonLdGenerator::SchemaToJson($person);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Person', $data['@type']);
		$this->assertSame('Jane Doe', $data['name']);
	}

	public function testPersonWithFullProfile(): void
	{
		$person = new Person(
			name: 'Dr. Emily Zhang',
			url: 'https://emilyzhang.example.com',
			image: 'https://emilyzhang.example.com/photo.jpg',
			email: 'emily@example.com',
			telephone: '+1-555-222-3333',
			jobTitle: 'Senior Research Scientist',
			worksFor: new Organization(name: 'BioGen Labs'),
			sameAs: [
				'https://twitter.com/emilyzhang',
				'https://linkedin.com/in/emilyzhang',
				'https://github.com/emilyzhang',
			],
			description: 'Genomics researcher specializing in CRISPR applications.',
			givenName: 'Emily',
			familyName: 'Zhang',
			address: new PostalAddress(
				addressLocality: 'Boston',
				addressRegion: 'MA',
				addressCountry: 'US',
			),
			alternateName: 'E. Zhang',
		);

		$json = JsonLdGenerator::SchemaToJson($person);
		$data = json_decode($json, true);

		$this->assertSame('Person', $data['@type']);
		$this->assertSame('Dr. Emily Zhang', $data['name']);
		$this->assertSame('https://emilyzhang.example.com', $data['url']);
		$this->assertSame('https://emilyzhang.example.com/photo.jpg', $data['image']);
		$this->assertSame('Senior Research Scientist', $data['jobTitle']);
		$this->assertSame('Organization', $data['worksFor']['@type']);
		$this->assertSame('BioGen Labs', $data['worksFor']['name']);
		$this->assertCount(3, $data['sameAs']);
		$this->assertSame('Emily', $data['givenName']);
		$this->assertSame('Zhang', $data['familyName']);
		$this->assertSame('PostalAddress', $data['address']['@type']);
		$this->assertSame('Boston', $data['address']['addressLocality']);
		$this->assertSame('E. Zhang', $data['alternateName']);
	}

	public function testPersonWithInteractionCounters(): void
	{
		$person = new Person(
			name: 'Social Creator',
			url: 'https://example.com/creator',
			interactionStatistic: new InteractionCounter(
				interactionType: 'https://schema.org/FollowAction',
				userInteractionCount: 5000,
				interactionService: 'https://twitter.com',
			),
			agentInteractionStatistic: new InteractionCounter(
				interactionType: 'https://schema.org/WriteAction',
				userInteractionCount: 350,
				interactionService: 'https://twitter.com',
			),
		);

		$json = JsonLdGenerator::SchemaToJson($person);
		$data = json_decode($json, true);

		$this->assertSame('InteractionCounter', $data['interactionStatistic']['@type']);
		$this->assertSame(5000, $data['interactionStatistic']['userInteractionCount']);
		$this->assertSame('InteractionCounter', $data['agentInteractionStatistic']['@type']);
		$this->assertSame(350, $data['agentInteractionStatistic']['userInteractionCount']);
	}

	public function testPersonNullFieldsOmitted(): void
	{
		$person = new Person(name: 'Simple Person');

		$json = JsonLdGenerator::SchemaToJson($person);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('url', $data);
		$this->assertArrayNotHasKey('image', $data);
		$this->assertArrayNotHasKey('email', $data);
		$this->assertArrayNotHasKey('jobTitle', $data);
		$this->assertArrayNotHasKey('worksFor', $data);
		$this->assertArrayNotHasKey('sameAs', $data);
		$this->assertArrayNotHasKey('interactionStatistic', $data);
		$this->assertArrayNotHasKey('agentInteractionStatistic', $data);
	}
}
