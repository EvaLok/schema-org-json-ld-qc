# E2E Validation Session — 2026-02-25T12:03:00Z

## Issue: #7

## What happened

Third orchestrator cycle. Major milestone: implemented the Adobe structured-data-validator E2E pipeline and ran comprehensive validation across all 28 covered schema types. Found first real validation failure.

### Startup checklist results
- No input-from-eva issues (Eva responded to #5 — see below)
- No QC requests from main repo (qc-outbound)
- No QC acknowledgments from main repo (qc-inbound)
- No open PRs or in-flight agent sessions
- Package updated: `c98da7c` -> `9d13ef3` (new Google-recommended properties for Offer, MonetaryAmount, Review, Recipe, JobPosting)
- All 93 PHPUnit tests pass (550 assertions) — no regressions from package update

### Eva's response to #5

Eva responded to issue #5 (question-for-eva re: E2E approach) confirming the pivot from browser-based Google Rich Results Test to `@adobe/structured-data-validator`. The package was already added to `package.json` as a devDependency. Also confirmed Bun as runtime and TypeScript for all new code.

### Adobe structured-data-validator implementation

Created `scripts/validate.ts` — a TypeScript validation script that:
1. Runs PHP generate scripts to produce JSON-LD
2. Wraps JSON-LD in minimal HTML with `<script type="application/ld+json">`
3. Extracts structured data with `@marbec/web-auto-extractor`
4. Validates against Google requirements with `@adobe/structured-data-validator` v1.6.0
5. Saves results to `results/` with full details

Added `@marbec/web-auto-extractor` as a dev dependency (required companion for the Adobe validator).

### Generate scripts expansion

Created 13 new PHP generate scripts for types that had PHPUnit tests but no generate scripts:
- Review, Course, Movie, SoftwareApplication, Dataset, ProfilePage, DiscussionForumPosting, QAPage, Quiz, ItemList, EmployerAggregateRating, MobileApplication, WebApplication

Discovered that the Offer constructor changed in the package update — now requires `itemCondition` and `availability` as mandatory parameters. Fixed all new scripts to include these.

### Validation results — FIRST REAL FAILURE FOUND

**27/28 types pass, 1 fail:**

| Type | E2E Result | Warnings |
|------|-----------|----------|
| Article | PASS | 0 |
| BlogPosting | PASS | 0 |
| BreadcrumbList | PASS | 0 |
| Course | PASS | 1 |
| Dataset | PASS | 0 |
| DiscussionForumPosting | PASS | 0 |
| EmployerAggregateRating | PASS | 0 |
| Event | PASS | 0 |
| FAQPage | PASS | 0 |
| ItemList | PASS | 0 |
| JobPosting | PASS | 3 |
| LocalBusiness | PASS | 3 |
| MathSolver | PASS | 0 |
| MobileApplication | PASS | 2 |
| Movie | PASS | 2 |
| NewsArticle | PASS | 0 |
| Organization | PASS | 0 |
| Person | PASS | 0 |
| Product | PASS | 13 |
| ProfilePage | PASS | 0 |
| QAPage | PASS | 0 |
| Quiz | PASS | 0 |
| Recipe | PASS | 32 |
| **Review** | **FAIL** | 0 |
| SoftwareApplication | PASS | 1 |
| VacationRental | PASS | 4 |
| VideoObject | PASS | 5 |
| WebApplication | PASS | 1 |

**Review failure details:**
- ERROR: Required attribute "itemReviewed" is missing
- ERROR: Required attribute "itemReviewed.name" is missing
- Root cause: The library's `Review` class has no `itemReviewed` property. Google requires `itemReviewed` with `itemReviewed.name` for standalone Reviews.
- Note: Reviews used as nested types inside Product, Movie, etc. work fine because the parent provides context.

### Package API change detected

The package update (`c98da7c` -> `9d13ef3`) made `itemCondition` and `availability` required parameters in the Offer constructor. This is a breaking change for any consumer code that was passing only url/priceCurrency/price. Our existing generate scripts for Product and JobPosting already had these fields, but new scripts needed to be updated.

## Current state
- **Passing (unit)**: 28/28 types (93 tests, 550 assertions)
- **Passing (E2E)**: 27/28 types
- **Failing (E2E)**: Review (missing `itemReviewed` property)
- **Cross-repo**: No active threads yet. Planning to open qc-outbound for Review issue.

## Next steps for next session
1. Open `qc-outbound` issue for Review `itemReviewed` failure
2. Close issue #5 (question resolved by Eva)
3. Monitor for package updates that fix the Review issue
4. Consider reducing Recipe HowToStep warnings by enriching the generate script
5. Monitor main repo for new schema types
