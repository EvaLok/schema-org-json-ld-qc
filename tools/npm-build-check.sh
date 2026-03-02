#!/usr/bin/env bash
# Validate the built npm package artifact.
# Builds the TS package, packs it, installs in a temp dir, and tests ESM/CJS imports.

set -euo pipefail

VENDOR_DIR="$(cd "$(dirname "$0")/.." && pwd)/vendor/evabee/schema-org-json-ld"
TEMP_DIR="$(mktemp -d)"

COPIED_DIST=0

cleanup() {
	rm -rf "$TEMP_DIR"
	# Clean up tarball if left behind
	rm -f "$VENDOR_DIR"/*.tgz
	# Clean up copied dist
	if [ "$COPIED_DIST" = "1" ]; then
		rm -rf "$VENDOR_DIR/dist"
	fi
	# Clean up fallback dist from tsup
	FALLBACK_DIST="$(dirname "$VENDOR_DIR")/dist"
	rm -rf "$FALLBACK_DIST"
}
trap cleanup EXIT

echo "=== Build Validation ==="
echo "Vendor dir: $VENDOR_DIR"
echo "Temp dir: $TEMP_DIR"
echo ""

# Build the package
echo "--- Building package ---"
bun run --cwd "$VENDOR_DIR" build 2>&1
echo ""

# tsup outputs to ../dist relative to ts/ config; copy if needed
FALLBACK_DIST="$(dirname "$VENDOR_DIR")/dist"
if [ ! -d "$VENDOR_DIR/dist" ] && [ -d "$FALLBACK_DIST" ]; then
	echo "Copying dist from fallback location..."
	cp -r "$FALLBACK_DIST" "$VENDOR_DIR/dist"
	COPIED_DIST=1
fi

# Check dist exists
if [ ! -d "$VENDOR_DIR/dist" ]; then
	echo "FAIL: dist/ directory does not exist after build."
	exit 1
fi

echo "--- Checking dist/ contents ---"
ls -la "$VENDOR_DIR/dist/"
echo ""

# Pack the tarball
echo "--- Packing tarball ---"
cd "$VENDOR_DIR"
TARBALL_NAME=$(npm pack 2>&1 | tail -1)
echo "Tarball: $TARBALL_NAME"

TARBALL_PATH="$VENDOR_DIR/$TARBALL_NAME"
if [ ! -f "$TARBALL_PATH" ]; then
	echo "FAIL: Tarball not found at $TARBALL_PATH"
	exit 1
fi
echo "PASS: Tarball created ($(stat -c%s "$TARBALL_PATH") bytes)"
echo ""

# Install in temp directory
echo "--- Installing in temp project ---"
cd "$TEMP_DIR"
npm init -y > /dev/null 2>&1
npm install "$TARBALL_PATH" > /dev/null 2>&1
echo "PASS: Installed from tarball"
echo ""

# Check file structure
echo "--- Checking installed package files ---"
ls -la "$TEMP_DIR/node_modules/@evabee/schema-org-json-ld/dist/"
echo ""

# Test ESM import
echo "--- Testing ESM import ---"
cat > "$TEMP_DIR/test-esm.mjs" << 'ESMEOF'
import * as pkg from '@evabee/schema-org-json-ld';
const { JsonLdGenerator, Product, Brand, Offer, ItemAvailability, Article, BreadcrumbList } = pkg;

// Check exports exist
const required = ['JsonLdGenerator', 'Product', 'Brand', 'Article', 'BreadcrumbList', 'ItemAvailability'];
for (const name of required) {
	if (!(name in pkg)) {
		throw new Error('ESM missing export: ' + name);
	}
}
console.log('ESM exports: OK (' + Object.keys(pkg).length + ' exports)');

// Generate JSON-LD
const product = new Product({
	name: 'Test Widget',
	image: ['https://example.com/widget.jpg'],
	description: 'A test product',
	sku: 'TEST-001',
	offers: [new Offer({
		url: 'https://example.com/widget',
		priceCurrency: 'USD',
		price: 29.99,
		availability: ItemAvailability.InStock,
	})],
	brand: new Brand('TestBrand'),
});
const json = JsonLdGenerator.schemaToJson(product);
const parsed = JSON.parse(json);
if (parsed['@context'] !== 'https://schema.org/' || parsed['@type'] !== 'Product' || parsed.name !== 'Test Widget') {
	throw new Error('ESM JSON-LD generation failed: ' + json);
}
console.log('ESM JSON-LD generation: OK');
ESMEOF

node "$TEMP_DIR/test-esm.mjs" 2>&1
echo ""

# Test CJS import
echo "--- Testing CJS require ---"
cat > "$TEMP_DIR/test-cjs.cjs" << 'CJSEOF'
const pkg = require('@evabee/schema-org-json-ld');
const { JsonLdGenerator, Product, Brand, Offer, ItemAvailability } = pkg;

const required = ['JsonLdGenerator', 'Product', 'Brand', 'Article', 'BreadcrumbList', 'ItemAvailability'];
for (const name of required) {
	if (!(name in pkg)) {
		throw new Error(`CJS missing export: ${name}`);
	}
}
console.log('CJS exports: OK (' + Object.keys(pkg).length + ' exports)');

const product = new Product({
	name: 'Test Widget CJS',
	image: ['https://example.com/widget.jpg'],
	description: 'A test product via CJS',
	sku: 'TEST-002',
	offers: [new Offer({
		url: 'https://example.com/widget',
		priceCurrency: 'USD',
		price: 39.99,
		availability: ItemAvailability.InStock,
	})],
	brand: new Brand('TestBrand'),
});
const json = JsonLdGenerator.schemaToJson(product);
const parsed = JSON.parse(json);
if (parsed['@context'] !== 'https://schema.org/' || parsed['@type'] !== 'Product' || parsed.name !== 'Test Widget CJS') {
	throw new Error('CJS JSON-LD generation failed: ' + json);
}
console.log('CJS JSON-LD generation: OK');
CJSEOF

node "$TEMP_DIR/test-cjs.cjs" 2>&1
echo ""

# Test TypeScript declarations
echo "--- Checking TypeScript declarations ---"
if [ -f "$TEMP_DIR/node_modules/@evabee/schema-org-json-ld/dist/index.d.ts" ]; then
	DECL_LINES=$(wc -l < "$TEMP_DIR/node_modules/@evabee/schema-org-json-ld/dist/index.d.ts")
	echo "PASS: index.d.ts exists ($DECL_LINES lines)"
else
	echo "FAIL: index.d.ts not found"
	exit 1
fi

if [ -f "$TEMP_DIR/node_modules/@evabee/schema-org-json-ld/dist/index.d.cts" ]; then
	echo "PASS: index.d.cts exists"
else
	echo "WARN: index.d.cts not found (CJS declaration)"
fi
echo ""

# Test multiple schema types from installed package
echo "--- Testing 10 representative schema types ---"
cat > "$TEMP_DIR/test-types.mjs" << 'TYPESEOF'
import {
	JsonLdGenerator, Product, Article, BreadcrumbList, Event, FAQPage,
	Recipe, VideoObject, LocalBusiness, Course, SoftwareApplication,
	Person, Organization, Brand, Offer, ListItem, Question, Answer,
	PostalAddress, Place, AggregateRating, ItemAvailability, NutritionInformation,
	HowToStep, CourseInstance, OpeningHoursSpecification, DayOfWeek,
	EventAttendanceModeEnumeration, EventStatusType, OfferItemCondition,
} from '@evabee/schema-org-json-ld';

const tests = [];

// 1. Article
const article = new Article({
	headline: 'Test Article',
	author: new Person({ name: 'Author' }),
	datePublished: '2025-01-01',
	publisher: new Organization({ name: 'Publisher', logo: 'https://example.com/logo.png' }),
	image: ['https://example.com/img.jpg'],
});
tests.push({ type: 'Article', json: JsonLdGenerator.schemaToJson(article) });

// 2. BreadcrumbList
const bc = new BreadcrumbList([
	new ListItem(1, 'Home', 'https://example.com/'),
	new ListItem(2, 'Products'),
]);
tests.push({ type: 'BreadcrumbList', json: JsonLdGenerator.schemaToJson(bc) });

// 3. Event
const event = new Event({
	name: 'Test Event',
	startDate: '2025-07-01T19:00',
	location: [new Place('Venue', new PostalAddress({ streetAddress: '123 Main St', addressLocality: 'City', addressRegion: 'ST', postalCode: '12345', addressCountry: 'US' }))],
	offers: new Offer({ url: 'https://example.com/tickets', priceCurrency: 'USD', price: 50, availability: ItemAvailability.InStock }),
});
tests.push({ type: 'Event', json: JsonLdGenerator.schemaToJson(event) });

// 4. FAQPage
const faq = new FAQPage([
	new Question({ name: 'Q1?', acceptedAnswer: new Answer('A1.') }),
]);
tests.push({ type: 'FAQPage', json: JsonLdGenerator.schemaToJson(faq) });

// 5. Recipe
const recipe = new Recipe({
	name: 'Test Recipe',
	image: ['https://example.com/recipe.jpg'],
	author: new Person({ name: 'Chef' }),
	datePublished: '2025-01-01',
	description: 'A test recipe.',
	prepTime: 'PT15M',
	cookTime: 'PT30M',
	totalTime: 'PT45M',
	recipeYield: '4 servings',
	recipeCategory: 'Main course',
	recipeCuisine: 'American',
	nutrition: new NutritionInformation('300 calories'),
	recipeIngredient: ['Ingredient 1', 'Ingredient 2'],
	recipeInstructions: [new HowToStep('Step 1'), new HowToStep('Step 2')],
});
tests.push({ type: 'Recipe', json: JsonLdGenerator.schemaToJson(recipe) });

// 6. VideoObject
const video = new VideoObject({
	name: 'Test Video',
	description: 'A test video.',
	thumbnailUrl: ['https://example.com/thumb.jpg'],
	uploadDate: '2025-01-01',
	contentUrl: 'https://example.com/video.mp4',
});
tests.push({ type: 'VideoObject', json: JsonLdGenerator.schemaToJson(video) });

// 7. LocalBusiness
const lb = new LocalBusiness({
	name: 'Test Business',
	image: 'https://example.com/biz.jpg',
	address: new PostalAddress({ streetAddress: '456 Oak Ave', addressLocality: 'Town', addressRegion: 'ST', postalCode: '67890', addressCountry: 'US' }),
	telephone: '+1-555-0100',
});
tests.push({ type: 'LocalBusiness', json: JsonLdGenerator.schemaToJson(lb) });

// 8. Course
const course = new Course({
	name: 'Test Course',
	description: 'A test course.',
	provider: new Organization({ name: 'University', sameAs: 'https://example.com/uni' }),
});
tests.push({ type: 'Course', json: JsonLdGenerator.schemaToJson(course) });

// 9. SoftwareApplication
const app = new SoftwareApplication({
	name: 'Test App',
	offers: new Offer({ url: 'https://example.com/app', priceCurrency: 'USD', price: 0, availability: ItemAvailability.InStock }),
	aggregateRating: new AggregateRating(4.5, 5, 1, 100),
	applicationCategory: 'UtilityApplication',
	operatingSystem: 'Android, iOS',
});
tests.push({ type: 'SoftwareApplication', json: JsonLdGenerator.schemaToJson(app) });

// 10. Product
const product = new Product({
	name: 'Test Product',
	image: ['https://example.com/prod.jpg'],
	description: 'A test product.',
	sku: 'TP-001',
	offers: [new Offer({ url: 'https://example.com/prod', priceCurrency: 'USD', price: 99.99, availability: ItemAvailability.InStock })],
	brand: new Brand('TestBrand'),
});
tests.push({ type: 'Product', json: JsonLdGenerator.schemaToJson(product) });

let pass = 0;
for (const t of tests) {
	const parsed = JSON.parse(t.json);
	if (parsed['@context'] === 'https://schema.org/' && parsed['@type']) {
		console.log(`  ${t.type}: OK (@type=${parsed['@type']})`);
		pass++;
	} else {
		console.log(`  ${t.type}: FAIL`);
	}
}
console.log(`Result: ${pass}/${tests.length} types generated valid JSON-LD`);
if (pass !== tests.length) process.exit(1);
TYPESEOF

node "$TEMP_DIR/test-types.mjs" 2>&1
echo ""

echo "=== Build Validation Complete ==="
echo "All checks passed."
