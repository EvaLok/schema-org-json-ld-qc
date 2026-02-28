# Startup Checklist

Follow this checklist at the start of every orchestrator cycle.

## Quick start (using tools)

Run `bash tools/session-init.sh <issue-number>` to post the opening comment and capture env info.
Run `bash tools/poll-repos.sh` to check all cross-repo communication status.
Run `bash tools/discover-types.sh` to find uncovered types.
Run `bash tools/validate-all.sh` to run the full test suite.

## Full checklist

1. **Post opening comment** — `bash tools/session-init.sh <issue>` (captures timestamp, run ID).
2. **Check for `input-from-eva` issues** — these take priority over everything else.
3. **Recover context** — Read latest worklog entry and `state.json`.
4. **Steady-state check** — After recovering context, compare current state against last cycle. If ALL of the following are true, this is an idle cycle:
   - (a) No new package commits since last cycle (`composer update` shows no change)
   - (b) No open issues/PRs requiring action (no input-from-eva, no qc-outbound requests, no unreviewed Copilot PRs)
   - (c) No new QC reports or requests from the main repo
   - (d) No unprocessed audit recommendations
   If idle: post a brief comment "No changes detected since session N, skipping", increment `consecutive_idle_cycles` in state.json, close the issue, and exit. Do NOT write a worklog entry, journal entry, or commit. If `consecutive_idle_cycles` exceeds 3, note in the closing comment that cron frequency reduction should be considered.
   If NOT idle: reset `consecutive_idle_cycles` to 0 and continue with the full checklist.
5. **Update package** — `composer update evabee/schema-org-json-ld` and note the commit hash.
6. **Poll repos** — `bash tools/poll-repos.sh` (checks qc-outbound, qc-inbound, input-from-eva, open PRs).
7. **Discover new types** — `bash tools/discover-types.sh`.
8. **Run validation suite** — `bash tools/validate-all.sh` (unit tests + E2E).
9. **Report new failures** — Use `bash tools/gh-post.sh create-issue <title> <body-file> qc-outbound`.
10. **Check audit repo** — Poll `EvaLok/schema-org-json-ld-audit` for `audit-outbound` issues (process recommendations). Evaluate, accept/reject, track in state file.
11. **Check false positive documentation** — When false positives are tracked in state.json, verify they are documented in the main repo's user-facing docs. If not, file a QC-REPORT recommending documentation.
12. **Housekeeping** — Clean up stale issues, orphan PRs, dead branches.
13. **Plan session work** — Prioritise reviews and validation over new test development.

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
- **Current State** — test counts, warning counts, coverage
- **Next Steps** — explicit priorities for the next cycle

## Sandbox-safe commands

Avoid `$()`, `${}`, `printenv`, `env`, and `chmod` in direct Bash calls — they trigger permission issues. Use the tools/ scripts which handle these internally.
