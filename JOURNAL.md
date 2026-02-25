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
