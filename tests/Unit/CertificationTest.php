<?php

namespace Evabee\SchemaOrgQc\Tests\Unit;

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Certification;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Organization;
use EvaLok\SchemaOrgJsonLd\v1\Schema\Rating;
use PHPUnit\Framework\TestCase;

class CertificationTest extends TestCase
{
	public function testBasicCertification(): void
	{
		$cert = new Certification(
			name: 'ENERGY STAR',
			issuedBy: new Organization(name: 'U.S. Environmental Protection Agency'),
		);
		$json = JsonLdGenerator::SchemaToJson($cert);
		$data = json_decode($json, true);

		$this->assertSame('Certification', $data['@type']);
		$this->assertSame('ENERGY STAR', $data['name']);
		$this->assertSame('Organization', $data['issuedBy']['@type']);
		$this->assertSame('U.S. Environmental Protection Agency', $data['issuedBy']['name']);
		$this->assertArrayNotHasKey('certificationIdentification', $data);
		$this->assertArrayNotHasKey('certificationRating', $data);
	}

	public function testCertificationWithAllProperties(): void
	{
		$cert = new Certification(
			name: 'EU Energy Label',
			issuedBy: new Organization(name: 'European Commission'),
			certificationIdentification: 'EU-ENERGY-A+++',
			certificationRating: new Rating(
				ratingValue: 5,
				bestRating: 5,
				worstRating: 1,
			),
		);
		$json = JsonLdGenerator::SchemaToJson($cert);
		$data = json_decode($json, true);

		$this->assertSame('EU Energy Label', $data['name']);
		$this->assertSame('EU-ENERGY-A+++', $data['certificationIdentification']);
		$this->assertSame('Rating', $data['certificationRating']['@type']);
		$this->assertSame(5, $data['certificationRating']['ratingValue']);
	}
}
