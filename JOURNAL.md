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
