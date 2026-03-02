# Session #137 — TS build validation and parity expansion

**Date**: 2026-03-02
**Issue**: [#137](https://github.com/EvaLok/schema-org-json-ld-qc/issues/137)
**Package commit**: a7a5131
**Type**: qc-request-ts-build-validation

## What happened

Processed QC-REQUEST [#331](https://github.com/EvaLok/schema-org-json-ld/issues/331) — "Comprehensive TypeScript build validation before npm publish." This is a multi-cycle effort; this session completed the first two deliverables.

### 1. Built-package validation (COMPLETE)

Created `tools/npm-build-check.sh` — validates the npm package artifact end-to-end:
- **Build**: tsup produces ESM (52.97 KB), CJS (57.80 KB), DTS (57.02 KB)
- **npm pack**: Tarball created (51,226 bytes)
- **ESM import**: 100 exports validated, JSON-LD generation works
- **CJS require**: 100 exports validated, JSON-LD generation works
- **Type declarations**: `index.d.ts` (1,418 lines) + `index.d.cts` present
- **Consumer test**: 10 representative types generate valid JSON-LD from installed package

### 2. Parity expansion: 25 → 39 types (COMPLETE)

Expanded `tools/ts-parity-check.ts` with 14 new types that already had PHP generate scripts:
- Dataset, DiscussionForumPosting, EmployerAggregateRating, ItemList, JobPosting, MemberProgram, MerchantReturnPolicy, Organization, Person, ProfilePage, Quiz, Review, VacationRental, @graph

Two mismatches found and fixed:
- **ItemList**: ListItem `url` (position 4) vs `item` (position 3) — BreadcrumbList uses `item`, ItemList uses `url`
- **Organization**: ContactPoint uses positional params `(telephone, email, contactType)`, not options object

Final: 39/39 match, 0 E2E errors, 15 warnings (all false positives).

### 3. Audit #49: self-scoped denominators (ACCEPTED)

Audit correctly identified that previous parity metrics used self-scoped denominators (25/25 = 100%) when actual coverage was 25/86 = 29%. Now reporting absolute denominators: **39/86 = 45%**.

## Validation results

- PHP: 188 unit tests (1133 assertions), 39/39 E2E, 0 errors, 15 warnings
- TS: 39/39 parity match, 0 E2E errors, 15 warnings (all false positives)
- Build: ESM+CJS+DTS all pass from packed tarball
- Class inventory: 86/86 PHP=TS

## Remaining work (future cycles)

- 47 building-block types need standalone parity (39/86 → 86/86)
- Consumer-perspective testing (ts-consumer/ project — dispatch to Copilot)

## Self-modifications

- Created `tools/npm-build-check.sh` (new build validation tool)
- Expanded `tools/ts-parity-check.ts` (14 new types, 19 new schema imports, 3 new enum imports)
- Updated `state.json` (parity results, build validation, warning baselines, processed issues)
