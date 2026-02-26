---
name: orchestrator-permissions
description: Documentation of allowed Bash commands, blocked constructs, and reliable patterns for the QC orchestrator workflow permission model.
user-invocable: false
---

# QC Orchestrator Permission Model

## Allowed commands

The QC orchestrator workflow permits these commands:

| Command | Examples |
|---------|----------|
| `gh *` | `gh api ...`, `gh issue list ...`, `gh issue comment ...` |
| `git *` | `git add`, `git commit`, `git push` |
| `php *` | `php vendor/bin/phpunit -c phpunit.xml --testsuite=unit` |
| `bun *` | `bun run scripts/validate.ts`, `bun install`, `bunx playwright test` |
| `composer *` | `composer update`, `composer install`, `composer run test-unit` |
| `bash tools/*.sh` | `bash tools/session-init.sh`, `bash tools/validate-all.sh` |
| `jq *` | `jq '.field' file.json` |
| `mkdir *` | `mkdir -p docs/worklog/2026-02-25` |
| `ls *` | `ls tools/`, `ls -la src/` |
| `date *` | `date -u '+%Y-%m-%d'` |
| `wc *` | `wc -l file` |
| `sort *` | `sort file` |
| `grep *` | `grep -A5 '"evabee/schema-org-json-ld"' composer.lock` |

## BLOCKED constructs (will cause denial and waste turns)

These shell constructs are **always blocked** by the sandbox, regardless of which commands they contain:

| Construct | Example | Why blocked |
|-----------|---------|-------------|
| `${}` substitution | `git commit -m "${VAR}"` | Sandbox blocks parameter substitution |
| `$()` substitution | `git commit -m "$(date)"` | Subprocess invocation |
| Pipes | `gh api ... \| jq ...` | Prefix match only covers first command |
| Compound `&&` / `||` | `git add . && git commit` | Not a single command |
| Heredocs `<<` | `gh api --input - <<'JSON'` | Shell construct |
| For loops | `for f in *.php; do ...; done` | Shell scripting |
| Process substitution `<()` | `gh api --input <(jq ...)` | Requires bash subprocess |
| Redirects `>` `>>` | `jq ... > file.json` | Output redirection blocked |
| Semicolons | `git add .; git commit` | Multiple commands |

### Key rule

**Each Bash tool call must be a single, simple command with no shell constructs.** If you need compound operations, use separate Bash tool calls or use the `tools/*.sh` helper scripts.

## NOT allowed directly

- `echo`, `printf` — use Write tool instead
- `cat`, `head`, `tail` — use Read tool instead
- `chmod` — invoke scripts with `bash tools/script.sh`
- `env`, `printenv` — cannot inspect environment
- `curl`, `wget` — use `gh api` or WebFetch

## Reliable patterns

### Running tests

```bash
php vendor/bin/phpunit -c phpunit.xml --testsuite=unit
```

```bash
bunx playwright test
```

Or use the all-in-one helper:

```bash
bash tools/validate-all.sh
```

### Posting comments

Write the body to a file first with the **Write** tool, then:

```bash
bash tools/gh-post.sh comment <issue-number> /tmp/comment-body.md
```

Or directly:

```bash
gh issue comment <NUMBER> -R EvaLok/schema-org-json-ld-qc --body-file /tmp/comment.md
```

### Creating cross-repo issues

Write the JSON payload with the **Write** tool, then:

```bash
gh api /repos/EvaLok/schema-org-json-ld/issues --method POST --input /tmp/issue.json
```

### Git commit and push

Use the helper script to avoid `$()` sandbox restriction:

```bash
bash tools/git-commit.sh /tmp/commit-msg.txt file1.php file2.php
```

Or use separate simple commands:

```bash
git add src/generate-article.php tests/Unit/ArticleTest.php
```
```bash
git commit -m "Add Article validation tests"
```
```bash
git push
```

### Updating the library dependency

```bash
composer update evabee/schema-org-json-ld
```

### Checking library version

```bash
grep -A5 '"evabee/schema-org-json-ld"' composer.lock
```

### Getting timestamps

```bash
date -u '+%Y-%m-%dT%H:%M:%SZ'
```
