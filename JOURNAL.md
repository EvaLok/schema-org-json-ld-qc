# QC Orchestrator Journal

## 2026-02-25 — Bootstrap Session (Issue #4)

### Library Status

The library (`evabee/schema-org-json-ld` at commit `c98da7c`) contains 65 PHP files in `src/v1/Schema/`, covering a wide range of schema.org types. The API pattern is clean: schema types extend `TypedSchema` with public constructor-promoted properties, and `JsonLdGenerator::SchemaToJson()` serializes them to JSON-LD. Enum types (like `ItemAvailability`, `OfferItemCondition`, `EventStatusType`, `DayOfWeek`) use PHP 8.1 backed enums with full schema.org URLs as values. A `PROPERTY_MAP` constant is supported for renaming PHP properties to different JSON-LD keys.

### Validation Findings

Built PHPUnit tests for 6 core rich result types: Article, FAQPage, Product, Recipe, LocalBusiness, Event. All 23 tests pass (142 assertions). The JSON-LD output is structurally correct for all tested types.

One minor observation: PHP's `json_encode` converts `float` values like `299.00` to integer `299` in JSON. This is not a bug — it's valid JSON and schema.org accepts both forms — but tests should use `assertEquals` instead of `assertSame` for float price comparisons.

### Google Rich Results Test — Infrastructure Failure

Attempted to validate FAQPage JSON-LD against Google Rich Results Test using Playwright + headless Chromium. The page loads correctly, the CodeMirror editor accepts the HTML code via JavaScript evaluation (`cmEl.CodeMirror.setValue(code)`), and the "test code" button becomes enabled. However, clicking the button triggers an alertdialog: **"Something went wrong — Log in and try again"**.

This happened consistently across retries. The page also has a reCAPTCHA iframe. This is an infrastructure limitation, not a package bug.

**Root causes to investigate:**
1. Google may require authentication (Google account login) for the code snippet testing feature
2. reCAPTCHA may be blocking headless browser submissions
3. The Rich Results Test may have recently changed to require auth

**Possible alternatives to explore:**
- Google's Rich Results API (if one exists) — might allow programmatic validation without browser automation
- Schema.org's own validator at validator.schema.org — may be more automation-friendly
- Use `https://validator.schema.org/` instead — it's specifically designed for structured data validation
- Consider a hybrid approach: PHPUnit for structural validation + manual Rich Results Test for periodic spot-checks

This is a significant finding. E2E validation against Google is the gold standard, but automating it from a headless browser in CI may not be feasible. Will raise with Eva if the problem persists next cycle.

### Decisions Made

1. **Started with 6 types**: Article, FAQPage, Product, Recipe, LocalBusiness, Event — these are the most common Google Rich Result types
2. **Usage scripts in `src/`**: One PHP script per type that demonstrates realistic usage. These serve as both fixtures and documentation.
3. **Test coverage strategy**: Each type gets tests for minimal construction, full construction with all fields, nested type handling, null field omission, and context handling
4. **CodeMirror interaction**: Discovered you must use `cmEl.CodeMirror.setValue()` to set content — the textarea is hidden behind overlays

### Open Questions

- Is there a programmatic API for Google Rich Results validation that doesn't require browser automation?
- Should we validate against Schema.org's validator instead (or in addition)?
- What's the right balance between PHPUnit structural tests and E2E browser tests?

### Cross-Repo Observations

No issues on the main repo's `qc-outbound` label, no `qc-inbound` acknowledgments. This is expected — it's the first session and the main orchestrator may not be running yet. No communication threads to track.

## 2026-02-25 — Expansion Session (Issue #6)

### Validation Findings

Expanded test coverage from 17 to 28 schema types. All 93 tests pass with 550 assertions. No package bugs found — every type tested produces structurally correct JSON-LD. The library's design is consistent: constructor-promoted properties, null omission, nested type serialization, and enum value handling all work correctly across all tested types.

Specific observations:
- **BlogPosting** correctly inherits from Article and overrides `@type` to `BlogPosting`
- **MathSolver** uses array `@type: ["MathSolver", "LearningResource"]` — correct per Google's spec
- **SolveMathAction** correctly uses `PROPERTY_MAP` to rename `mathExpressionInput` to `mathExpression-input` (hyphenated key)
- **MobileApplication** and **WebApplication** correctly inherit from SoftwareApplication and override `@type`
- **VacationRental** handles deep nesting well (VacationRental > Accommodation > QuantitativeValue)
- Float-to-int JSON encoding continues to be a pattern: `QuantitativeValue(value: 250.0)` serializes as `250` in JSON. Not a bug — tests should use `assertEquals` for these cases.

### E2E Validation Alternatives

Installed `structured-data-testing-tool` (npm) and created `scripts/validate-jsonld.js` for local validation. Results:
- Tool correctly validates JSON-LD is parseable for BlogPosting, Organization, Person, VacationRental
- Tool incorrectly fails on MathSolver because it doesn't handle array `@type` well — this is a tool limitation
- Tool only checks that structured data exists; it doesn't validate against Google's specific Rich Results requirements
- Verdict: useful as a sanity check in CI, but not a replacement for Google Rich Results Test

Issue #5 (question-for-eva) has no response yet. E2E validation against Google remains blocked.

### Uncovered Types Assessment

The 39 remaining uncovered types are mostly supporting/nested types:
- **Enums**: DayOfWeek, EventStatusType, ItemAvailability, OfferItemCondition — tested indirectly via parent types
- **Nested types**: Offer, Rating, PostalAddress, AggregateRating, ContactPoint, Brand — extensively tested as children of top-level types
- **Supporting types**: Answer, Question, ListItem, ImageObject — tested as part of Quiz, QAPage, ItemList, etc.
- **Remaining top-level types to consider**: None obvious — the library covers standard Rich Result types well

### Cross-Repo Observations

Still no activity on the main repo. No `qc-outbound` issues, no `qc-inbound` acknowledgments. The main orchestrator may not be running yet. Issue #5 has no response from Eva.

### Decisions Made

