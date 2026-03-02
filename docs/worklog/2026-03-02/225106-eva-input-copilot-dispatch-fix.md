# Session #144: Eva input — Copilot dispatch gap fix

## Summary

Handled input-from-eva [#143](https://github.com/EvaLok/schema-org-json-ld-qc/issues/143): identified three root causes preventing Copilot dispatch, fixed STARTUP_CHECKLIST.md and AGENTS.md, and dispatched 2 coverage expansion tasks.

## What Happened

Eva opened [#143](https://github.com/EvaLok/schema-org-json-ld-qc/issues/143) asking why Copilot agents aren't being dispatched. The last dispatch was [#85](https://github.com/EvaLok/schema-org-json-ld-qc/issues/85)/[PR #86](https://github.com/EvaLok/schema-org-json-ld-qc/pull/86) on 2026-02-27 — 3 days and 22 sessions ago.

**Root causes identified:**

1. **Idle check gap**: Step 4's idle criteria (a-d) didn't consider uncovered types. With 47 types untested but no package changes or cross-repo activity, sessions would idle.

2. **Priority language suppressed dispatch**: Step 13 said "Prioritise reviews and validation over new test development." In steady state (no reviews pending, all validation passing), this left nothing to do and no fallback to coverage expansion.

3. **No explicit dispatch step**: Discovery (step 6) found uncovered types but nothing bridged that to writing issue specs and dispatching Copilot.

**Fixes applied:**

- STARTUP_CHECKLIST.md: Added idle check condition (e) for uncovered types, replaced step 13 with mandatory Copilot dispatch step, added step 14 for remaining planning
- AGENTS.md: Added "Coverage expansion tasks" section with batching guidance

**Dispatched:**
- [#145](https://github.com/EvaLok/schema-org-json-ld-qc/issues/145): Place, PostalAddress, GeoCoordinates, ContactPoint, ImageObject
- [#146](https://github.com/EvaLok/schema-org-json-ld-qc/issues/146): Question, Answer, Offer, BroadcastEvent, AggregateOffer

Package updated c8b1c6f -> c4b4d88 (no PHP/TS source changes — main orchestrator docs/state only).

## Self-modifications

- **STARTUP_CHECKLIST.md**: Added idle check condition (e) for uncovered types with available agent slots. Replaced step 13 "Plan session work" with explicit "Dispatch Copilot for coverage expansion" step. Added step 14 for remaining planning. Rationale: the checklist lacked any mechanism to convert discovered coverage gaps into dispatched work.
- **AGENTS.md**: Added "Coverage expansion tasks" section documenting how to batch types, what files to create, and reference patterns. Rationale: coding agents need guidance on the project structure and conventions for coverage expansion tasks.

## Current State

- 188 unit tests (1133 assertions)
- 39/39 E2E (0 errors, 15 warnings — all false positives)
- 39/39 TS parity match
- Package: c4b4d88
- 2 Copilot agents in-flight (#145, #146)
- 47 uncovered types (10 being addressed by dispatched tasks)

## Next Steps

1. Review Copilot PRs from #145 and #146 when ready (next cycle)
2. Run CI verification on PRs
3. Merge if clean, dispatch next batch of coverage expansion
4. Continue until all high-value types have standalone tests
