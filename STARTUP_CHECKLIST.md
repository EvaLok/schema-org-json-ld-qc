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
4. **Update package** — `composer update evabee/schema-org-json-ld` and note the commit hash.
5. **Poll repos** — `bash tools/poll-repos.sh` (checks qc-outbound, qc-inbound, input-from-eva, open PRs).
6. **Discover new types** — `bash tools/discover-types.sh`.
7. **Run validation suite** — `bash tools/validate-all.sh` (unit tests + E2E).
8. **Report new failures** — Use `bash tools/gh-post.sh create-issue <title> <body-file> qc-outbound`.
9. **Housekeeping** — Clean up stale issues, orphan PRs, dead branches.
10. **Plan session work** — Prioritise reviews and validation over new test development.

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