1. **Prioritized top-level rich result types**: BlogPosting, MathSolver, VacationRental, Quiz, EmployerAggregateRating, ItemList — these are the types Google explicitly supports for Rich Results
2. **Added standalone entity types**: Organization, Person, Review — these appear both as nested types and as standalone rich results
3. **Added application subtypes**: MobileApplication, WebApplication — these test the inheritance hierarchy
4. **Installed structured-data-testing-tool**: Provides basic local validation without needing Google's service

## 2026-02-25 — E2E Validation Session (Issue #7)

### Adobe structured-data-validator: Working

The `@adobe/structured-data-validator` v1.6.0 with `@marbec/web-auto-extractor` v2.2.1 is now fully operational as our E2E validation pipeline. This replaces the failed Google Rich Results Test browser approach. The pipeline is:

1. PHP generate script -> JSON-LD output
2. Wrap in HTML with `<script type="application/ld+json">`
3. Extract with `@marbec/web-auto-extractor`
4. Validate with `@adobe/structured-data-validator` against Google requirements + schema.org spec
5. Save results to `results/`

Key observation: the validator fetches `https://schema.org/version/latest/schemaorg-all-https.jsonld` on every run for schema.org spec validation. This is a ~2MB download. Could consider caching it for faster CI runs.

### First Real Validation Failure: Review

**Review fails E2E validation** with 2 errors:
- Required attribute `itemReviewed` is missing
- Required attribute `itemReviewed.name` is missing

The library's `Review` class (at commit `9d13ef3`) has these constructor parameters: `author`, `reviewRating`, `reviewBody`, `datePublished`, `name` — but **no `itemReviewed` property**. Google's structured data spec requires `itemReviewed` with a `name` for standalone Review rich results.

This is a genuine library limitation. Reviews work fine as nested types (inside Product, Movie, etc.) because the parent provides context, but standalone `Review` output fails Google validation. This is exactly the kind of issue the QC process exists to find.

### Package API Change: Offer Constructor

The package update (`c98da7c` -> `9d13ef3`) changed the `Offer` constructor to require `itemCondition` (OfferItemCondition enum) and `availability` (ItemAvailability enum) as mandatory parameters. Previously these may have been optional. This is a breaking change for consumer code that was constructing Offers with only url/priceCurrency/price.

This caught me by surprise when creating new generate scripts — a real-world consumer would face the same issue after `composer update`. The breaking change is reasonable (Google requires these fields for valid Offer structured data), but it should be documented in a changelog.

### Validator Coverage Observations

The Adobe validator has dedicated type handlers for a specific subset of schema.org types:
- Product, JobPosting, Recipe, BreadcrumbList, VideoObject, Organization, Person, Review
- Offer, OfferShippingDetails, AggregateRating, Rating, Brand, ListItem, DefinedRegion
- ImageObject, QuantitativeValue, ShippingDeliveryTime
- HowToStep, HowToSection, HowToDirection, HowToTip
- Plus a global schema.org validator that checks any type against the spec

Types without dedicated handlers still get schema.org spec validation (property names, types), but don't get Google-specific Rich Results validation. This means our "pass" for types like Course, Dataset, Quiz, etc. means they're schema.org-valid but we can't confirm they'd produce Google Rich Results.

### Warning Patterns

Most warnings are about optional Google-recommended fields:
- `worstRating` — appears across many types with ratings. Google recommends it but doesn't require it.
- Recipe HowToStep fields (itemListElement, image, name, url, video) — each step gets 5 warnings, multiplied by 6-7 steps = 30+ warnings. These are per-step recommendations.
- Product has the most diverse warnings: aggregateRating, review, audience, color, gtin, etc.

These are all genuine recommendations from Google but not validation failures. The library correctly omits them when the user doesn't provide them (null handling works correctly).

### Cross-Repo Communication

Still no activity from the main repo orchestrator. Planning to open the first `qc-outbound` issue for the Review `itemReviewed` finding. This will be the first real test of the cross-repo communication protocol.

### Decisions Made

1. **Created TypeScript validation script** (`scripts/validate.ts`) using Bun — replaces the old JS script using structured-data-testing-tool
2. **Complete generate script coverage** — all 28 covered types now have generate scripts
3. **Treat warnings as advisory** — they represent Google recommendations, not failures. Track them in state but don't report as failures.
4. **Report Review failure as qc-outbound** — this is a real issue that needs fixing in the library

## 2026-02-25 — Package Update Validation Session (Issue #9)

### Package Changes (9d13ef3 -> efdce91)

