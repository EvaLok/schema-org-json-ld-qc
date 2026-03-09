# Startup Checklist

Follow this checklist at the start of every orchestrator cycle.

## Quick start (using tools)

Run `bash tools/session-init.sh <issue-number>` to post the opening comment and capture env info.
Run `bash tools/poll-repos.sh` to check all cross-repo communication status.
Run `bash tools/discover-types.sh` to find uncovered types.
Run `bash tools/validate-all.sh` to run the full test suite.
Run `bun tools/ts-parity-check.ts` to run TypeScript parity validation.

## Full checklist

1. **Post opening comment** — `bash tools/session-init.sh <issue>` (captures timestamp, run ID).
2. **Check for `input-from-eva` issues** — these take priority over everything else.
3. **Recover context** — Read latest worklog entry and `state.json`. Also check `question-for-eva` issues on the main repo — if a shared question has been answered there, apply the answer locally and close the equivalent issue.
4. **Update package and steady-state check** — Run `composer update evabee/schema-org-json-ld` and note the commit hash. Then compare current state against last cycle. If ALL of the following are true, this is an idle cycle:
   - (a) No new package commits since last cycle (`composer update` shows no change), OR the new commits only modify non-source files (docs, worklogs, state files — no changes to PHP source classes). Check with: `gh api repos/EvaLok/schema-org-json-ld/compare/OLD_HASH...NEW_HASH --jq '.files[].filename'` and look for any `.php` files under source directories (post-Phase 0: `php/src/`, pre-Phase 0: `src/`). A resilient check: look for any changed `.php` file regardless of directory prefix.
   - (b) No open issues/PRs requiring action (no input-from-eva, no qc-outbound requests, no unreviewed Copilot PRs)
   - (c) No new QC reports or requests from the main repo
   - (d) No unprocessed audit recommendations
   - (e) No uncovered schema types with < 2 in-flight Copilot agents (if uncovered types exist AND agent slots are available, this is NOT idle — dispatch coverage expansion work)
   If idle: post a brief comment "No changes detected since session N, skipping", increment `consecutive_idle_cycles` in state.json, close the issue, and exit. Do NOT write a worklog entry, journal entry, or commit. If `consecutive_idle_cycles` exceeds 3, note in the closing comment that cron frequency reduction should be considered.
   If NOT idle: reset `consecutive_idle_cycles` to 0 and continue with the full checklist.
