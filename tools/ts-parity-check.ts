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

// Additional schema types for expanded parity (25 → 39)
import { Accommodation } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Accommodation';
import { ContactPoint } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/ContactPoint';
import { AdministrativeArea } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/AdministrativeArea';
import { Comment } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Comment';
import { DataCatalog } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/DataCatalog';
import { DataDownload } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/DataDownload';
import { Dataset } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Dataset';
import { DiscussionForumPosting } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/DiscussionForumPosting';
import { EmployerAggregateRating } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/EmployerAggregateRating';
import { ItemList } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/ItemList';
import { JobPosting } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/JobPosting';
import { MemberProgram } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/MemberProgram';
import { MemberProgramTier } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/MemberProgramTier';
import { MerchantReturnPolicySeasonalOverride } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/MerchantReturnPolicySeasonalOverride';
import { ProfilePage } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/ProfilePage';
import { PropertyValue } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/PropertyValue';
import { Quiz } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Quiz';
import { Thing } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/Thing';
import { VacationRental } from '../vendor/evabee/schema-org-json-ld/ts/src/schema/VacationRental';

// Enums
import { DayOfWeek } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/DayOfWeek';
import { EventAttendanceModeEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/EventAttendanceModeEnumeration';
import { EventStatusType } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/EventStatusType';
import { FulfillmentTypeEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/FulfillmentTypeEnumeration';
import { ItemAvailability } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/ItemAvailability';
import { MerchantReturnEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/MerchantReturnEnumeration';
import { OfferItemCondition } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/OfferItemCondition';
import { ReturnFeesEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/ReturnFeesEnumeration';
import { RefundTypeEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/RefundTypeEnumeration';
import { ReturnLabelSourceEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/ReturnLabelSourceEnumeration';
import { ReturnMethodEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/ReturnMethodEnumeration';
import { TierBenefitEnumeration } from '../vendor/evabee/schema-org-json-ld/ts/src/enum/TierBenefitEnumeration';

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
	const breadcrumb = new BreadcrumbList({ itemListElement: [
		new ListItem({ position: 1, name: 'Home', item: 'https://example.com/' }),
		new ListItem({ position: 2, name: 'Electronics', item: 'https://example.com/electronics' }),
		new ListItem({ position: 3, name: 'Phones', item: 'https://example.com/electronics/phones' }),
		new ListItem({ position: 4, name: 'Pixel 9 Pro' }),
	] });
	results.set('BreadcrumbList', {
		type: 'BreadcrumbList',
		phpScript: 'src/generate-breadcrumblist.php',
		json: JsonLdGenerator.schemaToJson(breadcrumb),
	});

	// ===================================================================
	// 3. FAQPage — matches src/generate-faqpage.php
	// ===================================================================
	const faq = new FAQPage({ mainEntity: [
		new Question({
			name: 'What is JSON-LD?',
			acceptedAnswer: new Answer({
				text: 'JSON-LD is a method of encoding Linked Data using JSON. It allows data to be serialized in a way that is familiar to developers.',
			}),
		}),
		new Question({
			name: 'Why should I use structured data on my website?',
			acceptedAnswer: new Answer({
				text: 'Structured data helps search engines understand your content better and can enable rich results in search, such as FAQ snippets, recipe cards, and product listings.',
			}),
		}),
		new Question({
			name: 'How do I validate my structured data?',
			acceptedAnswer: new Answer({
				text: 'You can use the Google Rich Results Test at search.google.com/test/rich-results to validate your structured data and see which rich result types it supports.',
			}),
		}),
	] });
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
			new Place({
				name: 'Soldier Field',
				address: new PostalAddress({
					streetAddress: '1410 Special Olympics Dr',
					addressLocality: 'Chicago',
					addressRegion: 'IL',
					postalCode: '60605',
					addressCountry: 'US',
				}),
			}),
			new VirtualLocation({
				url: 'https://livestream.example.com/rolling-stones',
				name: 'Official Livestream',
			}),
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
		aggregateRating: new AggregateRating({ ratingValue: 4.6, bestRating: 5, worstRating: 1, ratingCount: 8250 }),
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
		aggregateRating: new AggregateRating({ ratingValue: 4.5, bestRating: 5, worstRating: 1, ratingCount: 32100 }),
		applicationCategory: 'HealthApplication',
		operatingSystem: 'Android 10+',
		datePublished: '2025-03-15',
		review: new Review({
			author: new Person({ name: 'FitnessGuru' }),
			reviewRating: new Rating({ ratingValue: 5, bestRating: 5, worstRating: 1 }),
			reviewBody: 'Best fitness tracking app I have ever used. Accurate heart rate monitoring.',
		}),
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
		aggregateRating: new AggregateRating({ ratingValue: 4.3, bestRating: 5, worstRating: 1, ratingCount: 5670 }),
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
		aggregateRating: new AggregateRating({ ratingValue: 8.1, bestRating: 10, worstRating: 1, ratingCount: 45230 }),
		dateCreated: '2025-06-15',
		datePublished: '2025-11-21',
		director: new Person({ name: 'Sofia Castellano' }),
		review: new Review({
			author: new Person({ name: 'Roger Chen' }),
			reviewRating: new Rating({ ratingValue: 9, bestRating: 10, worstRating: 1 }),
			reviewBody: 'A stunning visual exploration of mathematics in nature.',
		}),
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
		brand: new Brand({ name: 'ACME' }),
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
		audience: new PeopleAudience({ suggestedGender: 'unisex', suggestedMinAge: 18 }),
		hasCertification: [
			new Certification({
				name: 'ACME Safety Certified',
				issuedBy: new Organization({ name: 'ACME Safety Council' }),
				certificationIdentification: 'ASC-2025-0042',
			}),
		],
		aggregateRating: new AggregateRating({ ratingValue: 4.4, bestRating: 5, worstRating: 1, ratingCount: 89, reviewCount: 12 }),
		review: [
			new Review({
				author: new Person({ name: 'Fred Benson' }),
				reviewRating: new Rating({ ratingValue: 5, bestRating: 5, worstRating: 1 }),
				reviewBody: 'This is the best anvil I have ever used. Heavy duty and well built.',
				datePublished: '2025-04-01',
				name: 'Best anvil ever',
			}),
			new Review({
				author: new Person({ name: 'Sara Mitchell' }),
				reviewRating: new Rating({ ratingValue: 4, bestRating: 5, worstRating: 1 }),
				reviewBody: 'Great quality but a bit pricey for what you get.',
				datePublished: '2025-05-10',
				name: 'Good but expensive',
			}),
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
		offers: new AggregateOffer({ lowPrice: 149.99, priceCurrency: 'USD', highPrice: 249.99, offerCount: 8 }),
		brand: new Brand({ name: 'AudioTech' }),
		aggregateRating: new AggregateRating({ ratingValue: 4.6, bestRating: 5, worstRating: 1, ratingCount: 234, reviewCount: 45 }),
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
		audience: new PeopleAudience({ suggestedGender: 'unisex', suggestedMinAge: 13 }),
		review: [
			new Review({
				author: new Person({ name: 'Alex Chen' }),
				reviewRating: new Rating({ ratingValue: 5, bestRating: 5, worstRating: 1 }),
				reviewBody: 'Incredible noise cancellation and battery life.',
				datePublished: '2025-06-15',
				name: 'Best headphones ever',
			}),
		],
		hasCertification: [
			new Certification({
				name: 'Bluetooth 5.3 Certified',
				issuedBy: new Organization({ name: 'Bluetooth SIG' }),
				certificationIdentification: 'BT53-WBH-2025',
			}),
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
		brand: new Brand({ name: 'ClassicWear' }),
		mpn: 'OX-BLUE-M',
		material: '100% Premium Cotton',
		pattern: 'Solid',
		inProductGroupWithID: 'oxford-shirts',
		subjectOf: 'https://example.com/shirt-review',
		audience: new PeopleAudience({ suggestedGender: 'unisex', suggestedMinAge: 16 }),
		hasCertification: [
			new Certification({
				name: 'OEKO-TEX Standard 100',
				issuedBy: new Organization({ name: 'OEKO-TEX Association' }),
				certificationIdentification: 'OT-12345',
			}),
		],
		aggregateRating: new AggregateRating({ ratingValue: 4.6, bestRating: 5, worstRating: 1, ratingCount: 156 }),
		review: [
			new Review({
				author: new Person({ name: 'Sam T.' }),
				reviewRating: new Rating({ ratingValue: 5, bestRating: 5, worstRating: 1 }),
				reviewBody: 'Perfect fit and great quality cotton.',
				datePublished: '2025-08-15',
			}),
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
		brand: new Brand({ name: 'ClassicWear' }),
		mpn: 'OX-WHITE-M',
		material: '100% Premium Cotton',
		pattern: 'Solid',
		inProductGroupWithID: 'oxford-shirts',
		subjectOf: 'https://example.com/shirt-review',
		audience: new PeopleAudience({ suggestedGender: 'unisex', suggestedMinAge: 16 }),
		hasCertification: [
			new Certification({
				name: 'OEKO-TEX Standard 100',
				issuedBy: new Organization({ name: 'OEKO-TEX Association' }),
				certificationIdentification: 'OT-12345',
			}),
		],
		aggregateRating: new AggregateRating({ ratingValue: 4.5, bestRating: 5, worstRating: 1, ratingCount: 142 }),
		review: [
			new Review({
				author: new Person({ name: 'Jordan K.' }),
				reviewRating: new Rating({ ratingValue: 5, bestRating: 5, worstRating: 1 }),
				reviewBody: 'Crisp look and comfortable all day.',
				datePublished: '2025-09-03',
			}),
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
		brand: new Brand({ name: 'ClassicWear' }),
		aggregateRating: new AggregateRating({ ratingValue: 4.7, bestRating: 5, worstRating: 1, ratingCount: 312, reviewCount: 89 }),
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
			new HowToStep({
				text: 'Preheat oven to 350\u00B0F (175\u00B0C). Grease a 4x8 inch loaf pan.',
				name: 'Preheat the oven',
				url: 'https://example.com/banana-bread#step1',
				image: 'https://example.com/photos/banana-bread/step1.jpg',
				video: new Clip({ name: 'Preheating the Oven', startOffset: 0, url: 'https://example.com/videos/banana-bread.mp4?t=0', endOffset: 30 }),
				itemListElement: [
					'Set oven temperature to 350\u00B0F (175\u00B0C).',
					'Lightly grease a 4x8 inch loaf pan with butter or cooking spray.',
				],
			}),
			new HowToStep({
				text: 'Mash the bananas in a mixing bowl with a fork.',
				name: 'Mash bananas',
				url: 'https://example.com/banana-bread#step2',
				image: 'https://example.com/photos/banana-bread/step2.jpg',
				video: new Clip({ name: 'Mashing the Bananas', startOffset: 30, url: 'https://example.com/videos/banana-bread.mp4?t=30', endOffset: 90 }),
				itemListElement: [
					'Peel all ripe bananas and place them in a large bowl.',
					'Mash with a fork until mostly smooth with a few small lumps.',
				],
			}),
			new HowToStep({
				text: 'Mix in the melted butter, sugar, egg, and vanilla.',
				name: 'Mix wet ingredients',
				url: 'https://example.com/banana-bread#step3',
				image: 'https://example.com/photos/banana-bread/step3.jpg',
				video: new Clip({ name: 'Combining the Wet Ingredients', startOffset: 90, url: 'https://example.com/videos/banana-bread.mp4?t=90', endOffset: 150 }),
				itemListElement: [
					'Stir melted butter into the mashed bananas.',
					'Add sugar, beaten egg, and vanilla extract.',
					'Mix until the batter looks evenly combined.',
				],
			}),
			new HowToStep({
				text: 'Stir in the baking soda and salt. Mix in the flour.',
				name: 'Add dry ingredients',
				url: 'https://example.com/banana-bread#step4',
				image: 'https://example.com/photos/banana-bread/step4.jpg',
				video: new Clip({ name: 'Adding Dry Ingredients', startOffset: 150, url: 'https://example.com/videos/banana-bread.mp4?t=150', endOffset: 210 }),
				itemListElement: [
					'Sprinkle baking soda and salt over the wet batter.',
					'Add flour and fold gently just until no dry streaks remain.',
				],
			}),
			new HowToStep({
				text: 'Pour batter into prepared loaf pan.',
				name: 'Fill loaf pan',
				url: 'https://example.com/banana-bread#step5',
				image: 'https://example.com/photos/banana-bread/step5.jpg',
				video: new Clip({ name: 'Filling the Loaf Pan', startOffset: 210, url: 'https://example.com/videos/banana-bread.mp4?t=210', endOffset: 270 }),
				itemListElement: [
					'Scrape the batter from the bowl into the greased loaf pan.',
					'Spread the top evenly with a spatula for even baking.',
				],
			}),
			new HowToStep({
				text: 'Bake for 60 minutes or until a toothpick inserted comes out clean.',
				name: 'Bake banana bread',
				url: 'https://example.com/banana-bread#step6',
				image: 'https://example.com/photos/banana-bread/step6.jpg',
				video: new Clip({ name: 'Baking and Checking Doneness', startOffset: 270, url: 'https://example.com/videos/banana-bread.mp4?t=270', endOffset: 510 }),
				itemListElement: [
					'Place the pan on the center rack and bake for about 60 minutes.',
					'Check doneness by inserting a toothpick into the center.',
					'Cool in the pan for 10 minutes before transferring to a rack.',
				],
			}),
		],
		nutrition: new NutritionInformation({
			calories: '240 calories',
			fatContent: '8 g',
			carbohydrateContent: '40 g',
			proteinContent: '3 g',
			servingSize: '1 slice',
		}),
		aggregateRating: new AggregateRating({ ratingValue: 4.7, bestRating: 5, worstRating: 1, ratingCount: 256 }),
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
			new Clip({ name: 'Preparing Ingredients', startOffset: 0, url: 'https://example.com/videos/banana-bread.mp4?t=0', endOffset: 90 }),
			new Clip({ name: 'Mixing and Baking', startOffset: 90, url: 'https://example.com/videos/banana-bread.mp4?t=90', endOffset: 510 }),
		],
		publication: new BroadcastEvent({ isLiveBroadcast: true, startDate: '2025-01-10T10:00:00-05:00', endDate: '2025-01-10T11:00:00-05:00' }),
		ineligibleRegion: 'US-PR',
		interactionStatistic: new InteractionCounter({ interactionType: 'https://schema.org/WatchAction', userInteractionCount: 48250 }),
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
			new HowToSection({ name: 'Prepare the Mascarpone Cream', itemListElement: [
				new HowToStep({ text: 'Whisk egg yolks and sugar until thick and pale yellow.', name: 'Whisk yolks and sugar', url: 'https://example.com/tiramisu#cream-step1', image: 'https://example.com/photos/tiramisu/step-1.jpg', video: new Clip({ name: 'Whisking Yolks and Sugar', startOffset: 0, url: 'https://example.com/videos/tiramisu.mp4?t=0', endOffset: 30 }), itemListElement: ['Add egg yolks and sugar to a mixing bowl.', 'Whisk continuously until the mixture is pale and thick.'] }),
				new HowToStep({ text: 'Add mascarpone cheese and mix until smooth.', name: 'Blend in mascarpone', url: 'https://example.com/tiramisu#cream-step2', image: 'https://example.com/photos/tiramisu/step-2.jpg', video: new Clip({ name: 'Blending in Mascarpone', startOffset: 30, url: 'https://example.com/videos/tiramisu.mp4?t=30', endOffset: 90 }), itemListElement: ['Add mascarpone to the yolk mixture in portions.', 'Mix until the cream is smooth with no lumps.'] }),
				new HowToStep({ text: 'In a separate bowl, whip heavy cream to stiff peaks.', name: 'Whip heavy cream', url: 'https://example.com/tiramisu#cream-step3', image: 'https://example.com/photos/tiramisu/step-3.jpg', video: new Clip({ name: 'Whipping Heavy Cream', startOffset: 90, url: 'https://example.com/videos/tiramisu.mp4?t=90', endOffset: 150 }), itemListElement: ['Pour cold heavy cream into a chilled bowl.', 'Whip until the cream holds firm peaks.'] }),
				new HowToStep({ text: 'Gently fold whipped cream into the mascarpone mixture.', name: 'Fold cream mixture', url: 'https://example.com/tiramisu#cream-step4', image: 'https://example.com/photos/tiramisu/step-4.jpg', video: new Clip({ name: 'Folding the Cream Mixture', startOffset: 150, url: 'https://example.com/videos/tiramisu.mp4?t=150', endOffset: 210 }), itemListElement: ['Add whipped cream to the mascarpone base in batches.', 'Fold gently to keep the mixture airy and smooth.'] }),
			] }),
			new HowToSection({ name: 'Assemble the Tiramisu', itemListElement: [
				new HowToStep({ text: 'Combine espresso and coffee liqueur in a shallow dish.', name: 'Prepare espresso dip', url: 'https://example.com/tiramisu#assemble-step1', image: 'https://example.com/photos/tiramisu/step-5.jpg', video: new Clip({ name: 'Preparing the Espresso Dip', startOffset: 210, url: 'https://example.com/videos/tiramisu.mp4?t=210', endOffset: 270 }), itemListElement: ['Pour cooled espresso into a shallow dish.', 'Stir in coffee liqueur until fully combined.'] }),
				new HowToStep({ text: 'Quickly dip each ladyfinger into the espresso mixture.', name: 'Dip ladyfingers', url: 'https://example.com/tiramisu#assemble-step2', image: 'https://example.com/photos/tiramisu/step-6.jpg', video: new Clip({ name: 'Dipping Ladyfingers', startOffset: 270, url: 'https://example.com/videos/tiramisu.mp4?t=270', endOffset: 330 }), itemListElement: ['Dip each ladyfinger briefly on both sides.', 'Avoid soaking too long so the cookies stay structured.'] }),
				new HowToStep({ text: 'Arrange a layer of soaked ladyfingers in a 9x13 dish.', name: 'Layer ladyfingers', url: 'https://example.com/tiramisu#assemble-step3', image: 'https://example.com/photos/tiramisu/step-7.jpg', video: new Clip({ name: 'Layering Ladyfingers', startOffset: 330, url: 'https://example.com/videos/tiramisu.mp4?t=330', endOffset: 390 }), itemListElement: ['Place soaked ladyfingers tightly in a single layer.', 'Trim or break pieces to fill any gaps in the dish.'] }),
				new HowToStep({ text: 'Spread half the mascarpone cream over the ladyfingers.', name: 'Add first cream layer', url: 'https://example.com/tiramisu#assemble-step4', image: 'https://example.com/photos/tiramisu/step-8.jpg', video: new Clip({ name: 'Adding the First Cream Layer', startOffset: 390, url: 'https://example.com/videos/tiramisu.mp4?t=390', endOffset: 450 }), itemListElement: ['Spoon half of the mascarpone cream over the cookies.', 'Spread evenly to cover the entire ladyfinger layer.'] }),
				new HowToStep({ text: 'Repeat with a second layer of ladyfingers and cream.', name: 'Repeat layers', url: 'https://example.com/tiramisu#assemble-step5', image: 'https://example.com/photos/tiramisu/step-9.jpg', video: new Clip({ name: 'Building the Second Layer', startOffset: 450, url: 'https://example.com/videos/tiramisu.mp4?t=450', endOffset: 510 }), itemListElement: ['Add a second layer of dipped ladyfingers.', 'Top with the remaining mascarpone cream and smooth the surface.'] }),
			] }),
			new HowToSection({ name: 'Chill and Serve', itemListElement: [
				new HowToStep({ text: 'Cover with plastic wrap and refrigerate for at least 4 hours.', name: 'Chill tiramisu', url: 'https://example.com/tiramisu#serve-step1', image: 'https://example.com/photos/tiramisu/step-10.jpg', video: new Clip({ name: 'Chilling the Tiramisu', startOffset: 510, url: 'https://example.com/videos/tiramisu.mp4?t=510', endOffset: 690 }), itemListElement: ['Cover the dish tightly with plastic wrap.', 'Refrigerate for at least 4 hours so layers can set.'] }),
				new HowToStep({ text: 'Dust generously with cocoa powder before serving.', name: 'Finish with cocoa', url: 'https://example.com/tiramisu#serve-step2', image: 'https://example.com/photos/tiramisu/step-11.jpg', video: new Clip({ name: 'Finishing with Cocoa', startOffset: 690, url: 'https://example.com/videos/tiramisu.mp4?t=690', endOffset: 765 }), itemListElement: ['Use a fine sieve to dust cocoa powder over the top.', 'Slice and serve chilled for the best texture.'] }),
			] }),
		],
		aggregateRating: new AggregateRating({ ratingValue: 4.9, bestRating: 5, worstRating: 1, ratingCount: 184 }),
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
			new Clip({ name: 'Preparing the Cream', startOffset: 0, url: 'https://example.com/videos/tiramisu.mp4?t=0', endOffset: 210 }),
			new Clip({ name: 'Assembling Layers', startOffset: 210, url: 'https://example.com/videos/tiramisu.mp4?t=210', endOffset: 510 }),
			new Clip({ name: 'Finishing Touches', startOffset: 510, url: 'https://example.com/videos/tiramisu.mp4?t=510', endOffset: 765 }),
		],
		publication: new BroadcastEvent({ isLiveBroadcast: false, startDate: '2025-03-15T14:00:00+01:00', endDate: '2025-03-15T14:30:00+01:00' }),
		ineligibleRegion: 'GB-NIR',
		interactionStatistic: new InteractionCounter({ interactionType: 'https://schema.org/WatchAction', userInteractionCount: 31500 }),
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
		interactionStatistic: new InteractionCounter({ interactionType: 'WatchAction', userInteractionCount: 14503 }),
		publication: new BroadcastEvent({ isLiveBroadcast: false, startDate: '2025-02-05T08:00:00+00:00', endDate: '2025-02-05T09:00:00+00:00' }),
		hasPart: [
			new Clip({ name: 'Creating the Starter', startOffset: 0, url: 'https://example.com/video/sourdough-guide?t=0', endOffset: 180 }),
			new Clip({ name: 'Mixing the Dough', startOffset: 180, url: 'https://example.com/video/sourdough-guide?t=180', endOffset: 420 }),
			new Clip({ name: 'Shaping and Proofing', startOffset: 420, url: 'https://example.com/video/sourdough-guide?t=420', endOffset: 840 }),
			new Clip({ name: 'Baking the Loaf', startOffset: 840, url: 'https://example.com/video/sourdough-guide?t=840', endOffset: 1395 }),
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
			new CourseInstance({ courseMode: 'online', instructor: new Person({ name: 'Dr. Emily Zhang' }) }),
		],
		inLanguage: 'en',
		aggregateRating: new AggregateRating({ ratingValue: 4.8, bestRating: 5, worstRating: 1, ratingCount: 1024 }),
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
		geo: new GeoCoordinates({ latitude: 40.7614, longitude: -73.9826 }),
		aggregateRating: new AggregateRating({ ratingValue: 4.4, bestRating: 5, worstRating: 1, reviewCount: 267 }),
		review: new Review({
			author: 'James T.',
			reviewRating: new Rating({ ratingValue: 5, bestRating: 5, worstRating: 1 }),
			reviewBody: 'Best steak I have had in years. The service was impeccable.',
			datePublished: '2025-02-01',
		}),
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
		geo: new GeoCoordinates({ latitude: 45.5231, longitude: -122.6765 }),
		aggregateRating: new AggregateRating({ ratingValue: 4.7, bestRating: 5, worstRating: 1, reviewCount: 312 }),
		review: new Review({
			author: 'Maria G.',
			reviewRating: new Rating({ ratingValue: 5, bestRating: 5, worstRating: 1 }),
			reviewBody: 'The tasting menu was extraordinary. Every course was a masterpiece.',
			datePublished: '2025-11-15',
		}),
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
		geo: new GeoCoordinates({ latitude: 30.2087, longitude: -97.7796 }),
		openingHoursSpecification: [
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Monday, opens: '08:00', closes: '18:00' }),
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Tuesday, opens: '08:00', closes: '18:00' }),
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Wednesday, opens: '08:00', closes: '18:00' }),
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Thursday, opens: '08:00', closes: '18:00' }),
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Friday, opens: '08:00', closes: '18:00' }),
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Saturday, opens: '08:00', closes: '18:00' }),
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Sunday, opens: '10:00', closes: '16:00' }),
		],
		aggregateRating: new AggregateRating({ ratingValue: 4.8, bestRating: 5, worstRating: 1, reviewCount: 156 }),
		review: new Review({
			author: 'Carlos D.',
			reviewRating: new Rating({ ratingValue: 5, bestRating: 5, worstRating: 1 }),
			reviewBody: 'Incredible selection of native plants. The staff really knows their stuff.',
			datePublished: '2025-10-20',
		}),
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
	const mathSolver = new MathSolver({
		url: 'https://math.example.com/solver',
		usageInfo: 'https://math.example.com/terms',
		potentialAction: [
			new SolveMathAction({
				target: 'https://math.example.com/solve-algebra',
				mathExpressionInput: 'text',
				eduQuestionType: ['Polynomial', 'Linear Equation'],
			}),
			new SolveMathAction({
				target: 'https://math.example.com/solve-calculus',
				mathExpressionInput: 'latex',
				eduQuestionType: 'Integral',
			}),
		],
		name: 'MathWay Solver',
		inLanguage: 'en',
		learningResourceType: 'Math Solver',
		assesses: ['Algebra', 'Calculus'],
	});
	results.set('MathSolver', {
		type: 'MathSolver',
		phpScript: 'src/generate-mathsolver.php',
		json: JsonLdGenerator.schemaToJson(mathSolver),
	});

	// ===================================================================
	// 22. ShippingService — matches src/generate-shipping-service.php
	// ===================================================================
	const shippingService = new ShippingService({
		shippingConditions: new ShippingConditions({
			shippingDestination: new DefinedRegion({ addressCountry: 'US', addressRegion: ['CA', 'NY', 'TX'] }),
			shippingRate: new MonetaryAmount({ currency: 'USD', value: 5.99 }),
			transitTime: new ServicePeriod({
				duration: new QuantitativeValue({ unitCode: 'DAY', minValue: 3, maxValue: 7 }),
			}),
		}),
		name: 'Standard Shipping',
		description: 'Standard ground shipping within the US.',
		fulfillmentType: FulfillmentTypeEnumeration.FulfillmentTypeDelivery,
		handlingTime: new ServicePeriod({
			duration: new QuantitativeValue({ unitCode: 'DAY', minValue: 0, maxValue: 1 }),
			cutoffTime: '14:00:00-05:00',
		}),
	});
	results.set('ShippingService', {
		type: 'ShippingService',
		phpScript: 'src/generate-shipping-service.php',
		json: JsonLdGenerator.schemaToJson(shippingService),
	});

	// ===================================================================
	// 24. QAPage — matches src/generate-qapage.php
	// ===================================================================
	const qaPage = new QAPage({
		mainEntity: new Question({
			name: 'How do I validate JSON-LD structured data locally?',
			text: 'I want to validate my JSON-LD output against Google Rich Results requirements without using a browser. Is there a local tool?',
			answerCount: 2,
			acceptedAnswer: new Answer({
				text: 'Use @adobe/structured-data-validator — it validates against Google requirements locally with deterministic results.',
				author: new Person({ name: 'DevHelper' }),
				upvoteCount: 42,
				datePublished: '2025-02-20',
			}),
			suggestedAnswer: [
				new Answer({
					text: 'You can also use structured-data-testing-tool for basic structural checks, though it does not validate against Google-specific requirements.',
					author: new Person({ name: 'SchemaFan' }),
					upvoteCount: 15,
					datePublished: '2025-02-21',
				}),
			],
			author: new Person({ name: 'NewDev123' }),
			datePublished: '2025-02-19',
		}),
	});
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
		geo: new GeoCoordinates({ latitude: 40.7191, longitude: -73.9973 }),
		openingHoursSpecification: [
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Monday, opens: '11:00', closes: '22:00' }),
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Tuesday, opens: '11:00', closes: '22:00' }),
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Wednesday, opens: '11:00', closes: '22:00' }),
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Thursday, opens: '11:00', closes: '22:00' }),
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Friday, opens: '11:00', closes: '22:00' }),
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Saturday, opens: '10:00', closes: '23:00' }),
			new OpeningHoursSpecification({ dayOfWeek: DayOfWeek.Sunday, opens: '10:00', closes: '23:00' }),
		],
		aggregateRating: new AggregateRating({ ratingValue: 4.5, bestRating: 5, worstRating: 1, reviewCount: 487 }),
		review: [
			new Review({
				author: 'Anthony R.',
				reviewRating: new Rating({ ratingValue: 5, bestRating: 5, worstRating: 1 }),
				reviewBody: 'Best margherita pizza outside of Naples. The crust is perfection.',
				datePublished: '2025-12-01',
			}),
			new Review({
				author: 'Lisa M.',
				reviewRating: new Rating({ ratingValue: 4, bestRating: 5, worstRating: 1 }),
				reviewBody: 'Great food and atmosphere, but can get crowded on weekends.',
				datePublished: '2025-11-20',
			}),
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
	// 26. Clip — matches src/generate-clip.php
	// ===================================================================
	const clipStandalone = new Clip({
		name: 'Introduction to the Topic',
		startOffset: 0,
		url: 'https://example.com/video/tutorial?t=0',
		endOffset: 120,
	});
	results.set('Clip', {
		type: 'Clip',
		phpScript: 'src/generate-clip.php',
		json: JsonLdGenerator.schemaToJson(clipStandalone),
	});

	// ===================================================================
	// 27. Dataset — matches src/generate-dataset.php
	// ===================================================================
	const dataset = new Dataset({
		name: 'Global Ocean Temperature Records 1950-2025',
		description: 'Comprehensive dataset of ocean surface temperature measurements from 1950 to 2025, collected from buoys, satellites, and research vessels worldwide.',
		url: 'https://example.com/datasets/ocean-temp',
		creator: new Organization({ name: 'National Oceanographic Institute' }),
		license: 'https://creativecommons.org/licenses/by/4.0/',
		keywords: ['ocean temperature', 'climate data', 'marine science'],
		isAccessibleForFree: true,
		temporalCoverage: '1950/2025',
		includedInDataCatalog: new DataCatalog({ name: 'World Climate Data Repository' }),
		distribution: [new DataDownload({ contentUrl: 'https://example.com/datasets/ocean-temp/download.csv', encodingFormat: 'text/csv' })],
	});
	results.set('Dataset', {
		type: 'Dataset',
		phpScript: 'src/generate-dataset.php',
		json: JsonLdGenerator.schemaToJson(dataset),
	});

	// ===================================================================
	// 28. DiscussionForumPosting — matches src/generate-discussionforumposting.php
	// ===================================================================
	const dfp = new DiscussionForumPosting({
		author: new Person({ name: 'Alex Thompson' }),
		datePublished: '2025-03-10T14:30:00Z',
		text: 'Has anyone managed to get the new schema.org VacationRental type working with Google Rich Results? I keep getting validation warnings about missing fields.',
		headline: 'VacationRental schema validation issues',
		url: 'https://example.com/forum/posts/vacationrental-schema',
		comment: [
			new Comment({
				text: 'Yes! Make sure you include the address and aggregateRating fields. Those are recommended by Google.',
				author: new Person({ name: 'Sarah Dev' }),
				datePublished: '2025-03-10T15:45:00Z',
			}),
			new Comment({
				text: 'I found that the bestRating and worstRating fields on nested ratings also trigger warnings if omitted.',
				author: new Person({ name: 'Mike Builder' }),
				datePublished: '2025-03-10T16:20:00Z',
			}),
		],
	});
	results.set('DiscussionForumPosting', {
		type: 'DiscussionForumPosting',
		phpScript: 'src/generate-discussionforumposting.php',
		json: JsonLdGenerator.schemaToJson(dfp),
	});

	// ===================================================================
	// 29. EmployerAggregateRating — matches src/generate-employeraggregaterating.php
	// ===================================================================
	const ear = new EmployerAggregateRating({
		itemReviewed: new Organization({ name: 'TechCorp Industries' }),
		ratingValue: 4.2,
		ratingCount: 1847,
		reviewCount: 523,
		bestRating: 5,
		worstRating: 1,
	});
	results.set('EmployerAggregateRating', {
		type: 'EmployerAggregateRating',
		phpScript: 'src/generate-employeraggregaterating.php',
		json: JsonLdGenerator.schemaToJson(ear),
	});

	// ===================================================================
	// 30. ItemList — matches src/generate-itemlist.php
	// ===================================================================
	const itemList = new ItemList({
		itemListElement: [
			new ListItem({ position: 1, name: 'MacBook Pro 16-inch', url: 'https://example.com/best-laptops/macbook-pro' }),
			new ListItem({ position: 2, name: 'ThinkPad X1 Carbon', url: 'https://example.com/best-laptops/thinkpad-x1' }),
			new ListItem({ position: 3, name: 'Dell XPS 15', url: 'https://example.com/best-laptops/dell-xps-15' }),
		],
		itemListOrder: 'https://schema.org/ItemListOrderDescending',
		numberOfItems: 3,
	});
	results.set('ItemList', {
		type: 'ItemList',
		phpScript: 'src/generate-itemlist.php',
		json: JsonLdGenerator.schemaToJson(itemList),
	});

	// ===================================================================
	// 31. JobPosting — matches src/generate-jobposting.php
	// ===================================================================
	const jobPosting = new JobPosting({
		title: 'Senior Software Engineer',
		description: '<p>We are looking for a senior software engineer to lead our backend team. You will design and implement scalable APIs, mentor junior developers, and drive technical decisions.</p><p>Requirements: 5+ years experience with PHP or Python, experience with cloud infrastructure, strong communication skills.</p>',
		datePosted: '2025-03-01',
		hiringOrganization: new Organization({
			name: 'ACME Corp',
			url: 'https://acme.example.com',
			logo: 'https://acme.example.com/logo.png',
		}),
		jobLocation: new Place({
			name: 'ACME Headquarters',
			address: new PostalAddress({
				streetAddress: '100 Innovation Way',
				addressLocality: 'Austin',
				addressRegion: 'TX',
				postalCode: '78701',
				addressCountry: 'US',
			}),
		}),
		baseSalary: new MonetaryAmount({ currency: 'USD', minValue: 150000, maxValue: 200000 }),
		employmentType: 'FULL_TIME',
		validThrough: '2025-06-01',
		applicantLocationRequirements: new AdministrativeArea({ name: 'United States' }),
		jobLocationType: 'TELECOMMUTE',
		directApply: true,
		identifier: new PropertyValue({ name: 'Internal Job ID', value: 'SE-2025-0042' }),
	});
	results.set('JobPosting', {
		type: 'JobPosting',
		phpScript: 'src/generate-jobposting.php',
		json: JsonLdGenerator.schemaToJson(jobPosting),
	});

	// ===================================================================
	// 32. MemberProgram — matches src/generate-member-program.php
	// ===================================================================
	const memberProgram = new MemberProgram({
		name: 'ShopRewards Loyalty Program',
		description: 'Earn points on every purchase and unlock exclusive member benefits.',
		hasTiers: [
			new MemberProgramTier({
				name: 'Silver',
				hasTierBenefit: TierBenefitEnumeration.TierBenefitLoyaltyPoints,
				hasTierRequirement: 'No minimum spend required',
				membershipPointsEarned: new QuantitativeValue({ value: 1 }),
			}),
			new MemberProgramTier({
				name: 'Gold',
				hasTierBenefit: [TierBenefitEnumeration.TierBenefitLoyaltyPoints, TierBenefitEnumeration.TierBenefitLoyaltyPrice],
				hasTierRequirement: 'Spend $500 or more per year',
				membershipPointsEarned: new QuantitativeValue({ value: 2 }),
				url: 'https://www.example.com/rewards/gold',
			}),
		],
		url: 'https://www.example.com/rewards',
	});
	results.set('MemberProgram', {
		type: 'MemberProgram',
		phpScript: 'src/generate-member-program.php',
		json: JsonLdGenerator.schemaToJson(memberProgram),
	});

	// ===================================================================
	// 33. MerchantReturnPolicy — matches src/generate-merchant-return-policy.php
	// ===================================================================
	const mrp = new MerchantReturnPolicy({
		applicableCountry: ['US', 'CA'],
		returnPolicyCategory: MerchantReturnEnumeration.MerchantReturnFiniteReturnWindow,
		merchantReturnDays: 30,
		merchantReturnLink: 'https://www.example.com/returns',
		returnMethod: ReturnMethodEnumeration.ReturnByMail,
		returnFees: ReturnFeesEnumeration.FreeReturn,
		refundType: RefundTypeEnumeration.FullRefund,
		returnLabelSource: ReturnLabelSourceEnumeration.ReturnLabelDownloadAndPrint,
		customerRemorseReturnFees: ReturnFeesEnumeration.FreeReturn,
		customerRemorseReturnLabelSource: ReturnLabelSourceEnumeration.ReturnLabelDownloadAndPrint,
		itemDefectReturnFees: ReturnFeesEnumeration.FreeReturn,
		itemDefectReturnLabelSource: ReturnLabelSourceEnumeration.ReturnLabelInBox,
		returnPolicySeasonalOverride: new MerchantReturnPolicySeasonalOverride({
			startDate: '2026-11-29',
			endDate: '2027-01-31',
			returnPolicyCategory: MerchantReturnEnumeration.MerchantReturnFiniteReturnWindow,
			merchantReturnDays: 60,
		}),
	});
	results.set('MerchantReturnPolicy', {
		type: 'MerchantReturnPolicy',
		phpScript: 'src/generate-merchant-return-policy.php',
		json: JsonLdGenerator.schemaToJson(mrp),
	});

	// ===================================================================
	// 34. Organization — matches src/generate-organization.php
	// ===================================================================
	const orgStandalone = new Organization({
		name: 'TechStart Inc.',
		url: 'https://techstart.example.com',
		logo: 'https://techstart.example.com/logo.png',
		description: 'Leading technology startup accelerator.',
		email: 'info@techstart.example.com',
		telephone: '+1-555-123-4567',
		address: new PostalAddress({
			streetAddress: '123 Innovation Drive',
			addressLocality: 'San Francisco',
			addressRegion: 'CA',
			postalCode: '94105',
			addressCountry: 'US',
		}),
		contactPoint: new ContactPoint({ telephone: '+1-555-987-6543', contactType: 'customer service' }),
		sameAs: ['https://twitter.com/techstart', 'https://linkedin.com/company/techstart'],
		foundingDate: '2020-03-15',
		legalName: 'TechStart Incorporated',
		numberOfEmployees: new QuantitativeValue({ value: 150 }),
		taxID: '94-3456789',
		duns: '12-345-6789',
	});
	results.set('Organization', {
		type: 'Organization',
		phpScript: 'src/generate-organization.php',
		json: JsonLdGenerator.schemaToJson(orgStandalone),
	});

	// ===================================================================
	// 35. Person — matches src/generate-person.php
	// ===================================================================
	const personStandalone = new Person({
		name: 'Dr. Emily Zhang',
		url: 'https://emilyzhang.example.com',
		image: 'https://emilyzhang.example.com/photo.jpg',
		jobTitle: 'Senior Research Scientist',
		worksFor: new Organization({ name: 'BioGen Labs' }),
		sameAs: ['https://twitter.com/emilyzhang', 'https://linkedin.com/in/emilyzhang'],
		description: 'Genomics researcher specializing in CRISPR applications.',
		givenName: 'Emily',
		familyName: 'Zhang',
		address: new PostalAddress({
			addressLocality: 'Boston',
			addressRegion: 'MA',
			addressCountry: 'US',
		}),
	});
	results.set('Person', {
		type: 'Person',
		phpScript: 'src/generate-person.php',
		json: JsonLdGenerator.schemaToJson(personStandalone),
	});

	// ===================================================================
	// 36. ProfilePage — matches src/generate-profilepage.php
	// ===================================================================
	const profilePage = new ProfilePage({
		mainEntity: new Person({
			name: 'Ada Lovelace',
			url: 'https://example.com/profiles/ada-lovelace',
			sameAs: ['https://twitter.com/example_ada', 'https://www.linkedin.com/in/example-ada'],
		}),
		dateCreated: '2024-01-15',
		dateModified: '2025-03-20',
	});
	results.set('ProfilePage', {
		type: 'ProfilePage',
		phpScript: 'src/generate-profilepage.php',
		json: JsonLdGenerator.schemaToJson(profilePage),
	});

	// ===================================================================
	// 37. Quiz — matches src/generate-quiz.php
	// ===================================================================
	const quiz = new Quiz({
		hasPart: [
			new Question({
				name: 'What is the chemical symbol for water?',
				acceptedAnswer: new Answer({ text: 'H2O' }),
				eduQuestionType: 'Multiple choice',
			}),
			new Question({
				name: 'What planet is closest to the Sun?',
				acceptedAnswer: new Answer({ text: 'Mercury' }),
				eduQuestionType: 'Multiple choice',
			}),
		],
		about: 'General Science',
		name: 'Basic Science Quiz',
		description: 'Test your knowledge of basic science concepts.',
	});
	results.set('Quiz', {
		type: 'Quiz',
		phpScript: 'src/generate-quiz.php',
		json: JsonLdGenerator.schemaToJson(quiz),
	});

	// ===================================================================
	// 38. Review — matches src/generate-review.php
	// ===================================================================
	const reviewStandalone = new Review({
		author: new Person({ name: 'James Wilson' }),
		reviewRating: new Rating({ ratingValue: 4, bestRating: 5, worstRating: 1 }),
		reviewBody: 'Excellent product with great build quality. Minor issues with the manual.',
		datePublished: '2025-03-15',
		name: 'Great quality, minor documentation issues',
		itemReviewed: new Thing({ name: 'Acme Wireless Headphones' }),
	});
	results.set('Review', {
		type: 'Review',
		phpScript: 'src/generate-review.php',
		json: JsonLdGenerator.schemaToJson(reviewStandalone),
	});

	// ===================================================================
	// 39. VacationRental — matches src/generate-vacationrental.php
	// ===================================================================
	const vacationRental = new VacationRental({
		name: 'Seaside Villa Retreat',
		identifier: 'villa-seaside-42',
		image: [
			'https://example.com/villa-front.jpg',
			'https://example.com/villa-pool.jpg',
			'https://example.com/villa-interior.jpg',
		],
		latitude: 36.7783,
		longitude: -119.4179,
		containsPlace: new Accommodation({
			occupancy: new QuantitativeValue({ value: 8 }),
			numberOfBedrooms: 4,
			numberOfBathroomsTotal: 3,
			floorSize: new QuantitativeValue({ value: 250, unitCode: 'MTK' }),
		}),
		address: new PostalAddress({
			streetAddress: '123 Ocean Boulevard',
			addressLocality: 'Malibu',
			addressRegion: 'CA',
			postalCode: '90265',
			addressCountry: 'US',
		}),
		aggregateRating: new AggregateRating({ ratingValue: 4.9, bestRating: 5, worstRating: 1, reviewCount: 87 }),
		checkinTime: '15:00',
		checkoutTime: '11:00',
		datePublished: '2025-06-01',
		description: 'A stunning oceanfront villa with private pool and panoramic sea views.',
		review: [
			new Review({
				author: 'Traveler Kate',
				reviewRating: new Rating({ ratingValue: 5, bestRating: 5, worstRating: 1 }),
				reviewBody: 'Absolutely perfect. The views are incredible.',
			}),
		],
	});
	results.set('VacationRental', {
		type: 'VacationRental',
		phpScript: 'src/generate-vacationrental.php',
		json: JsonLdGenerator.schemaToJson(vacationRental),
	});

	// ===================================================================
	// 40. @graph — matches src/generate-graph.php
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

	const graphBreadcrumbs = new BreadcrumbList({ itemListElement: [
		new ListItem({ position: 1, name: 'Home', item: 'https://example.com/' }),
		new ListItem({ position: 2, name: 'Blog', item: 'https://example.com/blog/' }),
		new ListItem({ position: 3, name: 'Understanding JSON-LD', item: 'https://example.com/blog/jsonld-guide' }),
	] });

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
