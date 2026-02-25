<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\EmployerAggregateRating;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use PHPUnit\Framework\TestCase;

class EmployerAggregateRatingTest extends TestCase
{
	public function testMinimalEmployerAggregateRating(): void
	{
		$rating = new EmployerAggregateRating(
			itemReviewed: new Organization(name: 'Google'),
			ratingValue: 4.2,
		);

		$json = JsonLdGenerator::SchemaToJson($rating);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('EmployerAggregateRating', $data['@type']);
		$this->assertSame('Organization', $data['itemReviewed']['@type']);
		$this->assertSame('Google', $data['itemReviewed']['name']);
		$this->assertSame(4.2, $data['ratingValue']);
	}

	public function testEmployerAggregateRatingWithAllFields(): void
	{
		$rating = new EmployerAggregateRating(
			itemReviewed: new Organization(
				name: 'Acme Corp',
				url: 'https://acme.example.com',
				sameAs: ['https://linkedin.com/company/acme'],
			),
			ratingValue: 3.8,
			ratingCount: 450,
			reviewCount: 120,
			bestRating: 5,
			worstRating: 1,
		);

		$json = JsonLdGenerator::SchemaToJson($rating);
		$data = json_decode($json, true);

		$this->assertSame('EmployerAggregateRating', $data['@type']);
		$this->assertSame(3.8, $data['ratingValue']);
		$this->assertSame(450, $data['ratingCount']);
		$this->assertSame(120, $data['reviewCount']);
		$this->assertSame(5, $data['bestRating']);
		$this->assertSame(1, $data['worstRating']);
		$this->assertSame('Acme Corp', $data['itemReviewed']['name']);
		$this->assertSame('https://acme.example.com', $data['itemReviewed']['url']);
	}

	public function testEmployerAggregateRatingNullFieldsOmitted(): void
	{
		$rating = new EmployerAggregateRating(
			itemReviewed: new Organization(name: 'Minimal Co'),
			ratingValue: 4.0,
		);

		$json = JsonLdGenerator::SchemaToJson($rating);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('ratingCount', $data);
		$this->assertArrayNotHasKey('reviewCount', $data);
		$this->assertArrayNotHasKey('bestRating', $data);
		$this->assertArrayNotHasKey('worstRating', $data);
	}
}
