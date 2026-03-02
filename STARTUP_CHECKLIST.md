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
5. **Poll repos** — `bash tools/poll-repos.sh` (checks qc-outbound, qc-inbound, input-from-eva, open PRs).
6. **Discover new types** — `bash tools/discover-types.sh`. **Dual-language consistency**: compare PHP class count (`php/src/v1/`) against TS class count (`ts/src/`). If a new PHP class exists without a TS equivalent (or vice versa), file a `qc-outbound` issue flagging the parity gap — this is a regression, not just a coverage gap.
7. **Run validation suite** — `bash tools/validate-all.sh` (unit tests + E2E). Also run `bun tools/ts-parity-check.ts` for TypeScript parity validation (compares TS JSON-LD output against PHP baseline + Adobe validator). **Warning baseline check**: after validation, compare E2E warning counts against `warning_baselines` in state.json. If total warnings increase, investigate before updating — new warnings may indicate real regressions, not false positives. Only update baselines after confirming new warnings are false positives.
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
    a. Check `schema_types.uncovered` in state.json for types without consumer project tests.
    b. Batch 4-6 related types per issue spec (group by domain: commerce, content, location, etc.).
    c. Each issue spec must include: the schema types to cover, what files to create (`src/generate-*.php`, `tests/Unit/*Test.php`, `tests/E2E/*.spec.ts`), example field values, and the existing patterns to follow.
    d. Dispatch via `gh api /repos/EvaLok/schema-org-json-ld-qc/issues --method POST --input` with `agent_assignment` and `model: gpt-5.3-codex`.
    e. Update `agent_sessions.in_flight` in state.json after dispatch.
    f. **Never end a session with 0 in-flight agents when uncovered types exist.** Coverage expansion is core work, not optional.
14. **Plan remaining session work** — After dispatch, prioritise: PR reviews > validation re-runs > cross-repo communication > process improvements.

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