The main orchestrator shipped significant improvements in cycle 17:
- **New Clip class**: `name`, `startOffset`, `url`, optional `endOffset` — for VideoObject key moments (Google's rich results for Clip markup)
- **Product updated**: Added `aggregateRating` (AggregateRating) and `review` (Review|Review[]) properties
- **VideoObject updated**: Added `hasPart` (Clip[]) and `ineligibleRegion` properties

All changes are backward-compatible — existing tests passed without modification. The main orchestrator appears to be addressing quality findings (Product and VideoObject were among our higher-warning types). This is good signal that the QC feedback loop is working at the library level, even though the cross-repo issue protocol hasn't been tested yet (QC-REPORT #8 still unacknowledged).

### Validation Results

After enriching our generate scripts with the new properties:
- **Product warnings**: 13 -> 11 (aggregateRating and review warnings resolved)
- **VideoObject warnings**: 5 -> 1 (hasPart, expires, interactionStatistic, regionsAllowed all resolved; only `publication` remains)
- **Clip**: Clean pass, 0 errors, 0 warnings
- **Overall**: 28/29 pass, 61 warnings (down from 67). Only Review still fails.

### Cross-Repo Observations

QC-REPORT #8 (Review missing itemReviewed) has been open for ~1.5 hours with no acknowledgment from the main repo. The main orchestrator IS active (pushed efdce91 and opened issue #113). This may mean:
1. The orchestrator doesn't yet have the polling logic to discover QC issues
2. It discovered the issue but hasn't created a qc-inbound acknowledgment yet
3. It's prioritizing other work first

Not yet at the staleness threshold (3 cycles / ~9 hours). Will continue monitoring.

### Test Coverage Growth

- **Unit tests**: 93 -> 103 (10 new tests for Clip, Product aggregateRating/review, VideoObject hasPart/interactionStatistic/ineligibleRegion)
- **Assertions**: 550 -> 597
- **Types covered**: 28 -> 29 (added Clip)

### Observations on Library Quality

The library continues to produce structurally correct JSON-LD. The Offer constructor change (requiring itemCondition and availability) from the previous session is now well-understood — it aligns with Google's requirements. The new Product review property accepts both single Review and Review[] — a flexible API that handles both common cases.

The remaining uncovered types are mostly supporting/nested types that don't have standalone rich result types in Google's spec. The Clip type is an exception — it's new and used specifically for VideoObject key moments, which IS a Google rich result feature.

## 2026-02-25 — Fix Verification and Tooling Session (Issue #11)

### Cross-Repo Protocol: First Successful Round Trip

The cross-repo QC communication protocol has now completed its first full cycle:
1. **Session #7**: Found Review missing `itemReviewed` — opened QC-REPORT #8
2. **Session #9**: Still no acknowledgment — continued monitoring
3. **Between sessions**: Main orchestrator discovered our report, opened QC-ACK #115 on their repo, implemented fix (added `itemReviewed` to Review + new `Thing` class)
4. **This session**: Discovered QC-ACK #115, updated package (efdce91 -> cf9de6d), verified fix, closed QC-REPORT #8

**Total round-trip time**: ~2 hours from report to verified fix. This is excellent for an asynchronous cross-repo protocol. The main orchestrator's acknowledgment issue #115 correctly references our issue URL, and the fix is clean — adding optional `itemReviewed` of type `TypedSchema` is the right approach.

### Package Changes (efdce91 -> cf9de6d)

- **Review updated**: Added optional `itemReviewed` property (null|TypedSchema)
- **New Thing class**: Minimal schema type with just `name` — useful for `itemReviewed` when you don't need a full type-specific object

### Validation Results

**29/29 types pass E2E validation**, 0 errors, 61 warnings. This is the first time all covered types pass. Review now produces valid standalone rich result JSON-LD with `itemReviewed`.

Unit tests: 105 (up from 103), 606 assertions (up from 597). Added tests for `itemReviewed` with Thing and Person variants.

### Sandbox Permission Analysis (Eva Request #10)

Eva asked me to build tools to avoid recurring permission failures. Identified these blocked constructs:
- `$()` command substitution — always blocked
- `${}` parameter expansion — always blocked
- `printenv`/`env` — requires approval
- `chmod` — requires approval
- Complex pipe chains — sometimes blocked

Created 5 helper scripts in `tools/`:
- `session-init.sh` — environment capture + opening comment
- `gh-post.sh` — GitHub operations reading body from files
- `validate-all.sh` — combined unit + E2E test runner
- `poll-repos.sh` — cross-repo status check
- `discover-types.sh` — uncovered type discovery

Also created `CLAUDE.md` with sandbox-safe patterns and updated AGENTS.md and STARTUP_CHECKLIST.md.

**Key insight**: The tools themselves may require approval to run (`bash tools/script.sh` gets blocked too). The real value is dual: (1) when the sandbox becomes less restrictive, these scripts will save time, and (2) they document the operational patterns for future sessions. The CLAUDE.md file is the more immediately useful artifact — it teaches the agent which constructs to avoid.

### Decisions Made

1. **Close QC-REPORT #8**: Fix verified at cf9de6d. Full cross-repo round trip complete.
2. **Tools over workflow changes**: Created helper scripts rather than proposing workflow changes. The scripts solve the problem without requiring Eva to merge PRs.
3. **CLAUDE.md created**: This is the primary mechanism for teaching the agent sandbox-safe patterns.

### Open Questions

- Will `bash tools/script.sh` work in future sessions or will the sandbox continue blocking it?
- Should we propose adding `allowedTools` or `permissionPrompts` to the Claude Code settings to pre-approve common operations?
- The main orchestrator's acknowledgment latency (~2 hours) — is this acceptable or should we expect faster turnaround?

## 2026-02-25 — QC-REQUEST Validation Session (Issue #20)

### First Cross-Repo QC-REQUEST Processed

Processed our first inbound QC-REQUEST (#121 on the main repo). The main orchestrator explicitly requested validation of recent quality fixes. This is the other direction of the cross-repo protocol — where they initiate, we respond. The round trip:

1. Main orchestrator opens QC-REQUEST #121 asking us to validate PRs #111, #112, #117, #119
2. We open QC-ACK #21 on our repo acknowledging the request
3. We validate all items (most already validated in previous sessions)
4. We post results on our issue and close it

The protocol works smoothly in both directions now. Request -> Ack -> Validate -> Report -> Close.

### Package Changes (cf9de6d -> b32760f)

Three new types added since last session:
- **EventAttendanceModeEnumeration**: PHP 8.1 enum with Offline/Online/Mixed values. Clean design matching the existing enum pattern.
- **HowToSection**: Groups HowToSteps with a name. Used for Recipe multi-section instructions (e.g., "Make the Batter", "Make the Frosting", "Assemble").
- **VirtualLocation**: URL + optional name for online event locations. Event.location now accepts `Place|VirtualLocation|array`.

Also: Offer.itemCondition made optional (was mandatory), CourseInstance.courseMode made optional. Both are good changes — Google recommends but doesn't strictly require itemCondition, and courseMode is genuinely optional.

### Validation Findings

All 30 E2E validations pass with 0 errors. No new failures, no regressions. The HowToSection pattern produces a lot of per-step warnings (59 for the Tiramisu recipe — 11 steps x ~5 warnings each plus 3 recipe-level warnings). These are the same optional HowToStep fields (image, name, url, video, itemListElement) that we see on individual steps. The validator treats each step within a section the same way.

### Observations

1. **Helper tools work**: `bash tools/git-commit.sh` and `bash tools/session-init.sh` weren't needed this session (I committed directly), but the pattern of writing message files first continues to work well.
2. **Validation pipeline is stable**: The Adobe structured-data-validator has been consistent across 4 sessions now. No flaky results, no false positives.
3. **Test growth**: 105 -> 113 unit tests, 606 -> 645 assertions. Coverage is comprehensive for all top-level rich result types.
4. **Warning count increase**: 61 -> 120 (entirely from the new Recipe-sections generate script's 59 per-step warnings). Not concerning.

### Decisions Made

1. **Created separate generate script for HowToSection**: Rather than modifying the existing generate-recipe.php, created generate-recipe-sections.php to test the alternative pattern. This gives us both flat (HowToStep[]) and grouped (HowToSection[]) recipe instruction patterns.
2. **Updated Event generate script in-place**: The hybrid event pattern (Place + VirtualLocation) is more realistic than the previous physical-only version, so it's a better demonstration.
3. **Closed QC-ACK immediately**: All items were already validated in previous sessions; the new tests confirm the remaining items. No need to keep the thread open.

## 2026-02-25 — New Types Validation Session (Issue #22)

### Package Changes (b32760f -> 9cca8ec)

The main orchestrator's cycle 20 shipped LocalBusiness subtypes and Organization enhancements:
- **FoodEstablishment**: Extends LocalBusiness, adds `acceptsReservations` (bool|string). Clean design — the bool/string union allows both `true` and a reservation URL.
- **Restaurant**: Extends FoodEstablishment, just overrides `@type`. Minimal class, correct pattern.
- **Store**: Extends LocalBusiness, just overrides `@type`. Same pattern as Restaurant.
- **LocalBusiness updated**: Added `department` (LocalBusiness|LocalBusiness[]), `logo`, `email`, `sameAs`. The department property is interesting — it allows modeling a store with sub-departments (e.g., MegaMart with a Pharmacy department).
- **Organization updated**: Added `numberOfEmployees` (QuantitativeValue), plus business identifier properties: `taxID`, `vatID`, `naics`, `duns`, `leiCode`, `iso6523Code`, `globalLocationNumber`. These are Google-recommended properties for Organization rich results.

### Validation Findings

All 33/33 E2E validations pass with 0 errors. No regressions from the package update. The new types produce structurally correct JSON-LD. Warnings for FoodEstablishment, Restaurant, and Store are the same `bestRating`/`worstRating` pattern we see on LocalBusiness — advisory only.

The inheritance hierarchy works correctly:
- Restaurant correctly outputs `@type: "Restaurant"` (not "FoodEstablishment" or "LocalBusiness")
- Store correctly outputs `@type: "Store"` (not "LocalBusiness")
- All LocalBusiness properties are available on subtypes through PHP constructor forwarding

### OpeningHoursSpecification API Observation

Discovered that `OpeningHoursSpecification` takes a single `DayOfWeek` enum, not an array. This means you need 7 separate specs for a business open every day (one per day). This is a valid design — Google's spec shows individual day-of-week values too — but it's verbose. A consumer might expect array support for grouping days with the same hours. Not a bug, just an API observation worth noting.

### Test Growth

- **Unit tests**: 113 -> 133 (20 new tests across 5 test classes)
- **Assertions**: 645 -> 745
- **Types covered**: 30 -> 33 (added FoodEstablishment, Restaurant, Store)
- **E2E validations**: 30 -> 33 (added 3 new generate scripts)

### Cross-Repo Observations

No activity from the main repo this cycle. No open QC requests, no new acknowledgments. The main orchestrator's cycle 20 focused on LocalBusiness subtypes and Organization properties — both shipped without issues. The QC pipeline remains clean with 0 open reports.

### Coverage Assessment

With 33 top-level types covered, we've now validated every Google Rich Result type that the library supports. The remaining 43 uncovered types in the state file are all supporting/nested types (enums, nested objects, utility types). None of them produce standalone rich results — they're only used as properties of top-level types and are already tested indirectly through parent type tests.

## 2026-02-25 — PropertyValue and Incremental Improvements Session (Issue #23)

### Package Changes (9cca8ec -> 69187be)

The main orchestrator's cycle 21 shipped three changes:
- **PropertyValue** class: Simple `name`/`value` string pair for structured identifiers. Used by JobPosting.identifier for employer-internal job IDs. This is a Google-recommended field.
- **ImageObject.creator** widened to `null|Organization|Person`: Previously only accepted Organization. Now supports Person attribution (e.g., photographer credits). This aligns with schema.org's spec where `creator` accepts both types.
- **JobPosting.identifier** property: Optional `PropertyValue` for employer job IDs. Google recommends this for job posting rich results.

All changes are backward-compatible — existing tests passed without modification.

### Validation Findings

All 33/33 E2E validations pass with 0 errors. Updated generate-jobposting.php to include `identifier`, resolving one of three warnings (now 2 warnings: `applicantLocationRequirements` and `jobLocationType` remain — these are location-specific optional fields). Total warnings down from 130 to 129.

### Copilot Dispatch

Dispatched issue #24 for Copilot to create PropertyValue unit tests and add a JobPosting identifier test. This is the first Copilot dispatch in several cycles — previous sessions focused on direct validation and orchestrator tooling. The test spec is detailed: 2 methods in PropertyValueTest.php (basic construction + nested in JobPosting) and 1 method added to JobPostingTest.php.

### Operational Observations

The helper tools (`tools/session-init.sh`, `tools/git-commit.sh`) continue to work reliably. The startup sequence is now efficient: ~2 minutes from issue trigger to full validation run complete.

The main orchestrator is now working on supporting types (PropertyValue, ImageObject enhancements) rather than new top-level types. This suggests the library is reaching maturity for the core Rich Result types. The QC role is shifting from "find missing required fields" to "validate incremental improvements and edge cases."

### Cross-Repo Observations

No open QC-REQUEST issues from the main repo. No pending acknowledgments. The communication channel is quiet — both orchestrators appear to be in steady-state rather than fire-fighting mode. This is healthy.

### Decisions Made

1. **Direct-pushed generate script fix**: The identifier addition to generate-jobposting.php was 3 lines — well within the direct-push threshold.
2. **Delegated test writing**: PropertyValue tests + JobPosting identifier test dispatched to Copilot as issue #24.
3. **Skipped ImageObject standalone coverage**: ImageObject.creator is exercised indirectly through types that embed ImageObject. A standalone test would test the same serialization path. Not high priority.

## 2026-02-25 — QC-REQUEST and PR Review Session (Issue #26)

### Copilot Agent Quality: First PR Review

Reviewed and merged Copilot's first completed PR (#25, from issue #24). The code quality was good — clean tests, correct assertions, proper namespace and imports. The only issue was the lockfile pointing to an older package version (Copilot branched from master which was behind our local state). This is expected and easily resolved.

Observation: Copilot's turnaround was fast (~20 minutes from dispatch to PR). The detailed issue spec with exact code samples paid off — the output matched the spec closely. This confirms the pattern: **detailed specs with code examples produce high-quality Copilot output**.

### AggregateOffer: Union Type Validation

The library's `Product.offers` now accepts `array|AggregateOffer` — a PHP union type. Tested this ad-hoc before dispatching formal coverage. The JSON-LD output is structurally correct: when `AggregateOffer` is used, `offers` is a single object (not an array), which is the correct serialization for `AggregateOffer` per schema.org spec.

Interesting observation: the `priceValidUntil` warning only appears when using individual `Offer` objects, not with `AggregateOffer`. This makes sense — `priceValidUntil` is an Offer-level field, and `AggregateOffer` represents a price range across multiple offers without per-offer details.

### Cross-Repo Protocol: Second QC-REQUEST

Processed QC-REQUEST #141 (the second inbound request). The main orchestrator's requests are getting more structured — this one included specific PRs, a summary of changes, and explicit concerns to validate. The protocol is maturing. Both directions (report -> fix -> verify and request -> ack -> validate) now work smoothly.

### Warning Count Trend

Session-over-session warning trend: 67 -> 61 -> 120 -> 130 -> 129 -> 128. The big jump at 120 was recipe sections (HowToStep per-step warnings). Since then we've been steadily reducing warnings through better generate scripts (adding recommended optional fields). The remaining 128 warnings are mostly:
- Recipe HowToStep per-step warnings (~91 across both recipes)
- Product optional fields (~10)
- LocalBusiness/Restaurant/Store/FoodEstablishment bestRating/worstRating (~13)
- Various other optional fields (~14)

These are all genuine Google recommendations for optional fields, not validation failures. The library correctly omits them when the user doesn't provide values.

### Decisions Made

1. **Merged PR #25 without CI**: No CI workflow exists for PR branches. Verified tests pass locally (136 tests, 756 assertions). This is acceptable for now but worth noting — a CI workflow for PRs would be valuable.
2. **Ad-hoc AggregateOffer test**: Tested Product+AggregateOffer with a temp file (not committed) to answer QC-REQUEST #141 immediately. Delegated formal coverage to Copilot #28.
3. **Direct-pushed priceValidUntil**: 1-line addition to generate-product.php, well within threshold.

### Open Questions

- Should we propose a CI workflow for PRs? This would catch regressions before merge and reduce manual verification burden.
- The library appears to be approaching feature completeness for Google Rich Result types. Should we shift focus to edge case testing (empty arrays, null nesting, multiple nested types)?
- Is there value in testing AggregateOffer with `offerCount: 0` or `lowPrice: 0.00`? These are edge cases that real consumers might encounter.

## 2026-02-26 — Package Update and Dispatch Session (Issue #31)

### Library Maturity: Commerce Features

The library is now deep into commerce/merchant structured data. The latest additions — MerchantReturnPolicy (with 6 supporting enums), MemberProgram (loyalty programs), and ShippingService (with conditions, rates, transit times) — represent Google's merchant-listings rich result requirements. These are sophisticated, deeply nested types with many interdependencies.

Observation: The main orchestrator is building these types correctly on first attempt. MerchantReturnPolicy has 19 constructor parameters covering the full Google spec: return windows, fees, methods, refund types, label sources, and separate handling for customer remorse vs. defective items, plus seasonal overrides. This is impressive API design quality.

### Copilot Agent Performance

PR #29 (AggregateOffer) was clean — matched the spec closely, tests pass, code quality good. This is the third consecutive Copilot PR that required no revisions. The pattern is confirmed: **detailed issue specs with exact code samples and explicit constructor signatures produce reliable Copilot output on first attempt**.

The Copilot sandbox firewall continues to block `api.github.com` — the agent can't run `composer update` in its environment. This means:
1. The composer.lock in PRs always points to whatever commit was on the branch when Copilot started
2. We have to update the lockfile ourselves after merge (or just run `composer update` on master)

This is a known limitation, not a blocker. But it means we can never fully trust the PR's lockfile — always re-run `composer install`/`update` locally before testing.

### Direct QC Request Pattern

Eva opened issue #30 directly on our repo (not via the cross-repo protocol) to request validation of Cycles 23-24. This is more efficient than the standard protocol for cases where the human operator wants to trigger validation explicitly. The cross-repo protocol is designed for orchestrator-to-orchestrator communication; direct issues from Eva are a legitimate shortcut.

### Coverage Strategy Reassessment

With 34 E2E validations and 140 unit tests, we've covered all the "top-level" rich result types. The uncovered list (now 60 types) consists of:
- **Enums** (13): DayOfWeek, EventStatusType, ItemAvailability, MerchantReturnEnumeration, etc. — tested indirectly via parent types
- **Nested/supporting types** (47): AggregateRating, PostalAddress, ContactPoint, Offer, etc. — tested extensively as children of top-level types

The new merchant types (MerchantReturnPolicy, MemberProgram, ShippingService) are interesting because they're both standalone schema.org types AND nested within Organization. They'll need both standalone generate scripts (for E2E validation) and Organization integration (for the composite pattern).

### Observations on Warning Stability

Warning count: 128 -> 140 (the +12 is from the new Product+AggregateOffer script, which doesn't include review/aggregateRating, so it gets more optional-field warnings). The baseline warning set is very stable across sessions — same patterns repeating (HowToStep per-step warnings, Product optional fields, LocalBusiness bestRating/worstRating). No new warning categories discovered this session.

### Decisions Made

1. **Dispatched 2 Copilot tasks simultaneously**: MerchantReturnPolicy (#32) and MemberProgram+ShippingService (#33). This is at the concurrency limit (2 sessions max). Combined MemberProgram and ShippingService into one task because they're simpler types that naturally pair with Organization integration.
2. **Did not update Organization generate script**: This should include the new merchant properties, but it's >5 lines of changes, so it goes through Copilot. The Organization integration tests in both dispatched tasks will cover the unit test side.
3. **Merged PR without CI**: Same pattern as PR #25. No CI workflow for PRs. Local verification is the gate.

## 2026-02-26 — PR Review and Merchant Validation Session (Issue #36)

### Copilot Agent Performance: Concurrent PR Quality

Reviewed and merged two Copilot PRs that were dispatched simultaneously last session. Both completed in under 15 minutes (PR #34 in ~9 min, PR #35 in ~14 min). Code quality was consistently good across both:

- PR #34 (MerchantReturnPolicy): 6 focused unit tests covering the full enum surface (5 return/refund enums), seasonal override nesting, and MonetaryAmount for return shipping fees. Closely matched the spec.
- PR #35 (MemberProgram + ShippingService): 7 unit tests plus 2 Organization integration tests. Correctly adapted to the actual constructor signatures (e.g., using `addressRegion: []` for DefinedRegion, `FulfillmentTypeEnumeration` from `v1\Enum\` not `v1\Schema\`). Used `class_exists()` guards — unnecessary since we always run latest, but shows defensive coding.

**Pattern confirmed across 4 consecutive Copilot PRs**: detailed issue specs with exact code samples, explicit constructor signatures, and clear test method expectations produce reliable first-attempt output. Zero revisions requested on any of the 4 PRs (#25, #29, #34, #35).

### OrganizationTest Merge Conflict

Both PRs modified `OrganizationTest.php` (different import sets + different test methods). The conflict was trivial — just import statement overlap. Resolved by combining both import blocks. This is a predictable consequence of dispatching two tasks that both add Organization integration tests.

**Lesson**: When dispatching multiple tasks that modify the same file, consider sequencing them (dispatch second after first merges) or explicitly noting in the issue spec which imports and methods the file already has. For this case, the conflict was so simple it didn't justify the pipeline delay.

### Validation Pipeline: 37 Types, Zero Failures

The validation pipeline now covers 37 distinct E2E validations across all Google Rich Result types supported by the library. All pass with 0 errors. The 141 warnings are all advisory (optional fields). This is a comprehensive regression suite.

The new merchant types validate cleanly:
- MerchantReturnPolicy: 0 warnings — impressive for a complex type with 19 parameters
- MemberProgram: 0 warnings — the tier/benefit structure serializes correctly
- ShippingService: 1 warning — optional addressRegion/postalCode on DefinedRegion

### Cross-Repo Protocol: Third Successful Inbound

Processed QC-REQUEST #153 — the third inbound request. The protocol is now well-established in both directions. Response time from request to validated ACK: same session (~4 minutes). The main orchestrator is proactively requesting validation of new features as they ship. This is the healthy steady-state pattern.

### Coverage Maturity Assessment

With 37 top-level types covered (156 unit tests, 867 assertions, 37 E2E validations), the consumer project now exercises every significant schema type in the library. The 57 uncovered types are all nested/supporting types (enums, value objects, nested structs) that don't produce standalone rich results — they're tested indirectly through parent types.

The QC role is shifting from "expand coverage of new types" to:
1. **Regression guard**: Catch regressions as the library evolves
2. **Edge case depth**: Test boundary values, empty arrays, null nesting
3. **Cross-type interactions**: Test composite scenarios (e.g., Organization with all merchant properties together)
4. **Warning reduction**: Progressively add optional fields to generate scripts to reduce advisory warnings

### Decisions Made

1. **Merged PR #34 first, then #35**: Since both modified OrganizationTest.php, merged sequentially and resolved the conflict manually.
2. **Closed Eva's #30 and QC-ACK #37 in same session**: All validation complete, no reason to leave threads open.
3. **No new Copilot dispatches this session**: No new types to cover, pipeline empty, steady state.

## 2026-02-26 — Steady State and v1.0.0 Readiness Session (Issue #38)

### Library Maturity: v1.0.0 Recommended

The main orchestrator has formally recommended a v1.0.0 release (issue #154 on the library repo, tagged `question-for-eva`). Package update this session (bca8081 -> 36ff5d1) contained only state updates and README refreshes — no new schema types, no API changes. The library has been in maintenance mode for several cycles now.

As the QC gatekeeper, I created a comprehensive readiness assessment (issue #39, tagged `question-for-eva`) providing my independent evaluation. Key conclusions:
- All 37 top-level types pass E2E validation with 0 errors
- Only 1 bug found across 12 QC sessions (Review missing itemReviewed — fixed within 2 hours)
- 141 warnings are all advisory optional fields from Google, not validation failures
- The library API is consistent, well-designed, and handles edge cases correctly (null omission, nested types, enums, array vs single object, inheritance, PROPERTY_MAP)

### Experiment Observations: Cross-Vendor Orchestration

This QC project is now 12 sessions old, making it a reasonable point to reflect on the experiment:

1. **Claude orchestrating Copilot works well**: 4/4 Copilot PRs merged without revisions. The key is detailed issue specs with exact code samples and constructor signatures. Copilot is reliable for well-specified test-writing tasks.

2. **Cross-repo protocol is robust**: 1 QC-REPORT round trip (2-hour turnaround), 3 QC-REQUESTs processed, 1 direct Eva request. The issue-based communication protocol works for async orchestrator-to-orchestrator communication without write access to each other's repos.

3. **The QC pipeline compounds**: Starting from zero, the consumer project now has 156 unit tests (867 assertions) and 37 E2E validations. Each session builds on previous work. The marginal cost of validation drops as the suite grows.

4. **Steady state is healthy**: With comprehensive coverage established, sessions are increasingly about regression guarding rather than expanding coverage. This is the expected maturity curve.

5. **Session efficiency**: From issue trigger to full validation complete in ~3 minutes. The tooling investment (tools/, scripts/, CLAUDE.md) has paid off.

### Warning Stability

Warning count has been stable at 141 for 2 sessions. The breakdown is well-understood:
- 91: Recipe HowToStep per-step optional fields
- 22: Product optional fields
- 13: LocalBusiness family bestRating/worstRating
- 15: Various other optional fields

These represent the gap between "valid structured data" and "maximally complete structured data." The library correctly omits these fields when the consumer doesn't provide them. Reducing these would require the library to add more optional parameters to its constructors — a design trade-off, not a bug.

### Decisions Made

1. **Created v1.0.0 readiness assessment**: This is the highest-value orchestrator work for this session. The assessment provides Eva with an independent QC perspective on the release decision.
2. **No Copilot dispatches**: No new types to cover. Edge case testing could be valuable but isn't blocking v1.0.0.
3. **No code changes**: This was a pure validation and assessment session.

### Open Questions

- Will Eva proceed with v1.0.0? The QC assessment supports it.
- Should post-v1.0.0 focus shift to edge cases, or to monitoring for regressions in point releases?
- Is the 3-hour cycle interval appropriate for steady state, or could it be extended to reduce resource usage?

## 2026-02-26 — QC-REQUEST Processing and Copilot Dispatch (Issue #40)

### Library Activity Resumes: Product Enhancements

After two quiet sessions (steady state, no new types), the library shipped a significant Product/merchant listing update. Commit 870ece5 adds 5 new classes and expands Product from ~15 to 24 constructor parameters. The main orchestrator filed QC-REQUEST #165 proactively requesting validation — the fourth inbound request.

The new types cover Google's merchant-listings rich result requirements:
- **SizeSpecification**: Simple name/sizeSystem/sizeGroup triplet for product sizing
- **ProductGroup**: Variant grouping — this is a standalone rich result type, not just a supporting type
- **PeopleAudience**: Audience demographics (gender, age range) — used by Product.audience
- **Certification**: Product certifications with issuing organization — used by Product.hasCertification
- **UnitPriceSpecification**: Unit pricing with referenceQuantity and membership tier support — used by Offer.priceSpecification

### Backward Compatibility: Perfect

The package update introduced zero regressions. All 156 unit tests and 37 E2E validations pass without modification. This is remarkable given the scope of changes (24-parameter Product constructor, 5 new classes). The library team's approach of making all new properties optional with null defaults preserves backward compatibility perfectly.

### Copilot Dispatch Pattern: Mature

This is the 5th dispatch cycle (tasks #42 and #43). The issue spec pattern is now well-established:
1. Full constructor signatures with types and defaults
2. Complete code samples for every test method
3. Explicit import lists for every file
4. Clear notes about edge cases (union types, single vs array variants)

Both tasks were dispatched simultaneously (at the 2-session concurrency limit). Task #42 updates existing files (ProductTest.php, generate-product.php) while #43 creates entirely new files (ProductGroupTest, UnitPriceSpecificationTest, generate-product-group.php). They have a potential conflict on ProductTest.php (task #43 includes a testProductWithIsVariantOf that references ProductGroup), but this was intentionally put in the ProductGroupTest file to avoid the conflict.

### Warning Reduction Opportunity

The Product E2E warnings (currently 10 for Product, 12 for Product+AggregateOffer) include several fields that the library now supports: color, material, pattern, size, gtin, audience, hasCertification, isVariantOf. Once Copilot updates generate-product.php with these properties, we should see a significant warning reduction — potentially from 10 to ~2 (subjectOf and possibly mpn/gtin on all offers).

### Decisions Made

1. **Two parallel Copilot tasks**: Split by file conflict risk. Task #42 modifies ProductTest.php; task #43 creates ProductGroupTest.php (keeping isVariantOf test in that file, not ProductTest).
2. **Acknowledged QC-REQUEST immediately**: Opened QC-ACK #41 with preliminary findings (no regressions) even though full validation awaits Copilot results.
3. **No direct code changes this session**: All implementation delegated to Copilot, consistent with the orchestrator role.

### Open Questions

- Will the updated generate-product.php reduce Product warnings to near-zero?
- Does ProductGroup need its own E2E validation? (It's a standalone type, not just a supporting type)
- How does UnitPriceSpecification interact with the Adobe validator? It may not have a dedicated handler.

## 2026-02-26 — PR Review and @graph Discovery Session (Issue #45)

### @graph Support: Significant New Feature

The library (at 4494ee6) now supports composing multiple schemas into a single JSON-LD block via `JsonLdGenerator::SchemasToJson()` and `SchemasToObject()`. The output uses the `@graph` pattern — a single `@context` at the root with an array of schema objects. This is the standard pattern for pages that need multiple structured data types (e.g., Article + BreadcrumbList + Organization on a blog page).

Tested ad-hoc: the `@context` correctly appears only at the root, not on individual schemas in the graph. The API is clean — variadic `TypedSchema` params. This is a feature worth testing thoroughly because the context-stripping behavior is critical for valid `@graph` output.

### Copilot Dispatch Failure: First Observed

Issue #42 was assigned to Copilot but never triggered `copilot_work_started`. The timeline shows `assigned` events but no work events. This is the first dispatch failure in 7 attempts (85% success rate). No obvious cause — the issue spec was identical in structure to successful dispatches.

**Mitigation**: Close the failed issue, re-dispatch as a fresh issue (#46). The issue spec was already written, so the re-dispatch cost was minimal (copy the body). This is an expected failure mode in autonomous systems — retry is cheap.

**Pattern to watch**: If dispatch failures become more common, consider adding a check in the next cycle for `copilot_work_started` within N minutes of dispatch, with automatic re-dispatch if missing.

### ProductGroup E2E: Answered

ProductGroup E2E validation produces 24 warnings — these are per-variant Product optional fields (duplicated for each variant in the group). The validator treats each variant Product individually, so a ProductGroup with 2 variants gets ~12 Product warnings per variant. This is expected behavior, not an issue.

UnitPriceSpecification has no dedicated Adobe validator handler, but passes schema.org spec validation (property names and types are correct). This means we can confirm structural correctness but not Google-specific Rich Results requirements for unit pricing.

### Copilot Success Rate

After 7 dispatches: 6 successful PRs merged (all without revisions), 1 failed to start. The successful PRs have all been high quality — matching specs closely, correct assertions, clean code. The pattern of detailed issue specs with exact code samples continues to produce reliable results.

### Decisions Made

1. **Re-dispatched #42 as #46**: Minimal cost, same spec. Better than investigating why Copilot failed (opaque system).
2. **Dispatched @graph test spec (#47)**: The new API feature deserves dedicated test coverage. 6 test methods covering basic usage, context stripping, object API, single schema, order preservation, and nested types.
3. **Merged PR #44 manually**: Resolved composer.lock conflict by keeping our version (then ran `composer update` locally). This is the standard pattern for Copilot PRs.

### Open Questions

- Why did Copilot fail to start on issue #42? Is there a pattern to watch for?
- Should we add a "Copilot health check" to the startup checklist (verify in-flight sessions have started)?
- The library is now at 301 internal tests and has @graph support — is this heading toward a v1.1.0?

## 2026-02-26 — Warning Reduction Focus (Issue #50)

### Library API Feedback

Eva asked how we'd prefer to handle optional fields from a consumer perspective. Performed a thorough audit of which E2E warnings can be fixed (properties exist in library but our generate scripts don't use them) vs which need new library features. The breakdown:

- **~100 warnings fixable by us**: Just need to enrich generate scripts with `name`/`url`/`image` on HowToSteps, `bestRating`/`worstRating` on AggregateRating/Rating, and optional Product properties on AggregateOffer variant.
- **~58 warnings unfixable**: Library lacks HowToStep.video, HowToStep.itemListElement, Product.subjectOf, VideoObject.publication, Offer.gtin.

The library's named-parameter API with null defaults is genuinely excellent from a consumer perspective. No builders needed, no setter chains — just `new Type(required: val, optional: val)`. This scales cleanly and is idiomatic PHP 8.1+.

### Warning Reduction Strategy

Dispatched two targeted Copilot tasks to reduce 158 → ~62 warnings (60% reduction):
1. **#51**: Recipe HowToStep enrichment (name, url, image on every step)
2. **#52**: AggregateRating/Rating bestRating/worstRating across all types + Product AggregateOffer enrichment

These are mechanical enrichment tasks — adding property values to constructor calls in generate scripts and updating unit test assertions. This is the ideal Copilot task profile: clear, specific, low ambiguity.

### Steady State Observations

The system is in a very healthy steady state:
- 39/39 E2E pass, 0 errors across all types
- 185 unit tests with 987 assertions
- Package 5173eb8 has no new schema types since last session — main orchestrator is in v1.0.0 prep mode
- All cross-repo threads closed, no pending requests

The main repo's CHANGELOG.md creation signals v1.0.0 is approaching. Our QC coverage is comprehensive — every supported rich result type is validated. The remaining work is polish (reducing warnings) and waiting for the missing optional properties.

### Decisions Made

1. **Focused on warning reduction over new type coverage**: With no new schema types and no QC-REQUESTs, reducing warnings is the highest-value work. The warnings represent genuine gaps in our demonstration of the library's capabilities.
2. **Responded to Eva on #39 with detailed API design feedback**: This is an opportunity to influence the library's API for missing properties. Provided code snippets showing exactly how we'd want to use them.
3. **Did not pursue ProductGroup enrichment yet**: At 24 warnings, it's the third-largest contributor, but the variant Product enrichment is more complex (each variant needs material, pattern, mpn, etc.). Will tackle after the current batch merges.

## 2026-02-26 — Session #56: Warning Reduction Harvest (Issue #56)

### Warning Reduction Results

The two Copilot tasks from session #50 delivered excellent results. PRs #53 and #54 merged cleanly, reducing E2E warnings from **158 to 75** — a 53% reduction. The breakdown:

- **12 types went to zero warnings**: FoodEstablishment, LocalBusiness, Restaurant, Store, Course, SoftwareApplication, WebApplication (all via bestRating/worstRating addition)
- **Recipe**: 32→13 (HowToStep enrichment with name/url/image eliminated ~19 warnings)
- **Recipe Sections**: 59→24 (same pattern — 11 steps enriched)
- **Product AggregateOffer**: 12→4 (review, audience, color, material, etc. added)
- **VacationRental**: 4→1, **MobileApplication**: 2→1, **Movie**: 2→1

### Remaining Warning Analysis

Mapped every remaining warning (75 total) to root cause:

**Library limitations (~40 warnings)**: Properties that genuinely don't exist in the library classes:
- HowToStep.video (17 warnings) — each Recipe step lacks video support
- HowToStep.itemListElement (17 warnings) — sub-directions not supported
- VideoObject.publication (1) — no BroadcastEvent class
- Product.subjectOf (2-3) — not in Product class
- Movie/SoftwareApplication/VacationRental.datePublished (3) — not in their classes

**Fixable on our side (~31 warnings)**: Properties exist but we haven't set them:
- ProductGroup variants (24) — missing brand, mpn, material, pattern, etc. on individual Products
- Recipe.video at recipe level (2) — Recipe.video property exists, just unused
- Recipe Sections nutrition.calories (1) — missing nutrition info
- JobPosting location fields (2) — applicantLocationRequirements/jobLocationType exist
- Certification fields (2) — certificationIdentification exists

### QC-REPORT for Missing Properties

Per Eva's directive, filed [#57](https://github.com/EvaLok/schema-org-json-ld-qc/issues/57) as a formal QC-REPORT requesting the library add the missing properties. Prioritised HowToStep.video/itemListElement (P1, 34 warnings), datePublished on 3 types (P2, 3 warnings), and VideoObject.publication/Product.subjectOf (P3, 4 warnings).

### Agent Quality Observations

Copilot PRs #53 and #54 were both high quality:
- **No conflicts between PRs**: Despite both touching `src/generate-recipe.php`, the merge-ort strategy resolved them cleanly. This is because the changes were in non-overlapping regions (PR #53 touched the AggregateRating, PR #54 touched the HowToStep objects).
- **Consistent pattern following**: Both PRs followed the established patterns in the codebase — same coding style, same assertion patterns in tests.
- **Accurate scope**: Neither PR exceeded its spec. This validates the detailed issue spec approach.
- **Fast turnaround**: Both completed in 8-10 minutes.

### Decisions Made

1. **Filed QC-REPORT on own repo**: Eva approved dispatching the missing property request. Used `qc-outbound` label per protocol — the main orchestrator will discover it by polling.
2. **Dispatched ProductGroup enrichment now**: With the Recipe/rating batch done, ProductGroup (24 warnings) is the biggest remaining target. Also bundled miscellaneous fixes (Recipe video, JobPosting, Certification) into a second task.
3. **isVariantOf/subjectOf left unfixed**: isVariantOf would create circular references (Product→ProductGroup→Products), and subjectOf isn't in the library. Accepted these 4 warnings as irreducible without library changes.
