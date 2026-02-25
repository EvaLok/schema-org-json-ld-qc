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