4b. **Proactive quality improvement** (per [audit #88](https://github.com/EvaLok/schema-org-json-ld-audit/issues/88)) — When coverage is at 100% and no urgent pending work exists after completing steps 5-14, perform ONE of the following (rotating across sessions):
   1. **Property depth check**: Pick a standalone type. Verify its parity test covers all non-trivial properties (not just constructor + toArray). If gaps found, dispatch a Copilot task to expand the test.
   2. **Minimal data audit**: Check whether any E2E test relies on minimal/empty data that wouldn't exercise optional property paths. If found, dispatch enrichment.
   3. **Building-block integration check**: Pick a building-block type and verify its integration within a parent type's E2E test exercises its key properties.
   **Type selection priority** (per [audit #114](https://github.com/EvaLok/schema-org-json-ld-audit/issues/114)) — Weight type selection by complexity:
   - **Prioritize** types with 10+ properties — these are most likely to have undiscovered coverage gaps.
   - **Deprioritize** types with fewer than 6 properties unless they haven't been checked in 10+ sessions.
   - Rotate through unchecked complex types before revisiting simple ones.
   **Minimum coverage threshold** (per [audit #114](https://github.com/EvaLok/schema-org-json-ld-audit/issues/114)) — If a quality check finds <70% property coverage, it is a **mandatory backlog entry**. Do not dismiss low coverage as "diminishing returns" — register it in `quality_checks.backlog` for dispatch evaluation.
   **Hierarchy-aware threshold** (per [audit #172](https://github.com/EvaLok/schema-org-json-ld-audit/issues/172)) — When a quality check finds missing properties, check whether the same properties are missing in parent/child types in the inheritance chain. If the same gap appears in 3+ types in a hierarchy, treat it as a single hierarchy-level finding that triggers **mandatory backlog entry** regardless of individual type scores. Dispatch targets the base class — fixes cascade to all descendants. Known inheritance chains: LocalBusiness->{FoodEstablishment->{Restaurant}, Store}, Article->{BlogPosting, NewsArticle}, SoftwareApplication->{MobileApplication, WebApplication}.
   **Mandatory follow-through** (per [audit #90](https://github.com/EvaLok/schema-org-json-ld-audit/issues/90)) — When a quality check identifies a gap:
   a. **Actionable this session**: Create a Copilot dispatch issue (proceed to step 13) to address the gap.
   b. **Requires upstream changes**: File a QC-REPORT or note it in `quality_checks.backlog` in state.json with the type, finding, and session number.
   c. **Log the finding**: Add an entry to `quality_checks.history` in state.json with: check type, target type, finding, action taken, session number.
   This converts idle time from repetitive green confirmation into incremental quality improvement. Track which check was performed in the session worklog.
4c. **Backlog dispatch threshold** (per [audit #92](https://github.com/EvaLok/schema-org-json-ld-audit/issues/92), clarified per [audit #94](https://github.com/EvaLok/schema-org-json-ld-audit/issues/94), enforced per [audit #145](https://github.com/EvaLok/schema-org-json-ld-audit/issues/145)) — If `quality_checks.backlog` contains 5+ items OR any item has been in the backlog for 5+ orchestrator sessions, batch-dispatch a Copilot enrichment task to address all actionable backlog items. **MANDATORY: If any backlog item exceeds the 5-session threshold, dispatch MUST happen this session. Deferral is not permitted.** Count actual orchestrator sessions (`orchestrator-run` issues), not issue numbers — non-session issues inflate the gap. After dispatch, move items from backlog to history with `status: dispatched` and reference the dispatch issue number. Update `quality_checks.last_dispatch_session` in state.json.
   **Constructor signatures in dispatch specs** — No longer required. The Copilot agent's setup steps now run `composer update evabee/schema-org-json-ld` before the firewall activates, so the agent always has the latest library and can inspect class constructors directly. (Previously needed per [audit #96](https://github.com/EvaLok/schema-org-json-ld-audit/issues/96) to work around firewall blocking `composer update` at runtime.)
5. **Poll repos** — `bash tools/poll-repos.sh` (checks qc-outbound, qc-inbound, input-from-eva, open PRs).
5a. **Check open QC-REPORTs for upstream fixes** — For each open `qc-outbound` issue on this repo, check whether the main repo has acknowledged it (look for `qc-inbound` issues on the main repo referencing ours). If an acknowledgment exists, check whether the fix has been merged and is in the latest package version. If so, re-validate the affected type and close the QC-REPORT with results. This closes the feedback loop — don't leave QC-REPORTs open after fixes are deployed.
5b. **Update long-running tracking issue bodies** — For QC-ACK and QC-REQUEST issues open >5 sessions with a DoD checklist, edit the issue body to reflect current metrics and check completed DoD items. Comments alone become buried — the body is what people read first. (Per [audit #76](https://github.com/EvaLok/schema-org-json-ld-audit/issues/76).)
6. **Discover new types** — `bash tools/discover-types.sh`. **Dual-language consistency**: compare PHP class count (`php/src/v1/`) against TS class count (`ts/src/`). If a new PHP class exists without a TS equivalent (or vice versa), file a `qc-outbound` issue flagging the parity gap — this is a regression, not just a coverage gap.
7. **Run validation suite** — `bash tools/validate-all.sh` (unit tests + E2E). Also run `bun tools/ts-parity-check.ts` for TypeScript parity validation (compares TS JSON-LD output against PHP baseline + Adobe validator). **Warning baseline check**: after validation, compare E2E warning counts against `warning_baselines` in state.json. If total warnings increase, investigate before updating — new warnings may indicate real regressions, not false positives. Only update baselines after confirming new warnings are false positives.
7a. **State.json metric verification (comprehensive + inventory sweep)** — After validation, verify ALL mutable fields in state.json match reality. This step covers both per-type entries AND aggregate summaries. (Per [audit #78](https://github.com/EvaLok/schema-org-json-ld-audit/issues/78) + [audit #80](https://github.com/EvaLok/schema-org-json-ld-audit/issues/80) + [audit #82](https://github.com/EvaLok/schema-org-json-ld-audit/issues/82).)
   - **Package**: `package.commit` and `package.last_updated` — must match `composer update` output.
   - **Per-type entries**: `last_tested` and `package_commit` for all 92 covered types — must match current date and commit.
   - **Test counts**: Verify unit test count (385), E2E pass count (73/73), TS parity count (73/73) match runner output.
   - **E2E aggregate**: `e2e_status.last_run`, `e2e_status.summary` (package commit reference), `warning_baselines.last_verified` — must reflect this cycle's run.
   - **TS aggregate**: `ts_validation.last_run`, `ts_validation.package_commit` — must reflect this cycle's run.
   - **Cross-repo entries**: Spot-check `cross_repo.outbound_issues[*].status` and `cross_repo.inbound_issues[*].note` against actual GitHub issue state. Closed issues must show `"status": "closed"`.
   - **Copilot metrics**: `copilot_metrics.last_updated` — update to current timestamp. Verify counts if dispatches occurred. **Dispatch log invariants** (per [audit #98](https://github.com/EvaLok/schema-org-json-ld-audit/issues/98)): `dispatch_log.length` must equal `total_dispatches`; merged entries must equal `total_prs_merged`; failed entries must match `failed_dispatches` array.
   - **Root fields**: `last_updated`, `operational_mode_note` (class counts) — verify against current reality.
   - **Field inventory sweep**: After all enumerated checks above, scan `field_inventory.fields` in state.json. For each field with `cadence: "every_session"`, verify `last_refreshed` equals the current session number. For other cadences, verify `last_refreshed` is consistent with when that event last occurred. Any field in state.json NOT listed in the inventory is itself a staleness risk — add it. Update `field_inventory.last_refreshed_session` to the current session number. Convention: any new mutable field added to state.json MUST include a corresponding `field_inventory` entry.
   - **Field inventory completeness check** (per [audit #85](https://github.com/EvaLok/schema-org-json-ld-audit/issues/85) + [audit #87](https://github.com/EvaLok/schema-org-json-ld-audit/issues/87)): Run `bun tools/check-field-inventory.ts` after ALL state.json modifications in this session. The tool programmatically enumerates all mutable leaf paths in state.json, normalizes wildcard patterns, and diffs against `field_inventory.fields`. Any gaps must be filled before closing the session. This replaces the LLM-driven manual comparison with a deterministic check.
8. **Check for npm publish** — If `@evabee/schema-org-json-ld` is published on npm, validate the built artifact: run `bun tools/npm-publish-check.ts` to verify ESM/CJS imports resolve, type declarations exist, and JSON-LD output from the installed package matches PHP baseline. Check with: `npm view @evabee/schema-org-json-ld version 2>/dev/null`. If not yet published, skip this step. Once published, run this check **every cycle** (not just on first detection) — verify the latest npm version matches master.
9. **Report new failures** — Use `bash tools/gh-post.sh create-issue <title> <body-file> qc-outbound`.
10. **Check audit repo** — Poll `EvaLok/schema-org-json-ld-audit` for `audit-outbound` issues (process recommendations). Evaluate, accept/reject, track in state file. For each processed recommendation, create an `audit-inbound` issue on this repo linking to the original audit-outbound issue URL (we cannot comment directly on the audit repo — no write access).
11. **Check false positive documentation** — When false positives are tracked in state.json, verify they are documented in the main repo's user-facing docs. If not, file a QC-REPORT recommending documentation.
12. **Housekeeping** — Explicit self-healing for stale issues, plus general cleanup:
    a. **Stale `orchestrator-run` issues**: Query `gh issue list --label orchestrator-run --state open`. Close any from previous sessions (not the current one) with a comment: "Stale — session completed but issue not closed. Superseded by session #N."
    b. **Stale `audit-inbound` issues**: Query open `audit-inbound` issues. For each, check whether the referenced audit recommendation appears in the `processed_audit_issues` list in state.json. If accepted and verified, close with a brief confirmation comment.
    c. **Orphan PRs and dead branches**: Close draft PRs from failed agent sessions. Delete remote branches from merged or closed PRs.
    d. **Resolved QC threads**: Ensure both sides of completed cross-repo threads are closed.
13. **Dispatch Copilot for coverage expansion** — MANDATORY when uncovered types exist and < 2 agents are in-flight:
    a. **Classify uncovered types** before dispatch. Check `schema_types.uncovered_standalone` and `schema_types.uncovered_building_block` in state.json:
       - **Standalone types**: Can produce valid standalone rich results (e.g., Accommodation, Article). Full pipeline: generate script + unit test + E2E test + parity entry.
       - **Building-block types**: Designed to be nested inside parent types (e.g., QuantitativeValue, GeoShape, Schedule). Reduced pipeline: unit test only. No standalone generate script or E2E test — these types are validated through their parent types.
       - **Enum types**: Pure enumerations (DayOfWeek, ItemAvailability, etc.). Skip entirely — validated through parent types. Track in `schema_types.enums`, not in the uncovered list.
    b. Batch 10-15 types per issue spec. gpt-5.4 handles large, complex tasks well — give it volume and complexity rather than small simple tasks.
    c. Each issue spec must include: type classification (standalone vs building-block), files to create, example field values, patterns to follow, and explicit instructions on what NOT to create for building-block types.
    d. Dispatch via `gh api /repos/EvaLok/schema-org-json-ld-qc/issues --method POST --input` with `agent_assignment` and `model: gpt-5.4`.
    e. Update `agent_sessions.in_flight` in state.json after dispatch.
    f. **Never end a session with 0 in-flight agents when uncovered types exist.** Coverage expansion is core work, not optional.
14. **Review completed Copilot PRs** — MANDATORY before any new dispatch (step 13):
    a. **Stale dispatch detection and diagnosis**: For each `in_flight` agent session in state.json, check if Copilot has created a PR or posted any comment on the issue. If no Copilot activity and the dispatch is >2 hours old:
       1. **Diagnose**: Read the issue comments. If Copilot posted an error message (e.g. "service temporarily unavailable"), record the error class. If no comments at all, record as "silent failure".
       2. **Close** the issue, move to `completed` with `status: "failed"` and `failure_mode` (one of: `service_outage`, `silent_failure`, `spec_error`, `unknown`).
       3. **Re-dispatch** immediately, linking the new issue to the failed one.
       4. **Document**: Add a `failure_analysis` entry to `copilot_metrics` in state.json with `{issue, failure_mode, timestamp, re_dispatch_issue}`.
       Historical post-mortem (per [audit #83](https://github.com/EvaLok/schema-org-json-ld-audit/issues/83)): All 3 failures (#42, #151, #152) were Copilot infrastructure issues (1 explicit service outage, 2 silent failures from near-simultaneous dispatch). No spec quality issues identified. Re-dispatches succeeded in all cases.
    b. Query open PRs from dispatched issues: `gh pr list -R EvaLok/schema-org-json-ld-qc --state open`.
    c. For each Copilot PR with `copilot_work_finished`:
       - Verify generate scripts produce valid JSON-LD (`php src/generate-*.php`)
       - Verify unit tests follow existing patterns and pass (`php vendor/bin/phpunit`)
       - Run full E2E validation (`bash tools/validate-all.sh`)
       - Run TS parity check (`bun tools/ts-parity-check.ts`) to confirm no regressions
       - Mark as ready and merge if passing, request changes if not (with specific feedback)
    d. After merge: pull changes (`git pull origin master`), run full validation suite to catch integration issues.
    e. Update `agent_sessions`: move from `in_flight` to `completed`, record PR number and outcome.
    f. Update `schema_types`: move newly covered types from `uncovered` to `covered` in state.json.
    g. Close the dispatching issue with a summary of what was delivered.
    h. **Expand parity coverage**: For each newly covered type from the merged PR, add a parity entry to `tools/ts-parity-check.ts` matching the PHP generate script's data. Run `bun tools/ts-parity-check.ts` to verify. Update `ts_validation.parity_results.types_tested` and count in state.json. This ensures parity coverage advances in lockstep with E2E coverage.
    i. **Update Copilot metrics**: After processing all PRs and failed dispatches, update `copilot_metrics` in state.json with current `dispatch_to_pr_rate` and `pr_merge_rate`. **Append to `dispatch_log`** (per [audit #98](https://github.com/EvaLok/schema-org-json-ld-audit/issues/98)): add an entry for each dispatch outcome recorded this session with `{issue, status, pr_number, failure_mode}`.
15. **Plan remaining session work** — After reviews and dispatch, prioritise: validation re-runs > cross-repo communication > process improvements.

## Documentation conventions

### Clickable links

All issue/PR references in journal entries, worklog entries, and comments should use clickable markdown links:

```markdown
# Good — clickable, unambiguous
[#81](https://github.com/EvaLok/schema-org-json-ld-qc/issues/81)
[EvaLok/schema-org-json-ld#222](https://github.com/EvaLok/schema-org-json-ld/issues/222)

# Avoid — not clickable in all contexts
#81
EvaLok/schema-org-json-ld#222
```

### Journal entries

Journal entries live in `docs/journal/YYYY-MM-DD.md` (one file per calendar date). Each entry is headed:

```markdown
## Session Title ([#N](https://github.com/EvaLok/schema-org-json-ld-qc/issues/N))
```

The stub `JOURNAL.md` in the repo root links to all per-date files.

### Worklog entries

Worklog entries live in `docs/worklog/YYYY-MM-DD/HHMMSS-descriptive-title.md`. Include these sections:

- **Summary** — 2-3 sentence overview
- **What Happened** — detailed narrative
- **Self-modifications** — (when applicable) list any changes to orchestrator infrastructure: STARTUP_CHECKLIST.md, AGENTS.md, skills, tools/, permissions, or workflow files. Include brief rationale for each change.
- **Current State** — test counts, warning counts, coverage
- **Next Steps** — explicit priorities for the next cycle

## Sandbox-safe commands

Avoid `$()`, `${}`, `printenv`, `env`, and `chmod` in direct Bash calls — they trigger permission issues. Use the tools/ scripts which handle these internally.
