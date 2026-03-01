#!/usr/bin/env bun
/**
 * TypeScript parity check tool.
 *
 * Generates JSON-LD from TS types using the same data as our PHP generate scripts,
 * compares the output to PHP, and validates through Adobe's structured data validator.
 *
 * Usage: bun run tools/ts-parity-check.ts
 */

import Validator from '@adobe/structured-data-validator';
import WebAutoExtractor from '@marbec/web-auto-extractor';
import { execSync } from 'child_process';
import { resolve, dirname } from 'path';

// Import TS types from the vendor package source
import { JsonLdGenerator } from '../vendor/evabee/schema-org-json-ld/ts/src/JsonLdGenerator';

// Schema types
import { AggregateOffer } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/AggregateOffer';
import { AggregateRating } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/AggregateRating';
import { Answer } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Answer';
import { Article } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Article';
import { BlogPosting } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/BlogPosting';
import { Brand } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Brand';
import { BreadcrumbList } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/BreadcrumbList';
import { BroadcastEvent } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/BroadcastEvent';
import { Certification } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Certification';
import { Clip } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Clip';
import { Course } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Course';
import { CourseInstance } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/CourseInstance';
import { DefinedRegion } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/DefinedRegion';
import { Event } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Event';
import { FAQPage } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/FAQPage';
import { FoodEstablishment } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/FoodEstablishment';
import { GeoCoordinates } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/GeoCoordinates';
import { HowToSection } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/HowToSection';
import { HowToStep } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/HowToStep';
import { InteractionCounter } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/InteractionCounter';
import { ListItem } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/ListItem';
import { LocalBusiness } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/LocalBusiness';
import { MathSolver } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/MathSolver';
import { MerchantReturnPolicy } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/MerchantReturnPolicy';
import { MobileApplication } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/MobileApplication';
import { MonetaryAmount } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/MonetaryAmount';
import { Movie } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Movie';
import { NewsArticle } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/NewsArticle';
import { NutritionInformation } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/NutritionInformation';
import { Offer } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Offer';
import { OpeningHoursSpecification } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/OpeningHoursSpecification';
import { Organization } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Organization';
import { PeopleAudience } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/PeopleAudience';
import { Person } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Person';
import { Place } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Place';
import { PostalAddress } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/PostalAddress';
import { Product } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Product';
import { ProductGroup } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/ProductGroup';
import { QuantitativeValue } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/QuantitativeValue';
import { Question } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Question';
import { Rating } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Rating';
import { Recipe } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Recipe';
import { Review } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Review';
import { ServicePeriod } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/ServicePeriod';
import { ShippingConditions } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/ShippingConditions';
import { ShippingService } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/ShippingService';
import { SoftwareApplication } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/SoftwareApplication';
import { SolveMathAction } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/SolveMathAction';
import { QAPage } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/QAPage';
import { Restaurant } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Restaurant';
import { Store } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Store';
import { VideoObject } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/VideoObject';
import { VirtualLocation } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/VirtualLocation';
import { WebApplication } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/WebApplication';

