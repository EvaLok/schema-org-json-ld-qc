# Session 213 — Quality Check: property_depth on JobPosting + Course

## Summary

Routine validation cycle with no PHP source changes in upstream package (d3ebbe9→b3d81fb, infra/docs/Rust tools only). All validation green. Quality check: property_depth on JobPosting (12/15, 80%) and Course (10/10, 100%).

## What Happened

- Package updated from d3ebbe9 to b3d81fb — no PHP source changes (only workflows, docs, Rust tools, state files)
- Full validation suite: 400 unit tests (2060 assertions) PASS, 73/73 E2E (0 errors, 45 warnings — all known false positives), 73/73 TS parity match
- No input-from-eva issues
- No new QC requests from main repo
- Audit issues [#341](https://github.com/EvaLok/schema-org-json-ld-audit/issues/341) and [#350](https://github.com/EvaLok/schema-org-json-ld-audit/issues/350) already processed in previous sessions (both N/A — target main orchestrator)
- Quality check rotation: property_depth (after building_block_integration in session 559)
  - **JobPosting** (15 properties, last property_depth 183 sessions ago): 12/15 exercised (80%). Missing: educationRequirements, experienceRequirements, experienceInPlaceOfEducation — all Google beta union-type fields with dedicated building-block tests. Above 70% threshold.
  - **Course** (10 properties, last property_depth 270 sessions ago): 10/10 combined coverage (100%). Generate script 8/10, unit test 9/10, combined 10/10.

## Current State

- Unit tests: 400 (2060 assertions) — PASS
- E2E: 73/73 — PASS (45 warnings, all false positives)
- TS parity: 73/73 match — PASS
- Coverage: 92/92 types (full coverage)
- Package: b3d81fb
- Quality checks backlog: empty
- In-flight agents: 0

## Next Steps

- Continue quality check rotation (next: minimal_data_audit)
- Prioritize complex types (10+ properties) not recently audited
- Monitor upstream for PHP source changes
