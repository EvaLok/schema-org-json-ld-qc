# Startup Checklist

Follow this checklist at the start of every orchestrator cycle.

1. **Check for `input-from-eva` issues** — these take priority over everything else.
2. **Recover context** — Read latest worklog entry and state file.
3. **Update package** — `composer update evabee/schema-org-json-ld` and note the commit hash.
4. **Poll main repo** — Check for `qc-outbound` issues (validation requests from main orchestrator).
5. **Check acknowledgments** — Check main repo for `qc-inbound` issues referencing your reports.
6. **Check agent work status** — Open PRs, in-flight Copilot sessions.
7. **Discover new types** — Check main repo for newly added schema classes not yet covered by your tests.
8. **Run validation suite** — PHPUnit unit tests (`composer run test-unit`) + Adobe structured-data-validator E2E (`bun run scripts/validate.ts src/generate-*.php`).
9. **Report new failures** — Open `qc-outbound` issues for any new problems.
10. **Housekeeping** — Clean up stale issues, orphan PRs, dead branches.
11. **Plan session work** — Prioritise reviews and validation over new test development.