// Enums
import { DayOfWeek } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/DayOfWeek';
import { EventAttendanceModeEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/EventAttendanceModeEnumeration';
import { EventStatusType } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/EventStatusType';
import { FulfillmentTypeEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/FulfillmentTypeEnumeration';
import { ItemAvailability } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/ItemAvailability';
import { MerchantReturnEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/MerchantReturnEnumeration';
import { OfferItemCondition } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/OfferItemCondition';
import { ReturnFeesEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/ReturnFeesEnumeration';
import { ReturnMethodEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/ReturnMethodEnumeration';

interface ValidationIssue {
	rootType: string;
	dataFormat: string;
	issueMessage: string;
	severity: 'ERROR' | 'WARNING';
	fieldNames?: string[];
}

interface ParityResult {
	type: string;
	phpScript: string;
	tsJsonLd: string;
	phpJsonLd: string;
	parity: boolean;
	parityDiffs: string[];
	e2eErrors: number;
	e2eWarnings: number;
	e2eIssues: ValidationIssue[];
}

type TsEntry = { type: string; phpScript: string; json: string };

function generateTsJsonLd(): Map<string, TsEntry> {
	const results = new Map<string, TsEntry>();

	// ===================================================================
	// 1. Article — matches src/generate-article.php
	// ===================================================================
	const article = new Article({
		headline: 'Understanding Tidal Patterns in the North Sea',
		author: new Person({ name: 'Dr. Sarah Chen' }),
		datePublished: '2025-01-15',
		dateModified: '2025-02-01',
		description: 'A comprehensive guide to tidal patterns and their impact on coastal ecosystems.',
		publisher: new Organization({
			name: 'Nature Weekly',
			logo: 'https://example.com/logo.png',
		}),
		image: [
			'https://example.com/tidal-1x1.jpg',
			'https://example.com/tidal-4x3.jpg',
			'https://example.com/tidal-16x9.jpg',
		],
	});
	results.set('Article', {
		type: 'Article',
		phpScript: 'src/generate-article.php',
		json: JsonLdGenerator.schemaToJson(article),
	});

	// ===================================================================
	// 2. BreadcrumbList — matches src/generate-breadcrumblist.php
	// ===================================================================
	const breadcrumb = new BreadcrumbList([
		new ListItem(1, 'Home', 'https://example.com/'),
		new ListItem(2, 'Electronics', 'https://example.com/electronics'),
		new ListItem(3, 'Phones', 'https://example.com/electronics/phones'),
		new ListItem(4, 'Pixel 9 Pro'),
	]);
	results.set('BreadcrumbList', {
		type: 'BreadcrumbList',
		phpScript: 'src/generate-breadcrumblist.php',
		json: JsonLdGenerator.schemaToJson(breadcrumb),
	});

	// ===================================================================
	// 3. FAQPage — matches src/generate-faqpage.php
	// ===================================================================
	const faq = new FAQPage([
		new Question({
			name: 'What is JSON-LD?',
			acceptedAnswer: new Answer(
				'JSON-LD is a method of encoding Linked Data using JSON. It allows data to be serialized in a way that is familiar to developers.',
			),
		}),
		new Question({
			name: 'Why should I use structured data on my website?',
			acceptedAnswer: new Answer(
				'Structured data helps search engines understand your content better and can enable rich results in search, such as FAQ snippets, recipe cards, and product listings.',
			),
		}),
		new Question({
			name: 'How do I validate my structured data?',
			acceptedAnswer: new Answer(
				'You can use the Google Rich Results Test at search.google.com/test/rich-results to validate your structured data and see which rich result types it supports.',
			),
		}),
	]);
	results.set('FAQPage', {
		type: 'FAQPage',
		phpScript: 'src/generate-faqpage.php',
		json: JsonLdGenerator.schemaToJson(faq),
	});

	// ===================================================================
	// 4. Event — matches src/generate-event.php
	// ===================================================================
	const event = new Event({
		name: 'The Rolling Stones - Hackney Diamonds Tour',
		startDate: '2025-07-21T19:00-05:00',
		location: [
			new Place(
				'Soldier Field',
				new PostalAddress({
					streetAddress: '1410 Special Olympics Dr',
					addressLocality: 'Chicago',
					addressRegion: 'IL',
					postalCode: '60605',
					addressCountry: 'US',
				}),
			),
			new VirtualLocation(
				'https://livestream.example.com/rolling-stones',
				'Official Livestream',
			),
		],
		description: 'The Rolling Stones return to Chicago for one night only. Available in-person and via livestream.',
		endDate: '2025-07-21T23:00-05:00',
		eventAttendanceMode: EventAttendanceModeEnumeration.MixedEventAttendanceMode,
		eventStatus: EventStatusType.EventScheduled,
		image: ['https://example.com/rolling-stones-tour.jpg'],
		offers: new Offer({
			url: 'https://example.com/tickets/rolling-stones',
			priceCurrency: 'USD',
			price: 125.00,
			itemCondition: OfferItemCondition.NewCondition,
			availability: ItemAvailability.InStock,
		}),
		organizer: new Organization({
			name: 'Live Nation',
			url: 'https://www.livenation.com',
		}),
		performer: new Person({ name: 'The Rolling Stones' }),
	});
	results.set('Event', {
		type: 'Event',
		phpScript: 'src/generate-event.php',
		json: JsonLdGenerator.schemaToJson(event),
	});

	// ===================================================================
	// 5. BlogPosting — matches src/generate-blogposting.php
	//    Inheritance chain: BlogPosting -> Article
	// ===================================================================
	const blogPosting = new BlogPosting({
		headline: 'Understanding Dependency Injection in PHP',
		author: new Person({ name: 'Marcus Rivera' }),
		datePublished: '2025-11-20',
		dateModified: '2025-12-01',
		description: 'A practical guide to dependency injection patterns in modern PHP applications.',
		publisher: new Organization({
			name: 'PHP Weekly',
			logo: 'https://example.com/phpweekly-logo.png',
		}),
		image: ['https://example.com/di-php.jpg', 'https://example.com/di-diagram.png'],
	});
	results.set('BlogPosting', {
		type: 'BlogPosting',
		phpScript: 'src/generate-blogposting.php',
		json: JsonLdGenerator.schemaToJson(blogPosting),
	});

	// ===================================================================
	// 6. NewsArticle — matches src/generate-newsarticle.php
	//    Inheritance chain: NewsArticle -> Article
	// ===================================================================
	const newsArticle = new NewsArticle({
		headline: 'City Council Approves $50M Green Infrastructure Plan',
		author: new Person({ name: 'Sarah Greenfield' }),
		datePublished: '2025-03-10T09:00:00Z',
		dateModified: '2025-03-10T14:30:00Z',
		description: 'The city council voted unanimously to approve a $50 million plan for green infrastructure improvements including urban forests, rain gardens, and permeable pavements.',
		publisher: new Organization({
			name: 'Metro Daily News',
			logo: 'https://metrodaily.example.com/logo.png',
			url: 'https://metrodaily.example.com',
		}),
		image: [
			'https://example.com/photos/green-plan-1x1.jpg',
			'https://example.com/photos/green-plan-4x3.jpg',
			'https://example.com/photos/green-plan-16x9.jpg',
		],
	});
	results.set('NewsArticle', {
		type: 'NewsArticle',
		phpScript: 'src/generate-newsarticle.php',
		json: JsonLdGenerator.schemaToJson(newsArticle),
	});

	// ===================================================================
	// 7. SoftwareApplication — matches src/generate-softwareapplication.php
	// ===================================================================
	const softwareApp = new SoftwareApplication({
		name: 'TaskFlow Pro',
		offers: new Offer({
			url: 'https://example.com/taskflow-pro',
			priceCurrency: 'USD',
			price: 4.99,
			itemCondition: OfferItemCondition.NewCondition,
			availability: ItemAvailability.InStock,
		}),
		aggregateRating: new AggregateRating(4.6, 5, 1, 8250),
		applicationCategory: 'BusinessApplication',
		operatingSystem: 'Android, iOS',
		description: 'A powerful task management app for professionals.',
	});
	results.set('SoftwareApplication', {
		type: 'SoftwareApplication',
		phpScript: 'src/generate-softwareapplication.php',
		json: JsonLdGenerator.schemaToJson(softwareApp),
	});

	// ===================================================================
	// 8. MobileApplication — matches src/generate-mobileapplication.php
	//    Inheritance chain: MobileApplication -> SoftwareApplication
	// ===================================================================
	const mobileApp = new MobileApplication({
		name: 'FitTracker',
		offers: new Offer({
			url: 'https://play.google.com/store/apps/details?id=com.example.fittracker',
			priceCurrency: 'USD',
			price: 0,
			itemCondition: OfferItemCondition.NewCondition,
			availability: ItemAvailability.InStock,
		}),
		aggregateRating: new AggregateRating(4.5, 5, 1, 32100),
		applicationCategory: 'HealthApplication',
		operatingSystem: 'Android 10+',
		datePublished: '2025-03-15',
		review: new Review(
			new Person({ name: 'FitnessGuru' }),
			new Rating(5, 5, 1),
			'Best fitness tracking app I have ever used. Accurate heart rate monitoring.',
		),
		description: 'Track your workouts, heart rate, and daily steps with precision.',
	});
	results.set('MobileApplication', {
		type: 'MobileApplication',
		phpScript: 'src/generate-mobileapplication.php',
		json: JsonLdGenerator.schemaToJson(mobileApp),
	});

	// ===================================================================
	// 9. WebApplication — matches src/generate-webapplication.php
	//    Inheritance chain: WebApplication -> SoftwareApplication
	// ===================================================================
	const webApp = new WebApplication({
		name: 'CloudNote Editor',
		offers: new Offer({
			url: 'https://example.com/cloudnote',
			priceCurrency: 'USD',
			price: 9.99,
			itemCondition: OfferItemCondition.NewCondition,
			availability: ItemAvailability.InStock,
		}),
		aggregateRating: new AggregateRating(4.3, 5, 1, 5670),
		applicationCategory: 'ProductivityApplication',
		operatingSystem: 'All',
		description: 'A collaborative note-taking and document editing web application.',
		screenshot: 'https://example.com/screenshots/cloudnote-editor.png',
	});
	results.set('WebApplication', {
		type: 'WebApplication',
		phpScript: 'src/generate-webapplication.php',
		json: JsonLdGenerator.schemaToJson(webApp),
	});

	// ===================================================================
	// 10. Movie — matches src/generate-movie.php
	// ===================================================================
	const movie = new Movie({
		name: 'The Algorithmic Garden',
		image: 'https://example.com/photos/algorithmic-garden.jpg',
		aggregateRating: new AggregateRating(8.1, 10, 1, 45230),
		dateCreated: '2025-06-15',
		datePublished: '2025-11-21',
		director: new Person({ name: 'Sofia Castellano' }),
		review: new Review(
			new Person({ name: 'Roger Chen' }),
			new Rating(9, 10, 1),
			'A stunning visual exploration of mathematics in nature.',
		),
		description: 'A mathematician discovers that the patterns in an ancient garden hold the key to a revolutionary algorithm.',
		actor: [
			new Person({ name: 'Elena Voss' }),
			new Person({ name: 'Marcus Reid' }),
		],
	});
	results.set('Movie', {
		type: 'Movie',
		phpScript: 'src/generate-movie.php',
		json: JsonLdGenerator.schemaToJson(movie),
	});

	// ===================================================================
	// 11. Product — matches src/generate-product.php
	// ===================================================================
	const product = new Product({
		name: 'Executive Anvil',
		image: [
			'https://example.com/photos/1x1/anvil.jpg',
			'https://example.com/photos/4x3/anvil.jpg',
			'https://example.com/photos/16x9/anvil.jpg',
		],
		description: 'Sleek and deadly, this anvil is the epitome of executive style.',
		sku: '0446310786',
		offers: [
			new Offer({
				url: 'https://example.com/anvil',
				priceCurrency: 'USD',
				price: 119.99,
				itemCondition: OfferItemCondition.NewCondition,
				availability: ItemAvailability.InStock,
				priceValidUntil: '2026-12-31',
				hasMerchantReturnPolicy: new MerchantReturnPolicy({
					applicableCountry: 'US',
					returnPolicyCategory: MerchantReturnEnumeration.MerchantReturnFiniteReturnWindow,
					merchantReturnDays: 30,
					returnFees: ReturnFeesEnumeration.FreeReturn,
					returnMethod: ReturnMethodEnumeration.ReturnByMail,
				}),
			}),
		],
		brand: new Brand('ACME'),
		mpn: '925872',
		color: 'Slate Gray',
		material: 'Hardened Steel',
		pattern: 'Solid',
		size: 'Standard',
		gtin: '0012345678905',
		inProductGroupWithID: 'pg-executive-tools',
		isVariantOf: new ProductGroup({
			name: 'Executive Tools Collection',
			productGroupID: 'pg-executive-tools',
		}),
		subjectOf: 'https://example.com/product-review-video',
		audience: new PeopleAudience('unisex', 18),
		hasCertification: [
			new Certification(
				'ACME Safety Certified',
				new Organization({ name: 'ACME Safety Council' }),
				'ASC-2025-0042',
			),
		],
		aggregateRating: new AggregateRating(4.4, 5, 1, 89, 12),
		review: [
			new Review(
				new Person({ name: 'Fred Benson' }),
				new Rating(5, 5, 1),
				'This is the best anvil I have ever used. Heavy duty and well built.',
				'2025-04-01',
				'Best anvil ever',
			),
			new Review(
				new Person({ name: 'Sara Mitchell' }),
				new Rating(4, 5, 1),
				'Great quality but a bit pricey for what you get.',
				'2025-05-10',
				'Good but expensive',
			),
		],
	});
	results.set('Product', {
		type: 'Product',
		phpScript: 'src/generate-product.php',
		json: JsonLdGenerator.schemaToJson(product),
	});

	// ===================================================================
	// 12. Product (AggregateOffer) — matches src/generate-product-aggregate-offer.php
	// ===================================================================
	const productAggOffer = new Product({
		name: 'Wireless Bluetooth Headphones',
		image: [
			'https://example.com/photos/headphones-front.jpg',
			'https://example.com/photos/headphones-side.jpg',
		],
		description: 'Premium noise-cancelling wireless headphones with 30-hour battery life.',
		sku: 'WBH-PRO-2025',
		offers: new AggregateOffer(149.99, 'USD', 249.99, 8),
		brand: new Brand('AudioTech'),
		aggregateRating: new AggregateRating(4.6, 5, 1, 234, 45),
		mpn: 'AT-WBH-PRO',
		color: 'Matte Black',
		material: 'Premium ABS Plastic',
		pattern: 'Solid',
		size: 'One Size',
		gtin: '0098765432101',
		inProductGroupWithID: 'audio-headphones-pro',
		isVariantOf: new ProductGroup({
			name: 'AudioTech Pro Series',
			productGroupID: 'audio-headphones-pro',
		}),
		subjectOf: 'https://example.com/product-comparison-video',
		audience: new PeopleAudience('unisex', 13),
		review: [
			new Review(
				new Person({ name: 'Alex Chen' }),
				new Rating(5, 5, 1),
				'Incredible noise cancellation and battery life.',
				'2025-06-15',
				'Best headphones ever',
			),
		],
		hasCertification: [
			new Certification(
				'Bluetooth 5.3 Certified',
				new Organization({ name: 'Bluetooth SIG' }),
				'BT53-WBH-2025',
			),
		],
	});
	results.set('Product-AggregateOffer', {
		type: 'Product',
		phpScript: 'src/generate-product-aggregate-offer.php',
		json: JsonLdGenerator.schemaToJson(productAggOffer),
	});

	// ===================================================================
	// 13. ProductGroup — matches src/generate-product-group.php
	// ===================================================================
	const groupRef = new ProductGroup({
		name: 'Classic Oxford Shirt Collection',
		productGroupID: 'oxford-shirts',
		variesBy: ['https://schema.org/color', 'https://schema.org/size'],
		url: 'https://example.com/shirts/oxford',
	});

	const blueShirt = new Product({
		name: 'Classic Oxford Shirt - Blue',
		image: ['https://example.com/shirts/blue.jpg'],
		description: 'Classic oxford button-down shirt in blue.',
		sku: 'SHIRT-OX-BLUE-M',
		offers: [
			new Offer({
				url: 'https://example.com/shirts/blue',
				priceCurrency: 'USD',
				price: 89.00,
				availability: ItemAvailability.InStock,
				priceValidUntil: '2026-12-31',
			}),
		],
		brand: new Brand('ClassicWear'),
		mpn: 'OX-BLUE-M',
		material: '100% Premium Cotton',
		pattern: 'Solid',
		inProductGroupWithID: 'oxford-shirts',
		subjectOf: 'https://example.com/shirt-review',
		audience: new PeopleAudience('unisex', 16),
		hasCertification: [
			new Certification(
				'OEKO-TEX Standard 100',
				new Organization({ name: 'OEKO-TEX Association' }),
				'OT-12345',
			),
		],
		aggregateRating: new AggregateRating(4.6, 5, 1, 156),
		review: [
			new Review(
				new Person({ name: 'Sam T.' }),
				new Rating(5, 5, 1),
				'Perfect fit and great quality cotton.',
				'2025-08-15',
			),
		],
		color: 'Blue',
		size: 'M',
		gtin: '0012345678905',
		isVariantOf: groupRef,
	});

	const whiteShirt = new Product({
		name: 'Classic Oxford Shirt - White',
		image: ['https://example.com/shirts/white.jpg'],
		description: 'Classic oxford button-down shirt in white.',
		sku: 'SHIRT-OX-WHITE-M',
		offers: [
			new Offer({
				url: 'https://example.com/shirts/white',
				priceCurrency: 'USD',
				price: 89.00,
				availability: ItemAvailability.InStock,
				priceValidUntil: '2026-12-31',
			}),
		],
		brand: new Brand('ClassicWear'),
		mpn: 'OX-WHITE-M',
		material: '100% Premium Cotton',
		pattern: 'Solid',
		inProductGroupWithID: 'oxford-shirts',
		subjectOf: 'https://example.com/shirt-review',
		audience: new PeopleAudience('unisex', 16),
		hasCertification: [
			new Certification(
				'OEKO-TEX Standard 100',
				new Organization({ name: 'OEKO-TEX Association' }),
				'OT-12345',
			),
		],
		aggregateRating: new AggregateRating(4.5, 5, 1, 142),
		review: [
			new Review(
				new Person({ name: 'Jordan K.' }),
				new Rating(5, 5, 1),
				'Crisp look and comfortable all day.',
				'2025-09-03',
			),
		],
		color: 'White',
		size: 'M',
		gtin: '0012345678912',
		isVariantOf: groupRef,
	});

	const productGroup = new ProductGroup({
		name: 'Classic Oxford Shirt Collection',
		productGroupID: 'oxford-shirts',
		variesBy: ['https://schema.org/color', 'https://schema.org/size'],
		hasVariant: [blueShirt, whiteShirt],
		url: 'https://example.com/shirts/oxford',
		description: 'Our classic oxford button-down shirts, available in multiple colors and sizes.',
		brand: new Brand('ClassicWear'),
		aggregateRating: new AggregateRating(4.7, 5, 1, 312, 89),
	});
	results.set('ProductGroup', {
		type: 'ProductGroup',
		phpScript: 'src/generate-product-group.php',
		json: JsonLdGenerator.schemaToJson(productGroup),
	});

	// ===================================================================
	// 14. Recipe — matches src/generate-recipe.php
	// ===================================================================
	const recipe = new Recipe({
		name: 'Classic Banana Bread',
		image: [
			'https://example.com/photos/1x1/banana-bread.jpg',
			'https://example.com/photos/4x3/banana-bread.jpg',
			'https://example.com/photos/16x9/banana-bread.jpg',
		],
		author: new Person({ name: 'Mary Baker' }),
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
				'Preheat oven to 350\u00B0F (175\u00B0C). Grease a 4x8 inch loaf pan.',
				'Preheat the oven',
				'https://example.com/banana-bread#step1',
				'https://example.com/photos/banana-bread/step1.jpg',
				new Clip('Preheating the Oven', 0, 'https://example.com/videos/banana-bread.mp4?t=0', 30),
				[
					'Set oven temperature to 350\u00B0F (175\u00B0C).',
					'Lightly grease a 4x8 inch loaf pan with butter or cooking spray.',
				],
			),
			new HowToStep(
				'Mash the bananas in a mixing bowl with a fork.',
				'Mash bananas',
				'https://example.com/banana-bread#step2',
				'https://example.com/photos/banana-bread/step2.jpg',
				new Clip('Mashing the Bananas', 30, 'https://example.com/videos/banana-bread.mp4?t=30', 90),
				[
					'Peel all ripe bananas and place them in a large bowl.',
					'Mash with a fork until mostly smooth with a few small lumps.',
				],
			),
			new HowToStep(
				'Mix in the melted butter, sugar, egg, and vanilla.',
				'Mix wet ingredients',
				'https://example.com/banana-bread#step3',
				'https://example.com/photos/banana-bread/step3.jpg',
				new Clip('Combining the Wet Ingredients', 90, 'https://example.com/videos/banana-bread.mp4?t=90', 150),
				[
					'Stir melted butter into the mashed bananas.',
					'Add sugar, beaten egg, and vanilla extract.',
					'Mix until the batter looks evenly combined.',
				],
			),
			new HowToStep(
				'Stir in the baking soda and salt. Mix in the flour.',
				'Add dry ingredients',
				'https://example.com/banana-bread#step4',
				'https://example.com/photos/banana-bread/step4.jpg',
				new Clip('Adding Dry Ingredients', 150, 'https://example.com/videos/banana-bread.mp4?t=150', 210),
				[
					'Sprinkle baking soda and salt over the wet batter.',
					'Add flour and fold gently just until no dry streaks remain.',
				],
			),
			new HowToStep(
				'Pour batter into prepared loaf pan.',
				'Fill loaf pan',
				'https://example.com/banana-bread#step5',
				'https://example.com/photos/banana-bread/step5.jpg',
				new Clip('Filling the Loaf Pan', 210, 'https://example.com/videos/banana-bread.mp4?t=210', 270),
				[
					'Scrape the batter from the bowl into the greased loaf pan.',
					'Spread the top evenly with a spatula for even baking.',
				],
			),
			new HowToStep(
				'Bake for 60 minutes or until a toothpick inserted comes out clean.',
				'Bake banana bread',
				'https://example.com/banana-bread#step6',
				'https://example.com/photos/banana-bread/step6.jpg',
				new Clip('Baking and Checking Doneness', 270, 'https://example.com/videos/banana-bread.mp4?t=270', 510),
				[
					'Place the pan on the center rack and bake for about 60 minutes.',
					'Check doneness by inserting a toothpick into the center.',
					'Cool in the pan for 10 minutes before transferring to a rack.',
				],
			),
		],
		nutrition: new NutritionInformation({
			calories: '240 calories',
			fatContent: '8 g',
			carbohydrateContent: '40 g',
			proteinContent: '3 g',
			servingSize: '1 slice',
		}),
		aggregateRating: new AggregateRating(4.7, 5, 1, 256),
		video: new VideoObject({
			name: 'How to Make Banana Bread',
			thumbnailUrl: ['https://example.com/photos/banana-bread/video-thumbnail.jpg'],
			uploadDate: '2025-02-05',
			description: 'Watch our step-by-step guide to making the best banana bread.',
			contentUrl: 'https://example.com/videos/banana-bread.mp4',
			embedUrl: 'https://example.com/embed/banana-bread',
			duration: 'PT8M30S',
		}),
		expires: '2027-01-10',
		hasPart: [
			new Clip('Preparing Ingredients', 0, 'https://example.com/videos/banana-bread.mp4?t=0', 90),
			new Clip('Mixing and Baking', 90, 'https://example.com/videos/banana-bread.mp4?t=90', 510),
		],
		publication: new BroadcastEvent(true, '2025-01-10T10:00:00-05:00', '2025-01-10T11:00:00-05:00'),
		ineligibleRegion: 'US-PR',
		interactionStatistic: new InteractionCounter('https://schema.org/WatchAction', 48250),
	});
	results.set('Recipe', {
		type: 'Recipe',
		phpScript: 'src/generate-recipe.php',
		json: JsonLdGenerator.schemaToJson(recipe),
	});

	// ===================================================================
	// 15. Recipe (sections) — matches src/generate-recipe-sections.php
	// ===================================================================
	const recipeSections = new Recipe({
		name: 'Classic Tiramisu',
		image: [
			'https://example.com/photos/1x1/tiramisu.jpg',
			'https://example.com/photos/4x3/tiramisu.jpg',
			'https://example.com/photos/16x9/tiramisu.jpg',
		],
		author: new Person({ name: 'Chef Marco' }),
		datePublished: '2025-03-15',
		description: 'An authentic Italian tiramisu with espresso-soaked ladyfingers and mascarpone cream.',
		prepTime: 'PT30M',
		cookTime: 'PT0M',
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
			new HowToSection('Prepare the Mascarpone Cream', [
				new HowToStep(
					'Whisk egg yolks and sugar until thick and pale yellow.',
					'Whisk yolks and sugar',
					'https://example.com/tiramisu#cream-step1',
					'https://example.com/photos/tiramisu/step-1.jpg',
					new Clip('Whisking Yolks and Sugar', 0, 'https://example.com/videos/tiramisu.mp4?t=0', 30),
					['Add egg yolks and sugar to a mixing bowl.', 'Whisk continuously until the mixture is pale and thick.'],
				),
				new HowToStep(
					'Add mascarpone cheese and mix until smooth.',
					'Blend in mascarpone',
					'https://example.com/tiramisu#cream-step2',
					'https://example.com/photos/tiramisu/step-2.jpg',
					new Clip('Blending in Mascarpone', 30, 'https://example.com/videos/tiramisu.mp4?t=30', 90),
					['Add mascarpone to the yolk mixture in portions.', 'Mix until the cream is smooth with no lumps.'],
				),
				new HowToStep(
					'In a separate bowl, whip heavy cream to stiff peaks.',
					'Whip heavy cream',
					'https://example.com/tiramisu#cream-step3',
					'https://example.com/photos/tiramisu/step-3.jpg',
					new Clip('Whipping Heavy Cream', 90, 'https://example.com/videos/tiramisu.mp4?t=90', 150),
					['Pour cold heavy cream into a chilled bowl.', 'Whip until the cream holds firm peaks.'],
				),
				new HowToStep(
					'Gently fold whipped cream into the mascarpone mixture.',
					'Fold cream mixture',
					'https://example.com/tiramisu#cream-step4',
					'https://example.com/photos/tiramisu/step-4.jpg',
					new Clip('Folding the Cream Mixture', 150, 'https://example.com/videos/tiramisu.mp4?t=150', 210),
					['Add whipped cream to the mascarpone base in batches.', 'Fold gently to keep the mixture airy and smooth.'],
				),
			]),
			new HowToSection('Assemble the Tiramisu', [
				new HowToStep(
					'Combine espresso and coffee liqueur in a shallow dish.',
					'Prepare espresso dip',
					'https://example.com/tiramisu#assemble-step1',
					'https://example.com/photos/tiramisu/step-5.jpg',
					new Clip('Preparing the Espresso Dip', 210, 'https://example.com/videos/tiramisu.mp4?t=210', 270),
					['Pour cooled espresso into a shallow dish.', 'Stir in coffee liqueur until fully combined.'],
				),
				new HowToStep(
					'Quickly dip each ladyfinger into the espresso mixture.',
					'Dip ladyfingers',
					'https://example.com/tiramisu#assemble-step2',
					'https://example.com/photos/tiramisu/step-6.jpg',
					new Clip('Dipping Ladyfingers', 270, 'https://example.com/videos/tiramisu.mp4?t=270', 330),
					['Dip each ladyfinger briefly on both sides.', 'Avoid soaking too long so the cookies stay structured.'],
				),
				new HowToStep(
					'Arrange a layer of soaked ladyfingers in a 9x13 dish.',
					'Layer ladyfingers',
					'https://example.com/tiramisu#assemble-step3',
					'https://example.com/photos/tiramisu/step-7.jpg',
					new Clip('Layering Ladyfingers', 330, 'https://example.com/videos/tiramisu.mp4?t=330', 390),
					['Place soaked ladyfingers tightly in a single layer.', 'Trim or break pieces to fill any gaps in the dish.'],
				),
				new HowToStep(
					'Spread half the mascarpone cream over the ladyfingers.',
					'Add first cream layer',
					'https://example.com/tiramisu#assemble-step4',
					'https://example.com/photos/tiramisu/step-8.jpg',
					new Clip('Adding the First Cream Layer', 390, 'https://example.com/videos/tiramisu.mp4?t=390', 450),
					['Spoon half of the mascarpone cream over the cookies.', 'Spread evenly to cover the entire ladyfinger layer.'],
				),
				new HowToStep(
					'Repeat with a second layer of ladyfingers and cream.',
					'Repeat layers',
					'https://example.com/tiramisu#assemble-step5',
					'https://example.com/photos/tiramisu/step-9.jpg',
					new Clip('Building the Second Layer', 450, 'https://example.com/videos/tiramisu.mp4?t=450', 510),
					['Add a second layer of dipped ladyfingers.', 'Top with the remaining mascarpone cream and smooth the surface.'],
				),
			]),
			new HowToSection('Chill and Serve', [
				new HowToStep(
					'Cover with plastic wrap and refrigerate for at least 4 hours.',
					'Chill tiramisu',
					'https://example.com/tiramisu#serve-step1',
					'https://example.com/photos/tiramisu/step-10.jpg',
					new Clip('Chilling the Tiramisu', 510, 'https://example.com/videos/tiramisu.mp4?t=510', 690),
					['Cover the dish tightly with plastic wrap.', 'Refrigerate for at least 4 hours so layers can set.'],
				),
				new HowToStep(
					'Dust generously with cocoa powder before serving.',
					'Finish with cocoa',
					'https://example.com/tiramisu#serve-step2',
					'https://example.com/photos/tiramisu/step-11.jpg',
					new Clip('Finishing with Cocoa', 690, 'https://example.com/videos/tiramisu.mp4?t=690', 765),
					['Use a fine sieve to dust cocoa powder over the top.', 'Slice and serve chilled for the best texture.'],
				),
			]),
		],
		aggregateRating: new AggregateRating(4.9, 5, 1, 184),
		nutrition: new NutritionInformation({ calories: '450 calories' }),
		video: new VideoObject({
			name: 'Classic Tiramisu Tutorial',
			thumbnailUrl: ['https://example.com/photos/tiramisu/video-thumbnail.jpg'],
			uploadDate: '2025-03-15',
			description: 'Learn how to make authentic Italian tiramisu from scratch.',
			contentUrl: 'https://example.com/videos/tiramisu.mp4',
			embedUrl: 'https://example.com/embed/tiramisu',
			duration: 'PT12M45S',
		}),
		expires: '2027-03-15',
		hasPart: [
			new Clip('Preparing the Cream', 0, 'https://example.com/videos/tiramisu.mp4?t=0', 210),
			new Clip('Assembling Layers', 210, 'https://example.com/videos/tiramisu.mp4?t=210', 510),
			new Clip('Finishing Touches', 510, 'https://example.com/videos/tiramisu.mp4?t=510', 765),
		],
		publication: new BroadcastEvent(false, '2025-03-15T14:00:00+01:00', '2025-03-15T14:30:00+01:00'),
		ineligibleRegion: 'GB-NIR',
		interactionStatistic: new InteractionCounter('https://schema.org/WatchAction', 31500),
	});
	results.set('Recipe-Sections', {
		type: 'Recipe',
		phpScript: 'src/generate-recipe-sections.php',
		json: JsonLdGenerator.schemaToJson(recipeSections),
	});

	// ===================================================================
	// 16. VideoObject — matches src/generate-videoobject.php
	// ===================================================================
	const videoObject = new VideoObject({
		name: 'How to Make Sourdough Bread from Scratch',
		thumbnailUrl: [
			'https://example.com/photos/sourdough-1x1.jpg',
			'https://example.com/photos/sourdough-4x3.jpg',
			'https://example.com/photos/sourdough-16x9.jpg',
		],
		uploadDate: '2025-02-05T08:00:00+00:00',
		description: 'A step-by-step guide to making artisan sourdough bread at home, from creating your starter to baking the perfect loaf.',
		contentUrl: 'https://example.com/video/sourdough-guide.mp4',
		embedUrl: 'https://example.com/embed/sourdough-guide',
		duration: 'PT23M15S',
		expires: '2027-02-05T08:00:00+00:00',
		regionsAllowed: 'US,CA,GB,AU',
		interactionStatistic: new InteractionCounter('WatchAction', 14503),
		publication: new BroadcastEvent(false, '2025-02-05T08:00:00+00:00', '2025-02-05T09:00:00+00:00'),
		hasPart: [
			new Clip('Creating the Starter', 0, 'https://example.com/video/sourdough-guide?t=0', 180),
			new Clip('Mixing the Dough', 180, 'https://example.com/video/sourdough-guide?t=180', 420),
			new Clip('Shaping and Proofing', 420, 'https://example.com/video/sourdough-guide?t=420', 840),
			new Clip('Baking the Loaf', 840, 'https://example.com/video/sourdough-guide?t=840', 1395),
		],
	});
	results.set('VideoObject', {
		type: 'VideoObject',
		phpScript: 'src/generate-videoobject.php',
		json: JsonLdGenerator.schemaToJson(videoObject),
	});

	// ===================================================================
	// 17. Course — matches src/generate-course.php
	// ===================================================================
	const course = new Course({
		name: 'Introduction to Machine Learning',
		description: 'A comprehensive introduction to machine learning concepts, algorithms, and practical applications using Python.',
		provider: new Organization({ name: 'DataScience Academy' }),
		offers: [
			new Offer({
				url: 'https://example.com/courses/ml-intro',
				priceCurrency: 'USD',
				price: 199.99,
				itemCondition: OfferItemCondition.NewCondition,
				availability: ItemAvailability.InStock,
			}),
		],
		hasCourseInstance: [
			new CourseInstance('online', new Person({ name: 'Dr. Emily Zhang' })),
		],
		inLanguage: 'en',
		aggregateRating: new AggregateRating(4.8, 5, 1, 1024),
		image: 'https://example.com/photos/ml-course.jpg',
	});
	results.set('Course', {
		type: 'Course',
		phpScript: 'src/generate-course.php',
		json: JsonLdGenerator.schemaToJson(course),
	});

	// ===================================================================
	// 18. LocalBusiness — matches src/generate-localbusiness.php
	// ===================================================================
	const localBiz = new LocalBusiness({
		name: "Dave's Steak House",
		address: new PostalAddress({
			streetAddress: '148 W 51st St',
			addressLocality: 'New York',
			addressRegion: 'NY',
			postalCode: '10019',
			addressCountry: 'US',
		}),
		url: 'https://davessteakhouse.example.com',
		telephone: '+1-212-555-0100',
		description: 'Classic American steakhouse in the heart of Midtown Manhattan.',
		image: ['https://example.com/photos/daves-exterior.jpg'],
		priceRange: '$$$',
		geo: new GeoCoordinates(40.7614, -73.9826),
		aggregateRating: new AggregateRating(4.4, 5, 1, null, 267),
		review: new Review(
			'James T.',
			new Rating(5, 5, 1),
			'Best steak I have had in years. The service was impeccable.',
			'2025-02-01',
		),
		servesCuisine: 'American',
		logo: 'https://example.com/daves-logo.png',
	});
	results.set('LocalBusiness', {
		type: 'LocalBusiness',
		phpScript: 'src/generate-localbusiness.php',
		json: JsonLdGenerator.schemaToJson(localBiz),
	});

	// ===================================================================
	// 19. FoodEstablishment — matches src/generate-foodestablishment.php
	//     Inheritance chain: FoodEstablishment -> LocalBusiness
	// ===================================================================
	const foodEstab = new FoodEstablishment({
		name: 'The Golden Spoon Bistro',
		address: new PostalAddress({
			streetAddress: '742 Evergreen Terrace',
			addressLocality: 'Portland',
			addressRegion: 'OR',
			postalCode: '97205',
			addressCountry: 'US',
		}),
		url: 'https://goldenspoonbistro.example.com',
		telephone: '+1-503-555-0199',
		description: 'Farm-to-table bistro featuring seasonal Pacific Northwest cuisine.',
		image: ['https://example.com/photos/golden-spoon-exterior.jpg', 'https://example.com/photos/golden-spoon-interior.jpg'],
		priceRange: '$$$',
		geo: new GeoCoordinates(45.5231, -122.6765),
		aggregateRating: new AggregateRating(4.7, 5, 1, null, 312),
		review: new Review(
			'Maria G.',
			new Rating(5, 5, 1),
			'The tasting menu was extraordinary. Every course was a masterpiece.',
			'2025-11-15',
		),
		servesCuisine: 'Pacific Northwest',
		logo: 'https://example.com/golden-spoon-logo.png',
		acceptsReservations: true,
	});
	results.set('FoodEstablishment', {
		type: 'FoodEstablishment',
		phpScript: 'src/generate-foodestablishment.php',
		json: JsonLdGenerator.schemaToJson(foodEstab),
	});

	// ===================================================================
	// 20. Store — matches src/generate-store.php
	//     Inheritance chain: Store -> LocalBusiness
	// ===================================================================
	const store = new Store({
		name: 'GreenLeaf Garden Center',
		address: new PostalAddress({
			streetAddress: '500 Nursery Road',
			addressLocality: 'Austin',
			addressRegion: 'TX',
			postalCode: '78745',
			addressCountry: 'US',
		}),
		url: 'https://greenleafgarden.example.com',
		telephone: '+1-512-555-0150',
		description: 'Family-owned garden center specializing in native Texas plants and organic gardening supplies.',
		image: ['https://example.com/photos/greenleaf-storefront.jpg'],
		priceRange: '$$',
		geo: new GeoCoordinates(30.2087, -97.7796),
		openingHoursSpecification: [
			new OpeningHoursSpecification(DayOfWeek.Monday, '08:00', '18:00'),
			new OpeningHoursSpecification(DayOfWeek.Tuesday, '08:00', '18:00'),
			new OpeningHoursSpecification(DayOfWeek.Wednesday, '08:00', '18:00'),
			new OpeningHoursSpecification(DayOfWeek.Thursday, '08:00', '18:00'),
			new OpeningHoursSpecification(DayOfWeek.Friday, '08:00', '18:00'),
			new OpeningHoursSpecification(DayOfWeek.Saturday, '08:00', '18:00'),
			new OpeningHoursSpecification(DayOfWeek.Sunday, '10:00', '16:00'),
		],
		aggregateRating: new AggregateRating(4.8, 5, 1, null, 156),
		review: new Review(
			'Carlos D.',
			new Rating(5, 5, 1),
			'Incredible selection of native plants. The staff really knows their stuff.',
			'2025-10-20',
		),
		logo: 'https://example.com/greenleaf-logo.png',
	});
	results.set('Store', {
		type: 'Store',
		phpScript: 'src/generate-store.php',
		json: JsonLdGenerator.schemaToJson(store),
	});

	// ===================================================================
	// 21. MathSolver — matches src/generate-mathsolver.php
	//     Tests propertyMap: mathExpressionInput -> mathExpression-input
	// ===================================================================
	const mathSolver = new MathSolver(
		'https://math.example.com/solver',
		'https://math.example.com/terms',
		[
			new SolveMathAction(
				'https://math.example.com/solve-algebra',
				'text',
				['Polynomial', 'Linear Equation'],
			),
			new SolveMathAction(
				'https://math.example.com/solve-calculus',
				'latex',
				'Integral',
			),
		],
		'MathWay Solver',
		'en',
		'Math Solver',
		['Algebra', 'Calculus'],
	);
	results.set('MathSolver', {
		type: 'MathSolver',
		phpScript: 'src/generate-mathsolver.php',
		json: JsonLdGenerator.schemaToJson(mathSolver),
	});

	// ===================================================================
	// 22. ShippingService — matches src/generate-shipping-service.php
	// ===================================================================
	const shippingService = new ShippingService(
		new ShippingConditions({
			shippingDestination: new DefinedRegion('US', ['CA', 'NY', 'TX']),
			shippingRate: new MonetaryAmount('USD', 5.99),
			transitTime: new ServicePeriod(
				new QuantitativeValue(null, 'DAY', 3, 7),
			),
		}),
		'Standard Shipping',
		'Standard ground shipping within the US.',
		FulfillmentTypeEnumeration.FulfillmentTypeDelivery,
		new ServicePeriod(
			new QuantitativeValue(null, 'DAY', 0, 1),
			null,
			'14:00:00-05:00',
		),
	);
	results.set('ShippingService', {
		type: 'ShippingService',
		phpScript: 'src/generate-shipping-service.php',
		json: JsonLdGenerator.schemaToJson(shippingService),
	});

	// ===================================================================
	// 24. QAPage — matches src/generate-qapage.php
	// ===================================================================
	const qaPage = new QAPage(
		new Question({
			name: 'How do I validate JSON-LD structured data locally?',
			text: 'I want to validate my JSON-LD output against Google Rich Results requirements without using a browser. Is there a local tool?',
			answerCount: 2,
			acceptedAnswer: new Answer(
				'Use @adobe/structured-data-validator — it validates against Google requirements locally with deterministic results.',
				new Person({ name: 'DevHelper' }),
				null,
				42,
				'2025-02-20',
			),
			suggestedAnswer: [
				new Answer(
					'You can also use structured-data-testing-tool for basic structural checks, though it does not validate against Google-specific requirements.',
					new Person({ name: 'SchemaFan' }),
					null,
					15,
					'2025-02-21',
				),
			],
			author: new Person({ name: 'NewDev123' }),
			datePublished: '2025-02-19',
		}),
	);
	results.set('QAPage', {
		type: 'QAPage',
		phpScript: 'src/generate-qapage.php',
		json: JsonLdGenerator.schemaToJson(qaPage),
	});

	// ===================================================================
	// 25. Restaurant — matches src/generate-restaurant.php
	//     Inheritance chain: Restaurant -> FoodEstablishment -> LocalBusiness
	// ===================================================================
	const restaurant = new Restaurant({
		name: 'Bella Napoli Trattoria',
		address: new PostalAddress({
			streetAddress: '88 Little Italy Lane',
			addressLocality: 'New York',
			addressRegion: 'NY',
			postalCode: '10013',
			addressCountry: 'US',
		}),
		url: 'https://bellanapoli.example.com',
		telephone: '+1-212-555-0188',
		description: 'Authentic Neapolitan pizza and pasta in the heart of Little Italy.',
		image: ['https://example.com/photos/bella-napoli.jpg'],
		priceRange: '$$',
		geo: new GeoCoordinates(40.7191, -73.9973),
		openingHoursSpecification: [
			new OpeningHoursSpecification(DayOfWeek.Monday, '11:00', '22:00'),
			new OpeningHoursSpecification(DayOfWeek.Tuesday, '11:00', '22:00'),
			new OpeningHoursSpecification(DayOfWeek.Wednesday, '11:00', '22:00'),
			new OpeningHoursSpecification(DayOfWeek.Thursday, '11:00', '22:00'),
			new OpeningHoursSpecification(DayOfWeek.Friday, '11:00', '22:00'),
			new OpeningHoursSpecification(DayOfWeek.Saturday, '10:00', '23:00'),
			new OpeningHoursSpecification(DayOfWeek.Sunday, '10:00', '23:00'),
		],
		aggregateRating: new AggregateRating(4.5, 5, 1, null, 487),
		review: [
			new Review(
				'Anthony R.',
				new Rating(5, 5, 1),
				'Best margherita pizza outside of Naples. The crust is perfection.',
				'2025-12-01',
			),
			new Review(
				'Lisa M.',
				new Rating(4, 5, 1),
				'Great food and atmosphere, but can get crowded on weekends.',
				'2025-11-20',
			),
		],
		menu: 'https://bellanapoli.example.com/menu',
		servesCuisine: 'Italian',
		logo: 'https://example.com/bella-napoli-logo.png',
		acceptsReservations: 'https://bellanapoli.example.com/reservations',
	});
	results.set('Restaurant', {
		type: 'Restaurant',
		phpScript: 'src/generate-restaurant.php',
		json: JsonLdGenerator.schemaToJson(restaurant),
	});

	// ===================================================================
	// 26. @graph — matches src/generate-graph.php
	//     Tests schemasToJson (plural) for multi-schema output
	// ===================================================================
	const graphArticle = new Article({
		headline: 'Understanding JSON-LD and Structured Data',
		image: [
			'https://example.com/images/jsonld-guide.jpg',
			'https://example.com/images/jsonld-guide-wide.jpg',
		],
		author: [
			new Person({
				name: 'Sarah Chen',
				url: 'https://example.com/authors/sarah-chen',
			}),
		],
		publisher: new Organization({
			name: 'TechBlog Inc',
			logo: 'https://example.com/logo.png',
		}),
		datePublished: '2026-02-20',
		dateModified: '2026-02-25',
	});

	const graphBreadcrumbs = new BreadcrumbList([
		new ListItem(1, 'Home', 'https://example.com/'),
		new ListItem(2, 'Blog', 'https://example.com/blog/'),
		new ListItem(3, 'Understanding JSON-LD', 'https://example.com/blog/jsonld-guide'),
	]);

	const graphPublisher = new Organization({
		name: 'TechBlog Inc',
		url: 'https://example.com',
		logo: 'https://example.com/logo.png',
	});

	results.set('@graph', {
		type: '@graph',
		phpScript: 'src/generate-graph.php',
		json: JsonLdGenerator.schemasToJson(graphArticle, graphBreadcrumbs, graphPublisher),
	});

	return results;
}

function compareJsonLd(tsJson: string, phpJson: string): string[] {
	const diffs: string[] = [];
	const tsObj = JSON.parse(tsJson) as Record<string, unknown>;
	const phpObj = JSON.parse(phpJson) as Record<string, unknown>;

	function compare(ts: unknown, php: unknown, path: string): void {
		if (ts === php) return;

		if (typeof ts !== typeof php) {
			diffs.push(`${path}: type mismatch (TS: ${typeof ts}, PHP: ${typeof php})`);
			return;
		}

		if (ts === null || php === null) {
			if (ts !== php) diffs.push(`${path}: null mismatch (TS: ${ts}, PHP: ${php})`);
			return;
		}

		if (Array.isArray(ts) && Array.isArray(php)) {
			if (ts.length !== php.length) {
				diffs.push(`${path}: array length (TS: ${ts.length}, PHP: ${php.length})`);
			}
			const minLen = Math.min(ts.length, php.length);
			for (let i = 0; i < minLen; i++) {
				compare(ts[i], php[i], `${path}[${i}]`);
			}
			return;
		}

		if (typeof ts === 'object' && typeof php === 'object') {
			const tsKeys = Object.keys(ts as Record<string, unknown>).sort();
			const phpKeys = Object.keys(php as Record<string, unknown>).sort();

			for (const k of tsKeys) {
				if (!phpKeys.includes(k)) {
					diffs.push(`${path}.${k}: present in TS but not PHP`);
				}
			}
			for (const k of phpKeys) {
				if (!tsKeys.includes(k)) {
					diffs.push(`${path}.${k}: present in PHP but not TS`);
				}
			}
			for (const k of tsKeys) {
				if (phpKeys.includes(k)) {
					compare(
						(ts as Record<string, unknown>)[k],
						(php as Record<string, unknown>)[k],
						`${path}.${k}`,
					);
				}
			}
			return;
		}

		if (ts !== php) {
			diffs.push(`${path}: value mismatch (TS: ${JSON.stringify(ts)}, PHP: ${JSON.stringify(php)})`);
		}
	}

	compare(tsObj, phpObj, '$');
	return diffs;
}

function wrapInHtml(jsonLd: string): string {
	return `<!DOCTYPE html>
<html>
<head>
<script type="application/ld+json">
${jsonLd}
</script>
</head>
<body></body>
</html>`;
}

let schemaOrgJson: unknown = null;

async function fetchSchemaOrg(): Promise<unknown> {
	if (schemaOrgJson) return schemaOrgJson;
	console.log('Fetching schema.org definitions...');
	const response = await fetch('https://schema.org/version/latest/schemaorg-all-https.jsonld');
	schemaOrgJson = await response.json();
	console.log('Schema.org definitions loaded.');
	return schemaOrgJson;
}

async function validateJsonLd(jsonLd: string): Promise<{ errors: ValidationIssue[]; warnings: ValidationIssue[] }> {
	const html = wrapInHtml(jsonLd);
	const extractor = new WebAutoExtractor({ addLocation: true, embedSource: ['rdfa', 'microdata'] });
	const extractedData = extractor.parse(html);
	const schema = await fetchSchemaOrg();
	const validator = new Validator(schema);
	const issues = await validator.validate(extractedData) as ValidationIssue[];

	return {
		errors: issues.filter(i => i.severity === 'ERROR'),
		warnings: issues.filter(i => i.severity === 'WARNING'),
	};
}

async function main() {
	const projectRoot = resolve(dirname(new URL(import.meta.url).pathname), '..');

	console.log('=== TypeScript Parity Check ===\n');

	const tsResults = generateTsJsonLd();
	const results: ParityResult[] = [];
	let totalParityPass = 0;
	let totalParityFail = 0;
	let totalE2eErrors = 0;
	let totalE2eWarnings = 0;

	for (const [typeName, tsData] of tsResults) {
		console.log(`\n--- ${typeName} ---`);

		let phpJsonLd: string;
		const phpPath = resolve(projectRoot, tsData.phpScript);
		try {
			phpJsonLd = execSync(`php ${phpPath}`, { encoding: 'utf-8', cwd: projectRoot }).trim();
		} catch (err) {
			console.log(`  PHP ERROR: ${(err as Error).message}`);
			continue;
		}

		const diffs = compareJsonLd(tsData.json, phpJsonLd);
		const parity = diffs.length === 0;

		if (parity) {
			console.log('  Parity: MATCH');
			totalParityPass++;
		} else {
			console.log('  Parity: MISMATCH');
			for (const d of diffs) {
				console.log(`    ${d}`);
			}
			totalParityFail++;
		}

		const { errors, warnings } = await validateJsonLd(tsData.json);
		console.log(`  E2E: ${errors.length} errors, ${warnings.length} warnings`);

		if (errors.length > 0) {
			for (const e of errors) {
				console.log(`    ERROR: [${e.rootType}] ${e.issueMessage}`);
			}
		}
		if (warnings.length > 0) {
			for (const w of warnings) {
				console.log(`    WARN: [${w.rootType}] ${w.issueMessage}`);
			}
		}

		totalE2eErrors += errors.length;
		totalE2eWarnings += warnings.length;

		results.push({
			type: typeName,
			phpScript: tsData.phpScript,
			tsJsonLd: tsData.json,
			phpJsonLd,
			parity,
			parityDiffs: diffs,
			e2eErrors: errors.length,
			e2eWarnings: warnings.length,
			e2eIssues: [...errors, ...warnings],
		});
	}

	console.log('\n=== Summary ===');
	console.log(`Types tested: ${tsResults.size}`);
	console.log(`Parity: ${totalParityPass} match, ${totalParityFail} mismatch`);
	console.log(`E2E: ${totalE2eErrors} errors, ${totalE2eWarnings} warnings`);
	console.log(`Result: ${totalParityFail === 0 && totalE2eErrors === 0 ? 'PASS' : 'ISSUES FOUND'}`);

	if (totalParityFail > 0) {
		console.log('\n=== Parity Mismatches Detail ===');
		for (const r of results) {
			if (!r.parity) {
				console.log(`\n--- ${r.type} ---`);
				console.log('TS output:');
				console.log(r.tsJsonLd);
				console.log('PHP output:');
				console.log(r.phpJsonLd);
			}
		}
	}

	process.exit(totalParityFail > 0 || totalE2eErrors > 0 ? 1 : 0);
}

main();
