# Claude Code Configuration

## Sandbox-safe command patterns

The Claude Code sandbox blocks certain shell constructs. Use these patterns instead:

### Getting timestamps
```bash
# BAD: gh issue comment 11 -b "Timestamp: $(date -u)"
# GOOD: Run date separately, then use the value
date -u '+%Y-%m-%dT%H:%M:%SZ'
# Then use the output in your next command
```

### Posting GitHub comments
```bash
# For short bodies: use -b with simple text (no $() or ${})
gh issue comment 11 -R EvaLok/schema-org-json-ld-qc -b "Simple text here"

# For long bodies: write to a temp file first, then use -F
# Step 1: Write the body using the Write tool
# Step 2: gh issue comment 11 -R EvaLok/schema-org-json-ld-qc -F /tmp/comment-body.md
```

### Creating issues
```bash
# Use gh api with --input for complex issue creation
gh api /repos/EvaLok/schema-org-json-ld-qc/issues --method POST --input /tmp/issue.json
```

### Running tests
```bash
# Unit tests (from project root)
php vendor/bin/phpunit -c phpunit.xml --testsuite=unit

# E2E validation
bun run scripts/validate.ts src/generate-*.php
```

### Reading package info
```bash
# Use grep on composer.lock (not $() substitution)
grep -A5 '"evabee/schema-org-json-ld"' composer.lock
```

### Git commits
```bash
# BAD: git commit -m "$(cat <<'EOF' ... EOF)"  — $() is blocked
# GOOD: Write the message to a file first, then use the helper
# Step 1: Use the Write tool to create /tmp/commit-msg.txt
# Step 2: bash tools/git-commit.sh /tmp/commit-msg.txt [files...]
```

### Avoid these constructs in direct Bash calls
- `$()` — command substitution
- `${}` — parameter expansion (use plain `$VAR` instead, or avoid)
- `printenv` / `env` — use grep on specific files instead
- `chmod` — invoke scripts with `bash script.sh` instead

## Helper tools

See `tools/` directory for shell scripts that encapsulate complex operations.
Invoke with `bash tools/<script>.sh` (from project root).

## Permissions

The following commands should be allowed without prompting:
- `gh issue comment` / `gh issue close` / `gh issue create` / `gh api` (GitHub operations)
- `php vendor/bin/phpunit` (test runner)
- `bun run scripts/validate.ts` (E2E validation)
- `composer update` / `composer install` (dependency management)
- `bash tools/*.sh` (helper scripts)
- `git add` / `git commit` / `git push` (version control)
