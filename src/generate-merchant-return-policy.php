<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvaLok\SchemaOrgJsonLd\v1\JsonLdGenerator;
use EvaLok\SchemaOrgJsonLd\v1\Enum\MerchantReturnEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MerchantReturnPolicy;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MerchantReturnPolicySeasonalOverride;
use EvaLok\SchemaOrgJsonLd\v1\Schema\MonetaryAmount;
use EvaLok\SchemaOrgJsonLd\v1\Enum\RefundTypeEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ReturnFeesEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ReturnLabelSourceEnumeration;
use EvaLok\SchemaOrgJsonLd\v1\Enum\ReturnMethodEnumeration;

$policy = new MerchantReturnPolicy(
	applicableCountry: ['US', 'CA'],
	returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
	merchantReturnDays: 30,
	merchantReturnLink: 'https://www.example.com/returns',
	returnMethod: ReturnMethodEnumeration::ReturnByMail,
	returnFees: ReturnFeesEnumeration::FreeReturn,
	refundType: RefundTypeEnumeration::FullRefund,
	returnLabelSource: ReturnLabelSourceEnumeration::ReturnLabelDownloadAndPrint,
	customerRemorseReturnFees: ReturnFeesEnumeration::FreeReturn,
	customerRemorseReturnLabelSource: ReturnLabelSourceEnumeration::ReturnLabelDownloadAndPrint,
	itemDefectReturnFees: ReturnFeesEnumeration::FreeReturn,
	itemDefectReturnLabelSource: ReturnLabelSourceEnumeration::ReturnLabelInBox,
	returnPolicySeasonalOverride: new MerchantReturnPolicySeasonalOverride(
		startDate: '2026-11-29',
		endDate: '2027-01-31',
		returnPolicyCategory: MerchantReturnEnumeration::MerchantReturnFiniteReturnWindow,
		merchantReturnDays: 60,
	),
);

echo JsonLdGenerator::SchemaToJson($policy) . "\n";
