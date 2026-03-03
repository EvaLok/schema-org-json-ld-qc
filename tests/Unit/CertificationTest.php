<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\Enum\ItemAvailability;
use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Certification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Offer;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Product;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use PHPUnit\Framework\TestCase;

class CertificationTest extends TestCase
{
	public function testMinimalCertification(): void
	{
		$certification = new Certification(
			name: 'ISO 9001:2015 Quality Management',
			issuedBy: new Organization(name: 'International Organization for Standardization'),
		);
		$json = JsonLdGenerator::SchemaToJson($certification);
		$data = json_decode($json, true);

		$this->assertSame('https://schema.org/', $data['@context']);
		$this->assertSame('Certification', $data['@type']);
		$this->assertSame('ISO 9001:2015 Quality Management', $data['name']);
		$this->assertSame('Organization', $data['issuedBy']['@type']);
		$this->assertSame('International Organization for Standardization', $data['issuedBy']['name']);
		$this->assertArrayNotHasKey('certificationIdentification', $data);
		$this->assertArrayNotHasKey('certificationRating', $data);
	}

	public function testCertificationWithAllFields(): void
	{
		$certification = new Certification(
			name: 'ISO 9001:2015 Quality Management',
			issuedBy: new Organization(name: 'International Organization for Standardization'),
			certificationIdentification: 'ISO-9001-2025-00042',
			certificationRating: new Rating(
				ratingValue: 4.8,
				bestRating: 5,
				worstRating: 1,
			),
		);
		$json = JsonLdGenerator::SchemaToJson($certification);
		$data = json_decode($json, true);

		$this->assertSame('ISO-9001-2025-00042', $data['certificationIdentification']);
		$this->assertSame('Rating', $data['certificationRating']['@type']);
		$this->assertSame(4.8, $data['certificationRating']['ratingValue']);
		$this->assertSame(5, $data['certificationRating']['bestRating']);
		$this->assertSame(1, $data['certificationRating']['worstRating']);
	}

	public function testCertificationNullFieldsOmitted(): void
	{
		$certification = new Certification(
			name: 'ISO 9001:2015 Quality Management',
			issuedBy: new Organization(name: 'International Organization for Standardization'),
		);
		$json = JsonLdGenerator::SchemaToJson($certification);
		$data = json_decode($json, true);

		$this->assertArrayNotHasKey('certificationIdentification', $data);
		$this->assertArrayNotHasKey('certificationRating', $data);
	}

	public function testCertificationNestedInProduct(): void
	{
		$product = new Product(
			name: 'Acme Air Purifier Pro',
			image: ['https://example.com/images/air-purifier-pro.jpg'],
			description: 'HEPA air purifier with smart air-quality monitoring.',
			sku: 'ACME-AIR-PR-01',
			offers: [
				new Offer(
					url: 'https://example.com/products/acme-air-purifier-pro',
					priceCurrency: 'USD',
					price: 299.99,
					availability: ItemAvailability::InStock,
				),
			],
			hasCertification: new Certification(
				name: 'ISO 9001:2015 Quality Management',
				issuedBy: new Organization(name: 'International Organization for Standardization'),
				certificationIdentification: 'ISO-9001-2025-00042',
			),
		);
		$json = JsonLdGenerator::SchemaToJson($product);
		$data = json_decode($json, true);

		$this->assertSame('Certification', $data['hasCertification']['@type']);
		$this->assertSame('ISO-9001-2025-00042', $data['hasCertification']['certificationIdentification']);
		$this->assertSame('Organization', $data['hasCertification']['issuedBy']['@type']);
	}
}
