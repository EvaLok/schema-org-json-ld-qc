# Coding Agent Instructions

This is a **QC/validation project**, not a library. It consumes `evabee/schema-org-json-ld` as a Composer dependency and validates its JSON-LD output.

## Project structure

- `src/` — PHP scripts that instantiate schema types with realistic data and output JSON-LD
- `src/Fixtures/` — Realistic test data per schema type
- `tests/Unit/` — PHPUnit structural assertions on JSON-LD output
- `tests/E2E/` — Playwright specs that submit JSON-LD to Google Rich Results Test
- `results/` — Structured test results

## Key rules

- **Never modify the `evabee/schema-org-json-ld` package** — only consume it via Composer
- **Tab indentation** everywhere (matching main repo convention)
- **PHP 8.1+ minimum** — do not use features unavailable in 8.1
- **One PHPUnit test class per schema type** in `tests/Unit/`
- **One Playwright spec per schema type** in `tests/E2E/`
- **Use TDD**: write failing tests first, then implement the code to make them pass

## Testing

- PHPUnit 10.x: `composer run test-unit`
- Playwright: `npx playwright test`
- CI only runs PHPUnit (`php-tests` job). E2E tests are the orchestrator's responsibility.

## Composer dependency

The package is pulled from a VCS repository:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/EvaLok/schema-org-json-ld"
        }
    ],
    "require": {
        "evabee/schema-org-json-ld": "dev-master"
    }
}
```

Run `composer update evabee/schema-org-json-ld` to pull the latest version.

## Playwright / E2E patterns

- Use headless Chromium
- Submit JSON-LD wrapped in minimal HTML via the Rich Results Test code snippet input
- Distinguish between package failures (real bugs) and infrastructure failures (CAPTCHAs, rate limiting)
- Store results in `results/` with: schema type, timestamp, package commit, pass/fail, errors, the JSON-LD tested
